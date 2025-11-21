<?php
class Booking
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== DANH SÁCH BOOKING ====================

    public function getAll($filters = [])
    {
        $sql = "SELECT 
                    b.*,
                    t.tour_name,
                    t.code as tour_code,
                    t.duration_days,
                    c.full_name as customer_name,
                    c.phone as customer_phone,
                    c.email as customer_email
                FROM bookings b
                JOIN tours t ON b.tour_id = t.tour_id
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['tour_id'])) {
            $sql .= " AND b.tour_id = ?";
            $params[] = $filters['tour_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND b.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                c.full_name LIKE ? 
                OR c.phone LIKE ? 
                OR b.booking_id LIKE ? 
                OR t.tour_name LIKE ?
                OR b.organization_name LIKE ?
                OR b.contact_name LIKE ?
                OR b.contact_phone LIKE ?
            )";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================== CHI TIẾT BOOKING ====================

    public function getById($id)
    {
        $sql = "SELECT 
                    b.*,
                    t.tour_name,
                    t.code as tour_code,
                    t.duration_days,
                    c.full_name as customer_name,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    c.address as customer_address
                FROM bookings b
                JOIN tours t ON b.tour_id = t.tour_id
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                WHERE b.booking_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ==================== LẤY CHI TIẾT BOOKING ====================

    public function getBookingDetails($booking_id)
    {
        $sql = "SELECT * FROM booking_details WHERE booking_id = ? ORDER BY detail_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }

    // ==================== LẤY BOOKING THEO TOUR ====================

    public function getByTour($tour_id)
    {
        $sql = "SELECT 
                    b.*,
                    c.full_name as customer_name,
                    c.phone as customer_phone
                FROM bookings b
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                WHERE b.tour_id = ?
                ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    // ==================== TẠO BOOKING ====================

    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            // Kiểm tra số chỗ trống nếu có tour_date
            if (!empty($data['tour_date'])) {
                $checkSql = "SELECT 
                    COALESCE(SUM(b.num_adults + b.num_children + b.num_infants), 0) as total_booked,
                    COALESCE(MAX(ts.max_participants), 0) as max_capacity
                FROM tour_schedules ts
                LEFT JOIN bookings b ON ts.schedule_id = (SELECT schedule_id FROM tour_schedules WHERE tour_id = ? AND departure_date = ? LIMIT 1)
                WHERE ts.tour_id = ? AND ts.departure_date = ? AND ts.status IN ('Open', 'Confirmed')";

                $stmt = $this->conn->prepare($checkSql);
                $stmt->execute([$data['tour_id'], $data['tour_date'], $data['tour_id'], $data['tour_date']]);
                $availability = $stmt->fetch();

                if ($availability) {
                    $totalGuests = ($data['num_adults'] ?? 0) + ($data['num_children'] ?? 0) + ($data['num_infants'] ?? 0);
                    $available = $availability['max_capacity'] - $availability['total_booked'];

                    if ($availability['max_capacity'] > 0 && $available < $totalGuests) {
                        throw new Exception("Chỉ còn {$available} chỗ trống cho ngày này!");
                    }
                }
            }

            // Tạo booking - Đảm bảo customer_id là NULL chứ không phải empty string
            $sql = "INSERT INTO bookings (
                        tour_id, tour_date, customer_id, booking_type, organization_name,
                        contact_name, contact_phone, contact_email,
                        num_adults, num_children, num_infants, special_requests,
                        status, total_amount
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['tour_id'] ?: null,
                $data['tour_date'] ?: null,
                $data['customer_id'] ?: null,
                $data['booking_type'] ?? 'Cá nhân',
                $data['organization_name'] ?: null,
                $data['contact_name'] ?: null,
                $data['contact_phone'] ?: null,
                $data['contact_email'] ?: null,
                (int) ($data['num_adults'] ?? 0),
                (int) ($data['num_children'] ?? 0),
                (int) ($data['num_infants'] ?? 0),
                $data['special_requests'] ?: null,
                $data['status'] ?? 'Chờ xác nhận',
                (float) ($data['total_amount'] ?? 0)
            ]);

            $booking_id = $this->conn->lastInsertId();

            // Thêm booking details nếu có
            if (!empty($data['details'])) {
                $sql = "INSERT INTO booking_details (booking_id, service_name, quantity, unit_price) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);

                foreach ($data['details'] as $detail) {
                    if (!empty($detail['service_name'])) {
                        $stmt->execute([
                            $booking_id,
                            $detail['service_name'],
                            $detail['quantity'] ?? 1,
                            $detail['unit_price'] ?? 0
                        ]);
                    }
                }
            }

            $this->conn->commit();
            return $booking_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }    // ==================== CẬP NHẬT BOOKING ====================

    public function update($id, $data)
    {
        $sql = "UPDATE bookings SET
                    tour_id = ?,
                    tour_date = ?,
                    customer_id = ?,
                    booking_type = ?,
                    organization_name = ?,
                    contact_name = ?,
                    contact_phone = ?,
                    contact_email = ?,
                    num_adults = ?,
                    num_children = ?,
                    num_infants = ?,
                    special_requests = ?,
                    total_amount = ?,
                    status = ?
                WHERE booking_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'] ?: null,
            $data['tour_date'] ?: null,
            $data['customer_id'] ?: null,
            $data['booking_type'] ?? 'Cá nhân',
            $data['organization_name'] ?: null,
            $data['contact_name'] ?: null,
            $data['contact_phone'] ?: null,
            $data['contact_email'] ?: null,
            (int) ($data['num_adults'] ?? 0),
            (int) ($data['num_children'] ?? 0),
            (int) ($data['num_infants'] ?? 0),
            $data['special_requests'] ?: null,
            (float) ($data['total_amount'] ?? 0),
            $data['status'] ?? 'Chờ xác nhận',
            $id
        ]);
    }

    // ==================== CẬP NHẬT TRẠNG THÁI ====================

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    // ==================== HỦY BOOKING ====================

    public function cancel($id)
    {
        return $this->updateStatus($id, 'Hủy');
    }

    // ==================== THỐNG KÊ ====================

    public function getStatistics()
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as total,
                    SUM(total_amount) as total_revenue
                FROM bookings
                GROUP BY status";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ==================== THỐNG KÊ DOANH THU THEO TOUR ====================

    public function getRevenuByTour($tour_id)
    {
        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(num_adults + num_children + num_infants) as total_guests,
                    SUM(total_amount) as total_revenue
                FROM bookings
                WHERE tour_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetch();
    }

    // ==================== XÓA BOOKING DETAIL ====================

    public function deleteBookingDetail($detail_id)
    {
        $sql = "DELETE FROM booking_details WHERE detail_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$detail_id]);
    }

    // ==================== THÊM BOOKING DETAIL ====================

    public function addBookingDetail($booking_id, $service_name, $quantity, $unit_price)
    {
        $sql = "INSERT INTO booking_details (booking_id, service_name, quantity, unit_price) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$booking_id, $service_name, $quantity, $unit_price]);
    }
}
