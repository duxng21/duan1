<?php
class SpecialNote
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== GHI CHÚ ĐẶC BIỆT ====================

    /**
     * Lấy tất cả ghi chú theo booking
     */
    public function getNotesByBooking($booking_id, $filters = [])
    {
        $sql = "SELECT 
                    gsn.*,
                    gl.full_name,
                    gl.phone,
                    gl.email,
                    u.full_name as creator_name,
                    u2.full_name as resolver_name
                FROM guest_special_notes gsn
                INNER JOIN guest_list gl ON gsn.guest_id = gl.guest_id
                LEFT JOIN users u ON gsn.created_by = u.user_id
                LEFT JOIN users u2 ON gsn.resolved_by = u2.user_id
                WHERE gsn.booking_id = ?";

        $params = [$booking_id];

        // Filters
        if (!empty($filters['priority'])) {
            $sql .= " AND gsn.priority_level = ?";
            $params[] = $filters['priority'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND gsn.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['note_type'])) {
            $sql .= " AND gsn.note_type = ?";
            $params[] = $filters['note_type'];
        }

        $sql .= " ORDER BY 
                    FIELD(gsn.priority_level, 'High', 'Medium', 'Low'),
                    FIELD(gsn.status, 'Pending', 'Acknowledged', 'In Progress', 'Resolved'),
                    gsn.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy ghi chú theo schedule_id
     */
    public function getNotesBySchedule($schedule_id, $filters = [])
    {
        $sql = "SELECT 
                    gsn.*,
                    gl.full_name,
                    gl.phone,
                    gl.email,
                    gl.room_number,
                    tb.booking_code,
                    u.full_name as creator_name,
                    u2.full_name as resolver_name
                FROM guest_special_notes gsn
                INNER JOIN guest_list gl ON gsn.guest_id = gl.guest_id
                INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                LEFT JOIN users u ON gsn.created_by = u.user_id
                LEFT JOIN users u2 ON gsn.resolved_by = u2.user_id
                WHERE tb.schedule_id = ?";

        $params = [$schedule_id];

        if (!empty($filters['priority'])) {
            $sql .= " AND gsn.priority_level = ?";
            $params[] = $filters['priority'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND gsn.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['note_type'])) {
            $sql .= " AND gsn.note_type = ?";
            $params[] = $filters['note_type'];
        }

        $sql .= " ORDER BY 
                    FIELD(gsn.priority_level, 'High', 'Medium', 'Low'),
                    FIELD(gsn.status, 'Pending', 'Acknowledged', 'In Progress', 'Resolved'),
                    gsn.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết 1 ghi chú
     */
    public function getNoteById($note_id)
    {
        $sql = "SELECT 
                    gsn.*,
                    gl.full_name,
                    gl.phone,
                    gl.email,
                    gl.room_number,
                    tb.booking_code,
                    tb.schedule_id,
                    u.full_name as creator_name,
                    u2.full_name as resolver_name
                FROM guest_special_notes gsn
                INNER JOIN guest_list gl ON gsn.guest_id = gl.guest_id
                INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                LEFT JOIN users u ON gsn.created_by = u.user_id
                LEFT JOIN users u2 ON gsn.resolved_by = u2.user_id
                WHERE gsn.note_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$note_id]);
        return $stmt->fetch();
    }

    /**
     * Thêm ghi chú mới
     */
    public function createNote($data)
    {
        // Validate required fields
        if (empty($data['guest_id']) || empty($data['booking_id']) || empty($data['note_content'])) {
            throw new Exception("Thiếu thông tin bắt buộc!");
        }

        $sql = "INSERT INTO guest_special_notes 
                (guest_id, booking_id, note_type, note_content, priority_level, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['guest_id'],
            $data['booking_id'],
            $data['note_type'] ?? 'Other',
            $data['note_content'],
            $data['priority_level'] ?? 'Medium',
            $data['created_by'] ?? $_SESSION['user_id'],
            'Pending'
        ]);

        if ($result) {
            $note_id = $this->conn->lastInsertId();

            // Gửi thông báo cho HDV và hậu cần
            $this->sendNotifications($note_id, $data['booking_id']);

            return $note_id;
        }

        return false;
    }

    /**
     * Cập nhật ghi chú
     */
    public function updateNote($note_id, $data)
    {
        $sql = "UPDATE guest_special_notes SET
                    note_type = ?,
                    note_content = ?,
                    priority_level = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE note_id = ?";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['note_type'] ?? 'Other',
            $data['note_content'],
            $data['priority_level'] ?? 'Medium',
            $note_id
        ]);

        if ($result) {
            // Gửi thông báo cập nhật
            $note = $this->getNoteById($note_id);
            if ($note) {
                $this->sendNotifications($note_id, $note['booking_id'], 'updated');
            }
        }

        return $result;
    }

    /**
     * Cập nhật trạng thái ghi chú
     */
    public function updateStatus($note_id, $status, $handler_notes = null)
    {
        $allowed_statuses = ['Pending', 'Acknowledged', 'In Progress', 'Resolved'];

        if (!in_array($status, $allowed_statuses)) {
            throw new Exception("Trạng thái không hợp lệ!");
        }

        $sql = "UPDATE guest_special_notes SET
                    status = ?,
                    handler_notes = ?,
                    updated_at = CURRENT_TIMESTAMP";

        $params = [$status, $handler_notes];

        // Nếu resolved, lưu thông tin người xử lý
        if ($status === 'Resolved') {
            $sql .= ", resolved_at = CURRENT_TIMESTAMP, resolved_by = ?";
            $params[] = $_SESSION['user_id'] ?? null;
        }

        $sql .= " WHERE note_id = ?";
        $params[] = $note_id;

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa ghi chú
     */
    public function deleteNote($note_id)
    {
        $sql = "DELETE FROM guest_special_notes WHERE note_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$note_id]);
    }

    /**
     * Gửi thông báo cho nhân viên liên quan
     */
    private function sendNotifications($note_id, $booking_id, $action = 'created')
    {
        // Lấy schedule_id từ booking
        $sql = "SELECT schedule_id FROM tour_bookings WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        $schedule = $stmt->fetch();

        if (!$schedule) {
            return false;
        }

        // Lấy danh sách HDV được phân công
        $sql = "SELECT DISTINCT ss.staff_id, u.user_id
                FROM schedule_staff ss
                INNER JOIN staff s ON ss.staff_id = s.staff_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE ss.schedule_id = ? AND u.user_id IS NOT NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule['schedule_id']]);
        $staff = $stmt->fetchAll();

        // Thêm admin/điều hành
        $sqlAdmin = "SELECT user_id FROM users WHERE role IN ('ADMIN', 'STAFF') AND status = 1";
        $stmtAdmin = $this->conn->prepare($sqlAdmin);
        $stmtAdmin->execute();
        $admins = $stmtAdmin->fetchAll();

        // Insert notifications
        $sqlInsert = "INSERT INTO special_note_notifications (note_id, recipient_id, recipient_type)
                      VALUES (?, ?, ?)";
        $stmtInsert = $this->conn->prepare($sqlInsert);

        foreach ($staff as $s) {
            $stmtInsert->execute([$note_id, $s['user_id'], 'Guide']);
        }

        foreach ($admins as $admin) {
            $stmtInsert->execute([$note_id, $admin['user_id'], 'Admin']);
        }

        return true;
    }

    /**
     * Lấy thống kê ghi chú
     */
    public function getNoteStatistics($booking_id = null, $schedule_id = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total_notes,
                    SUM(CASE WHEN priority_level = 'High' THEN 1 ELSE 0 END) as high_priority,
                    SUM(CASE WHEN priority_level = 'Medium' THEN 1 ELSE 0 END) as medium_priority,
                    SUM(CASE WHEN priority_level = 'Low' THEN 1 ELSE 0 END) as low_priority,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Acknowledged' THEN 1 ELSE 0 END) as acknowledged,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN note_type = 'Dietary' THEN 1 ELSE 0 END) as dietary,
                    SUM(CASE WHEN note_type = 'Medical' THEN 1 ELSE 0 END) as medical,
                    SUM(CASE WHEN note_type = 'Allergy' THEN 1 ELSE 0 END) as allergy,
                    SUM(CASE WHEN note_type = 'Mobility' THEN 1 ELSE 0 END) as mobility
                FROM guest_special_notes gsn";

        $params = [];

        if ($booking_id) {
            $sql .= " WHERE gsn.booking_id = ?";
            $params[] = $booking_id;
        } elseif ($schedule_id) {
            $sql .= " INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                      WHERE tb.schedule_id = ?";
            $params[] = $schedule_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Lấy danh sách khách có yêu cầu đặc biệt (cho báo cáo)
     */
    public function getSpecialRequirementsReport($schedule_id)
    {
        $sql = "SELECT 
                    gl.guest_id,
                    gl.full_name,
                    gl.phone,
                    gl.room_number,
                    GROUP_CONCAT(
                        CONCAT(
                            CASE gsn.note_type
                                WHEN 'Dietary' THEN '🍽️'
                                WHEN 'Medical' THEN '💊'
                                WHEN 'Allergy' THEN '⚠️'
                                WHEN 'Mobility' THEN '♿'
                                ELSE '📝'
                            END,
                            ' ',
                            gsn.note_content
                        )
                        SEPARATOR ' | '
                    ) as all_requirements,
                    MAX(CASE 
                        WHEN gsn.priority_level = 'High' THEN 3
                        WHEN gsn.priority_level = 'Medium' THEN 2
                        ELSE 1
                    END) as max_priority,
                    COUNT(gsn.note_id) as note_count
                FROM guest_list gl
                INNER JOIN tour_bookings tb ON gl.booking_id = tb.booking_id
                LEFT JOIN guest_special_notes gsn ON gl.guest_id = gsn.guest_id
                WHERE tb.schedule_id = ?
                AND (gl.special_requirements IS NOT NULL OR gsn.note_id IS NOT NULL)
                GROUP BY gl.guest_id, gl.full_name, gl.phone, gl.room_number
                ORDER BY max_priority DESC, gl.full_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markNotificationAsRead($notification_id, $user_id)
    {
        $sql = "UPDATE special_note_notifications 
                SET is_read = 1, read_at = CURRENT_TIMESTAMP
                WHERE notification_id = ? AND recipient_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$notification_id, $user_id]);
    }

    /**
     * Lấy thông báo chưa đọc của user
     */
    public function getUnreadNotifications($user_id)
    {
        $sql = "SELECT 
                    snn.*,
                    gsn.note_content,
                    gsn.priority_level,
                    gsn.note_type,
                    gl.full_name as guest_name,
                    tb.booking_code
                FROM special_note_notifications snn
                INNER JOIN guest_special_notes gsn ON snn.note_id = gsn.note_id
                INNER JOIN guest_list gl ON gsn.guest_id = gl.guest_id
                INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                WHERE snn.recipient_id = ? AND snn.is_read = 0
                ORDER BY snn.sent_at DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy thống kê tổng quan hệ thống
     */
    public function getOverallStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total_notes,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_notes,
                    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_notes,
                    SUM(CASE WHEN priority_level = 'High' THEN 1 ELSE 0 END) as high_priority,
                    AVG(CASE WHEN resolved_at IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) 
                        ELSE NULL END) as avg_resolution_hours,
                    COUNT(DISTINCT booking_id) as affected_bookings,
                    COUNT(DISTINCT guest_id) as affected_guests
                FROM guest_special_notes 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy danh sách ghi chú ưu tiên cao chưa xử lý
     */
    public function getUrgentNotes()
    {
        $sql = "SELECT 
                    gsn.*,
                    gl.full_name,
                    gl.phone,
                    tb.booking_code,
                    ts.departure_date,
                    t.tour_name,
                    u.full_name as creator_name,
                    TIMESTAMPDIFF(HOUR, gsn.created_at, NOW()) as hours_pending
                FROM guest_special_notes gsn
                INNER JOIN guest_list gl ON gsn.guest_id = gl.guest_id
                INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                INNER JOIN tour_schedules ts ON tb.schedule_id = ts.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                LEFT JOIN users u ON gsn.created_by = u.user_id
                WHERE gsn.priority_level = 'High' 
                AND gsn.status IN ('Pending', 'Acknowledged')
                AND ts.departure_date >= CURDATE()
                ORDER BY gsn.created_at ASC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy báo cáo hiệu quả xử lý theo tháng
     */
    public function getMonthlyEfficiencyReport()
    {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as total_notes,
                    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_notes,
                    ROUND(SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as resolution_rate,
                    AVG(CASE WHEN resolved_at IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) 
                        ELSE NULL END) as avg_resolution_hours
                FROM guest_special_notes 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy báo cáo hiệu quả phục vụ đặc biệt sau tour
     */
    public function getServiceEfficiencyReport($schedule_id)
    {
        $sql = "SELECT 
                    COUNT(*) as total_special_requests,
                    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as fulfilled_requests,
                    ROUND(SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as fulfillment_rate,
                    AVG(CASE WHEN resolved_at IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) 
                        ELSE NULL END) as avg_response_time,
                    GROUP_CONCAT(DISTINCT note_type) as service_categories,
                    SUM(CASE WHEN priority_level = 'High' AND status = 'Resolved' THEN 1 ELSE 0 END) as critical_resolved,
                    COUNT(DISTINCT guest_id) as guests_served
                FROM guest_special_notes gsn
                INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                WHERE tb.schedule_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetch();
    }

    /**
     * Gửi thông báo nhắc nhở trước tour
     */
    public function sendPreTourReminder($schedule_id)
    {
        try {
            // Lấy tất cả ghi chú chưa hoàn thành của schedule
            $sql = "SELECT DISTINCT gsn.note_id 
                    FROM guest_special_notes gsn
                    INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                    WHERE tb.schedule_id = ? AND gsn.status != 'Resolved'";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$schedule_id]);
            $notes = $stmt->fetchAll();

            if (empty($notes)) {
                return false;
            }

            // Gửi thông báo nhắc nhở cho từng ghi chú
            $success_count = 0;
            foreach ($notes as $note) {
                // Tạo thông báo nhắc nhở
                $this->sendNotifications($note['note_id'], null, 'reminder');
                $success_count++;
            }

            return $success_count;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Đếm số thông báo chưa đọc
     */
    public function getUnreadNotificationCount($user_id)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM special_note_notifications 
                WHERE recipient_id = ? AND is_read = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        
        return $result['count'] ?? 0;
    }

    /**
     * Sao chép ghi chú từ booking trước (cho khách quen)
     */
    public function copyNotesFromPreviousBooking($guest_id, $current_booking_id, $previous_booking_id, $created_by)
    {
        try {
            // Lấy ghi chú từ booking trước
            $sql = "SELECT note_type, note_content, priority_level 
                    FROM guest_special_notes 
                    WHERE guest_id = ? AND booking_id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$guest_id, $previous_booking_id]);
            $previous_notes = $stmt->fetchAll();

            if (empty($previous_notes)) {
                return 0;
            }

            // Sao chép từng ghi chú
            $insert_sql = "INSERT INTO guest_special_notes 
                          (guest_id, booking_id, note_type, note_content, priority_level, created_by, status)
                          VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

            $insert_stmt = $this->conn->prepare($insert_sql);
            $copied_count = 0;

            foreach ($previous_notes as $note) {
                $result = $insert_stmt->execute([
                    $guest_id,
                    $current_booking_id,
                    $note['note_type'],
                    $note['note_content'] . ' (Sao chép từ booking trước)',
                    $note['priority_level'],
                    $created_by
                ]);

                if ($result) {
                    $note_id = $this->conn->lastInsertId();
                    // Gửi thông báo cho ghi chú mới
                    $this->sendNotifications($note_id, $current_booking_id);
                    $copied_count++;
                }
            }

            return $copied_count;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Lấy lịch sử ghi chú của khách (tất cả booking)
     */
    public function getGuestNoteHistory($guest_id, $limit = 10)
    {
        $sql = "SELECT 
                    gsn.*,
                    tb.booking_code,
                    ts.departure_date,
                    t.tour_name,
                    u.full_name as creator_name
                FROM guest_special_notes gsn
                INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                INNER JOIN tour_schedules ts ON tb.schedule_id = ts.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                LEFT JOIN users u ON gsn.created_by = u.user_id
                WHERE gsn.guest_id = ?
                ORDER BY gsn.created_at DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$guest_id, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật phản hồi của khách hàng
     */
    public function updateCustomerFeedback($note_id, $feedback_rating, $feedback_comment)
    {
        $sql = "UPDATE guest_special_notes 
                SET customer_feedback_rating = ?, 
                    customer_feedback_comment = ?,
                    feedback_date = CURRENT_TIMESTAMP
                WHERE note_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$feedback_rating, $feedback_comment, $note_id]);
    }

    /**
     * Lấy thống kê phản hồi khách hàng
     */
    public function getCustomerFeedbackStats($schedule_id = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total_feedback,
                    AVG(customer_feedback_rating) as avg_rating,
                    SUM(CASE WHEN customer_feedback_rating >= 4 THEN 1 ELSE 0 END) as positive_feedback,
                    SUM(CASE WHEN customer_feedback_rating <= 2 THEN 1 ELSE 0 END) as negative_feedback
                FROM guest_special_notes gsn";

        $params = [];
        
        if ($schedule_id) {
            $sql .= " INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
                      WHERE tb.schedule_id = ? AND gsn.customer_feedback_rating IS NOT NULL";
            $params[] = $schedule_id;
        } else {
            $sql .= " WHERE gsn.customer_feedback_rating IS NOT NULL";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
