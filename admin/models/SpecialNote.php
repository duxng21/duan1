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
}
