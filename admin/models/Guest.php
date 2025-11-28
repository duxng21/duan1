<?php

class Guest
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== DANH SÁCH KHÁCH ====================

    /**
     * Lấy danh sách khách theo booking_id
     * 1a. Hệ thống truy xuất danh sách khách
     */
    public function getGuestsByBooking($booking_id, $filters = [])
    {
        $sql = "SELECT 
                    gl.*,
                    b.booking_type,
                    b.status as booking_status,
                    t.tour_name,
                    t.code as tour_code
                FROM guest_list gl
                JOIN bookings b ON gl.booking_id = b.booking_id
                JOIN tours t ON b.tour_id = t.tour_id
                WHERE gl.booking_id = ?";
        
        $params = [$booking_id];

        // A1: Lọc khách theo trạng thái thanh toán
        if (!empty($filters['payment_status'])) {
            $sql .= " AND gl.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        // A1: Lọc khách theo trạng thái check-in
        if (!empty($filters['check_in_status'])) {
            $sql .= " AND gl.check_in_status = ?";
            $params[] = $filters['check_in_status'];
        }

        // A1: Lọc khách theo loại phòng
        if (!empty($filters['room_status'])) {
            if ($filters['room_status'] == 'assigned') {
                $sql .= " AND gl.room_number IS NOT NULL";
            } elseif ($filters['room_status'] == 'unassigned') {
                $sql .= " AND gl.room_number IS NULL";
            }
        }

        $sql .= " ORDER BY gl.full_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách khách theo schedule_id
     */
    public function getGuestsBySchedule($schedule_id, $filters = [])
    {
        $sql = "SELECT 
                    gl.*,
                    b.booking_type,
                    b.status as booking_status,
                    ts.tour_id,
                    t.tour_name,
                    t.code as tour_code
                FROM guest_list gl
                JOIN bookings b ON gl.booking_id = b.booking_id
                JOIN tour_schedules ts ON b.tour_id = ts.tour_id AND DATE(b.tour_date) = DATE(ts.departure_date)
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ts.schedule_id = ?";
        
        $params = [$schedule_id];

        // Áp dụng filters tương tự
        if (!empty($filters['payment_status'])) {
            $sql .= " AND gl.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['check_in_status'])) {
            $sql .= " AND gl.check_in_status = ?";
            $params[] = $filters['check_in_status'];
        }

        if (!empty($filters['room_status'])) {
            if ($filters['room_status'] == 'assigned') {
                $sql .= " AND gl.room_number IS NOT NULL";
            } elseif ($filters['room_status'] == 'unassigned') {
                $sql .= " AND gl.room_number IS NULL";
            }
        }

        $sql .= " ORDER BY gl.full_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin chi tiết 1 khách
     */
    public function getGuestById($guest_id)
    {
        $sql = "SELECT 
                    gl.*,
                    b.booking_type,
                    b.status as booking_status,
                    b.tour_date,
                    t.tour_name,
                    t.code as tour_code
                FROM guest_list gl
                JOIN bookings b ON gl.booking_id = b.booking_id
                JOIN tours t ON b.tour_id = t.tour_id
                WHERE gl.guest_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$guest_id]);
        return $stmt->fetch();
    }

    // ==================== CHECK-IN MANAGEMENT ====================

    /**
     * Bước 3a & 3b: Cập nhật trạng thái check-in và lưu giờ check-in
     */
    public function updateCheckInStatus($guest_id, $status = 'Checked-In')
    {
        // E2: Kiểm tra check-in trùng
        $guest = $this->getGuestById($guest_id);
        if (!$guest) {
            throw new Exception('Khách không tồn tại!');
        }

        if ($guest['check_in_status'] == 'Checked-In' && $status == 'Checked-In') {
            throw new Exception('Khách này đã check-in lúc ' . 
                date('d/m/Y H:i', strtotime($guest['check_in_time'])));
        }

        $sql = "UPDATE guest_list 
                SET check_in_status = ?, 
                    check_in_time = CASE 
                        WHEN ? = 'Checked-In' THEN NOW()
                        ELSE check_in_time 
                    END,
                    updated_at = NOW()
                WHERE guest_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $status, $guest_id]);
    }

    // ==================== ROOM ASSIGNMENT ====================

    /**
     * Bước 4a & 4b: Gán khách vào phòng và lưu thông tin phòng
     */
    public function assignRoom($guest_id, $room_number, $room_type = 'Standard')
    {
        $sql = "UPDATE guest_list 
                SET room_number = ?, 
                    room_type = ?,
                    updated_at = NOW()
                WHERE guest_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$room_number, $room_type, $guest_id]);
    }

    /**
     * Kiểm tra phòng đã được sử dụng chưa
     */
    public function isRoomOccupied($room_number, $booking_id = null)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM guest_list gl
                JOIN bookings b ON gl.booking_id = b.booking_id
                WHERE gl.room_number = ? 
                AND gl.check_in_status = 'Checked-In'";
        
        $params = [$room_number];
        
        if ($booking_id) {
            $sql .= " AND gl.booking_id = ?";
            $params[] = $booking_id;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }

    // ==================== SUMMARY & REPORTS ====================

    /**
     * Bước 5: Báo cáo tóm tắt đoàn theo booking
     */
    public function getGuestSummary($booking_id)
    {
        $sql = "SELECT 
                    COUNT(*) as total_guests,
                    SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female_count,
                    SUM(CASE WHEN is_adult = 1 THEN 1 ELSE 0 END) as adult_count,
                    SUM(CASE WHEN is_adult = 0 THEN 1 ELSE 0 END) as child_count,
                    SUM(CASE WHEN check_in_status = 'Checked-In' THEN 1 ELSE 0 END) as checked_in_count,
                    SUM(CASE WHEN check_in_status = 'No-Show' THEN 1 ELSE 0 END) as no_show_count,
                    SUM(CASE WHEN check_in_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN room_number IS NOT NULL THEN 1 ELSE 0 END) as room_assigned_count,
                    SUM(CASE WHEN payment_status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN payment_status = 'Pending' THEN 1 ELSE 0 END) as pending_payment_count
                FROM guest_list 
                WHERE booking_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetch();
    }

    /**
     * Báo cáo tóm tắt theo schedule
     */
    public function getGuestSummaryBySchedule($schedule_id)
    {
        $sql = "SELECT 
                    COUNT(*) as total_guests,
                    SUM(CASE WHEN gl.gender = 'Male' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN gl.gender = 'Female' THEN 1 ELSE 0 END) as female_count,
                    SUM(CASE WHEN gl.is_adult = 1 THEN 1 ELSE 0 END) as adult_count,
                    SUM(CASE WHEN gl.is_adult = 0 THEN 1 ELSE 0 END) as child_count,
                    SUM(CASE WHEN gl.check_in_status = 'Checked-In' THEN 1 ELSE 0 END) as checked_in_count,
                    SUM(CASE WHEN gl.check_in_status = 'No-Show' THEN 1 ELSE 0 END) as no_show_count,
                    SUM(CASE WHEN gl.check_in_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN gl.room_number IS NOT NULL THEN 1 ELSE 0 END) as room_assigned_count,
                    SUM(CASE WHEN gl.payment_status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN gl.payment_status = 'Pending' THEN 1 ELSE 0 END) as pending_payment_count
                FROM guest_list gl
                JOIN bookings b ON gl.booking_id = b.booking_id
                JOIN tour_schedules ts ON b.tour_id = ts.tour_id AND DATE(b.tour_date) = DATE(ts.departure_date)
                WHERE ts.schedule_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetch();
    }

    /**
     * Thống kê phòng đã phân bổ
     */
    public function getRoomStatistics($booking_id = null, $schedule_id = null)
    {
        if ($booking_id) {
            $sql = "SELECT 
                        room_type,
                        COUNT(*) as guest_count,
                        COUNT(DISTINCT room_number) as room_count
                    FROM guest_list 
                    WHERE booking_id = ? AND room_number IS NOT NULL
                    GROUP BY room_type";
            $params = [$booking_id];
        } elseif ($schedule_id) {
            $sql = "SELECT 
                        gl.room_type,
                        COUNT(*) as guest_count,
                        COUNT(DISTINCT gl.room_number) as room_count
                    FROM guest_list gl
                    JOIN bookings b ON gl.booking_id = b.booking_id
                    JOIN tour_schedules ts ON b.tour_id = ts.tour_id AND DATE(b.tour_date) = DATE(ts.departure_date)
                    WHERE ts.schedule_id = ? AND gl.room_number IS NOT NULL
                    GROUP BY gl.room_type";
            $params = [$schedule_id];
        } else {
            return [];
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================== GUEST MANAGEMENT ====================

    /**
     * Thêm khách mới
     */
    public function createGuest($data)
    {
        // E1: Dữ liệu không khớp booking → cảnh báo lỗi
        $sql = "SELECT booking_id FROM bookings WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$data['booking_id']]);
        
        if (!$stmt->fetch()) {
            throw new Exception('Booking không tồn tại!');
        }

        $sql = "INSERT INTO guest_list (
                    booking_id, full_name, id_card, birth_date, gender, 
                    phone, email, address, is_adult, special_needs, 
                    payment_status, check_in_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['booking_id'],
            $data['full_name'],
            $data['id_card'] ?? null,
            $data['birth_date'] ?? null,
            $data['gender'] ?? 'Other',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['is_adult'] ?? 1,
            $data['special_needs'] ?? null,
            $data['payment_status'] ?? 'Pending'
        ]);
        
        return $result ? $this->conn->lastInsertId() : false;
    }

    /**
     * Cập nhật thông tin khách
     */
    public function updateGuest($guest_id, $data)
    {
        $sql = "UPDATE guest_list SET
                    full_name = ?,
                    id_card = ?,
                    birth_date = ?,
                    gender = ?,
                    phone = ?,
                    email = ?,
                    address = ?,
                    is_adult = ?,
                    special_needs = ?,
                    payment_status = ?,
                    updated_at = NOW()
                WHERE guest_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['id_card'] ?? null,
            $data['birth_date'] ?? null,
            $data['gender'] ?? 'Other',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['is_adult'] ?? 1,
            $data['special_needs'] ?? null,
            $data['payment_status'] ?? 'Pending',
            $guest_id
        ]);
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus($guest_id, $status)
    {
        $sql = "UPDATE guest_list SET payment_status = ?, updated_at = NOW() WHERE guest_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $guest_id]);
    }

    /**
     * Xóa khách
     */
    public function deleteGuest($guest_id)
    {
        // Kiểm tra đã check-in chưa
        $guest = $this->getGuestById($guest_id);
        if ($guest && $guest['check_in_status'] == 'Checked-In') {
            throw new Exception('Không thể xóa khách đã check-in!');
        }

        $sql = "DELETE FROM guest_list WHERE guest_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$guest_id]);
    }

    // ==================== VALIDATION ====================

    /**
     * Validate dữ liệu khách
     */
    public function validateGuestData($data, $isUpdate = false)
    {
        $errors = [];

        if (empty($data['full_name'])) {
            $errors[] = 'Họ tên không được để trống';
        }

        if (!empty($data['birth_date'])) {
            $birthYear = date('Y', strtotime($data['birth_date']));
            $currentYear = date('Y');
            if ($birthYear > $currentYear || $birthYear < ($currentYear - 120)) {
                $errors[] = 'Năm sinh không hợp lệ';
            }
        }

        if (!empty($data['phone'])) {
            if (!preg_match('/^[0-9+\-\s()]{10,15}$/', $data['phone'])) {
                $errors[] = 'Số điện thoại không đúng định dạng';
            }
        }

        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không đúng định dạng';
            }
        }

        if (!in_array($data['gender'] ?? '', ['Male', 'Female', 'Other'])) {
            $errors[] = 'Giới tính không hợp lệ';
        }

        return $errors;
    }

    // ==================== SEARCH & FILTER ====================

    /**
     * Tìm kiếm khách theo từ khóa
     */
    public function searchGuests($keyword, $booking_id = null, $schedule_id = null)
    {
        if ($booking_id) {
            $sql = "SELECT * FROM guest_list 
                    WHERE booking_id = ? 
                    AND (full_name LIKE ? OR id_card LIKE ? OR phone LIKE ?)
                    ORDER BY full_name ASC";
            $params = [$booking_id, "%$keyword%", "%$keyword%", "%$keyword%"];
        } elseif ($schedule_id) {
            $sql = "SELECT gl.* 
                    FROM guest_list gl
                    JOIN bookings b ON gl.booking_id = b.booking_id
                    JOIN tour_schedules ts ON b.tour_id = ts.tour_id AND DATE(b.tour_date) = DATE(ts.departure_date)
                    WHERE ts.schedule_id = ?
                    AND (gl.full_name LIKE ? OR gl.id_card LIKE ? OR gl.phone LIKE ?)
                    ORDER BY gl.full_name ASC";
            $params = [$schedule_id, "%$keyword%", "%$keyword%", "%$keyword%"];
        } else {
            return [];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách phòng đã sử dụng
     */
    public function getUsedRooms($booking_id = null, $schedule_id = null)
    {
        if ($booking_id) {
            $sql = "SELECT DISTINCT room_number, room_type, COUNT(*) as guest_count
                    FROM guest_list 
                    WHERE booking_id = ? AND room_number IS NOT NULL
                    GROUP BY room_number, room_type
                    ORDER BY room_number";
            $params = [$booking_id];
        } elseif ($schedule_id) {
            $sql = "SELECT DISTINCT gl.room_number, gl.room_type, COUNT(*) as guest_count
                    FROM guest_list gl
                    JOIN bookings b ON gl.booking_id = b.booking_id
                    JOIN tour_schedules ts ON b.tour_id = ts.tour_id AND DATE(b.tour_date) = DATE(ts.departure_date)
                    WHERE ts.schedule_id = ? AND gl.room_number IS NOT NULL
                    GROUP BY gl.room_number, gl.room_type
                    ORDER BY gl.room_number";
            $params = [$schedule_id];
        } else {
            return [];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}