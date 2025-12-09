<?php
/**
 * Hệ thống thông báo tự động
 * Gửi thông báo qua email/SMS cho nhân sự và đối tác
 */

/**
 * Gửi thông báo khi phân công nhân sự vào lịch tour
 */
function notifyStaffAssignment($schedule_id, $staff_id)
{
    try {
        $conn = connectDB();

        // Lấy thông tin lịch khởi hành
        $sqlSchedule = "SELECT ts.*, t.tour_name, t.code as tour_code
                        FROM tour_schedules ts
                        JOIN tours t ON ts.tour_id = t.tour_id
                        WHERE ts.schedule_id = ?";
        $stmtSchedule = $conn->prepare($sqlSchedule);
        $stmtSchedule->execute([$schedule_id]);
        $schedule = $stmtSchedule->fetch();

        if (!$schedule) {
            return false;
        }

        // Lấy thông tin nhân viên
        $sqlStaff = "SELECT * FROM staff WHERE staff_id = ?";
        $stmtStaff = $conn->prepare($sqlStaff);
        $stmtStaff->execute([$staff_id]);
        $staff = $stmtStaff->fetch();

        if (!$staff || !$staff['email']) {
            return false;
        }

        // Lưu log thông báo
        $sqlLog = "INSERT INTO notification_logs (
                    notification_type, recipient_type, recipient_id, 
                    recipient_name, recipient_contact, 
                    schedule_id, title, message, status
                   ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $title = "Phân công tour: " . $schedule['tour_name'];
        $message = "Bạn đã được phân công vào tour {$schedule['tour_code']} - {$schedule['tour_name']}\n";
        $message .= "Ngày khởi hành: " . date('d/m/Y', strtotime($schedule['departure_date'])) . "\n";
        $message .= "Điểm tập trung: {$schedule['meeting_point']}\n";
        $message .= "Giờ tập trung: {$schedule['meeting_time']}\n";

        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([
            'staff_assignment',
            'staff',
            $staff_id,
            $staff['full_name'],
            $staff['email'],
            $schedule_id,
            $title,
            $message,
            'pending'
        ]);

        // TODO: Tích hợp email service thực tế (PHPMailer, SendGrid, etc.)
        // sendEmail($staff['email'], $title, $message);

        return true;
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Gửi thông báo khi phân công dịch vụ (nhà hàng, khách sạn, xe, v.v.)
 */
function notifyServiceAssignment($schedule_id, $service_id)
{
    try {
        $conn = connectDB();

        // Lấy thông tin lịch khởi hành
        $sqlSchedule = "SELECT ts.*, t.tour_name, t.code as tour_code
                        FROM tour_schedules ts
                        JOIN tours t ON ts.tour_id = t.tour_id
                        WHERE ts.schedule_id = ?";
        $stmtSchedule = $conn->prepare($sqlSchedule);
        $stmtSchedule->execute([$schedule_id]);
        $schedule = $stmtSchedule->fetch();

        if (!$schedule) {
            return false;
        }

        // Lấy thông tin dịch vụ/đối tác
        $sqlService = "SELECT * FROM services WHERE service_id = ?";
        $stmtService = $conn->prepare($sqlService);
        $stmtService->execute([$service_id]);
        $service = $stmtService->fetch();

        if (!$service || !$service['contact_email']) {
            return false;
        }

        // Lưu log thông báo
        $sqlLog = "INSERT INTO notification_logs (
                    notification_type, recipient_type, recipient_id, 
                    recipient_name, recipient_contact, 
                    schedule_id, title, message, status
                   ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        switch ($service['service_type']) {
            case 'Hotel':
                $serviceTypeVN = 'Khách sạn';
                break;
            case 'Restaurant':
                $serviceTypeVN = 'Nhà hàng';
                break;
            case 'Transport':
                $serviceTypeVN = 'Phương tiện';
                break;
            case 'Flight':
                $serviceTypeVN = 'Vé máy bay';
                break;
            case 'Insurance':
                $serviceTypeVN = 'Bảo hiểm';
                break;
            default:
                $serviceTypeVN = 'Dịch vụ';
                break;
        }

        $title = "Đặt {$serviceTypeVN}: {$service['service_name']}";
        $message = "Tour {$schedule['tour_code']} - {$schedule['tour_name']} đã đặt {$serviceTypeVN} của bạn\n";
        $message .= "Ngày khởi hành: " . date('d/m/Y', strtotime($schedule['departure_date'])) . "\n";
        $message .= "Ngày kết thúc: " . date('d/m/Y', strtotime($schedule['return_date'])) . "\n";

        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([
            'service_assignment',
            'partner',
            $service_id,
            $service['provider_name'],
            $service['contact_email'],
            $schedule_id,
            $title,
            $message,
            'pending'
        ]);

        // TODO: Tích hợp email service thực tế
        // sendEmail($service['contact_email'], $title, $message);

        return true;
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Gửi thông báo cập nhật lịch khởi hành
 */
function notifyScheduleUpdate($schedule_id, $update_type = 'general')
{
    try {
        $conn = connectDB();

        // Lấy thông tin lịch
        $sqlSchedule = "SELECT ts.*, t.tour_name, t.code as tour_code
                        FROM tour_schedules ts
                        JOIN tours t ON ts.tour_id = t.tour_id
                        WHERE ts.schedule_id = ?";
        $stmtSchedule = $conn->prepare($sqlSchedule);
        $stmtSchedule->execute([$schedule_id]);
        $schedule = $stmtSchedule->fetch();

        if (!$schedule) {
            return false;
        }

        // Lấy danh sách nhân viên được phân công
        $sqlStaff = "SELECT s.* FROM staff s
                     JOIN schedule_staff ss ON s.staff_id = ss.staff_id
                     WHERE ss.schedule_id = ?";
        $stmtStaff = $conn->prepare($sqlStaff);
        $stmtStaff->execute([$schedule_id]);
        $staffList = $stmtStaff->fetchAll();

        // Lấy danh sách đối tác dịch vụ
        $sqlServices = "SELECT serv.* FROM services serv
                        JOIN schedule_services ss ON serv.service_id = ss.service_id
                        WHERE ss.schedule_id = ?";
        $stmtServices = $conn->prepare($sqlServices);
        $stmtServices->execute([$schedule_id]);
        $serviceList = $stmtServices->fetchAll();

        $title = "Cập nhật lịch tour: " . $schedule['tour_name'];
        $message = "Lịch tour {$schedule['tour_code']} - {$schedule['tour_name']} đã được cập nhật\n";
        $message .= "Ngày khởi hành: " . date('d/m/Y', strtotime($schedule['departure_date'])) . "\n";
        $message .= "Vui lòng kiểm tra thông tin chi tiết trong hệ thống.\n";

        // Gửi thông báo cho nhân viên
        foreach ($staffList as $staff) {
            if ($staff['email']) {
                notifyStaffAssignment($schedule_id, $staff['staff_id']);
            }
        }

        // Gửi thông báo cho đối tác
        foreach ($serviceList as $service) {
            if ($service['contact_email']) {
                notifyServiceAssignment($schedule_id, $service['service_id']);
            }
        }

        return true;
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Lấy danh sách thông báo chưa gửi
 */
function getPendingNotifications($limit = 100)
{
    try {
        $conn = connectDB();
        $sql = "SELECT * FROM notification_logs 
                WHERE status = 'pending' 
                ORDER BY created_at ASC 
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return [];
    }
}

/**
 * Đánh dấu thông báo đã gửi
 */
function markNotificationSent($notification_id)
{
    try {
        $conn = connectDB();
        $sql = "UPDATE notification_logs 
                SET status = 'sent', sent_at = NOW() 
                WHERE notification_id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$notification_id]);
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Gửi thông báo khi tạo booking mới (Use Case 1)
 * @param int $booking_id ID của booking mới tạo
 * @return bool
 */
function notifyBookingCreated($booking_id)
{
    try {
        $conn = connectDB();

        // Lấy thông tin booking
        $sql = "SELECT b.*, t.tour_name, c.full_name as customer_name, c.email as customer_email, c.phone as customer_phone
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.tour_id
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                WHERE b.booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            return false;
        }

        // Xác định thông tin người nhận
        if ($booking['booking_type'] == 'Đoàn') {
            $recipientName = $booking['contact_name'];
            $recipientContact = $booking['contact_email'] ?: $booking['contact_phone'];
        } else {
            $recipientName = $booking['customer_name'];
            $recipientContact = $booking['customer_email'] ?: $booking['customer_phone'];
        }

        // Tạo nội dung thông báo
        $title = "Xác nhận booking tour #{$booking_id}";
        $message = "Cảm ơn bạn đã đặt tour {$booking['tour_name']}! " .
            "Ngày khởi hành: " . date('d/m/Y', strtotime($booking['tour_date'])) . ". " .
            "Số khách: {$booking['num_adults']} người lớn" .
            ($booking['num_children'] > 0 ? " + {$booking['num_children']} trẻ em" : "") . ". " .
            "Tổng tiền: " . number_format($booking['total_amount']) . " VNĐ. " .
            "Trạng thái: Chờ xác nhận. Chúng tôi sẽ liên hệ với bạn trong vòng 24h.";

        // Lưu vào notification_logs
        $insertSql = "INSERT INTO notification_logs (
                        notification_type, recipient_type, recipient_id, 
                        recipient_name, recipient_contact, 
                        schedule_id, title, message, status
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertSql);
        return $stmt->execute([
            'booking_confirmation',
            $booking['booking_type'] == 'Đoàn' ? 'group' : 'customer',
            $booking['customer_id'],
            $recipientName,
            $recipientContact,
            null, // booking không có schedule_id
            $title,
            $message,
            'pending'
        ]);
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Gửi thông báo khi booking được xác nhận/cập nhật
 * @param int $booking_id
 * @param string $action 'confirmed', 'cancelled', 'updated'
 * @return bool
 */
function notifyBookingStatusChange($booking_id, $action)
{
    try {
        $conn = connectDB();

        // Lấy thông tin booking
        $sql = "SELECT b.*, t.tour_name, c.full_name as customer_name, c.email as customer_email
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.tour_id
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                WHERE b.booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            return false;
        }

        // Nội dung theo action
        $titles = [
            'confirmed' => "Booking #{$booking_id} đã được xác nhận",
            'cancelled' => "Booking #{$booking_id} đã bị hủy",
            'updated' => "Booking #{$booking_id} đã được cập nhật"
        ];

        $messages = [
            'confirmed' => "Booking tour {$booking['tour_name']} của bạn đã được xác nhận! Vui lòng thanh toán đặt cọc để giữ chỗ.",
            'cancelled' => "Booking tour {$booking['tour_name']} của bạn đã bị hủy. Nếu có thắc mắc, vui lòng liên hệ với chúng tôi.",
            'updated' => "Thông tin booking tour {$booking['tour_name']} của bạn đã được cập nhật. Vui lòng kiểm tra lại chi tiết."
        ];

        $recipientName = $booking['booking_type'] == 'Đoàn' ? $booking['contact_name'] : $booking['customer_name'];
        $recipientContact = $booking['booking_type'] == 'Đoàn' ? $booking['contact_email'] : $booking['customer_email'];

        $insertSql = "INSERT INTO notification_logs (
                        notification_type, recipient_type, recipient_id, 
                        recipient_name, recipient_contact, 
                        schedule_id, title, message, status
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertSql);
        return $stmt->execute([
            'booking_' . $action,
            $booking['booking_type'] == 'Đoàn' ? 'group' : 'customer',
            $booking['customer_id'],
            $recipientName,
            $recipientContact,
            null,
            $titles[$action],
            $messages[$action],
            'pending'
        ]);
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}
