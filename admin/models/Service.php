<?php
class Service
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== DANH SÁCH DỊCH VỤ ====================

    public function getAll()
    {
        $sql = "SELECT s.*, p.partner_name, p.partner_type, p.phone as partner_phone
                FROM services s
                LEFT JOIN partners p ON s.partner_id = p.partner_id
                ORDER BY s.service_type, s.service_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActive()
    {
        $sql = "SELECT s.*, p.partner_name
                FROM services s
                LEFT JOIN partners p ON s.partner_id = p.partner_id
                WHERE s.status = 1 AND p.status = 1
                ORDER BY s.service_type, s.service_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT s.*, p.partner_name, p.partner_type, p.contact_person, p.phone as partner_phone
                FROM services s
                LEFT JOIN partners p ON s.partner_id = p.partner_id
                WHERE s.service_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByType($type)
    {
        $sql = "SELECT s.*, p.partner_name
                FROM services s
                LEFT JOIN partners p ON s.partner_id = p.partner_id
                WHERE s.service_type = ? AND s.status = 1 AND p.status = 1
                ORDER BY s.service_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    public function getByPartner($partner_id)
    {
        $sql = "SELECT * FROM services WHERE partner_id = ? ORDER BY service_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$partner_id]);
        return $stmt->fetchAll();
    }

    // ==================== THÊM DỊCH VỤ ====================

    public function create($data)
    {
        // Validate
        if (empty($data['service_name'])) {
            throw new Exception('Tên dịch vụ không được để trống!');
        }

        if (empty($data['partner_id'])) {
            throw new Exception('Vui lòng chọn đối tác cung cấp!');
        }

        // Kiểm tra partner tồn tại
        $checkPartner = "SELECT COUNT(*) FROM partners WHERE partner_id = ?";
        $stmtPartner = $this->conn->prepare($checkPartner);
        $stmtPartner->execute([$data['partner_id']]);
        if ($stmtPartner->fetchColumn() == 0) {
            throw new Exception('Đối tác không tồn tại!');
        }

        $sql = "INSERT INTO services (
                    partner_id, service_name, service_type, description, 
                    unit_price, unit, capacity, location, contact_phone, 
                    provider_name, rating, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['partner_id'],
            $data['service_name'],
            $data['service_type'] ?? 'Other',
            $data['description'] ?? null,
            $data['unit_price'] ?? 0.00,
            $data['unit'] ?? 'pax',
            $data['capacity'] ?? null,
            $data['location'] ?? null,
            $data['contact_phone'] ?? null,
            $data['provider_name'] ?? null,
            $data['rating'] ?? 0.00,
            $data['status'] ?? 1,
            $data['notes'] ?? null
        ]);

        if (!$result) {
            throw new Exception('Thêm dịch vụ thất bại!');
        }

        return $this->conn->lastInsertId();
    }

    // ==================== CẬP NHẬT DỊCH VỤ ====================

    public function update($id, $data)
    {
        // Validate
        if (empty($data['service_name'])) {
            throw new Exception('Tên dịch vụ không được để trống!');
        }

        if (empty($data['partner_id'])) {
            throw new Exception('Vui lòng chọn đối tác cung cấp!');
        }

        $sql = "UPDATE services SET 
                    partner_id = ?,
                    service_name = ?, 
                    service_type = ?, 
                    description = ?, 
                    unit_price = ?, 
                    unit = ?, 
                    capacity = ?, 
                    location = ?, 
                    contact_phone = ?, 
                    provider_name = ?, 
                    rating = ?, 
                    status = ?, 
                    notes = ?
                WHERE service_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['partner_id'],
            $data['service_name'],
            $data['service_type'] ?? 'Other',
            $data['description'] ?? null,
            $data['unit_price'] ?? 0.00,
            $data['unit'] ?? 'pax',
            $data['capacity'] ?? null,
            $data['location'] ?? null,
            $data['contact_phone'] ?? null,
            $data['provider_name'] ?? null,
            $data['rating'] ?? 0.00,
            $data['status'] ?? 1,
            $data['notes'] ?? null,
            $id
        ]);
    }

    // ==================== XÓA DỊCH VỤ ====================

    public function delete($id)
    {
        // Kiểm tra dịch vụ có được sử dụng trong lịch không
        $checkSql = "SELECT COUNT(*) FROM schedule_services WHERE service_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$id]);

        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception('Không thể xóa dịch vụ đã được sử dụng trong lịch khởi hành!');
        }

        $sql = "DELETE FROM services WHERE service_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ==================== KIỂM TRA DỊCH VỤ KHẢ DỤNG ====================

    /**
     * Kiểm tra dịch vụ có đủ capacity cho số lượng yêu cầu không
     */
    public function checkAvailability($service_id, $required_quantity, $date = null)
    {
        $service = $this->getById($service_id);
        
        if (!$service) {
            return ['available' => false, 'message' => 'Dịch vụ không tồn tại!'];
        }

        if ($service['status'] != 1) {
            return ['available' => false, 'message' => 'Dịch vụ hiện không khả dụng!'];
        }

        // Nếu dịch vụ có capacity giới hạn
        if ($service['capacity'] > 0 && $required_quantity > $service['capacity']) {
            return [
                'available' => false, 
                'message' => 'Dịch vụ chỉ đủ cho ' . $service['capacity'] . ' ' . $service['unit'] . '!'
            ];
        }

        // Kiểm tra xem dịch vụ đã được đặt cho ngày này chưa (nếu có date)
        if ($date) {
            $sql = "SELECT SUM(ss.quantity) as total_booked
                    FROM schedule_services ss
                    JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                    WHERE ss.service_id = ? 
                    AND ts.departure_date = ?
                    AND ts.status NOT IN ('Cancelled')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$service_id, $date]);
            $result = $stmt->fetch();
            
            $total_booked = $result['total_booked'] ?? 0;
            $remaining = $service['capacity'] - $total_booked;

            if ($remaining < $required_quantity) {
                return [
                    'available' => false,
                    'message' => 'Dịch vụ chỉ còn ' . $remaining . ' ' . $service['unit'] . ' cho ngày này!',
                    'remaining' => $remaining
                ];
            }
        }

        return ['available' => true, 'message' => 'Dịch vụ khả dụng'];
    }

    // ==================== TÌM KIẾM DỊCH VỤ ====================

    public function search($filters = [])
    {
        $sql = "SELECT s.*, p.partner_name, p.partner_type
                FROM services s
                LEFT JOIN partners p ON s.partner_id = p.partner_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['service_type'])) {
            $sql .= " AND s.service_type = ?";
            $params[] = $filters['service_type'];
        }

        if (!empty($filters['partner_id'])) {
            $sql .= " AND s.partner_id = ?";
            $params[] = $filters['partner_id'];
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND s.status = ?";
            $params[] = (int)$filters['status'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND s.unit_price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND s.unit_price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['location'])) {
            $sql .= " AND s.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (s.service_name LIKE ? OR s.description LIKE ? OR s.provider_name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY s.service_type, s.service_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================== THỐNG KÊ DỊCH VỤ ====================

    public function getStatistics()
    {
        $sql = "SELECT 
                    service_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_count,
                    AVG(unit_price) as avg_price,
                    AVG(rating) as avg_rating
                FROM services
                GROUP BY service_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUsageCount($service_id)
    {
        $sql = "SELECT COUNT(DISTINCT schedule_id) as usage_count
                FROM schedule_services 
                WHERE service_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$service_id]);
        $result = $stmt->fetch();
        return $result['usage_count'] ?? 0;
    }

    public function getTotalRevenue($service_id, $from_date = null, $to_date = null)
    {
        $sql = "SELECT SUM(ss.total_price) as total_revenue
                FROM schedule_services ss
                JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                WHERE ss.service_id = ?";
        
        $params = [$service_id];

        if ($from_date) {
            $sql .= " AND ts.departure_date >= ?";
            $params[] = $from_date;
        }

        if ($to_date) {
            $sql .= " AND ts.departure_date <= ?";
            $params[] = $to_date;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total_revenue'] ?? 0;
    }

    // ==================== CẬP NHẬT ĐÁNH GIÁ ====================

    public function updateRating($service_id, $rating)
    {
        if ($rating < 0 || $rating > 5) {
            throw new Exception('Đánh giá phải từ 0 đến 5!');
        }

        $sql = "UPDATE services SET rating = ? WHERE service_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$rating, $service_id]);
    }

    // ==================== GỢI Ý DỊCH VỤ ====================

    /**
     * Gợi ý dịch vụ phù hợp cho tour dựa trên số lượng khách và thời gian
     */
    public function getSuggestedServices($service_type, $guest_count, $date = null)
    {
        $sql = "SELECT s.*, p.partner_name, p.rating as partner_rating
                FROM services s
                LEFT JOIN partners p ON s.partner_id = p.partner_id
                WHERE s.service_type = ? 
                AND s.status = 1 
                AND p.status = 1
                AND (s.capacity >= ? OR s.capacity IS NULL OR s.capacity = 0)
                ORDER BY s.rating DESC, s.unit_price ASC
                LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$service_type, $guest_count]);
        return $stmt->fetchAll();
    }
}
