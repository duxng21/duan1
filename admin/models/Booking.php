<?php
class Booking
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // Đồng bộ lại giá booking theo giá của lịch khởi hành
    // ==================== ĐỒNG BỘ TẤT CẢ THÔNG TIN TỪ LỊCH SANG BOOKING ====================
    public function syncPricesBySchedule($schedule_id)
    {
        try {
            // Lấy TẤT CẢ thông tin từ lịch khởi hành
            $stmt = $this->conn->prepare("SELECT * FROM tour_schedules WHERE schedule_id = ?");
            $stmt->execute([$schedule_id]);
            $schedule = $stmt->fetch();
            if (!$schedule) {
                return false;
            }

            $priceAdult = (float) ($schedule['price_adult'] ?? 0);
            $priceChild = (float) ($schedule['price_child'] ?? 0);
            $numAdults = (int) ($schedule['num_adults'] ?? 0);
            $numChildren = (int) ($schedule['num_children'] ?? 0);
            $numInfants = (int) ($schedule['num_infants'] ?? 0);
            $tourId = $schedule['tour_id'];
            $departureDate = $schedule['departure_date'];

            // Lấy tất cả booking liên kết với lịch này
            $stmt = $this->conn->prepare("SELECT b.booking_id FROM bookings b
                                           WHERE b.tour_id = ? AND b.tour_date = ? AND b.status != 'Đã hủy'");
            $stmt->execute([$tourId, $departureDate]);
            $bookings = $stmt->fetchAll();

            if (empty($bookings)) {
                return true;
            }            // Tính tổng tiền dựa trên số lượng khách TỪ LỊCH
            $newTotal = ($numAdults * $priceAdult) + ($numChildren * $priceChild) + ($numInfants * $priceChild * 0.1);

            // Cập nhật TẤT CẢ thông tin từ lịch vào booking
            // Bao gồm: số lượng khách, tổng tiền, thông tin liên hệ
            $upd = $this->conn->prepare("UPDATE bookings SET 
                num_adults = ?,
                num_children = ?,
                num_infants = ?,
                total_amount = ?,
                contact_name = ?,
                contact_phone = ?,
                contact_email = ?
                WHERE booking_id = ?");

            foreach ($bookings as $b) {
                $upd->execute([
                    $numAdults,
                    $numChildren,
                    $numInfants,
                    round($newTotal, 2),
                    $schedule['customer_name'],
                    $schedule['customer_phone'],
                    $schedule['customer_email'],
                    (int) $b['booking_id']
                ]);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM bookings";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int) $result['total'] : 0;
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

        // Handle status filter
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'not_cancelled') {
                // Special filter to hide cancelled bookings
                $sql .= " AND b.status != 'Đã hủy'";
            } else {
                // Regular status filter
                $sql .= " AND b.status = ?";
                $params[] = $filters['status'];
            }
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
                    c.address as customer_address,
                    ts.schedule_id,
                    ts.return_date as schedule_return_date,
                    ts.meeting_point as schedule_meeting_point,
                    ts.meeting_time as schedule_meeting_time,
                    ts.max_participants as schedule_max_participants,
                    ts.num_adults as schedule_num_adults,
                    ts.num_children as schedule_num_children,
                    ts.num_infants as schedule_num_infants,
                    ts.price_adult as schedule_price_adult,
                    ts.price_child as schedule_price_child,
                    ts.customer_name as schedule_customer_name,
                    ts.customer_phone as schedule_customer_phone,
                    ts.customer_email as schedule_customer_email,
                    ts.status as schedule_status,
                    ts.notes as schedule_notes
                FROM bookings b
                JOIN tours t ON b.tour_id = t.tour_id
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                LEFT JOIN tour_schedules ts ON b.tour_id = ts.tour_id AND b.tour_date = ts.departure_date
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

    // ==================== TẠO BOOKING - UC1 Enhanced ====================

    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            // UC1: Tạo booking với tracking
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
                $data['status'] ?? 'Giữ chỗ',
                (float) ($data['total_amount'] ?? 0)
            ]);

            $booking_id = $this->conn->lastInsertId();

            // UC1: Log initial status
            $initialStatus = $data['status'] ?? 'Giữ chỗ';
            $this->logStatusChange($booking_id, $initialStatus, $_SESSION['user_id'] ?? null, 'Booking được tạo');

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

            // UC1: Queue notification (không block transaction)
            $this->queueConfirmationNotification($booking_id);

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
            $data['status'] ?? 'Giữ chỗ',
            $id
        ]);
    }

    // ==================== CẬP NHẬT TRẠNG THÁI - UC2 Enhanced ====================

    public function updateStatus($id, $status, $user_id = null, $notes = null)
    {
        try {
            // Trim and validate status
            $status = trim($status);

            $this->conn->beginTransaction();

            // Get current booking info
            $booking = $this->getById($id);
            if (!$booking) {
                throw new Exception('Booking không tồn tại');
            }

            $old_status = $booking['status'];            // UC2: Update status with tracking
            $sql = "UPDATE bookings 
                    SET status = ?
                    WHERE booking_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                $errorInfo = $this->conn->errorInfo();
                throw new Exception('Prepare statement lỗi: ' . $errorInfo[2]);
            }

            $result = $stmt->execute([
                $status,
                $id
            ]);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Lỗi cập nhật booking: ' . $errorInfo[2]);
            }

            // UC2: Log status change
            $this->logStatusChange($id, $status, $user_id, $notes, $old_status);

            // UC2: Queue notification based on status (không throw lỗi nếu thất bại)
            try {
                $this->queueStatusNotification($id, $status, $old_status);
            } catch (Exception $e) {
                // Log notification error nhưng không dừng quá trình cập nhật
                error_log('Notification queue error: ' . $e->getMessage());
            }

            $this->conn->commit();
            return $result;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }    // UC2: Log status change history
    public function logStatusChange($booking_id, $new_status, $changed_by = null, $notes = null, $old_status = null)
    {
        $sql = "INSERT INTO booking_status_history (
                    booking_id, old_status, new_status, changed_by, notes, ip_address, user_agent
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $booking_id,
            $old_status,
            $new_status,
            $changed_by ?? $_SESSION['user_id'] ?? null,
            $notes,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    // UC2: Get status history
    public function getStatusHistory($booking_id)
    {
        $sql = "SELECT bsh.*, u.full_name as changed_by_name
                FROM booking_status_history bsh
                LEFT JOIN users u ON bsh.changed_by = u.user_id
                WHERE bsh.booking_id = ?
                ORDER BY bsh.changed_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }

    // UC1: Queue confirmation notification
    public function queueConfirmationNotification($booking_id)
    {
        $booking = $this->getById($booking_id);
        if (!$booking)
            return false;

        $recipient = $booking['customer_email'] ?: $booking['contact_email'];
        if (empty($recipient))
            return false;

        // Get template
        $template = $this->getNotificationTemplate('booking_created_email');
        if (!$template)
            return false;

        // Replace variables
        $message = $this->replaceTemplateVariables($template['body'], [
            'booking_id' => $booking['booking_id'],
            'customer_name' => $booking['customer_name'] ?: $booking['contact_name'],
            'tour_name' => $booking['tour_name'],
            'tour_date' => $booking['tour_date'],
            'total_guests' => $booking['num_adults'] + $booking['num_children'] + $booking['num_infants'],
            'total_amount' => number_format($booking['total_amount'], 0, ',', '.'),
            'status' => $booking['status'],
            'company_name' => 'Tour Management Company'
        ]);

        $subject = $this->replaceTemplateVariables($template['subject'], [
            'booking_id' => $booking['booking_id']
        ]);

        return $this->queueNotification($booking_id, 'email', $recipient, $subject, $message);
    }

    // UC2: Queue status change notification
    public function queueStatusNotification($booking_id, $new_status, $old_status)
    {
        try {
            $booking = $this->getById($booking_id);
            if (!$booking)
                return false;

            $recipient = $booking['customer_email'] ?: $booking['contact_email'];
            if (empty($recipient))
                return false;

            // Map status to template
            $template_map = [
                'Đã xác nhận' => 'booking_confirmed_email',
                'Đã đặt cọc' => 'booking_confirmed_email',
                'Đã hoàn thành' => 'booking_completed_email',
                'Đã hủy' => 'booking_cancelled_email'
            ];

            if (!isset($template_map[$new_status]))
                return false;

            $template = $this->getNotificationTemplate($template_map[$new_status]);
            if (!$template)
                return false;

            $vars = [
                'booking_id' => $booking['booking_id'],
                'customer_name' => $booking['customer_name'] ?: $booking['contact_name'],
                'tour_name' => $booking['tour_name'],
                'tour_date' => $booking['tour_date'],
                'total_guests' => $booking['num_adults'] + $booking['num_children'] + $booking['num_infants'],
                'cancel_reason' => 'Theo yêu cầu khách hàng',
                'company_name' => 'Tour Management Company',
                'hotline' => '1900-xxxx',
                'support_email' => 'support@example.com',
                'review_link' => 'https://example.com/review'
            ];

            $message = $this->replaceTemplateVariables($template['body'], $vars);
            $subject = $this->replaceTemplateVariables($template['subject'], $vars);

            return $this->queueNotification($booking_id, 'email', $recipient, $subject, $message);
        } catch (Exception $e) {
            error_log('queueStatusNotification error: ' . $e->getMessage());
            return false;
        }
    }

    // Queue notification
    private function queueNotification($booking_id, $type, $recipient, $subject, $message)
    {
        $sql = "INSERT INTO booking_notifications (
                    booking_id, notification_type, recipient, subject, message, status
                ) VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$booking_id, $type, $recipient, $subject, $message]);
    }

    // Get notification template
    private function getNotificationTemplate($template_name)
    {
        $sql = "SELECT * FROM notification_templates WHERE template_name = ? AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$template_name]);
        return $stmt->fetch();
    }

    // Replace template variables
    private function replaceTemplateVariables($text, $vars)
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }

    // ==================== HỦY BOOKING ====================

    public function cancel($id)
    {
        return $this->updateStatus($id, 'Đã hủy');
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

    // ==================== USE CASE 3: GUEST MANAGEMENT ====================

    /**
     * Lấy danh sách khách theo booking_id
     */
    public function getGuestsByBooking($booking_id, $filters = [])
    {
        // Lấy booking info để tìm schedule
        $booking = $this->getById($booking_id);
        if (!$booking) {
            return [];
        }

        // Tìm schedule từ tour_id và tour_date
        $sql = "SELECT schedule_id FROM tour_schedules 
                WHERE tour_id = ? AND departure_date = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking['tour_id'], $booking['tour_date']]);
        $schedule = $stmt->fetch();

        if (!$schedule) {
            return [];
        }

        // Lấy danh sách khách từ schedule_group_members
        $sql = "SELECT * FROM schedule_group_members 
                WHERE schedule_id = ? 
                ORDER BY member_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule['schedule_id']]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách khách theo schedule_id
     */
    public function getGuestsBySchedule($schedule_id, $filters = [])
    {
        $sql = "SELECT * FROM schedule_group_members 
                WHERE schedule_id = ?";
        $params = [$schedule_id];

        $sql .= " ORDER BY member_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin 1 guest
     */
    public function getGuestById($guest_id)
    {
        $sql = "SELECT * FROM guest_list WHERE guest_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$guest_id]);
        return $stmt->fetch();
    }

    /**
     * Cập nhật check-in status cho schedule_group_members
     */
    public function updateGroupMemberCheckIn($member_id, $status = 'Checked-In')
    {
        $sql = "UPDATE schedule_group_members 
                SET check_in_status = ?, 
                    check_in_time = NOW() 
                WHERE member_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $member_id]);
    }

    /**
     * Cập nhật check-in status (legacy, cho guest_list)
     */
    public function updateCheckIn($guest_id, $status = 'Checked-In')
    {
        $sql = "UPDATE guest_list 
                SET check_in_status = ?, 
                    check_in_time = NOW() 
                WHERE guest_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $guest_id]);
    }

    /**
     * Phân phòng khách sạn
     */
    public function assignRoom($guest_id, $room_number)
    {
        $sql = "UPDATE guest_list 
                SET room_number = ? 
                WHERE guest_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$room_number, $guest_id]);
    }

    /**
     * Báo cáo tóm tắt đoàn
     */
    public function getGuestSummary($booking_id)
    {
        // Tổng số khách
        $sql = "SELECT 
                    COUNT(*) as total_guests,
                    SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female_count,
                    SUM(CASE WHEN is_adult = 1 THEN 1 ELSE 0 END) as adult_count,
                    SUM(CASE WHEN is_adult = 0 THEN 1 ELSE 0 END) as child_count,
                    SUM(CASE WHEN check_in_status = 'Checked-In' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN check_in_status = 'No-Show' THEN 1 ELSE 0 END) as no_show,
                    SUM(CASE WHEN room_number IS NOT NULL THEN 1 ELSE 0 END) as room_assigned
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
                    SUM(CASE WHEN gl.check_in_status = 'Checked-In' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN gl.check_in_status = 'No-Show' THEN 1 ELSE 0 END) as no_show,
                    SUM(CASE WHEN gl.room_number IS NOT NULL THEN 1 ELSE 0 END) as room_assigned
                FROM guest_list gl
                JOIN tour_bookings tb ON gl.booking_id = tb.booking_id
                WHERE tb.schedule_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tóm tắt khách hàng theo tour (tất cả lịch/booking của tour)
     */
    public function getGuestSummaryByTour($tour_id)
    {
        $sql = "SELECT 
                    COUNT(gl.guest_id) as total_guests,
                    SUM(CASE WHEN gl.is_adult = 1 THEN 1 ELSE 0 END) as adult_count,
                    SUM(CASE WHEN gl.is_adult = 0 THEN 1 ELSE 0 END) as child_count
                FROM guest_list gl
                JOIN tour_bookings tb ON gl.booking_id = tb.booking_id
                JOIN tour_schedules ts ON tb.schedule_id = ts.schedule_id
                WHERE ts.tour_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetch();
    }

    /**
     * Lấy thông tin schedule
     */
    public function getScheduleInfo($schedule_id)
    {
        $sql = "SELECT ts.*, t.tour_name, t.code 
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ts.schedule_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetch();
    }

    // ==================== BÁO CÁO DOANH THU TỪ BOOKINGS ====================

    public function getRevenueReport($filters = [])
    {
        $sql = "SELECT 
                    DATE_FORMAT(b.booking_date, '%Y-%m') as month,
                    COUNT(DISTINCT b.booking_id) as total_bookings,
                    SUM(b.num_adults) as total_adults,
                    SUM(b.num_children) as total_children,
                    SUM(b.num_infants) as total_infants,
                    SUM(b.num_adults + b.num_children + b.num_infants) as total_guests,
                    SUM(b.total_amount) as total_revenue,
                    SUM(CASE WHEN b.status = 'Đã hoàn thành' THEN b.total_amount ELSE 0 END) as confirmed_revenue,
                    SUM(CASE WHEN b.status = 'Giữ chỗ' THEN b.total_amount ELSE 0 END) as pending_revenue,
                    SUM(CASE WHEN b.status = 'Đã hủy' THEN b.total_amount ELSE 0 END) as cancelled_revenue
                FROM bookings b
                WHERE 1=1";

        $params = [];

        if (!empty($filters['from_date'])) {
            $sql .= " AND b.booking_date >= ?";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND b.booking_date <= ?";
            $params[] = $filters['to_date'];
        }

        if (!empty($filters['tour_id'])) {
            $sql .= " AND b.tour_id = ?";
            $params[] = $filters['tour_id'];
        }

        $sql .= " GROUP BY DATE_FORMAT(b.booking_date, '%Y-%m')
                  ORDER BY month DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRevenueByTour($from_date = null, $to_date = null)
    {
        $sql = "SELECT 
                    t.tour_id,
                    t.tour_name,
                    t.code as tour_code,
                    COUNT(DISTINCT b.booking_id) as total_bookings,
                    SUM(b.num_adults + b.num_children + b.num_infants) as total_guests,
                    SUM(b.total_amount) as total_revenue,
                    AVG(b.total_amount) as avg_booking_value
                FROM bookings b
                JOIN tours t ON b.tour_id = t.tour_id
                WHERE b.status NOT IN ('Đã hủy')";

        $params = [];

        if ($from_date) {
            $sql .= " AND b.booking_date >= ?";
            $params[] = $from_date;
        }

        if ($to_date) {
            $sql .= " AND b.booking_date <= ?";
            $params[] = $to_date;
        }

        $sql .= " GROUP BY t.tour_id, t.tour_name, t.code
                  ORDER BY total_revenue DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


}

