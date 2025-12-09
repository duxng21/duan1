<?php
class StaffExpanded extends Staff
{
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
        $sql = "INSERT INTO staff_certificates (
                    staff_id, certificate_name, certificate_type, certificate_number,
                    issued_by, issued_date, expiry_date, attachment, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                AND sc.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND s.status = 1
                ORDER BY sc.expiry_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    // ==================== NGÔN NGỮ ====================

    public function getLanguages($staff_id)
    {
        $sql = "SELECT * FROM staff_languages WHERE staff_id = ? ORDER BY proficiency_level DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }

    public function addLanguage($data)
    {
        $sql = "INSERT INTO staff_languages (
                    staff_id, language_name, proficiency_level, 
                    certificate_name, certificate_score, notes
                ) VALUES (?, ?, ?, ?, ?, ?)";

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

    // ==================== LỊCH NGHỈ / BẬN ====================

    public function getTimeOffWithDateRange($staff_id, $from_date = null, $to_date = null)
    {
        $sql = "SELECT * FROM staff_time_off WHERE staff_id = ?";
        $params = [$staff_id];

        if ($from_date) {
            $sql .= " AND to_date >= ?";
            $params[] = $from_date;
        }

        if ($to_date) {
            $sql .= " AND from_date <= ?";
            $params[] = $to_date;
        }

        $sql .= " ORDER BY from_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function requestTimeOff($data)
    {
        $sql = "INSERT INTO staff_time_off (
                    staff_id, timeoff_type, from_date, to_date, 
                    reason, attachment, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['staff_id'],
            $data['timeoff_type'],
            $data['from_date'],
            $data['to_date'],
            $data['reason'] ?? null,
            $data['attachment'] ?? null,
            'Chờ duyệt'
        ]);
    }

