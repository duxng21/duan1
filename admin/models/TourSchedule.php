<?php
class TourSchedule
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== LỊCH KHỞI HÀNH ====================

    public function getAllSchedules()
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code,
                    tc.category_name,
                    COUNT(DISTINCT tb.booking_id) as total_bookings,
                    COALESCE(SUM(tb.number_of_guests), 0) as total_guests
                FROM tour_schedules ts
                LEFT JOIN tours t ON ts.tour_id = t.tour_id
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_bookings tb ON ts.schedule_id = tb.schedule_id
                GROUP BY ts.schedule_id
                ORDER BY ts.departure_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSchedulesByTour($tour_id)
    {
        $sql = "SELECT 
                    ts.*,
                    COUNT(DISTINCT tb.booking_id) as total_bookings,
                    COALESCE(SUM(tb.number_of_guests), 0) as total_guests
                FROM tour_schedules ts
                LEFT JOIN tour_bookings tb ON ts.schedule_id = tb.schedule_id
                WHERE ts.tour_id = ?
                GROUP BY ts.schedule_id
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function getScheduleById($id)
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code,
                    COUNT(DISTINCT tb.booking_id) as total_bookings,
                    COALESCE(SUM(tb.number_of_guests), 0) as total_guests
                FROM tour_schedules ts
                LEFT JOIN tours t ON ts.tour_id = t.tour_id
                LEFT JOIN tour_bookings tb ON ts.schedule_id = tb.schedule_id
                WHERE ts.schedule_id = ?
                GROUP BY ts.schedule_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAvailableSchedules()
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code,
                    (ts.max_participants - ts.current_participants) as slots_available
                FROM tour_schedules ts
                LEFT JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ts.status IN ('Open', 'Confirmed')
                AND ts.departure_date >= CURDATE()
                AND ts.current_participants < ts.max_participants
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function checkScheduleConflict($tour_id, $departure_date, $exclude_schedule_id = null)
    {
        $sql = "SELECT COUNT(*) FROM tour_schedules 
                WHERE tour_id = ? 
                AND departure_date = ?
                AND status != 'Cancelled'";

        $params = [$tour_id, $departure_date];

        if ($exclude_schedule_id) {
            $sql .= " AND schedule_id != ?";
            $params[] = $exclude_schedule_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function createSchedule($data)
    {
        try {
            // Kiểm tra trùng lịch
            if ($this->checkScheduleConflict($data['tour_id'], $data['departure_date'])) {
                throw new Exception("Đã có lịch khởi hành cho tour này vào ngày đã chọn!");
            }

            $sql = "INSERT INTO tour_schedules (
                        tour_id, departure_date, return_date, meeting_point, 
                        meeting_time, max_participants, current_participants, 
                        price_adult, price_child, status, notes
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['tour_id'],
                $data['departure_date'],
                $data['return_date'],
                $data['meeting_point'] ?? '',
                $data['meeting_time'] ?? '',
                $data['max_participants'] ?? 0,
                0, // current_participants
                $data['price_adult'] ?? 0,
                $data['price_child'] ?? 0,
                $data['status'] ?? 'Open',
                $data['notes'] ?? ''
            ]);

            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateSchedule($id, $data)
    {
        // Kiểm tra trùng lịch (trừ chính nó)
        if ($this->checkScheduleConflict($data['tour_id'], $data['departure_date'], $id)) {
            throw new Exception("Đã có lịch khởi hành cho tour này vào ngày đã chọn!");
        }

        $sql = "UPDATE tour_schedules SET
                    departure_date = ?,
                    return_date = ?,
                    meeting_point = ?,
                    meeting_time = ?,
                    max_participants = ?,
                    price_adult = ?,
                    price_child = ?,
                    status = ?,
                    notes = ?
                WHERE schedule_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_date'],
            $data['return_date'],
            $data['meeting_point'] ?? '',
            $data['meeting_time'] ?? '',
            $data['max_participants'] ?? 0,
            $data['price_adult'] ?? 0,
            $data['price_child'] ?? 0,
            $data['status'] ?? 'Open',
            $data['notes'] ?? '',
            $id
        ]);
    }

    public function deleteSchedule($id)
    {
        // Kiểm tra xem có booking nào chưa
        $sql = "SELECT COUNT(*) FROM tour_bookings WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Không thể xóa lịch khởi hành đã có đặt tour!");
        }

        $sql = "DELETE FROM tour_schedules WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ==================== NHÂN SỰ ====================

    public function getAllStaff($type = null)
    {
        $sql = "SELECT * FROM staff WHERE status = 1";
        if ($type) {
            $sql .= " AND staff_type = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getScheduleStaff($schedule_id)
    {
        $sql = "SELECT 
                    ss.*,
                    s.full_name,
                    s.phone,
                    s.staff_type
                FROM schedule_staff ss
                JOIN staff s ON ss.staff_id = s.staff_id
                WHERE ss.schedule_id = ?
                ORDER BY s.staff_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    public function checkStaffAvailability($staff_id, $departure_date, $return_date, $exclude_schedule_id = null)
    {
        $sql = "SELECT COUNT(*) FROM schedule_staff ss
                JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                WHERE ss.staff_id = ?
                AND ts.status != 'Cancelled'
                AND (
                    (ts.departure_date BETWEEN ? AND ?)
                    OR (ts.return_date BETWEEN ? AND ?)
                    OR (? BETWEEN ts.departure_date AND ts.return_date)
                )";

        $params = [$staff_id, $departure_date, $return_date, $departure_date, $return_date, $departure_date];

        if ($exclude_schedule_id) {
            $sql .= " AND ts.schedule_id != ?";
            $params[] = $exclude_schedule_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    public function assignStaff($schedule_id, $staff_id, $role)
    {
        // Kiểm tra xem nhân viên đã được phân công chưa
        $sql = "SELECT COUNT(*) FROM schedule_staff 
                WHERE schedule_id = ? AND staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id, $staff_id]);

        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Nhân viên này đã được phân công cho lịch khởi hành này!");
        }

        $sql = "INSERT INTO schedule_staff (schedule_id, staff_id, role) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $staff_id, $role]);
    }

    public function removeStaff($schedule_id, $staff_id)
    {
        $sql = "DELETE FROM schedule_staff 
                WHERE schedule_id = ? AND staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $staff_id]);
    }

    // ==================== DỊCH VỤ ====================

    public function getAllServices($type = null)
    {
        $sql = "SELECT * FROM services WHERE status = 1";
        if ($type) {
            $sql .= " AND service_type = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getScheduleServices($schedule_id)
    {
        $sql = "SELECT 
                    sserv.*,
                    serv.service_name,
                    serv.service_type,
                    serv.provider_name,
                    serv.contact_phone
                FROM schedule_services sserv
                JOIN services serv ON sserv.service_id = serv.service_id
                WHERE sserv.schedule_id = ?
                ORDER BY serv.service_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    public function assignService($schedule_id, $service_id, $quantity, $unit_price, $notes)
    {
        $sql = "INSERT INTO schedule_services (schedule_id, service_id, quantity, unit_price, notes) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    quantity = VALUES(quantity), 
                    unit_price = VALUES(unit_price),
                    notes = VALUES(notes)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $service_id, $quantity, $unit_price, $notes]);
    }

    public function removeService($schedule_id, $service_id)
    {
        $sql = "DELETE FROM schedule_services 
                WHERE schedule_id = ? AND service_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $service_id]);
    }

    // ==================== BÁO CÁO & XUẤT ====================

    public function getScheduleReport($schedule_id)
    {
        $schedule = $this->getScheduleById($schedule_id);
        $staff = $this->getScheduleStaff($schedule_id);
        $services = $this->getScheduleServices($schedule_id);

        return [
            'schedule' => $schedule,
            'staff' => $staff,
            'services' => $services
        ];
    }

    public function getCalendarView($month, $year)
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE MONTH(ts.departure_date) = ? 
                AND YEAR(ts.departure_date) = ?
                AND ts.status != 'Cancelled'
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$month, $year]);
        return $stmt->fetchAll();
    }
}
