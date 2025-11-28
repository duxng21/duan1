<?php

class GuestController
{
    public $bookingModel;
    public $guestModel;
    
    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->guestModel = new Guest();
    }

    // ==================== DANH SÁCH KHÁCH ====================

    /**
     * Bước 1: Actor chọn tour cần xem -> Hiển thị danh sách khách
     */
    public function ListGuests()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;
        $filters = [
            'check_in_status' => $_GET['check_in_status'] ?? '',
            'payment_status' => $_GET['payment_status'] ?? '',
            'room_status' => $_GET['room_status'] ?? ''
        ];

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số booking_id hoặc schedule_id!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            // 1a. Hệ thống truy xuất danh sách khách
            if ($booking_id) {
                $guests = $this->guestModel->getGuestsByBooking($booking_id, $filters);
                $booking = $this->bookingModel->getById($booking_id);
                $summary = $this->guestModel->getGuestSummary($booking_id);
                $pageTitle = "Danh sách khách - " . ($booking['tour_name'] ?? 'N/A');
            } else {
                $guests = $this->guestModel->getGuestsBySchedule($schedule_id, $filters);
                $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
                $summary = $this->guestModel->getGuestSummaryBySchedule($schedule_id);
                $pageTitle = "Danh sách khách - " . ($schedule['tour_name'] ?? 'N/A');
            }

            // 1b. Hiển thị họ tên, liên hệ, giới tính, năm sinh, trạng thái thanh toán
            require_once './views/booking/guest_list.php';

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: ?act=danh-sach-booking');
            exit;
        }
    }

    // ==================== IN DANH SÁCH ĐOÀN ====================

    /**
     * Bước 2: Actor in danh sách đoàn
     * 2a. Hệ thống tạo mẫu danh sách chuẩn
     * 2b. Cho phép in hoặc tải file PDF
     */
    public function PrintGuestList()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            if ($booking_id) {
                $guests = $this->guestModel->getGuestsByBooking($booking_id);
                $booking = $this->bookingModel->getById($booking_id);
                $summary = $this->guestModel->getGuestSummary($booking_id);
                $tourInfo = $booking;
            } else {
                $guests = $this->guestModel->getGuestsBySchedule($schedule_id);
                $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
                $summary = $this->guestModel->getGuestSummaryBySchedule($schedule_id);
                $tourInfo = $schedule;
            }

            // 2a. Tạo mẫu danh sách chuẩn
            require_once './views/booking/export_guest_pdf.php';

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: ?act=danh-sach-khach&' . 
                   ($booking_id ? 'booking_id=' . $booking_id : 'schedule_id=' . $schedule_id));
            exit;
        }
    }

    // ==================== CHECK-IN KHÁCH ====================

    /**
     * Bước 3: HDV thực hiện check-in
     * 3a. Chọn khách → đánh dấu trạng thái (đã đến / vắng mặt)
     * 3b. Hệ thống lưu giờ check-in
     */
    public function CheckIn()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            $guest_id = $_POST['guest_id'] ?? null;
            $status = $_POST['check_in_status'] ?? 'Checked-In';
            $booking_id = $_POST['booking_id'] ?? null;
            $schedule_id = $_POST['schedule_id'] ?? null;

            if (!$guest_id) {
                throw new Exception('Thiếu thông tin khách!');
            }

            // E2: Check-in trùng → hiển thị cảnh báo
            $guest = $this->guestModel->getGuestById($guest_id);
            if ($guest && $guest['check_in_status'] == 'Checked-In' && $status == 'Checked-In') {
                throw new Exception('Khách này đã check-in lúc ' . 
                    date('d/m/Y H:i', strtotime($guest['check_in_time'])));
            }

            // 3a. Đánh dấu trạng thái 
            // 3b. Lưu giờ check-in
            $result = $this->guestModel->updateCheckInStatus($guest_id, $status);

            if ($result) {
                $statusText = match($status) {
                    'Checked-In' => 'đã đến',
                    'No-Show' => 'vắng mặt',
                    default => $status
                };
                $_SESSION['success'] = "Đã cập nhật trạng thái khách: {$statusText}!";

                // Ghi log hoạt động
                $this->logGuestActivity($guest_id, 'check_in', [
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'old_status' => $guest['check_in_status'] ?? null,
                    'new_status' => $status,
                    'check_in_time' => date('Y-m-d H:i:s')
                ]);
            } else {
                throw new Exception('Cập nhật thất bại!');
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        // Redirect về danh sách
        $redirect = '?act=danh-sach-khach';
        if ($booking_id) {
            $redirect .= '&booking_id=' . $booking_id;
        } elseif ($schedule_id) {
            $redirect .= '&schedule_id=' . $schedule_id;
        }
        header('Location: ' . $redirect);
        exit;
    }

    // ==================== PHÂN PHÒNG KHÁCH SẠN ====================

    /**
     * Bước 4: Actor phân phòng khách sạn
     * 4a. Gán khách vào phòng (đơn, đôi, nhóm)
     * 4b. Hệ thống lưu thông tin phòng
     */
    public function AssignRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            $guest_id = $_POST['guest_id'] ?? null;
            $room_number = $_POST['room_number'] ?? null;
            $room_type = $_POST['room_type'] ?? 'Standard';
            $booking_id = $_POST['booking_id'] ?? null;
            $schedule_id = $_POST['schedule_id'] ?? null;

            if (!$guest_id || !$room_number) {
                throw new Exception('Vui lòng nhập đầy đủ thông tin phòng!');
            }

            // 4a. Gán khách vào phòng
            // 4b. Lưu thông tin phòng
            $result = $this->guestModel->assignRoom($guest_id, $room_number, $room_type);

            if ($result) {
                $_SESSION['success'] = "Đã phân phòng {$room_number} cho khách!";

                // Ghi log hoạt động
                $guest = $this->guestModel->getGuestById($guest_id);
                $this->logGuestActivity($guest_id, 'room_assigned', [
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'guest_name' => $guest['full_name'] ?? ''
                ]);
            } else {
                throw new Exception('Phân phòng thất bại!');
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        // Redirect về danh sách
        $redirect = '?act=danh-sach-khach';
        if ($booking_id) {
            $redirect .= '&booking_id=' . $booking_id;
        } elseif ($schedule_id) {
            $redirect .= '&schedule_id=' . $schedule_id;
        }
        header('Location: ' . $redirect);
        exit;
    }

    // ==================== BÁO CÁO TÓM TẮT ĐOÀN ====================

    /**
     * Bước 5: Hệ thống hiển thị báo cáo tóm tắt đoàn
     */
    public function SummaryReport()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            if ($booking_id) {
                $summary = $this->guestModel->getGuestSummary($booking_id);
                $booking = $this->bookingModel->getById($booking_id);
                $checkedInGuests = $this->guestModel->getGuestsByBooking($booking_id, ['check_in_status' => 'Checked-In']);
                $tourInfo = $booking;
            } else {
                $summary = $this->guestModel->getGuestSummaryBySchedule($schedule_id);
                $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
                $checkedInGuests = $this->guestModel->getGuestsBySchedule($schedule_id, ['check_in_status' => 'Checked-In']);
                $tourInfo = $schedule;
            }

            require_once './views/booking/guest_summary.php';

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: ?act=danh-sach-booking');
            exit;
        }
    }

    // ==================== LUỒNG PHỤ ====================

    /**
     * A2: Xuất danh sách khách đã check-in
     */
    public function ExportCheckedInGuests()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;
        $format = $_GET['format'] ?? 'excel'; // excel hoặc pdf

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            // Filter chỉ lấy khách đã check-in
            $filters = ['check_in_status' => 'Checked-In'];

            if ($booking_id) {
                $guests = $this->guestModel->getGuestsByBooking($booking_id, $filters);
                $booking = $this->bookingModel->getById($booking_id);
                $tourInfo = $booking;
            } else {
                $guests = $this->guestModel->getGuestsBySchedule($schedule_id, $filters);
                $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
                $tourInfo = $schedule;
            }

            if ($format == 'pdf') {
                // Export PDF
                require_once './views/booking/export_checkedin_pdf.php';
            } else {
                // Export Excel
                $this->exportExcel($guests, $tourInfo, 'Danh sách khách đã check-in');
            }

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $redirect = '?act=danh-sach-khach';
            if ($booking_id) {
                $redirect .= '&booking_id=' . $booking_id;
            } elseif ($schedule_id) {
                $redirect .= '&schedule_id=' . $schedule_id;
            }
            header('Location: ' . $redirect);
            exit;
        }
    }

    // ==================== THÊM KHÁCH MỚI ====================

    /**
     * Thêm khách mới vào danh sách
     */
    public function AddGuest()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        
        if (!$booking_id) {
            $_SESSION['error'] = 'Thiếu booking_id!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $booking = $this->bookingModel->getById($booking_id);
        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        require_once './views/booking/add_guest.php';
    }

    /**
     * Lưu khách mới
     */
    public function StoreGuest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        try {
            $booking_id = $_POST['booking_id'] ?? null;
            
            if (!$booking_id) {
                throw new Exception('Thiếu booking_id!');
            }

            // E1: Dữ liệu không khớp booking → cảnh báo lỗi
            $booking = $this->bookingModel->getById($booking_id);
            if (!$booking) {
                throw new Exception('Booking không tồn tại!');
            }

            $data = [
                'booking_id' => $booking_id,
                'full_name' => $_POST['full_name'] ?? '',
                'id_card' => $_POST['id_card'] ?? '',
                'birth_date' => $_POST['birth_date'] ?? null,
                'gender' => $_POST['gender'] ?? 'Other',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'address' => $_POST['address'] ?? '',
                'is_adult' => ($_POST['is_adult'] ?? '1') == '1' ? 1 : 0,
                'special_needs' => $_POST['special_needs'] ?? '',
                'payment_status' => $_POST['payment_status'] ?? 'Pending'
            ];

            // Validate
            if (empty($data['full_name'])) {
                throw new Exception('Vui lòng nhập họ tên!');
            }

            $guest_id = $this->guestModel->createGuest($data);

            if ($guest_id) {
                $_SESSION['success'] = 'Thêm khách thành công!';
                
                // Ghi log
                $this->logGuestActivity($guest_id, 'created', [
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'guest_name' => $data['full_name'],
                    'booking_id' => $booking_id
                ]);
            } else {
                throw new Exception('Thêm khách thất bại!');
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ?act=danh-sach-khach&booking_id=' . ($booking_id ?? ''));
        exit;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Export Excel
     */
    private function exportExcel($guests, $tourInfo, $title)
    {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . 
               str_replace(' ', '_', $title) . '_' . date('Y-m-d') . '.xls"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        echo '<table border="1">';
        echo '<tr style="background-color: #4CAF50; color: white;">';
        echo '<th>STT</th><th>Họ tên</th><th>CMND/CCCD</th><th>Giới tính</th>';
        echo '<th>Năm sinh</th><th>SĐT</th><th>Trạng thái</th>';
        echo '<th>Phòng</th><th>Giờ check-in</th>';
        echo '</tr>';

        $stt = 1;
        foreach ($guests as $guest) {
            echo '<tr>';
            echo '<td>' . $stt++ . '</td>';
            echo '<td>' . htmlspecialchars($guest['full_name']) . '</td>';
            echo '<td>' . htmlspecialchars($guest['id_card'] ?? '') . '</td>';
            echo '<td>' . match($guest['gender']) {
                'Male' => 'Nam',
                'Female' => 'Nữ', 
                default => 'Khác'
            } . '</td>';
            echo '<td>' . ($guest['birth_date'] ? date('Y', strtotime($guest['birth_date'])) : '') . '</td>';
            echo '<td>' . htmlspecialchars($guest['phone'] ?? '') . '</td>';
            echo '<td>' . ($guest['check_in_status'] == 'Checked-In' ? 'Đã đến' : 
                            ($guest['check_in_status'] == 'No-Show' ? 'Vắng mặt' : 'Chưa check-in')) . '</td>';
            echo '<td>' . htmlspecialchars($guest['room_number'] ?? 'Chưa phân') . '</td>';
            echo '<td>' . ($guest['check_in_time'] ? 
                          date('d/m/Y H:i', strtotime($guest['check_in_time'])) : '') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }

    /**
     * Ghi log hoạt động khách
     */
    private function logGuestActivity($guest_id, $action, $details = [])
    {
        try {
            $conn = connectDB();
            $sql = "INSERT INTO guest_activity_logs (guest_id, user_id, action, details, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $guest_id,
                $details['user_id'] ?? null,
                $action,
                json_encode($details, JSON_UNESCAPED_UNICODE)
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Guest activity log error: " . $e->getMessage());
            return false;
        }
    }
}