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
            'search' => $_GET['search'] ?? '',
            'include_cancelled' => $_GET['include_cancelled'] ?? '0'
        ];

        // Nếu không muốn xem booking hủy, thêm filter status
        if ($filters['include_cancelled'] !== '1' && empty($filters['status'])) {
            $filters['status'] = 'not_cancelled'; // Custom filter value
        }

        $bookings = $this->bookingModel->getAll($filters);
        $statistics = $this->bookingModel->getStatistics();
        $tours = $this->tourModel->getAll();

        require_once './views/booking/list_booking.php';
    }

    // ==================== THÊM BOOKING ====================

    public function AddBooking()
    {
        // Lấy danh sách lịch khởi hành còn chỗ (với số lượng khách từ schedule)
        $conn = connectDB();
        $stmt = $conn->query("
            SELECT 
                ts.schedule_id,
                ts.tour_id,
                ts.departure_date,
                ts.return_date,
                ts.meeting_point,
                ts.meeting_time,
                ts.max_participants,
                ts.price_adult,
                ts.price_child,
                ts.status,
                ts.num_adults,
                ts.num_children,
                ts.num_infants,
                COALESCE(ts.current_participants, 0) as current_participants,
                (ts.max_participants - COALESCE(ts.current_participants, 0)) as available_slots,
                t.tour_name,
                t.code as tour_code,
                t.duration_days
            FROM tour_schedules ts
            INNER JOIN tours t ON ts.tour_id = t.tour_id
            WHERE ts.status IN ('Open', 'Confirmed')
              AND ts.departure_date >= CURDATE()
              AND (ts.max_participants - COALESCE(ts.current_participants, 0)) > 0
            ORDER BY ts.departure_date ASC, t.tour_name
        ");
        $schedules = $stmt->fetchAll();

        // Lấy customers
        $stmt = $conn->query("SELECT * FROM customers ORDER BY full_name");
        $customers = $stmt->fetchAll();

        require_once './views/booking/add_booking.php';
    }

    // Tạo booking từ một lịch khởi hành cụ thể (prefill tour/date/prices)
    public function AddBookingFromSchedule()
    {
        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $conn = connectDB();
        // Chỉ lấy tours đã có lịch khởi hành
        $stmt = $conn->query("\r
            SELECT \r
                t.tour_id,\r
                t.tour_name,\r
                t.code,\r
                t.duration_days,\r
                COALESCE(MIN(ts.price_adult), 0) as price_adult,\r
                COALESCE(MIN(ts.price_child), 0) as price_child,\r
                COUNT(DISTINCT ts.schedule_id) as schedule_count\r
            FROM tours t\r
            INNER JOIN tour_schedules ts ON t.tour_id = ts.tour_id\r
            WHERE t.status = 'Public'\r
              AND ts.status IN ('Open', 'Confirmed')\r
              AND ts.departure_date >= CURDATE()\r
            GROUP BY t.tour_id\r
            HAVING schedule_count > 0\r
            ORDER BY t.tour_name\r
        ");
        $tours = $stmt->fetchAll();

        // Customers
        $stmt = $conn->query("SELECT * FROM customers ORDER BY full_name");
        $customers = $stmt->fetchAll();

        // Lấy thông tin schedule để prefill
        $bookingModel = new Booking();
        $schedule = $bookingModel->getScheduleInfo($schedule_id);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $prefill = [
            'schedule_id' => $schedule['schedule_id'],
            'tour_id' => $schedule['tour_id'],
            'tour_date' => $schedule['departure_date'],
            'price_adult' => (float) ($schedule['price_adult'] ?? 0),
            'price_child' => (float) ($schedule['price_child'] ?? 0)
        ];

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
            if (empty($_POST['schedule_id'])) {
                throw new Exception('Vui lòng chọn lịch khởi hành!');
            }

            // Lấy thông tin schedule để lấy tour_id và giá
            $conn = connectDB();
            $stmt = $conn->prepare("
                SELECT ts.*, t.tour_name 
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ts.schedule_id = ?
            ");
            $stmt->execute([$_POST['schedule_id']]);
            $schedule = $stmt->fetch();

            if (!$schedule) {
                throw new Exception('Lịch khởi hành không tồn tại!');
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

            // Tính tổng tiền từ giá schedule
            $numAdults = intval($_POST['num_adults']);
            $numChildren = intval($_POST['num_children'] ?? 0);
            $numInfants = intval($_POST['num_infants'] ?? 0);
            $totalGuests = $numAdults + $numChildren + $numInfants;

            // ========== KIỂM TRA SỐ LƯỢNG KHÁCH KHÔNG VƯỢT QUÁ CHỖ TỐI ĐA ==========
            $maxParticipants = intval($schedule['max_participants'] ?? 0);
            $allowOverbook = !empty($_POST['allow_overbook']) ? 1 : 0;

            if (!$allowOverbook && $totalGuests > $maxParticipants) {
                throw new Exception("Lỗi: Tổng số khách ($totalGuests) vượt quá số chỗ tối đa ($maxParticipants) của lịch! 
                    Vui lòng giảm số lượng khách hoặc bật tùy chọn 'Cho phép overbook'.");
            }

            $totalAmount = ($numAdults * (float) $schedule['price_adult'])
                + ($numChildren * (float) $schedule['price_child'])
                + ($numInfants * (float) $schedule['price_child'] * 0.1);

            // Validate status
            $validStatuses = ['Giữ chỗ', 'Đã đặt cọc', 'Đã thanh toán', 'Đã hủy', 'Đã hoàn thành'];
            $status = $_POST['status'] ?? 'Giữ chỗ';
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Trạng thái booking không hợp lệ!');
            }

            $data = [
                'schedule_id' => intval($_POST['schedule_id']),
                'tour_id' => $schedule['tour_id'],
                'tour_date' => $schedule['departure_date'],
                'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
                'booking_type' => $bookingType,
                'organization_name' => !empty($_POST['organization_name']) ? $_POST['organization_name'] : null,
                'contact_name' => !empty($_POST['contact_name']) ? $_POST['contact_name'] : null,
                'contact_phone' => !empty($_POST['contact_phone']) ? $_POST['contact_phone'] : null,
                'contact_email' => !empty($_POST['contact_email']) ? $_POST['contact_email'] : null,
                'num_adults' => $numAdults,
                'num_children' => $numChildren,
                'num_infants' => $numInfants,
                'special_requests' => !empty($_POST['special_requests']) ? $_POST['special_requests'] : null,
                'total_amount' => $totalAmount,
                'status' => $status,
                'allow_overbook' => $allowOverbook
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

            // Liên kết booking với lịch khởi hành (tour_bookings)
            try {
                $conn = connectDB();
                $stmt = $conn->prepare("INSERT INTO tour_bookings (schedule_id, booking_id, number_of_guests) VALUES (?,?,?)");
                $stmt->execute([
                    (int) $data['schedule_id'],
                    (int) $booking_id,
                    (int) $totalGuests
                ]);
            } catch (Exception $e) {
                // Không block nếu bảng chưa tồn tại hoặc lỗi nhẹ
            }

            // === Use Case 1: Gửi thông báo xác nhận booking ===
            try {
                if (function_exists('notifyBookingCreated')) {
                    notifyBookingCreated($booking_id);
                }
            } catch (Exception $e) {
                // Không block nếu gửi thông báo thất bại
            }

            // === Use Case 1: Ghi log activity ===
            try {
                if (method_exists($this, 'logBookingActivity')) {
                    $this->logBookingActivity($booking_id, 'created', [
                        'user_id' => $_SESSION['user_id'] ?? null,
                        'booking_type' => $bookingType,
                        'tour_id' => $data['tour_id'],
                        'total_amount' => $data['total_amount']
                    ]);
                }
            } catch (Exception $e) {
                // Không block nếu log thất bại
            }

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

        // Lấy danh sách khách trong đoàn từ schedule (nếu có schedule_id)
        $groupMembers = [];
        if (!empty($booking['tour_date'])) {
            // Tìm schedule_id từ tour_date và tour_id
            $conn = connectDB();
            $stmt = $conn->prepare("SELECT schedule_id FROM tour_schedules WHERE tour_id = ? AND departure_date = ? LIMIT 1");
            $stmt->execute([$booking['tour_id'], $booking['tour_date']]);
            $schedule = $stmt->fetch();

            if ($schedule) {
                // Lấy danh sách thành viên từ schedule_group_members
                $stmt = $conn->prepare("SELECT * FROM schedule_group_members WHERE schedule_id = ? ORDER BY member_id");
                $stmt->execute([$schedule['schedule_id']]);
                $groupMembers = $stmt->fetchAll();
            }
        }

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

            // Validate status
            $validStatuses = ['Giữ chỗ', 'Đã đặt cọc', 'Đã thanh toán', 'Đã hủy', 'Đã hoàn thành'];
            $status = $_POST['status'] ?? $oldBooking['status'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Trạng thái booking không hợp lệ!');
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
                'status' => $status
            ];

            // ========== KIỂM TRA SỐ LƯỢNG KHÁCH KHÔNG VƯỢT QUÁ CHỖ TỐI ĐA ==========
            // Lấy thông tin lịch khởi hành
            $tourId = $data['tour_id'];
            $tourDate = $data['tour_date'];

            if ($tourId && $tourDate) {
                $conn = connectDB();
                $stmt = $conn->prepare("SELECT max_participants FROM tour_schedules WHERE tour_id = ? AND departure_date = ? LIMIT 1");
                $stmt->execute([$tourId, $tourDate]);
                $schedule = $stmt->fetch();

                if ($schedule) {
                    $totalGuests = $data['num_adults'] + $data['num_children'] + $data['num_infants'];
                    $maxParticipants = intval($schedule['max_participants'] ?? 0);
                    $allowOverbook = !empty($_POST['allow_overbook']) ? 1 : 0;

                    if (!$allowOverbook && $totalGuests > $maxParticipants) {
                        throw new Exception("Lỗi: Tổng số khách ($totalGuests) vượt quá số chỗ tối đa ($maxParticipants) của lịch! 
                            Vui lòng giảm số lượng khách hoặc bật tùy chọn 'Cho phép overbook'.");
                    }

                    $data['allow_overbook'] = $allowOverbook;
                }
            }

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
        $id = (int) ($_POST['booking_id'] ?? $_GET['id'] ?? 0);
        $status = trim($_POST['status'] ?? $_GET['status'] ?? '');

        try {
            if (empty($id)) {
                throw new Exception('Thiếu thông tin booking!');
            }

            if (empty($status)) {
                throw new Exception('Thiếu thông tin trạng thái!');
            }

            $this->bookingModel->updateStatus($id, $status);

            // === Use Case 1: Gửi thông báo thay đổi trạng thái ===
            switch ($status) {
                case 'Đã đặt cọc':
                    $action = 'confirmed';
                    break;
                case 'Đã hủy':
                    $action = 'cancelled';
                    break;
                default:
                    $action = 'updated';
                    break;
            }
            try {
                notifyBookingStatusChange($id, $action);
            } catch (Exception $e) {
                error_log('Notification error: ' . $e->getMessage());
            }

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

    // ==================== DOCUMENT GENERATION ====================

    /**
     * Tạo và tải báo giá PDF
     */
    public function GenerateQuotePDF()
    {
        try {
            $booking_id = $_GET['booking_id'] ?? null;
            if (!$booking_id) {
                throw new Exception('Thiếu booking_id!');
            }

            // Load composer autoload
            require_once __DIR__ . '/../../vendor/autoload.php';

            $docModel = new BookingDocument();

            // Generate quote data with HTML
            $quoteData = $docModel->generateQuote($booking_id);

            // Create PDF with mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            $mpdf->WriteHTML($quoteData['html_content']);

            // Save PDF to file
            $uploadDir = __DIR__ . '/../../uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = "bao-gia-{$booking_id}-" . date('Ymd-His') . ".pdf";
            $filePath = $uploadDir . $fileName;
            $mpdf->Output($filePath, 'F');

            // Update file path in database
            $docModel->updateFilePath($quoteData['document_id'], 'uploads/documents/' . $fileName);

            // Download PDF
            $mpdf->Output($fileName, 'D');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tạo báo giá: ' . $e->getMessage();
            header('Location: ?act=chi-tiet-booking&id=' . ($booking_id ?? ''));
            exit();
        }
    }

    /**
     * Tạo và tải hợp đồng PDF
     */
    public function GenerateContractPDF()
    {
        try {
            $booking_id = $_GET['booking_id'] ?? null;
            if (!$booking_id) {
                throw new Exception('Thiếu booking_id!');
            }

            require_once __DIR__ . '/../../vendor/autoload.php';

            $docModel = new BookingDocument();
            $contractData = $docModel->generateContract($booking_id);

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 25,
                'margin_bottom' => 25
            ]);

            $mpdf->WriteHTML($contractData['html_content']);

            $uploadDir = __DIR__ . '/../../uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = "hop-dong-{$booking_id}-" . date('Ymd-His') . ".pdf";
            $filePath = $uploadDir . $fileName;
            $mpdf->Output($filePath, 'F');

            $docModel->updateFilePath($contractData['document_id'], 'uploads/documents/' . $fileName);

            $mpdf->Output($fileName, 'D');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tạo hợp đồng: ' . $e->getMessage();
            header('Location: ?act=chi-tiet-booking&id=' . ($booking_id ?? ''));
            exit();
        }
    }

    /**
     * Tạo và tải hóa đơn VAT PDF
     */
    public function GenerateInvoicePDF()
    {
        try {
            $booking_id = $_GET['booking_id'] ?? null;
            if (!$booking_id) {
                throw new Exception('Thiếu booking_id!');
            }

            require_once __DIR__ . '/../../vendor/autoload.php';

            $docModel = new BookingDocument();
            $invoiceData = $docModel->generateInvoice($booking_id);

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20
            ]);

            $mpdf->WriteHTML($invoiceData['html_content']);

            $uploadDir = __DIR__ . '/../../uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = "hoa-don-{$booking_id}-" . date('Ymd-His') . ".pdf";
            $filePath = $uploadDir . $fileName;
            $mpdf->Output($filePath, 'F');

            $docModel->updateFilePath($invoiceData['document_id'], 'uploads/documents/' . $fileName);

            $mpdf->Output($fileName, 'D');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tạo hóa đơn: ' . $e->getMessage();
            header('Location: ?act=chi-tiet-booking&id=' . ($booking_id ?? ''));
            exit();
        }
    }

    /**
     * Gửi tài liệu qua email
     */
    public function SendDocumentEmail()
    {
        try {
            $document_id = $_POST['document_id'] ?? null;
            $email_to = $_POST['email_to'] ?? null;

            if (!$document_id || !$email_to) {
                throw new Exception('Thiếu thông tin gửi email!');
            }

            if (!filter_var($email_to, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ!');
            }

            require_once __DIR__ . '/../../vendor/autoload.php';

            $docModel = new BookingDocument();
            $document = $docModel->getDocumentById($document_id);

            if (!$document) {
                throw new Exception('Không tìm thấy tài liệu!');
            }

            // Check access permission
            if (!$docModel->canAccess($document_id)) {
                throw new Exception('Bạn không có quyền gửi tài liệu này!');
            }

            // Send email with PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // TODO: Move to config
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // TODO: Move to env
            $mail->Password = 'your-app-password'; // TODO: Move to env
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Email content
            $mail->setFrom('info@abctravel.vn', 'ABC Travel');
            $mail->addAddress($email_to);

            $documentTypes = [
                'QUOTE' => 'Báo giá',
                'CONTRACT' => 'Hợp đồng',
                'INVOICE' => 'Hóa đơn'
            ];

            $docTypeName = $documentTypes[$document['document_type']] ?? 'Tài liệu';
            $mail->Subject = $docTypeName . ' - ' . $document['document_number'];

            $mail->isHTML(true);
            $mail->Body = "
                <p>Kính gửi Quý khách,</p>
                <p>Đính kèm {$docTypeName} <strong>{$document['document_number']}</strong> từ ABC Travel.</p>
                <p>Vui lòng xem và phản hồi nếu có thắc mắc.</p>
                <p>Trân trọng,<br>ABC Travel</p>
            ";

            // Attach PDF file
            if ($document['file_path'] && file_exists(__DIR__ . '/../../' . $document['file_path'])) {
                $mail->addAttachment(__DIR__ . '/../../' . $document['file_path'], $document['file_name']);
            }

            $mail->send();

            // Mark as sent
            $docModel->markAsSent($document_id, $email_to);

            $_SESSION['success'] = "Đã gửi {$docTypeName} đến {$email_to}!";
            header('Location: ?act=chi-tiet-booking&id=' . $document['booking_id']);
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi gửi email: ' . $e->getMessage();
            $booking_id = $document['booking_id'] ?? ($_POST['booking_id'] ?? '');
            header('Location: ?act=chi-tiet-booking&id=' . $booking_id);
            exit();
        }
    }

    /**
     * Xem danh sách tài liệu của booking
     */
    public function ViewDocuments()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        if (!$booking_id) {
            $_SESSION['error'] = 'Thiếu booking_id!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        $docModel = new BookingDocument();
        $documents = $docModel->getDocumentsByBooking($booking_id);
        $booking = $this->bookingModel->getById($booking_id);

        require_once './views/booking/documents_list.php';
    }

    /**
     * Tải document đã tạo
     */
    public function DownloadDocument()
    {
        try {
            $document_id = $_GET['document_id'] ?? null;
            if (!$document_id) {
                throw new Exception('Thiếu document_id!');
            }

            $docModel = new BookingDocument();

            if (!$docModel->canAccess($document_id)) {
                throw new Exception('Bạn không có quyền tải tài liệu này!');
            }

            $document = $docModel->getDocumentById($document_id);
            if (!$document) {
                throw new Exception('Không tìm thấy tài liệu!');
            }

            $filePath = __DIR__ . '/../../' . $document['file_path'];
            if (!file_exists($filePath)) {
                throw new Exception('File không tồn tại!');
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $document['file_name'] . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tải file: ' . $e->getMessage();
            header('Location: ?act=danh-sach-booking');
            exit();
        }
    }

    /**
     * In tài liệu (mở PDF trong tab mới)
     */
    public function PrintDocument()
    {
        try {
            $document_id = $_GET['document_id'] ?? null;
            if (!$document_id) {
                throw new Exception('Thiếu document_id!');
            }

            $docModel = new BookingDocument();

            if (!$docModel->canAccess($document_id)) {
                throw new Exception('Bạn không có quyền in tài liệu này!');
            }

            $document = $docModel->getDocumentById($document_id);
            if (!$document) {
                throw new Exception('Không tìm thấy tài liệu!');
            }

            $filePath = __DIR__ . '/../../' . $document['file_path'];
            if (!file_exists($filePath)) {
                throw new Exception('File không tồn tại!');
            }

            // Open PDF in browser (inline) for printing
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $document['file_name'] . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi mở file: ' . $e->getMessage();
            header('Location: ?act=danh-sach-booking');
            exit();
        }
    }
}