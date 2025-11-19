<?php
class Staff
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== DANH SÁCH NHÂN SỰ ====================

    public function getAll()
    {
        $sql = "SELECT * FROM staff ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM staff WHERE staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByType($type)
    {
        $sql = "SELECT * FROM staff WHERE staff_type = ? AND status = 1 ORDER BY full_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    public function getActive()
    {
        $sql = "SELECT * FROM staff WHERE status = 1 ORDER BY staff_type, full_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ==================== THÊM NHÂN SỰ ====================

    public function create($data)
    {
        $sql = "INSERT INTO staff (
                    full_name, staff_type, phone, email, id_card, 
                    license_number, experience_years, languages, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['staff_type'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['id_card'] ?? null,
            $data['license_number'] ?? null,
            $data['experience_years'] ?? 0,
            $data['languages'] ?? null,
            $data['status'] ?? 1,
            $data['notes'] ?? null
        ]);
    }

    // ==================== CẬP NHẬT NHÂN SỰ ====================

    public function update($id, $data)
    {
        $sql = "UPDATE staff SET 
                    full_name = ?, 
                    staff_type = ?, 
                    phone = ?, 
                    email = ?, 
                    id_card = ?, 
                    license_number = ?, 
                    experience_years = ?, 
                    languages = ?, 
                    status = ?, 
                    notes = ?
                WHERE staff_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['staff_type'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['id_card'] ?? null,
            $data['license_number'] ?? null,
            $data['experience_years'] ?? 0,
            $data['languages'] ?? null,
            $data['status'] ?? 1,
            $data['notes'] ?? null,
            $id
        ]);
    }

    // ==================== XÓA NHÂN SỰ ====================

    public function delete($id)
    {
        // Kiểm tra nhân sự có đang được phân công không
        $checkSql = "SELECT COUNT(*) as count FROM schedule_staff WHERE staff_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return ['success' => false, 'message' => 'Không thể xóa nhân sự đã được phân công vào lịch!'];
        }

        $sql = "DELETE FROM staff WHERE staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([$id]);

        return [
            'success' => $success,
            'message' => $success ? 'Xóa nhân sự thành công!' : 'Xóa nhân sự thất bại!'
        ];
    }

    // ==================== THỐNG KÊ NHÂN SỰ ====================

    public function getStatistics()
    {
        $sql = "SELECT 
                    staff_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_count
                FROM staff
                GROUP BY staff_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSchedulesByStaff($staff_id, $from_date = null, $to_date = null)
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    ss.role,
                    ss.assigned_at
                FROM schedule_staff ss
                JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ss.staff_id = ?";

        $params = [$staff_id];

        if ($from_date) {
            $sql .= " AND ts.departure_date >= ?";
            $params[] = $from_date;
        }

        if ($to_date) {
            $sql .= " AND ts.departure_date <= ?";
            $params[] = $to_date;
        }

        $sql .= " ORDER BY ts.departure_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================== KIỂM TRA TRÙNG LỊCH ====================

    public function checkAvailability($staff_id, $departure_date, $return_date, $exclude_schedule_id = null)
    {
        $sql = "SELECT 
                    ts.schedule_id,
                    ts.departure_date,
                    ts.return_date,
                    t.tour_name
                FROM schedule_staff ss
                JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ss.staff_id = ?
                AND ts.status != 'Cancelled'
                AND (
                    (ts.departure_date BETWEEN ? AND ?)
                    OR (ts.return_date BETWEEN ? AND ?)
                    OR (? BETWEEN ts.departure_date AND ts.return_date)
                    OR (? BETWEEN ts.departure_date AND ts.return_date)
                )";

        $params = [$staff_id, $departure_date, $return_date, $departure_date, $return_date, $departure_date, $return_date];

        if ($exclude_schedule_id) {
            $sql .= " AND ts.schedule_id != ?";
            $params[] = $exclude_schedule_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