    public function approveTimeOff($timeoff_id, $approved_by, $notes = null)
    {
        $sql = "UPDATE staff_time_off SET 
                    status = 'Đã duyệt', 
                    approved_by = ?, 
                    approved_at = NOW(),
                    notes = ?
                WHERE timeoff_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$approved_by, $notes, $timeoff_id]);
    }

    public function rejectTimeOff($timeoff_id, $notes)
    {
        $sql = "UPDATE staff_time_off SET 
                    status = 'Từ chối', 
                    notes = ?
                WHERE timeoff_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$notes, $timeoff_id]);
    }

    public function checkTimeOffConflict($staff_id, $from_date, $to_date, $exclude_id = null)
    {
        $sql = "SELECT * FROM staff_time_off 
                WHERE staff_id = ? 
                AND status IN ('Chờ duyệt', 'Đã duyệt')
                AND (
                    (from_date BETWEEN ? AND ?)
                    OR (to_date BETWEEN ? AND ?)
                    OR (? BETWEEN from_date AND to_date)
                )";

        $params = [$staff_id, $from_date, $to_date, $from_date, $to_date, $from_date];

        if ($exclude_id) {
            $sql .= " AND timeoff_id != ?";
            $params[] = $exclude_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================== LỊCH SỬ DẪN TOUR ====================

    public function getTourHistory($staff_id, $filters = [])
    {
        $sql = "SELECT sth.*, t.tour_name, t.code as tour_code 
                FROM staff_tour_history sth
                JOIN tours t ON sth.tour_id = t.tour_id
                WHERE sth.staff_id = ?";

        $params = [$staff_id];

        if (!empty($filters['from_date'])) {
            $sql .= " AND sth.departure_date >= ?";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND sth.departure_date <= ?";
            $params[] = $filters['to_date'];
        }

        if (!empty($filters['completed_status'])) {
            $sql .= " AND sth.completed_status = ?";
            $params[] = $filters['completed_status'];
        }

        $sql .= " ORDER BY sth.departure_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addTourHistory($data)
    {
        $sql = "INSERT INTO staff_tour_history (
                    staff_id, schedule_id, tour_id, role, departure_date, return_date,
                    number_of_guests, customer_feedback, customer_rating,
                    manager_feedback, manager_rating, issues, completed_status,
                    salary_paid, bonus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['staff_id'],
            $data['schedule_id'],
            $data['tour_id'],
            $data['role'] ?? 'Hướng dẫn viên',
            $data['departure_date'],
            $data['return_date'] ?? null,
            $data['number_of_guests'] ?? 0,
            $data['customer_feedback'] ?? null,
            $data['customer_rating'] ?? 0,
            $data['manager_feedback'] ?? null,
            $data['manager_rating'] ?? 0,
            $data['issues'] ?? null,
            $data['completed_status'] ?? 'Hoàn thành',
            $data['salary_paid'] ?? 0,
            $data['bonus'] ?? 0
        ]);

        // Cập nhật tổng số tour và rating trung bình của staff
        if ($result) {
            $this->updateStaffPerformance($data['staff_id']);
        }

        return $result;
    }

    public function updateTourHistory($history_id, $data)
    {
        $sql = "UPDATE staff_tour_history SET 
                    customer_feedback = ?,
                    customer_rating = ?,
                    manager_feedback = ?,
                    manager_rating = ?,
                    issues = ?,
                    completed_status = ?,
                    salary_paid = ?,
                    bonus = ?
                WHERE history_id = ?";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['customer_feedback'] ?? null,
            $data['customer_rating'] ?? 0,
            $data['manager_feedback'] ?? null,
            $data['manager_rating'] ?? 0,
            $data['issues'] ?? null,
            $data['completed_status'] ?? 'Hoàn thành',
            $data['salary_paid'] ?? 0,
            $data['bonus'] ?? 0,
            $history_id
        ]);

        if ($result) {
            // Lấy staff_id từ history để cập nhật performance
            $historyData = $this->getTourHistoryById($history_id);
            if ($historyData) {
                $this->updateStaffPerformance($historyData['staff_id']);
            }
        }

        return $result;
    }

    public function getTourHistoryById($history_id)
    {
        $sql = "SELECT * FROM staff_tour_history WHERE history_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$history_id]);
        return $stmt->fetch();
    }

    private function updateStaffPerformance($staff_id)
    {
        $sql = "UPDATE staff SET 
                    total_tours = (
                        SELECT COUNT(*) FROM staff_tour_history 
                        WHERE staff_id = ? AND completed_status IN ('Hoàn thành tốt', 'Hoàn thành')
                    ),
                    performance_rating = (
                        SELECT AVG((customer_rating + manager_rating) / 2) 
                        FROM staff_tour_history 
                        WHERE staff_id = ? AND customer_rating > 0 AND manager_rating > 0
                    )
                WHERE staff_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$staff_id, $staff_id, $staff_id]);
    }

    // ==================== ĐÁNH GIÁ ĐỊNH KỲ ====================

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

        $sql = "INSERT INTO staff_evaluations (
                    staff_id, evaluation_period, evaluator_name,
                    professional_skill, communication_skill, responsibility,
                    problem_solving, customer_service, teamwork, average_score,
                    strengths, weaknesses, improvement_plan, notes, evaluation_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['staff_id'],
            $data['evaluation_period'],
            $data['evaluator_name'] ?? null,
            $data['professional_skill'] ?? 0,
            $data['communication_skill'] ?? 0,
            $data['responsibility'] ?? 0,
            $data['problem_solving'] ?? 0,
            $data['customer_service'] ?? 0,
            $data['teamwork'] ?? 0,
            round($avg, 2),
            $data['strengths'] ?? null,
            $data['weaknesses'] ?? null,
            $data['improvement_plan'] ?? null,
            $data['notes'] ?? null,
            $data['evaluation_date'] ?? date('Y-m-d')
        ]);
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
        $sql = "INSERT INTO staff_experiences (
                    staff_id, company_name, position, from_date, to_date,
                    description, achievements
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

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

    // ==================== THỐNG KÊ & BÁO CÁO ====================

    public function getPerformanceReport($staff_id, $from_date, $to_date)
    {
        $sql = "SELECT 
                    COUNT(*) as total_tours,
                    SUM(number_of_guests) as total_guests,
                    AVG(customer_rating) as avg_customer_rating,
                    AVG(manager_rating) as avg_manager_rating,
                    SUM(salary_paid) as total_salary,
                    SUM(bonus) as total_bonus,
                    COUNT(CASE WHEN completed_status = 'Hoan thanh tot' THEN 1 END) as excellent_tours,
                    COUNT(CASE WHEN completed_status = 'Co van de' THEN 1 END) as problem_tours
                FROM staff_tour_history
                WHERE staff_id = ? AND departure_date BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id, $from_date, $to_date]);
        return $stmt->fetch();
    }

    public function getAvailableStaff($date, $filters = [])
    {
        $sql = "SELECT s.* 
                FROM staff s
                WHERE s.status = 1
                AND s.staff_id NOT IN (
                    SELECT DISTINCT ss.staff_id 
                    FROM schedule_staff ss
                    JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                    WHERE ts.status NOT IN ('Cancelled', 'Completed')
                    AND ? BETWEEN ts.departure_date AND IFNULL(ts.return_date, ts.departure_date)
                )
                AND s.staff_id NOT IN (
                    SELECT staff_id 
                    FROM staff_time_off
                    WHERE status = 'Đã duyệt'
                    AND ? BETWEEN from_date AND to_date
                )";

        $params = [$date, $date];

        if (!empty($filters['staff_type'])) {
            $sql .= " AND s.staff_type = ?";
            $params[] = $filters['staff_type'];
        }

        if (!empty($filters['staff_category'])) {
            $sql .= " AND s.staff_category = ?";
            $params[] = $filters['staff_category'];
        }

        if (!empty($filters['language'])) {
            $sql .= " AND s.staff_id IN (
                        SELECT staff_id FROM staff_languages 
                        WHERE language_name = ?
                    )";
            $params[] = $filters['language'];
        }

        $sql .= " ORDER BY s.performance_rating DESC, s.total_tours DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTopPerformers($limit = 10, $period = 'month')
    {
        switch ($period) {
            case 'week':
                $dateCondition = 'DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $dateCondition = 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'quarter':
                $dateCondition = 'DATE_SUB(CURDATE(), INTERVAL 90 DAY)';
                break;
            case 'year':
                $dateCondition = 'DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
            default:
                $dateCondition = 'DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
        }

        $sql = "SELECT 
                    s.staff_id,
                    s.full_name,
                    s.staff_type,
                    s.performance_rating,
                    COUNT(sth.history_id) as tours_count,
                    AVG(sth.customer_rating) as avg_rating,
                    SUM(sth.salary_paid + sth.bonus) as total_earnings
                FROM staff s
                LEFT JOIN staff_tour_history sth ON s.staff_id = sth.staff_id 
                    AND sth.departure_date >= $dateCondition
                WHERE s.status = 1
                GROUP BY s.staff_id
                ORDER BY avg_rating DESC, tours_count DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
