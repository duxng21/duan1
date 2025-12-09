<?php

class BookingController
{
    public $bookingModel;
    public $tourModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->tourModel = new Tour();
    }

    // ==================== DANH SÁCH BOOKING ====================

    public function ListBooking()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $bookings = $this->bookingModel->getAll($filters);
        $statistics = $this->bookingModel->getStatistics();
        $tours = $this->tourModel->getAll();

        require_once './views/booking/list_booking.php';
    }

    // ==================== THÊM BOOKING ====================

    public function AddBooking()
    {
        // Lấy tours với giá mẫu từ schedule gần nhất
        $conn = connectDB();
        $stmt = $conn->query("
            SELECT 
                t.tour_id,
                t.tour_name,
                t.code,
                t.duration_days,
                COALESCE(MIN(ts.price_adult), 0) as price_adult,
                COALESCE(MIN(ts.price_child), 0) as price_child
            FROM tours t
            LEFT JOIN tour_schedules ts ON t.tour_id = ts.tour_id
            WHERE t.status = 'Public'
            GROUP BY t.tour_id
            ORDER BY t.tour_name
        ");
        $tours = $stmt->fetchAll();

        // Lấy customers
        $stmt = $conn->query("SELECT * FROM customers ORDER BY full_name");
        $customers = $stmt->fetchAll();

        require_once './views/booking/add_booking.php';
    }

    // ==================== LƯU BOOKING ====================

    public function StoreBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=them-booking');
            exit;
        }

        try {
            // Validate
            if (empty($_POST['tour_id'])) {
                throw new Exception('Vui lòng chọn tour!');
            }

            // Validate theo loại booking
            $bookingType = $_POST['booking_type'] ?? 'Cá nhân';

            if ($bookingType == 'Đoàn') {
                // Đoàn: bắt buộc có tên tổ chức và thông tin liên hệ
                if (empty($_POST['organization_name'])) {
                    throw new Exception('Vui lòng nhập tên công ty/tổ chức!');
                }
                if (empty($_POST['contact_name']) || empty($_POST['contact_phone'])) {
                    throw new Exception('Vui lòng nhập đầy đủ thông tin liên hệ!');
                }
            } else {
                // Cá nhân: bắt buộc chọn khách hàng
                if (empty($_POST['customer_id'])) {
                    throw new Exception('Vui lòng chọn khách hàng!');
                }
            }

            if (empty($_POST['num_adults']) || $_POST['num_adults'] < 1) {
                throw new Exception('Số người lớn phải >= 1!');
            }

            $data = [
                'tour_id' => !empty($_POST['tour_id']) ? intval($_POST['tour_id']) : null,
                'tour_date' => !empty($_POST['tour_date']) ? $_POST['tour_date'] : null,
                'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
                'booking_type' => $bookingType,
                'organization_name' => !empty($_POST['organization_name']) ? $_POST['organization_name'] : null,
                'contact_name' => !empty($_POST['contact_name']) ? $_POST['contact_name'] : null,
                'contact_phone' => !empty($_POST['contact_phone']) ? $_POST['contact_phone'] : null,
                'contact_email' => !empty($_POST['contact_email']) ? $_POST['contact_email'] : null,
                'num_adults' => intval($_POST['num_adults']),
                'num_children' => intval($_POST['num_children'] ?? 0),
                'num_infants' => intval($_POST['num_infants'] ?? 0),
                'special_requests' => !empty($_POST['special_requests']) ? $_POST['special_requests'] : null,
                'total_amount' => floatval($_POST['total_amount']),
                'status' => $_POST['status'] ?? 'Chờ xác nhận'
            ];

            // Lấy booking details từ form nếu có
            if (!empty($_POST['service_name'])) {
                $data['details'] = [];
                foreach ($_POST['service_name'] as $index => $service_name) {
                    if (!empty($service_name)) {
                        $data['details'][] = [
                            'service_name' => $service_name,
                            'quantity' => intval($_POST['quantity'][$index] ?? 1),
                            'unit_price' => floatval($_POST['unit_price'][$index] ?? 0)
                        ];
                    }
                }
            }

            $booking_id = $this->bookingModel->create($data);

            // === Use Case 1: Gửi thông báo xác nhận booking ===
            notifyBookingCreated($booking_id);

            // === Use Case 1: Ghi log activity ===
            $this->logBookingActivity($booking_id, 'created', [
                'user_id' => $_SESSION['user_id'] ?? null,
                'booking_type' => $bookingType,
                'tour_id' => $data['tour_id'],
                'total_amount' => $data['total_amount']
            ]);

            $_SESSION['success'] = 'Tạo booking thành công! ' . ($bookingType == 'Đoàn' ? 'Đã xác nhận tạm thời cho đoàn.' : '');
            header('Location: ?act=chi-tiet-booking&id=' . $booking_id);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=them-booking');
            exit;
        }
    }

    // ==================== CHI TIẾT BOOKING ====================

    public function BookingDetail()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $bookingDetails = $this->bookingModel->getBookingDetails($id);

        // === Use Case 2: Lấy lịch sử thay đổi booking ===
        $bookingLogs = $this->getBookingLogs($id);

        // === Use Case 2: Kiểm tra quyền chỉnh sửa ===
        $canEdit = $this->canEditBooking($_SESSION['user_id'] ?? 0, $_SESSION['role'] ?? '');

        require_once './views/booking/booking_detail.php';
    }

    // ==================== SỬA BOOKING ====================

    public function EditBooking()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $tours = $this->tourModel->getAll();

        // Lấy customers
        $customerModel = new stdClass();
        $customerModel->conn = connectDB();
        $stmt = $customerModel->conn->query("SELECT * FROM customers ORDER BY full_name");
        $customers = $stmt->fetchAll();

        $bookingDetails = $this->bookingModel->getBookingDetails($id);

        require_once './views/booking/edit_booking.php';
    }

    // ==================== CẬP NHẬT BOOKING ====================

    public function UpdateBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        try {
            // === Use Case 2: Kiểm tra quyền chỉnh sửa ===
            if (!$this->canEditBooking($_SESSION['user_id'] ?? 0, $_SESSION['role'] ?? '')) {
                throw new Exception('Bạn không có quyền thực hiện thao tác này!');
            }

            // Lấy thông tin booking cũ để so sánh
            $oldBooking = $this->bookingModel->getById($id);

            if (!$oldBooking) {
                throw new Exception('Booking không tồn tại!');
            }

            $data = [
                'tour_id' => !empty($_POST['tour_id']) ? intval($_POST['tour_id']) : null,
                'tour_date' => !empty($_POST['tour_date']) ? $_POST['tour_date'] : null,
                'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
                'booking_type' => !empty($_POST['booking_type']) ? $_POST['booking_type'] : 'Cá nhân',
                'organization_name' => !empty($_POST['organization_name']) ? $_POST['organization_name'] : null,
                'contact_name' => !empty($_POST['contact_name']) ? $_POST['contact_name'] : null,
                'contact_phone' => !empty($_POST['contact_phone']) ? $_POST['contact_phone'] : null,
                'contact_email' => !empty($_POST['contact_email']) ? $_POST['contact_email'] : null,
                'num_adults' => intval($_POST['num_adults']),
                'num_children' => intval($_POST['num_children'] ?? 0),
                'num_infants' => intval($_POST['num_infants'] ?? 0),
                'special_requests' => !empty($_POST['special_requests']) ? $_POST['special_requests'] : null,
                'total_amount' => floatval($_POST['total_amount']),
                'status' => $_POST['status']
            ];

            $this->bookingModel->update($id, $data);

            // === Use Case 2: Ghi log activity ===
            $this->logBookingActivity($id, 'updated', [
                'user_id' => $_SESSION['user_id'] ?? null,
                'changes' => $this->getChanges($oldBooking, $data)
            ]);

            $_SESSION['success'] = 'Cập nhật booking thành công!';
            header('Location: ?act=chi-tiet-booking&id=' . $id);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=sua-booking&id=' . $id);
            exit;
        }
    }

    // ==================== CẬP NHẬT TRẠNG THÁI ====================

    public function UpdateStatus()
    {
        // Hỗ trợ cả GET và POST
        $id = $_POST['booking_id'] ?? $_GET['id'] ?? 0;
        $status = $_POST['status'] ?? $_GET['status'] ?? '';

        try {
            if (empty($id)) {
                throw new Exception('Thiếu thông tin booking!');
            }

            if (empty($status)) {
                throw new Exception('Thiếu thông tin trạng thái!');
            }

            $this->bookingModel->updateStatus($id, $status);

            // === Use Case 1: Gửi thông báo thay đổi trạng thái ===
            $action = match ($status) {
                'Đã đặt cọc' => 'confirmed',
                'Hủy' => 'cancelled',
                default => 'updated'
            };
            notifyBookingStatusChange($id, $action);

            // === Use Case 1: Ghi log activity ===
            $this->logBookingActivity($id, 'status_changed', [
                'user_id' => $_SESSION['user_id'] ?? null,
                'old_status' => null, // có thể lấy từ DB nếu cần
                'new_status' => $status
            ]);

            $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ?act=chi-tiet-booking&id=' . $id);
        exit;
    }

    // ==================== HỦY BOOKING ====================

    public function CancelBooking()
    {
        $id = $_GET['id'] ?? 0;

        try {
            $this->bookingModel->cancel($id);
            $_SESSION['success'] = 'Đã hủy booking!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ?act=danh-sach-booking');
        exit;
    }

    // ==================== IN PHIẾU BOOKING ====================

    public function PrintBooking()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $bookingDetails = $this->bookingModel->getBookingDetails($id);

        require_once './views/booking/print_booking.php';
    }

    // ==================== GHI LOG ACTIVITY (Use Case 1) ====================

    /**
     * Ghi lại mọi hoạt động liên quan đến booking
     * @param int $booking_id
     * @param string $action 'created', 'updated', 'status_changed', 'cancelled'
     * @param array $details Chi tiết bổ sung (user_id, old_value, new_value, etc.)
     */
    private function logBookingActivity($booking_id, $action, $details = [])
    {
        try {
            $conn = connectDB();
            $sql = "INSERT INTO booking_logs (booking_id, user_id, action, details, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $booking_id,
                $details['user_id'] ?? null,
                $action,
                json_encode($details, JSON_UNESCAPED_UNICODE)
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Booking activity log error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy lịch sử thay đổi booking (Use Case 2)
     * @param int $booking_id
     * @return array
     */
    private function getBookingLogs($booking_id)
    {
        try {
            $conn = connectDB();
            $sql = "SELECT bl.*, u.full_name as user_name 
                    FROM booking_logs bl
                    LEFT JOIN users u ON bl.user_id = u.user_id
                    WHERE bl.booking_id = ?
                    ORDER BY bl.created_at DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$booking_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get booking logs error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra quyền chỉnh sửa booking (Use Case 2)
     * @param int $user_id
     * @param string $role
     * @return bool
     */
    private function canEditBooking($user_id, $role)
    {
        // Admin và Manager có quyền chỉnh sửa
        if (in_array($role, ['Admin', 'Manager', 'Quản trị viên', 'Hướng dẫn viên'])) {
            return true;
        }

        // Staff có quyền giới hạn (có thể thêm logic phức tạp hơn)
        if ($role === 'Staff' || $role === 'Nhân viên') {
            return true;
        }

        return false;
    }

    /**
     * So sánh thay đổi giữa booking cũ và mới
     * @param array $old
     * @param array $new
     * @return array
     */
    private function getChanges($old, $new)
    {
        $changes = [];
        $fields = ['num_adults', 'num_children', 'num_infants', 'tour_date', 'special_requests', 'total_amount'];

        foreach ($fields as $field) {
            if (isset($old[$field]) && isset($new[$field]) && $old[$field] != $new[$field]) {
                $changes[$field] = [
                    'old' => $old[$field],
                    'new' => $new[$field]
                ];
            }
        }

        return $changes;
    }

    // ==================== USE CASE 3: QUẢN LÝ DANH SÁCH KHÁCH & CHECK-IN ====================

    /**
     * Bước 1: Xem danh sách khách của booking/tour
     */
    public function ViewGuestList()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số booking_id hoặc schedule_id!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        // Lấy danh sách khách
        if ($booking_id) {
            $guests = $this->bookingModel->getGuestsByBooking($booking_id);
            $booking = $this->bookingModel->getById($booking_id);
            $summary = $this->bookingModel->getGuestSummary($booking_id);
        } else {
            $guests = $this->bookingModel->getGuestsBySchedule($schedule_id);
            $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
            $summary = $this->bookingModel->getGuestSummaryBySchedule($schedule_id);
        }

        require_once './views/booking/guest_list.php';
    }

    /**
     * Bước 3: HDV thực hiện check-in
     */
    public function CheckInGuest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $guest_id = $_POST['guest_id'] ?? null;
                $status = $_POST['status'] ?? 'Checked-In';

                if (!$guest_id) {
                    throw new Exception('Thiếu guest_id!');
                }

                // E2: Kiểm tra đã check-in chưa
                $guest = $this->bookingModel->getGuestById($guest_id);
                if ($guest['check_in_status'] == 'Checked-In') {
                    throw new Exception('Khách này đã check-in trước đó!');
                }

                $result = $this->bookingModel->updateCheckIn($guest_id, $status);

                if ($result) {
                    $_SESSION['success'] = 'Check-in thành công!';
                } else {
                    $_SESSION['error'] = 'Check-in thất bại!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            $booking_id = $_POST['booking_id'] ?? null;
            if ($booking_id) {
                header('Location: ?act=danh-sach-khach&booking_id=' . $booking_id);
            } else {
                header('Location: ?act=danh-sach-booking');
            }
            exit();
        }
    }

    /**
     * Bước 4: Phân phòng khách sạn
     */
    public function AssignRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $guest_id = $_POST['guest_id'] ?? null;
                $room_number = $_POST['room_number'] ?? null;

                if (!$guest_id || !$room_number) {
                    throw new Exception('Vui lòng nhập đầy đủ thông tin!');
                }

                $result = $this->bookingModel->assignRoom($guest_id, $room_number);

                if ($result) {
                    $_SESSION['success'] = 'Phân phòng thành công!';
                } else {
                    $_SESSION['error'] = 'Phân phòng thất bại!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            $booking_id = $_POST['booking_id'] ?? null;
            if ($booking_id) {
                header('Location: ?act=danh-sach-khach&booking_id=' . $booking_id);
            } else {
                header('Location: ?act=danh-sach-booking');
            }
            exit();
        }
    }

    /**
     * Bước 2: In danh sách đoàn (PDF)
     */
    public function ExportGuestListPDF()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        if ($booking_id) {
            $guests = $this->bookingModel->getGuestsByBooking($booking_id);
            $booking = $this->bookingModel->getById($booking_id);
            $summary = $this->bookingModel->getGuestSummary($booking_id);
        } else {
            $guests = $this->bookingModel->getGuestsBySchedule($schedule_id);
            $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
            $summary = $this->bookingModel->getGuestSummaryBySchedule($schedule_id);
        }

        require_once './views/booking/export_guest_pdf.php';
    }

    /**
     * Bước 5: Báo cáo tóm tắt đoàn
     */
    public function GuestSummaryReport()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        if ($booking_id) {
            $summary = $this->bookingModel->getGuestSummary($booking_id);
            $booking = $this->bookingModel->getById($booking_id);
        } else {
            $summary = $this->bookingModel->getGuestSummaryBySchedule($schedule_id);
            $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
        }

        require_once './views/booking/guest_summary.php';
    }

    /**
     * A2: Xuất danh sách khách đã check-in
     */
    public function ExportCheckedInGuests()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$booking_id && !$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        // Filter chỉ lấy khách đã check-in
        $filters = ['check_in_status' => 'Checked-In'];

        if ($booking_id) {
            $guests = $this->bookingModel->getGuestsByBooking($booking_id, $filters);
            $booking = $this->bookingModel->getById($booking_id);
        } else {
            $guests = $this->bookingModel->getGuestsBySchedule($schedule_id, $filters);
            $schedule = $this->bookingModel->getScheduleInfo($schedule_id);
        }

        // Export to Excel
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Danh_sach_da_check_in_' . date('Y-m-d') . '.xls"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        echo '<table border="1">';
        echo '<tr style="background-color: #4CAF50; color: white;">';
        echo '<th>STT</th><th>Họ tên</th><th>CMND</th><th>Giới tính</th><th>Năm sinh</th>';
        echo '<th>SĐT</th><th>Phòng</th><th>Giờ check-in</th>';
        echo '</tr>';

        $stt = 1;
        foreach ($guests as $guest) {
            echo '<tr>';
            echo '<td>' . $stt++ . '</td>';
            echo '<td>' . htmlspecialchars($guest['full_name']) . '</td>';
            echo '<td>' . htmlspecialchars($guest['id_card'] ?? '') . '</td>';
            echo '<td>' . ($guest['gender'] == 'Male' ? 'Nam' : ($guest['gender'] == 'Female' ? 'Nữ' : 'Khác')) . '</td>';
            echo '<td>' . ($guest['birth_date'] ? date('Y', strtotime($guest['birth_date'])) : '') . '</td>';
            echo '<td>' . htmlspecialchars($guest['phone'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($guest['room_number'] ?? 'Chưa phân') . '</td>';
            echo '<td>' . ($guest['check_in_time'] ? date('d/m/Y H:i', strtotime($guest['check_in_time'])) : '') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit();
    }
}