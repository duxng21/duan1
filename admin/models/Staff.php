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
                    full_name, date_of_birth, gender, address, avatar, phone, email, 
                    id_card, license_number, experience_years, languages, staff_type,
                    staff_category, specialization, group_specialty, health_status, 
                    health_notes, emergency_contact, emergency_phone, bank_account, 
                    bank_name, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? 'Nam',
            $data['address'] ?? null,
            $data['avatar'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['id_card'] ?? null,
            $data['license_number'] ?? null,
            $data['experience_years'] ?? 0,
            $data['languages'] ?? null,
            $data['staff_type'],
            $data['staff_category'] ?? 'Nội địa',
            $data['specialization'] ?? null,
            $data['group_specialty'] ?? 'Cả hai',
            $data['health_status'] ?? 'Tốt',
            $data['health_notes'] ?? null,
            $data['emergency_contact'] ?? null,
            $data['emergency_phone'] ?? null,
            $data['bank_account'] ?? null,
            $data['bank_name'] ?? null,
            $data['status'] ?? 1,
            $data['notes'] ?? null
        ]);
    }

    // ==================== CẬP NHẬT NHÂN SỰ ====================

    public function update($id, $data)
    {
        $sql = "UPDATE staff SET 
                    full_name = ?, 
                    date_of_birth = ?,
                    gender = ?,
                    address = ?,
                    avatar = ?,
                    phone = ?, 
                    email = ?, 
                    id_card = ?, 
                    license_number = ?, 
                    experience_years = ?, 
                    languages = ?,
                    staff_type = ?,
                    staff_category = ?,
                    specialization = ?,
                    group_specialty = ?,
                    health_status = ?,
                    health_notes = ?,
                    emergency_contact = ?,
                    emergency_phone = ?,
                    bank_account = ?,
                    bank_name = ?,
                    status = ?, 
                    notes = ?
                WHERE staff_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? 'Nam',
            $data['address'] ?? null,
            $data['avatar'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['id_card'] ?? null,
            $data['license_number'] ?? null,
            $data['experience_years'] ?? 0,
            $data['languages'] ?? null,
            $data['staff_type'],
            $data['staff_category'] ?? 'Nội địa',
            $data['specialization'] ?? null,
            $data['group_specialty'] ?? 'Cả hai',
            $data['health_status'] ?? 'Tốt',
            $data['health_notes'] ?? null,
            $data['emergency_contact'] ?? null,
            $data['emergency_phone'] ?? null,
            $data['bank_account'] ?? null,
            $data['bank_name'] ?? null,
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

    // ==================== CHỨNG CHỈ ====================

    public function getCertificates($staff_id)
    {
        $sql = "SELECT * FROM staff_certificates WHERE staff_id = ? ORDER BY expiry_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }

    public function addCertificate($data)
    {
        $sql = "INSERT INTO staff_certificates (staff_id, certificate_name, certificate_type, 
                certificate_number, issued_by, issued_date, expiry_date, attachment, status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['staff_id'],
            $data['certificate_name'],
            $data['certificate_type'],
            $data['certificate_number'] ?? null,
            $data['issued_by'] ?? null,
            $data['issued_date'] ?? null,
            $data['expiry_date'] ?? null,
            $data['attachment'] ?? null,
            $data['status'] ?? 'Còn hạn',
            $data['notes'] ?? null
        ]);
    }

    public function updateCertificate($certificate_id, $data)
    {
        $sql = "UPDATE staff_certificates SET 
                certificate_name = ?, certificate_type = ?, certificate_number = ?,
                issued_by = ?, issued_date = ?, expiry_date = ?, attachment = ?, 
                status = ?, notes = ?
                WHERE certificate_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['certificate_name'],
            $data['certificate_type'],
            $data['certificate_number'] ?? null,
            $data['issued_by'] ?? null,
            $data['issued_date'] ?? null,
            $data['expiry_date'] ?? null,
            $data['attachment'] ?? null,
            $data['status'] ?? 'Còn hạn',
            $data['notes'] ?? null,
            $certificate_id
        ]);
    }

    public function deleteCertificate($certificate_id)
    {
        $sql = "DELETE FROM staff_certificates WHERE certificate_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$certificate_id]);
    }

    public function getExpiringCertificates($days = 30)
    {
        $sql = "SELECT sc.*, s.full_name, s.phone 
                FROM staff_certificates sc
                JOIN staff s ON sc.staff_id = s.staff_id
                WHERE sc.expiry_date IS NOT NULL 
                AND sc.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND sc.expiry_date >= CURDATE()
                AND sc.status != 'Hết hạn'
                ORDER BY sc.expiry_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    // ==================== NGÔN NGỮ ====================

    public function getLanguages($staff_id)
    {
        $sql = "SELECT * FROM staff_languages WHERE staff_id = ? ORDER BY language_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }

    public function addLanguage($data)
    {
        $sql = "INSERT INTO staff_languages (staff_id, language_name, proficiency_level,
                certificate_name, certificate_score, notes)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['staff_id'],
            $data['language_name'],
            $data['proficiency_level'],
            $data['certificate_name'] ?? null,
            $data['certificate_score'] ?? null,
            $data['notes'] ?? null
        ]);
    }

    public function deleteLanguage($language_id)
    {
        $sql = "DELETE FROM staff_languages WHERE language_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$language_id]);
    }

    // ==================== LỊCH NGHỈ / LỊCH BẬN ====================

    public function getTimeOff($staff_id = null, $status = null)
    {
        $sql = "SELECT sto.*, s.full_name 
                FROM staff_time_off sto
                JOIN staff s ON sto.staff_id = s.staff_id
                WHERE 1=1";
        $params = [];

        if ($staff_id) {
            $sql .= " AND sto.staff_id = ?";
            $params[] = $staff_id;
        }

        if ($status) {
            $sql .= " AND sto.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY sto.from_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addTimeOff($data)
    {
        // Kiểm tra trùng lịch nghỉ
        $checkSql = "SELECT * FROM staff_time_off 
                     WHERE staff_id = ? AND status IN ('Chờ duyệt', 'Đã duyệt')
                     AND ((from_date BETWEEN ? AND ?) OR (to_date BETWEEN ? AND ?))";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([
            $data['staff_id'],
            $data['from_date'],
            $data['to_date'],
            $data['from_date'],
            $data['to_date']
        ]);

        if ($checkStmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Đã có lịch nghỉ trong khoảng thời gian này!'];
        }

        $sql = "INSERT INTO staff_time_off (staff_id, timeoff_type, from_date, to_date,
                reason, attachment, status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            $data['staff_id'],
            $data['timeoff_type'],
            $data['from_date'],
            $data['to_date'],
            $data['reason'] ?? null,
            $data['attachment'] ?? null,
            $data['status'] ?? 'Chờ duyệt',
            $data['notes'] ?? null
        ]);

        return ['success' => $success, 'message' => $success ? 'Đăng ký nghỉ thành công!' : 'Lỗi!'];
    }

    public function approveTimeOff($timeoff_id, $approved_by, $notes = null)
    {
        $sql = "UPDATE staff_time_off SET 
                status = 'Đã duyệt', approved_by = ?, approved_at = NOW(), notes = ?
                WHERE timeoff_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$approved_by, $notes, $timeoff_id]);
    }

    public function rejectTimeOff($timeoff_id, $notes)
    {
        $sql = "UPDATE staff_time_off SET status = 'Từ chối', notes = ? WHERE timeoff_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$notes, $timeoff_id]);
    }

    public function checkTimeOffConflict($staff_id, $departure_date, $return_date)
    {
        $sql = "SELECT * FROM staff_time_off 
                WHERE staff_id = ? AND status = 'Đã duyệt'
                AND ((from_date BETWEEN ? AND ?) OR (to_date BETWEEN ? AND ?)
                     OR (? BETWEEN from_date AND to_date) OR (? BETWEEN from_date AND to_date))";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $staff_id,
            $departure_date,
            $return_date,
            $departure_date,
            $return_date,
            $departure_date,
            $return_date
        ]);
        return $stmt->fetchAll();
    }

    // ==================== LỊCH SỬ TOUR ====================

    public function getTourHistory($staff_id, $limit = null)
    {
        $sql = "SELECT sth.*, t.tour_name, t.code 
                FROM staff_tour_history sth
                JOIN tours t ON sth.tour_id = t.tour_id
                WHERE sth.staff_id = ?
                ORDER BY sth.departure_date DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }

    public function addTourHistory($data)
    {
        $sql = "INSERT INTO staff_tour_history (staff_id, schedule_id, tour_id, role,
                departure_date, return_date, number_of_guests, customer_feedback, 
                customer_rating, manager_feedback, manager_rating, issues, 
                completed_status, salary_paid, bonus)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['staff_id'],
            $data['schedule_id'],
            $data['tour_id'],
            $data['role'] ?? 'Hướng dẫn viên',
            $data['departure_date'],
            $data['return_date'] ?? null,
            $data['number_of_guests'] ?? 0,
            $data['customer_feedback'] ?? null,
            $data['customer_rating'] ?? 0.0,
            $data['manager_feedback'] ?? null,
            $data['manager_rating'] ?? 0.0,
            $data['issues'] ?? null,
            $data['completed_status'] ?? 'Hoàn thành',
            $data['salary_paid'] ?? 0,
            $data['bonus'] ?? 0
        ]);
    }

    public function updateTourHistory($history_id, $data)
    {
        $sql = "UPDATE staff_tour_history SET 
                customer_feedback = ?, customer_rating = ?, manager_feedback = ?,
                manager_rating = ?, issues = ?, completed_status = ?, 
                salary_paid = ?, bonus = ?
                WHERE history_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['customer_feedback'] ?? null,
            $data['customer_rating'] ?? 0.0,
            $data['manager_feedback'] ?? null,
            $data['manager_rating'] ?? 0.0,
            $data['issues'] ?? null,
            $data['completed_status'] ?? 'Hoàn thành',
            $data['salary_paid'] ?? 0,
            $data['bonus'] ?? 0,
            $history_id
        ]);
    }

    // ==================== ĐÁNH GIÁ ====================

    public function getEvaluations($staff_id)
    {
        $sql = "SELECT * FROM staff_evaluations 
                WHERE staff_id = ? 
                ORDER BY evaluation_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }

    public function addEvaluation($data)
    {
        // Tính điểm trung bình
        $avg = ($data['professional_skill'] + $data['communication_skill'] +
            $data['responsibility'] + $data['problem_solving'] +
            $data['customer_service'] + $data['teamwork']) / 6;

        $sql = "INSERT INTO staff_evaluations (staff_id, evaluation_period, evaluator_name,
                professional_skill, communication_skill, responsibility, problem_solving,
                customer_service, teamwork, average_score, strengths, weaknesses,
                improvement_plan, notes, evaluation_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            $data['staff_id'],
            $data['evaluation_period'],
            $data['evaluator_name'] ?? null,
            $data['professional_skill'] ?? 0,
            $data['communication_skill'] ?? 0,
            $data['responsibility'] ?? 0,
            $data['problem_solving'] ?? 0,
            $data['customer_service'] ?? 0,
            $data['teamwork'] ?? 0,
            round($avg, 1),
            $data['strengths'] ?? null,
            $data['weaknesses'] ?? null,
            $data['improvement_plan'] ?? null,
            $data['notes'] ?? null,
            $data['evaluation_date']
        ]);

        // Cập nhật performance_rating trong bảng staff
        if ($success) {
            $updateSql = "UPDATE staff SET performance_rating = ? WHERE staff_id = ?";
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->execute([round($avg, 2), $data['staff_id']]);
        }

        return $success;
    }

    // ==================== KINH NGHIỆM ====================

    public function getExperiences($staff_id)
    {
        $sql = "SELECT * FROM staff_experiences 
                WHERE staff_id = ? 
                ORDER BY from_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }

    public function addExperience($data)
    {
        $sql = "INSERT INTO staff_experiences (staff_id, company_name, position,
                from_date, to_date, description, achievements)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['staff_id'],
            $data['company_name'] ?? null,
            $data['position'] ?? null,
            $data['from_date'] ?? null,
            $data['to_date'] ?? null,
            $data['description'] ?? null,
            $data['achievements'] ?? null
        ]);
    }

    public function deleteExperience($experience_id)
    {
        $sql = "DELETE FROM staff_experiences WHERE experience_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$experience_id]);
    }

    // ==================== PHÂN TÍCH HIỆU SUẤT ====================

    public function getPerformanceStats($staff_id)
    {
        $sql = "SELECT 
                    COUNT(*) as total_tours,
                    AVG(customer_rating) as avg_customer_rating,
                    AVG(manager_rating) as avg_manager_rating,
                    SUM(salary_paid + bonus) as total_earnings,
                    SUM(CASE WHEN completed_status = 'Hoàn thành tốt' THEN 1 ELSE 0 END) as excellent_count,
                    SUM(CASE WHEN completed_status = 'Có vấn đề' THEN 1 ELSE 0 END) as issue_count
                FROM staff_tour_history
                WHERE staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetch();
    }

    public function getStaffByCategory($category)
    {
        $sql = "SELECT * FROM staff WHERE staff_category = ? AND status = 1 ORDER BY full_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    public function getStaffBySpecialization($specialization)
    {
        $sql = "SELECT * FROM staff WHERE specialization LIKE ? AND status = 1 ORDER BY full_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['%' . $specialization . '%']);
        return $stmt->fetchAll();
    }
}

