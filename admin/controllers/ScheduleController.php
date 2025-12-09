<?php
require_once __DIR__ . '/../models/GroupMember.php';

class ScheduleController
{
    public $modelSchedule;
    public $modelTour;

    public function __construct()
    {
        $this->modelSchedule = new TourSchedule();
        $this->modelTour = new Tour();
    }

    // ==================== API: LẤY TÓMS TẮT KHÁCH HÀNG ====================
    public function ApiGuestSummary()
    {
        header('Content-Type: application/json; charset=utf-8');

        $tour_id = $_GET['tour_id'] ?? null;
        if (!$tour_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu tour_id']);
            exit();
        }

        try {
            $bookingModel = new Booking();
            $summary = $bookingModel->getGuestSummaryByTour((int) $tour_id);

            echo json_encode([
                'success' => true,
                'adult_count' => (int) ($summary['adult_count'] ?? 0),
                'child_count' => (int) ($summary['child_count'] ?? 0),
                'total' => (int) ($summary['adult_count'] ?? 0) + (int) ($summary['child_count'] ?? 0)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // ==================== API: LẤY LỊCH TRÌNH CHI TIẾT TOUR ====================
    public function ApiTourItineraries()
    {
        header('Content-Type: application/json; charset=utf-8');

        $tour_id = $_GET['tour_id'] ?? null;
        if (!$tour_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu tour_id']);
            exit();
        }

        try {
            $modelTourDetail = new TourDetail();
            $itineraries = $modelTourDetail->getItineraries((int) $tour_id);

            echo json_encode([
                'success' => true,
                'data' => $itineraries
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // ==================== DANH SÁCH LỊCH KHỞI HÀNH ====================

    public function ListSchedule()
    {
        requireLogin();
        requirePermission('tour.view');
        $tour_id = $_GET['tour_id'] ?? null;

        if (isGuide()) {
            // HDV: chỉ xem lịch được phân công (không lọc theo tour nếu không có)
            $staff_id = $_SESSION['staff_id'] ?? null;
            $all = $staff_id ? $this->modelSchedule->getAllStaffAssignments(['staff_id' => $staff_id]) : [];
            // Chuyển assignments thành schedules duy nhất
            $unique = [];
            foreach ($all as $a) {
                $unique[$a['schedule_id']] = $a['schedule_id'];
            }
            $schedulesRaw = [];
            foreach ($unique as $sid) {
                $s = $this->modelSchedule->getScheduleById($sid);
                if ($s)
                    $schedulesRaw[] = $s;
            }
            if ($tour_id) {
                $schedules = array_filter($schedulesRaw, function ($s) use ($tour_id) {
                    return $s['tour_id'] == $tour_id;
                });
                $tour = $this->modelTour->getById($tour_id);
            } else {
                $schedules = $schedulesRaw;
                $tour = null;
            }
            $tours = []; // Không cần danh sách đầy đủ đối với HDV
        } else {
            if ($tour_id) {
                $schedules = $this->modelSchedule->getSchedulesByTour($tour_id);
                $tour = $this->modelTour->getById($tour_id);
            } else {
                $schedules = $this->modelSchedule->getAllSchedules();
                $tour = null;
            }
            $tours = $this->modelTour->getAll();
        }
        require_once './views/schedule/list_schedule.php';
    }

    // ==================== THÊM LỊCH KHỞI HÀNH ====================

    public function AddSchedule()
    {
        requireRole('ADMIN');
        $tour_id = $_GET['tour_id'] ?? null;
        $tours = $this->modelTour->getAll();
        $itineraries = [];
        if ($tour_id) {
            $modelTourDetail = new TourDetail();
            $itineraries = $modelTourDetail->getItineraries($tour_id);
        }
        require_once './views/schedule/add_schedule.php';
    }

    public function StoreSchedule()
    {
        requireRole('ADMIN');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['tour_id'])) {
                    $_SESSION['error'] = 'Vui lòng chọn tour!';
                    header('Location: ?act=them-lich-khoi-hanh');
                    exit();
                }

                if (empty($_POST['departure_date'])) {
                    $_SESSION['error'] = 'Vui lòng nhập ngày khởi hành!';
                    header('Location: ?act=them-lich-khoi-hanh');
                    exit();
                }

                // ========== KIỂM TRA TRÙNG LỊCH TRÌNH ==========
                // Kiểm tra xem đã có lịch trình cho tour này vào ngày này chưa
                $tourId = (int) $_POST['tour_id'];
                $departureDate = $_POST['departure_date'];

                $stmt = $this->modelSchedule->conn->prepare("
                    SELECT COUNT(*) as count FROM tour_schedules 
                    WHERE tour_id = ? AND departure_date = ?
                ");
                $stmt->execute([$tourId, $departureDate]);
                $result = $stmt->fetch();

                if ($result['count'] > 0) {
                    // Lấy tên tour để báo lỗi
                    $tour = $this->modelTour->getById($tourId);
                    $tourName = $tour['tour_name'] ?? 'N/A';
                    $tourCode = $tour['code'] ?? 'N/A';
                    $_SESSION['error'] = "Lỗi! Đã tồn tại lịch trình cho tour <strong>$tourCode - $tourName</strong> vào ngày <strong>$departureDate</strong>. Không được lên lịch trình trùng!";
                    header('Location: ?act=them-lich-khoi-hanh');
                    exit();
                }

                $data = [
                    'tour_id' => $tourId,
                    'departure_date' => $departureDate,
                    'return_date' => !empty($_POST['return_date']) ? $_POST['return_date'] : null,
                    'meeting_point' => $_POST['meeting_point'] ?? '',
                    'meeting_time' => $_POST['meeting_time'] ?? '',
                    'customer_name' => $_POST['customer_name'] ?? null,
                    'customer_phone' => $_POST['customer_phone'] ?? null,
                    'customer_email' => $_POST['customer_email'] ?? null,
                    'max_participants' => (int) ($_POST['max_participants'] ?? 0),
                    'num_adults' => (int) ($_POST['num_adults'] ?? 0),
                    'num_children' => (int) ($_POST['num_children'] ?? 0),
                    'num_infants' => (int) ($_POST['num_infants'] ?? 0),
                    'price_adult' => $_POST['price_adult'] ?? 0,
                    'price_child' => $_POST['price_child'] ?? 0,
                    'status' => $_POST['status'] ?? 'Open',
                    'notes' => $_POST['notes'] ?? ''
                ];

                // Validation: kiểm tra số khách không vượt quá chỗ tối đa
                $totalGuests = $data['num_adults'] + $data['num_children'] + $data['num_infants'];
                if ($totalGuests > $data['max_participants']) {
                    $_SESSION['error'] = 'Lỗi: Tổng số khách (' . $totalGuests . ') vượt quá số chỗ tối đa (' . $data['max_participants'] . ')!';
                    header('Location: ?act=them-lich-khoi-hanh');
                    exit();
                }

                $schedule_id = $this->modelSchedule->createSchedule($data);

                $_SESSION['success'] = 'Thêm lịch khởi hành thành công!';
                header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=them-lich-khoi-hanh');
                exit();
            }
        }
    }

    // ==================== API: ĐỒNG BỘ BOOKING KHI LỊCH THAY ĐỔI ====================
    public function ApiSyncBookingFromSchedule()
    {
        header('Content-Type: application/json; charset=utf-8');

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu schedule_id']);
            exit();
        }

        try {
            $schedule = $this->modelSchedule->getScheduleById($schedule_id);
            if (!$schedule) {
                echo json_encode(['success' => false, 'message' => 'Lịch không tồn tại']);
                exit();
            }

            // Lấy tất cả booking của lịch này
            $bookingModel = new Booking();
            $bookings = $bookingModel->getAll(['tour_id' => $schedule['tour_id'], 'tour_date' => $schedule['departure_date']]);

            echo json_encode([
                'success' => true,
                'data' => [
                    'schedule_id' => $schedule['schedule_id'],
                    'tour_id' => $schedule['tour_id'],
                    'departure_date' => $schedule['departure_date'],
                    'meeting_point' => $schedule['meeting_point'],
                    'meeting_time' => $schedule['meeting_time'],
                    'price_adult' => $schedule['price_adult'],
                    'price_child' => $schedule['price_child'],
                    'status' => $schedule['status'],
                    'max_participants' => $schedule['max_participants'],
                    'booking_count' => count($bookings ?? [])
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    // ==================== CHI TIẾT & PHÂN CÔNG ====================

    public function ScheduleDetail()
    {
        requireLogin();
        $schedule_id = $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }
        // Quyền xem: Admin hoặc HDV được phân công
        requireOwnScheduleOrAdmin($schedule_id, 'schedule.view_own');
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $staff = $this->modelSchedule->getScheduleStaff($schedule_id);
        $services = $this->modelSchedule->getScheduleServices($schedule_id);
        $allStaff = $this->modelSchedule->getAllStaff();
        // Chỉ hiển thị nhân sự là Hướng dẫn viên
        $allStaff = array_values(array_filter($allStaff, function ($s) {
            return (isset($s['staff_type']) && strtolower($s['staff_type']) === 'guide')
                || (isset($s['role']) && stripos($s['role'], 'hướng dẫn') !== false);
        }));
        $allServices = $this->modelSchedule->getAllServices();
        $journeyLogs = $this->modelSchedule->getJourneyLogs($schedule_id);

        // Lấy danh sách thành viên trong đoàn
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM schedule_group_members WHERE schedule_id = ? ORDER BY member_id ASC");
        $stmt->execute([$schedule_id]);
        $groupMembers = $stmt->fetchAll();

        // Lấy số lượng khách từ schedule (không phải từ guest_list)
        $numAdults = (int) ($schedule['num_adults'] ?? 0);
        $numChildren = (int) ($schedule['num_children'] ?? 0);
        $numInfants = (int) ($schedule['num_infants'] ?? 0);

        // Tính tổng tiền dựa trên số lượng khách trong schedule
        $priceAdult = (float) ($schedule['price_adult'] ?? 0);
        $priceChild = (float) ($schedule['price_child'] ?? 0);
        $estimatedTotal = ($numAdults * $priceAdult) + ($numChildren * $priceChild) + ($numInfants * $priceChild * 0.1);

        require_once './views/schedule/schedule_detail.php';
    }

    // ==================== SỬA LỊCH KHỞI HÀNH ====================

    public function EditSchedule()
    {
        requireRole('ADMIN');
        $schedule_id = $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $tours = $this->modelTour->getAll();
        require_once './views/schedule/edit_schedule.php';
    }

    public function UpdateSchedule()
    {
        requireRole('ADMIN');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $schedule_id = $_GET['id'] ?? null;
                if (!$schedule_id) {
                    $_SESSION['error'] = 'Thiếu tham số schedule_id!';
                    header('Location: ?act=danh-sach-lich-khoi-hanh');
                    exit();
                }

                $data = [
                    'tour_id' => !empty($_POST['tour_id']) ? $_POST['tour_id'] : null,
                    'departure_date' => $_POST['departure_date'],
                    'return_date' => !empty($_POST['return_date']) ? $_POST['return_date'] : null,
                    'meeting_point' => !empty($_POST['meeting_point']) ? $_POST['meeting_point'] : null,
                    'meeting_time' => !empty($_POST['meeting_time']) ? $_POST['meeting_time'] : null,
                    'customer_name' => $_POST['customer_name'] ?? null,
                    'customer_phone' => $_POST['customer_phone'] ?? null,
                    'customer_email' => $_POST['customer_email'] ?? null,
                    'max_participants' => !empty($_POST['max_participants']) ? (int) $_POST['max_participants'] : 0,
                    'num_adults' => !empty($_POST['num_adults']) ? (int) $_POST['num_adults'] : 0,
                    'num_children' => !empty($_POST['num_children']) ? (int) $_POST['num_children'] : 0,
                    'num_infants' => !empty($_POST['num_infants']) ? (int) $_POST['num_infants'] : 0,
                    'price_adult' => !empty($_POST['price_adult']) ? (float) $_POST['price_adult'] : 0,
                    'price_child' => !empty($_POST['price_child']) ? (float) $_POST['price_child'] : 0,
                    'status' => !empty($_POST['status']) ? $_POST['status'] : 'Open',
                    'notes' => !empty($_POST['notes']) ? $_POST['notes'] : null
                ];

                // ========== KIỂM TRA TRÙNG LỊCH TRÌNH KHI CẬP NHẬT ==========
                // Kiểm tra xem nếu thay đổi tour hoặc ngày thì có trùng với lịch khác không
                $tourId = $data['tour_id'];
                $departureDate = $data['departure_date'];

                $stmt = $this->modelSchedule->conn->prepare("
                    SELECT COUNT(*) as count FROM tour_schedules 
                    WHERE tour_id = ? AND departure_date = ? AND schedule_id != ?
                ");
                $stmt->execute([$tourId, $departureDate, $schedule_id]);
                $result = $stmt->fetch();

                if ($result['count'] > 0) {
                    // Lấy tên tour để báo lỗi
                    $tour = $this->modelTour->getById($tourId);
                    $tourName = $tour['tour_name'] ?? 'N/A';
                    $tourCode = $tour['code'] ?? 'N/A';
                    $_SESSION['error'] = "Lỗi! Đã tồn tại lịch trình khác cho tour <strong>$tourCode - $tourName</strong> vào ngày <strong>$departureDate</strong>. Không được cập nhật sang lịch trình trùng!";
                    header('Location: ?act=sua-lich-khoi-hanh&id=' . $schedule_id);
                    exit();
                }

                // Validation: kiểm tra số khách không vượt quá chỗ tối đa
                $totalGuests = $data['num_adults'] + $data['num_children'] + $data['num_infants'];
                if ($totalGuests > $data['max_participants']) {
                    $_SESSION['error'] = 'Lỗi: Tổng số khách (' . $totalGuests . ') vượt quá số chỗ tối đa (' . $data['max_participants'] . ')!';
                    header('Location: ?act=sua-lich-khoi-hanh&id=' . $schedule_id);
                    exit();
                }

                $this->modelSchedule->updateSchedule($schedule_id, $data);

                // Đồng bộ giá các booking liên quan khi giá lịch thay đổi
                try {
                    $bookingModel = new Booking();
                    $bookingModel->syncPricesBySchedule((int) $schedule_id);
                } catch (Exception $e) {
                    // Bỏ qua nếu lỗi nhẹ
                }

                $_SESSION['success'] = 'Cập nhật lịch khởi hành thành công! (Tất cả thông tin booking liên quan đã được đồng bộ tự động: giá tour, thông tin liên hệ, v.v.)';
                header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=sua-lich-khoi-hanh&id=' . $schedule_id);
                exit();
            }
        }
    }

    public function DeleteSchedule()
    {
        requireRole('ADMIN');
        try {
            $schedule_id = $_GET['id'] ?? null;
            if (!$schedule_id) {
                $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            } else {
                $this->modelSchedule->deleteSchedule($schedule_id);
                $_SESSION['success'] = 'Xóa lịch khởi hành thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ?act=danh-sach-lich-khoi-hanh');
        exit();
    }

    // ==================== PHÂN CÔNG NHÂN SỰ ====================

    public function AssignStaff()
    {
        requireRole('ADMIN');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $schedule_id = $_POST['schedule_id'] ?? null;
                $staff_id = $_POST['staff_id'] ?? null;
                // Luôn gán vai trò là Hướng dẫn viên
                $role = 'Hướng dẫn viên';

                if (!$schedule_id || !$staff_id) {
                    $_SESSION['error'] = 'Thiếu thông tin!';
                    header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                    exit();
                }

                // Lấy thông tin lịch để check availability
                $schedule = $this->modelSchedule->getScheduleById($schedule_id);

                // Chỉ cho phép phân công nhân sự có loại Guide
                $staffModel = new Staff();
                $staffInfo = $staffModel->getById($staff_id);
                if (!$staffInfo || strtolower($staffInfo['staff_type']) !== 'guide') {
                    $_SESSION['error'] = 'Chỉ phân công Hướng dẫn viên.';
                    header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                    exit();
                }

                // Kiểm tra nhân viên có rảnh không
                if (!$this->modelSchedule->checkStaffAvailability($staff_id, $schedule['departure_date'], $schedule['return_date'])) {
                    $_SESSION['warning'] = 'Nhân viên này đã có lịch trình khác trong khoảng thời gian này!';
                } else {
                    $this->modelSchedule->assignStaff($schedule_id, $staff_id, $role);
                    // Gửi thông báo tự động cho nhân viên
                    notifyStaffAssignment($schedule_id, $staff_id);
                    $_SESSION['success'] = 'Phân công nhân sự thành công!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
            exit();
        }
    }

    public function RemoveStaff()
    {
        requireRole('ADMIN');
        try {
            $schedule_id = $_GET['schedule_id'] ?? null;
            $staff_id = $_GET['staff_id'] ?? null;

            if (!$schedule_id || !$staff_id) {
                $_SESSION['error'] = 'Thiếu thông tin!';
            } else {
                $this->modelSchedule->removeStaff($schedule_id, $staff_id);
                $_SESSION['success'] = 'Xóa nhân sự khỏi lịch thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }

    // ==================== PHÂN CÔNG DỊCH VỤ ====================

    public function AssignService()
    {
        requireRole('ADMIN');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $schedule_id = $_POST['schedule_id'] ?? null;
                $service_id = $_POST['service_id'] ?? null;
                $quantity = $_POST['quantity'] ?? 1;
                $unit_price = $_POST['unit_price'] ?? 0;
                $notes = $_POST['notes'] ?? '';

                if (!$schedule_id || !$service_id) {
                    $_SESSION['error'] = 'Thiếu thông tin!';
                    header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                    exit();
                }

                $this->modelSchedule->assignService($schedule_id, $service_id, $quantity, $unit_price, $notes);
                // Gửi thông báo tự động cho đối tác dịch vụ
                notifyServiceAssignment($schedule_id, $service_id);
                $_SESSION['success'] = 'Phân công dịch vụ thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
            exit();
        }
    }

    public function RemoveService()
    {
        requireRole('ADMIN');
        try {
            $schedule_id = $_GET['schedule_id'] ?? null;
            $service_id = $_GET['service_id'] ?? null;

            if (!$schedule_id || !$service_id) {
                $_SESSION['error'] = 'Thiếu thông tin!';
            } else {
                $this->modelSchedule->removeService($schedule_id, $service_id);
                $_SESSION['success'] = 'Xóa dịch vụ khỏi lịch thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }

    // ==================== LIÊN KẾT DỊCH VỤ NHÀ CUNG CẤP (UC2) ====================

    public function AddServiceLink()
    {
        requireRole('ADMIN');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schedule_id = intval($_POST['schedule_id'] ?? 0);
            $supplier_id = intval($_POST['supplier_id'] ?? 0);
            if ($schedule_id <= 0 || $supplier_id <= 0) {
                $_SESSION['error'] = 'Thiếu lịch hoặc nhà cung cấp!';
                header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                exit();
            }

            $data = [
                'service_type' => trim($_POST['service_type'] ?? ''),
                'service_description' => trim($_POST['service_description'] ?? ''),
                'unit_price' => (float) ($_POST['unit_price'] ?? 0),
                'quantity' => (int) ($_POST['quantity'] ?? 1),
                'currency' => trim($_POST['currency'] ?? 'VND'),
                'notes' => trim($_POST['notes'] ?? ''),
                'status' => trim($_POST['status'] ?? 'pending')
            ];

            try {
                $this->modelSchedule->linkService($schedule_id, $supplier_id, $data);
                $_SESSION['success'] = 'Đã liên kết dịch vụ với lịch!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi liên kết dịch vụ: ' . $e->getMessage();
            }
            header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
            exit();
        }
        // GET: hiển thị form thêm dịch vụ liên kết
        requireRole('ADMIN');
        $schedule_id = $_GET['schedule_id'] ?? null;
        require_once './views/schedule/add_service_link.php';
    }

    public function UpdateServiceLink()
    {
        requireRole('ADMIN');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }
        $link_id = intval($_POST['link_id'] ?? 0);
        $schedule_id = intval($_POST['schedule_id'] ?? 0);
        $data = [
            'service_type' => trim($_POST['service_type'] ?? ''),
            'service_description' => trim($_POST['service_description'] ?? ''),
            'unit_price' => (float) ($_POST['unit_price'] ?? 0),
            'quantity' => (int) ($_POST['quantity'] ?? 1),
            'currency' => trim($_POST['currency'] ?? 'VND'),
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => trim($_POST['status'] ?? 'pending')
        ];
        try {
            $ok = $this->modelSchedule->updateService($link_id, $data);
            $_SESSION[$ok ? 'success' : 'warning'] = $ok ? 'Đã cập nhật dịch vụ.' : 'Không có thay đổi.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi cập nhật: ' . $e->getMessage();
        }
        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }

    public function UnlinkService()
    {
        requireRole('ADMIN');
        $link_id = intval($_GET['link_id'] ?? 0);
        $schedule_id = intval($_GET['schedule_id'] ?? 0);
        if ($link_id <= 0 || $schedule_id <= 0) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }
        try {
            $ok = $this->modelSchedule->unlinkService($link_id);
            $_SESSION[$ok ? 'success' : 'warning'] = $ok ? 'Đã hủy liên kết dịch vụ.' : 'Không tìm thấy dịch vụ.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi hủy liên kết: ' . $e->getMessage();
        }
        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }

    // ==================== XEM LỊCH THEO THÁNG ====================

    public function CalendarView()
    {
        requireLogin();
        requirePermission('tour.view');
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        if (isGuide()) {
            $staff_id = $_SESSION['staff_id'] ?? null;
            $assignments = $staff_id ? $this->modelSchedule->getAllStaffAssignments(['staff_id' => $staff_id]) : [];
            $unique = [];
            foreach ($assignments as $a) {
                $unique[$a['schedule_id']] = $a['schedule_id'];
            }
            $schedules = [];
            foreach ($unique as $sid) {
                $sch = $this->modelSchedule->getScheduleById($sid);
                if ($sch && date('m', strtotime($sch['departure_date'])) == $month && date('Y', strtotime($sch['departure_date'])) == $year) {
                    $schedules[] = $sch;
                }
            }
        } else {
            $schedules = $this->modelSchedule->getCalendarView($month, $year);
        }
        require_once './views/schedule/calendar_view.php';
    }

    // ==================== XUẤT BÁO CÁO ====================

    public function ExportSchedule()
    {
        requireRole('ADMIN');
        $schedule_id = $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $report = $this->modelSchedule->getScheduleReport($schedule_id);
        require_once './views/schedule/export_schedule.php';
    }

    // ==================== TỔNG QUAN PHÂN CÔNG NHÂN SỰ ====================

    public function StaffAssignments()
    {
        requireRole('ADMIN');
        $filters = [
            'staff_id' => $_GET['staff_id'] ?? null,
            'staff_type' => $_GET['staff_type'] ?? null,
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'),
            'to_date' => $_GET['to_date'] ?? date('Y-m-t')
        ];

        $assignments = $this->modelSchedule->getAllStaffAssignments($filters);
        $allStaff = $this->modelSchedule->getAllStaff();
        $stats = $this->modelSchedule->getStaffAssignmentStats($filters);

        require_once './views/schedule/staff_assignments.php';
    }

    // ==================== BULK: TẠO LỊCH CHO TẤT CẢ TOUR ====================
    public function SeedSchedulesForAllTours()
    {
        requireRole('ADMIN');
        try {
            $tours = $this->modelTour->getAll();
            $created = 0;
            $skipped = 0;

            foreach ($tours as $tour) {
                // Kiểm tra đã có lịch tương lai chưa
                $conn = connectDB();
                $stmt = $conn->prepare("SELECT COUNT(*) FROM tour_schedules WHERE tour_id = ? AND departure_date >= CURDATE() AND status != 'Cancelled'");
                $stmt->execute([$tour['tour_id']]);
                $hasFuture = $stmt->fetchColumn() > 0;
                if ($hasFuture) {
                    $skipped++;
                    continue;
                }

                // Tạo lịch mặc định: khởi hành sau 14 ngày, kết thúc theo duration_days nếu có
                $departure = date('Y-m-d', strtotime('+14 days'));
                $return = null;
                if (!empty($tour['duration_days']) && (int) $tour['duration_days'] > 1) {
                    $return = date('Y-m-d', strtotime($departure . ' +' . ((int) $tour['duration_days'] - 1) . ' days'));
                }

                $data = [
                    'tour_id' => $tour['tour_id'],
                    'departure_date' => $departure,
                    'return_date' => $return,
                    'meeting_point' => 'Văn phòng công ty',
                    'meeting_time' => '07:00',
                    'max_participants' => 30,
                    'price_adult' => 0,
                    'price_child' => 0,
                    'status' => 'Open',
                    'notes' => 'Lịch tự động tạo'
                ];

                try {
                    $this->modelSchedule->createSchedule($data);
                    $created++;
                } catch (Exception $e) {
                    // Nếu trùng ngày hoặc lỗi, bỏ qua
                    $skipped++;
                }
            }

            $_SESSION['success'] = 'Đã tạo lịch khởi hành: ' . $created . ' tour — Bỏ qua: ' . $skipped;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi seed lịch: ' . $e->getMessage();
        }

        header('Location: ?act=danh-sach-lich-khoi-hanh');
        exit();
    }

    // =============== HDV CHECK-IN ===============
    public function GuideCheckIn()
    {
        requireLogin();
        $schedule_id = $_POST['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'schedule.checkin');
        if (isGuide()) {
            $staff_id = $_SESSION['staff_id'] ?? null;
            if ($staff_id) {
                $ok = $this->modelSchedule->setStaffCheckIn($schedule_id, $staff_id);
                if ($ok) {
                    logUserActivity('guide_checkin', 'schedule', $schedule_id, 'HDV check-in');
                    $_SESSION['success'] = 'Check-in thành công!';
                } else {
                    $_SESSION['warning'] = 'Không thể check-in (cần thêm cột check_in_time).';
                }
            }
        }
        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }

    // =============== HDV LƯU NHẬT KÝ ===============
    public function GuideSaveJourneyLog()
    {
        requireLogin();
        $schedule_id = $_POST['schedule_id'] ?? null;
        $log_text = trim($_POST['log_text'] ?? '');
        if (!$schedule_id || $log_text === '') {
            $_SESSION['error'] = 'Thiếu dữ liệu nhật ký.';
            header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'schedule.log.update');
        if (isGuide()) {
            $staff_id = $_SESSION['staff_id'] ?? null;
            if ($staff_id) {
                $res = $this->modelSchedule->addJourneyLog($schedule_id, $staff_id, $log_text);
                if ($res) {
                    logUserActivity('guide_add_log', 'schedule', $schedule_id, 'Thêm nhật ký');
                    $_SESSION['success'] = 'Đã lưu nhật ký.';
                } else {
                    $_SESSION['warning'] = 'Không thể lưu (cần tạo bảng schedule_journey_logs).';
                }
            }
        }
        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }

    // =============== THAY ĐỔI TRẠNG THÁI TOUR ===============
    public function ChangeScheduleStatus()
    {
        requireRole('ADMIN');
        try {
            $schedule_id = $_POST['schedule_id'] ?? $_GET['schedule_id'] ?? null;
            $new_status = $_POST['status'] ?? $_GET['status'] ?? null;

            if (!$schedule_id || !$new_status) {
                $_SESSION['error'] = 'Thiếu thông tin trạng thái!';
                header('Location: ?act=danh-sach-lich-khoi-hanh');
                exit();
            }

            $this->modelSchedule->changeScheduleStatus($schedule_id, $new_status);

            $statusNames = [
                'Open' => 'Mở đặt',
                'Full' => 'Đầy',
                'Confirmed' => 'Đã xác nhận',
                'In Progress' => 'Đang diễn ra',
                'Completed' => 'Hoàn thành',
                'Cancelled' => 'Đã hủy'
            ];

            $_SESSION['success'] = 'Đã chuyển trạng thái sang: ' . ($statusNames[$new_status] ?? $new_status);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
        exit();
    }



    // ==================== USE CASE 1: HDV VIEWS ====================

    /**
     * Danh sách tour được phân công cho HDV (lịch làm việc)
     * Use Case 1: Bước 2, 3
     */
    public function MyTours()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');

        $staff_id = $_SESSION['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Không tìm thấy thông tin nhân viên!';
            header('Location: ?act=/');
            exit();
        }

        // Lọc theo các điều kiện (Use Case 1 - Bước 3a)
        $filter_month = $_GET['month'] ?? null;
        $filter_week = $_GET['week'] ?? null;
        $filter_status = $_GET['status'] ?? null;
        $filter_from_date = $_GET['from_date'] ?? null;
        $filter_to_date = $_GET['to_date'] ?? null;

        // Lấy danh sách các assignment của HDV
        $modelStaff = new Staff();
        $modelTour = new Tour();

        // Tạo date range dựa trên filter
        if ($filter_month) {
            $month = intval($filter_month);
            $year = intval($_GET['year'] ?? date('Y'));
            $first_day = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
            $last_day = date('Y-m-d', mktime(0, 0, 0, $month + 1, 0, $year));
            $from_date = $first_day;
            $to_date = $last_day;
        } elseif ($filter_week) {
            $week = intval($filter_week);
            $year = intval($_GET['year'] ?? date('Y'));
            $week_date = new DateTime();
            $week_date->setISODate($year, $week);
            $from_date = $week_date->format('Y-m-d');
            $to_date = $week_date->modify('+6 days')->format('Y-m-d');
        } elseif ($filter_from_date && $filter_to_date) {
            $from_date = $filter_from_date;
            $to_date = $filter_to_date;
        } else {
            // Mặc định: từ hôm nay đến 1 năm sau
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d', strtotime('+1 year'));
        }

        // Lấy lịch trình của HDV trong khoảng thời gian
        $assignments = $this->modelSchedule->getAllStaffAssignments([
            'staff_id' => $staff_id,
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);

        // Lọc theo status tour nếu có
        if ($filter_status) {
            $assignments = array_filter($assignments, function ($a) use ($filter_status) {
                return $a['schedule_status'] === $filter_status;
            });
        }

        // Nhóm các lịch trình thành danh sách tour duy nhất
        $tours_data = [];
        $tour_cache = [];

        foreach ($assignments as $assignment) {
            $tour_id = $assignment['tour_id'];
            $schedule_id = $assignment['schedule_id'];

            if (!isset($tour_cache[$tour_id])) {
                $tour = $modelTour->getById($tour_id);
                $tour_cache[$tour_id] = $tour;
            }

            $schedule = $this->modelSchedule->getScheduleById($schedule_id);

            $key = $tour_id . '_' . $schedule_id;
            $tours_data[$key] = [
                'tour' => $tour_cache[$tour_id],
                'schedule' => $schedule,
                'assignment' => $assignment
            ];
        }

        // E2: Nếu không có tour nào
        if (empty($tours_data)) {
            $no_tours_message = 'Hiện tại bạn chưa được phân công tour nào.';
        } else {
            $no_tours_message = null;
        }

        // Lấy các tour ngắn gọn để hiển thị (E2)
        $tours = $tours_data;

        require_once './views/schedule/my_tours_list.php';
    }

    /**
     * Chi tiết tour dành cho HDV
     * Use Case 1: Bước 4, 5
     */
    public function MyTourDetail()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');

        $schedule_id = $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $staff_id = $_SESSION['staff_id'] ?? null;

        // Kiểm tra HDV có được phân công schedule này không
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Kiểm tra quyền
        $staff = $this->modelSchedule->getScheduleStaff($schedule_id);
        $is_assigned = false;
        foreach ($staff as $s) {
            if ($s['staff_id'] == $staff_id) {
                $is_assigned = true;
                break;
            }
        }

        if (!$is_assigned && !isAdmin()) {
            $_SESSION['error'] = 'Bạn không được phân công cho lịch này!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Lấy chi tiết tour (Use Case 1 - Bước 4b)
        $modelTour = new Tour();
        $modelTourDetail = new TourDetail();
        $groupMemberModel = new GroupMember();

        $tour = $modelTour->getById($schedule['tour_id']);
        $itineraries = $modelTourDetail->getItineraries($schedule['tour_id']);
        $gallery = $modelTourDetail->getGallery($schedule['tour_id']);
        $policies = $modelTourDetail->getPolicies($schedule['tour_id']);
        $assigned_staff = $staff;

        // Lấy danh sách thành viên trong đoàn (guest list)
        $groupMembers = $groupMemberModel->getByScheduleId($schedule_id);

        // Tính số ngày
        $departure = new DateTime($schedule['departure_date']);
        $return = new DateTime($schedule['return_date'] ?? $schedule['departure_date']);
        $days_diff = $departure->diff($return)->days + 1;
        $total_days = $days_diff;

        require_once './views/schedule/tour_detail_hdv.php';
    }

    /**
     * Xem nhiệm vụ của tôi
     * Use Case 1: Bước 5
     */
    public function MyTasks()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');

        $staff_id = $_SESSION['staff_id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Kiểm tra HDV được phân công
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        $staff = $this->modelSchedule->getScheduleStaff($schedule_id);
        $is_assigned = false;
        foreach ($staff as $s) {
            if ($s['staff_id'] == $staff_id) {
                $is_assigned = true;
                break;
            }
        }

        if (!$is_assigned && !isAdmin()) {
            $_SESSION['error'] = 'Bạn không được phân công cho lịch này!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelTour = new Tour();
        $tour = $modelTour->getById($schedule['tour_id']);

        // Lấy danh sách nhiệm vụ (Use Case 1 - Bước 5a)
        // Tạm thời, nhiệm vụ được lấy từ:
        // 1. Tour itinerary
        // 2. Special notes
        // 3. Journey logs (ghi chú trong quá trình diễn ra)

        $modelSpecialNote = new SpecialNote();
        $special_notes = $modelSpecialNote->getNotesBySchedule($schedule_id);

        $modelTourDetail = new TourDetail();
        $itineraries = $modelTourDetail->getItineraries($schedule['tour_id']);

        $journey_logs = $this->modelSchedule->getJourneyLogs($schedule_id);

        // Xây dựng task list từ các nguồn
        $tasks = [];

        // Task từ tour itinerary
        foreach ($itineraries as $iti) {
            $tasks[] = [
                'id' => 'iti_' . $iti['itinerary_id'],
                'type' => 'Hướng dẫn đoàn',
                'title' => 'Ngày ' . $iti['day_number'] . ': ' . $iti['title'],
                'time' => 'Cả ngày',
                'location' => '',
                'responsible' => 'Hướng dẫn viên',
                'description' => $iti['description'],
                'status' => 'Pending'
            ];
        }

        // Task từ special notes
        foreach ($special_notes as $note) {
            $tasks[] = [
                'id' => 'note_' . $note['note_id'],
                'type' => 'Ghi chú đặc biệt',
                'title' => $note['title'] ?? 'Ghi chú từ quản lý',
                'time' => $note['note_date'] ?? '',
                'location' => '',
                'responsible' => 'Quản lý',
                'description' => $note['content'],
                'status' => 'Pending'
            ];
        }

        // Lấy trạng thái hoàn thành nhiệm vụ đã lưu
        $taskCheck = new TaskCheck();
        $doneMap = $taskCheck->getDoneMap($schedule_id);
        $completed = 0;
        foreach ($tasks as &$t) {
            $key = $t['id'];
            $t['done'] = isset($doneMap[$key]) ? (bool) $doneMap[$key] : false;
            if ($t['done'])
                $completed++;
        }
        unset($t);

        $completed_count = $completed;

        require_once __DIR__ . '/../views/schedule/my_tasks.php';
    }

    /**
     * Lưu trạng thái hoàn thành nhiệm vụ của HDV
     */
    public function SaveTasks()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $schedule_id = $_POST['schedule_id'] ?? null;
        $doneKeys = $_POST['done_keys'] ?? [];
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'schedule.log.update');

        // Optional: titles map from form
        $titles = $_POST['task_titles'] ?? [];
        $userId = $_SESSION['user_id'] ?? null;

        $taskCheck = new TaskCheck();
        $ok = $taskCheck->saveBulk((int) $schedule_id, array_map('strval', (array) $doneKeys), $userId, $titles);
        if ($ok) {
            $_SESSION['success'] = 'Đã lưu tiến độ nhiệm vụ.';

            // ========== AUTO UPDATE BOOKING STATUS ==========
            // Kiểm tra xem tất cả task có hoàn thành không
            // Nếu có → tự động chuyển booking sang "Đã hoàn thành"
            try {
                $schedule = $this->modelSchedule->getScheduleById((int) $schedule_id);
                if ($schedule) {
                    $conn = connectDB();

                    // Lấy danh sách tất cả task cho schedule này
                    $modelTourDetail = new TourDetail();
                    $itineraries = $modelTourDetail->getItineraries($schedule['tour_id']);

                    // Tính tổng task
                    $totalTasks = count($itineraries);

                    // Tính task hoàn thành
                    $doneMap = $taskCheck->getDoneMap((int) $schedule_id);
                    $completedTasks = 0;
                    foreach ($itineraries as $iti) {
                        $key = 'iti_' . $iti['itinerary_id'];
                        if (isset($doneMap[$key]) && $doneMap[$key]) {
                            $completedTasks++;
                        }
                    }

                    // Nếu tất cả task hoàn thành → update booking
                    if ($totalTasks > 0 && $completedTasks >= $totalTasks) {
                        // Lấy tất cả booking cho schedule này
                        $stmt = $conn->prepare("
                            SELECT DISTINCT b.booking_id FROM bookings b
                            INNER JOIN tour_schedules ts ON ts.tour_id = b.tour_id AND ts.departure_date = b.tour_date
                            WHERE ts.schedule_id = ? AND b.status != 'Đã hủy'
                        ");
                        $stmt->execute([(int) $schedule_id]);
                        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Update status của tất cả booking thành "Đã hoàn thành"
                        if (!empty($bookings)) {
                            $bookingModel = new Booking();
                            foreach ($bookings as $booking) {
                                try {
                                    $bookingModel->updateStatus(
                                        (int) $booking['booking_id'],
                                        'Đã hoàn thành',
                                        $userId,
                                        'Tự động cập nhật khi hướng dẫn viên hoàn thành tất cả nhiệm vụ'
                                    );
                                } catch (Exception $e) {
                                    // Log error nhưng không block
                                    error_log('Error updating booking status: ' . $e->getMessage());
                                }
                            }
                            $_SESSION['success'] .= ' — Tất cả booking liên quan đã được cập nhật sang "Đã hoàn thành".';
                        }

                        // ========== AUTO UPDATE SERVICE LINKS STATUS ==========
                        // Cập nhật status của tất cả service links thành "Completed" (hoàn thành)
                        try {
                            $stmt = $conn->prepare("
                                UPDATE schedule_service_links 
                                SET status = 2
                                WHERE schedule_id = ? AND status = 1
                            ");
                            $stmt->execute([(int) $schedule_id]);
                            $updatedServices = $stmt->rowCount();

                            if ($updatedServices > 0) {
                                // Gửi thông báo cho các nhà cung cấp
                                $stmt = $conn->prepare("
                                    SELECT DISTINCT supplier_id FROM schedule_service_links
                                    WHERE schedule_id = ? AND status = 2
                                ");
                                $stmt->execute([(int) $schedule_id]);
                                $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($suppliers as $supplier) {
                                    // TODO: Gửi thông báo cho nhà cung cấp khi có chức năng notification
                                    // notifySupplierCompletion((int) $schedule_id, (int) $supplier['supplier_id']);
                                }

                                $_SESSION['success'] .= ' — ' . $updatedServices . ' dịch vụ bên hứng đã được cập nhật sang "Đã hoàn thành".';
                            }
                        } catch (Exception $e) {
                            error_log('Error updating service links: ' . $e->getMessage());
                        }

                        // ========== AUTO UPDATE SCHEDULE STATUS ========== 
                        // Khi tất cả task đã hoàn thành, chuyển lịch sang "Completed"
                        try {
                            if (!empty($schedule['status']) && $schedule['status'] !== 'Completed') {
                                $this->modelSchedule->changeScheduleStatus((int) $schedule_id, 'Completed');
                                $_SESSION['success'] .= ' — Lịch khởi hành đã được chuyển sang "Completed".';
                            }
                        } catch (Exception $e) {
                            // Không block, chỉ log nếu có ràng buộc (ví dụ thiếu check-in/nhật ký)
                            error_log('Error auto-completing schedule: ' . $e->getMessage());
                        }
                    }
                }
            } catch (Exception $e) {
                // Log error nhưng không block người dùng
                error_log('Error in auto-update booking: ' . $e->getMessage());
            }
        } else {
            $_SESSION['error'] = 'Không thể lưu tiến độ. Vui lòng thử lại.';
        }
        header('Location: ?act=hdv-nhiem-vu-cua-toi&schedule_id=' . (int) $schedule_id);
        exit();
    }

    // ==================== UC2: DANH SÁCH KHÁCH CHO HDV ====================
    public function GuestList()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');

        $schedule_id = $_GET['schedule_id'] ?? $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Quyền: HDV phải được phân công
        requireOwnScheduleOrAdmin($schedule_id, 'guest.view');

        $filters = [
            'check_in_status' => $_GET['check_in_status'] ?? null,
            'has_room' => isset($_GET['has_room']) ? (bool) $_GET['has_room'] : null,
            'search' => $_GET['search'] ?? null,
        ];

        $bookingModel = new Booking();
        $scheduleInfo = $bookingModel->getScheduleInfo($schedule_id);
        $guests = $bookingModel->getGuestsBySchedule($schedule_id, $filters);
        $summary = $bookingModel->getGuestSummaryBySchedule($schedule_id);

        require_once './views/schedule/guest_list_hdv.php';
    }

    public function ExportGuestList()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');
        $schedule_id = $_GET['schedule_id'] ?? $_GET['id'] ?? null;
        $format = $_GET['format'] ?? 'excel';
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'guest.export');

        $bookingModel = new Booking();
        $guests = $bookingModel->getGuestsBySchedule($schedule_id);
        $scheduleInfo = $bookingModel->getScheduleInfo($schedule_id);

        if ($format === 'pdf') {
            require_once './views/booking/export_guest_pdf.php';
            return;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="guest-list-"' . $schedule_id . '.xls');
        echo "<table border='1'>";
        echo "<tr><th>Họ tên</th><th>Giới tính</th><th>Điện thoại</th><th>Email</th><th>Nhóm</th><th>Phòng</th><th>Trạng thái</th></tr>";
        foreach ($guests as $g) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($g['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($g['gender']) . "</td>";
            echo "<td>" . htmlspecialchars($g['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($g['email']) . "</td>";
            echo "<td>" . htmlspecialchars($g['group_name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($g['room_number'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($g['check_in_status'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit();
    }

    // ==================== UC4: ĐIỂM DANH KHÁCH CHO HDV ====================
    public function GuestCheckIn()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');
        $schedule_id = $_GET['schedule_id'] ?? $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'guest.checkin');

        $bookingModel = new Booking();
        $scheduleInfo = $bookingModel->getScheduleInfo($schedule_id);
        $guests = $bookingModel->getGuestsBySchedule($schedule_id);

        require_once './views/schedule/guest_checkin.php';
    }

    public function SaveCheckInBatch()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }
        $schedule_id = $_POST['schedule_id'] ?? null;
        $updates = $_POST['updates'] ?? []; // array of [guest_id => status]
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'guest.checkin');

        $bookingModel = new Booking();
        $okCount = 0;
        foreach ($updates as $member_id => $status) {
            // Giữ nguyên các giá trị hợp lệ: Checked-In, No-Show, Late, Pending
            $status = in_array($status, ['Checked-In', 'No-Show', 'Late', 'Pending']) ? $status : 'Pending';
            // Cập nhật vào schedule_group_members (dùng member_id)
            if ($bookingModel->updateGroupMemberCheckIn((int) $member_id, $status)) {
                $okCount++;
            }
        }
        $_SESSION['success'] = 'Đã cập nhật điểm danh cho ' . $okCount . ' khách.';
        header('Location: ?act=hdv-diem-danh&schedule_id=' . $schedule_id);
        exit();
    }

    public function ExportCheckInReport()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');
        $schedule_id = $_GET['schedule_id'] ?? null;
        $format = $_GET['format'] ?? 'pdf';
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }
        requireOwnScheduleOrAdmin($schedule_id, 'guest.export');

        $bookingModel = new Booking();
        $guests = $bookingModel->getGuestsBySchedule($schedule_id);
        $summary = $bookingModel->getGuestSummaryBySchedule($schedule_id);

        if ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="checkin-report-"' . $schedule_id . '.xls');
            echo "<table border='1'>";
            echo "<tr><th>Họ tên</th><th>Trạng thái</th><th>Thời gian</th></tr>";
            foreach ($guests as $g) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($g['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($g['check_in_status'] ?? 'Pending') . "</td>";
                echo "<td>" . htmlspecialchars($g['check_in_time'] ?? '') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            exit();
        } else {
            // Simple HTML export as PDF placeholder
            header('Content-Type: text/html');
            echo '<h3>Báo cáo điểm danh - Lịch #' . htmlspecialchars($schedule_id) . '</h3>';
            echo '<p>Tổng khách: ' . (int) $summary['total_guests'] . ' — Đã check-in: ' . (int) $summary['checked_in'] . ' — Vắng: ' . (int) $summary['no_show'] . '</p>';
            echo '<table border="1" cellpadding="6"><tr><th>Họ tên</th><th>Trạng thái</th><th>Thời gian</th></tr>';
            foreach ($guests as $g) {
                echo '<tr><td>' . htmlspecialchars($g['full_name']) . '</td><td>' . htmlspecialchars($g['check_in_status'] ?? 'Pending') . '</td><td>' . htmlspecialchars($g['check_in_time'] ?? '') . '</td></tr>';
            }
            echo '</table>';
            exit();
        }
    }

    /**
     * Xem lịch tháng
     * Use Case 1: Bước 6, Luồng phụ A2
     */
    public function MyCalendarView()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');

        $staff_id = $_SESSION['staff_id'] ?? null;
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        // Lấy danh sách lịch khởi hành trong tháng
        $calendar_data = $this->modelSchedule->getCalendarView($month, $year);

        // Lọc chỉ các tour được phân công cho HDV
        $assignments = $this->modelSchedule->getAllStaffAssignments([
            'staff_id' => $staff_id,
            'from_date' => date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)),
            'to_date' => date('Y-m-d', mktime(0, 0, 0, $month + 1, 0, $year))
        ]);

        $assigned_schedule_ids = array_column($assignments, 'schedule_id');

        // Lọc calendar_data theo assigned_schedule_ids
        $calendar_data = array_filter($calendar_data, function ($item) use ($assigned_schedule_ids) {
            return in_array($item['schedule_id'], $assigned_schedule_ids);
        });

        // Xây dựng mảng ngày -> danh sách tour
        $calendar_events = [];
        foreach ($calendar_data as $schedule) {
            $day = (int) date('d', strtotime($schedule['departure_date']));
            if (!isset($calendar_events[$day])) {
                $calendar_events[$day] = [];
            }
            $calendar_events[$day][] = $schedule;
        }

        require_once './views/schedule/calendar_view_hdv.php';
    }

    /**
     * Xuất lịch trình
     * Use Case 1: Bước 7, Luồng phụ A3
     */
    public function ExportMySchedule()
    {
        requireLogin();
        requireGuideRole('schedule.view_own');

        $format = $_GET['format'] ?? 'pdf';
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $staff_id = $_SESSION['staff_id'] ?? null;

        // Kiểm tra quyền
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        $staff = $this->modelSchedule->getScheduleStaff($schedule_id);
        $is_assigned = false;
        foreach ($staff as $s) {
            if ($s['staff_id'] == $staff_id) {
                $is_assigned = true;
                break;
            }
        }

        if (!$is_assigned && !isAdmin()) {
            $_SESSION['error'] = 'Bạn không được phép xuất lịch này!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        try {
            $modelTour = new Tour();
            $modelTourDetail = new TourDetail();
            $tour = $modelTour->getById($schedule['tour_id']);
            $itineraries = $modelTourDetail->getItineraries($schedule['tour_id']);

            if ($format === 'excel') {
                $this->exportScheduleToExcel($schedule, $tour, $itineraries);
            } else {
                $this->exportScheduleToPDF($schedule, $tour, $itineraries);
            }
        } catch (Exception $e) {
            // E4: Lỗi khi tải xuống
            $_SESSION['error'] = 'Tải xuống thất bại: ' . $e->getMessage();
            header('Location: ?act=hdv-chi-tiet-tour&id=' . $schedule_id);
            exit();
        }
    }

    /**
     * Xuất sang PDF
     */
    private function exportScheduleToPDF($schedule, $tour, $itineraries)
    {
        // Sử dụng thư viện TCPDF hoặc tương tự
        // Tạm thời: xuất HTML với header để in
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="lich-tour-' . $schedule['schedule_id'] . '.pdf"');

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Lịch tour</title></head><body>';
        echo '<h1>' . htmlspecialchars($tour['tour_name']) . '</h1>';
        echo '<p>Mã tour: ' . htmlspecialchars($tour['code']) . '</p>';
        echo '<p>Khởi hành: ' . $schedule['departure_date'] . '</p>';
        echo '<p>Kết thúc: ' . ($schedule['return_date'] ?? $schedule['departure_date']) . '</p>';
        echo '<h3>Lịch trình:</h3>';
        echo '<ul>';
        foreach ($itineraries as $iti) {
            echo '<li><strong>Ngày ' . $iti['day_number'] . ': ' . $iti['title'] . '</strong><br>';
            echo $iti['description'] . '</li>';
        }
        echo '</ul>';
        echo '</body></html>';
        exit();
    }

    /**
     * Xuất sang Excel
     */
    private function exportScheduleToExcel($schedule, $tour, $itineraries)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="lich-tour-' . $schedule['schedule_id'] . '.xls"');

        echo "<table border='1'>";
        echo "<tr><td colspan='2'><b>LỊCH TOUR</b></td></tr>";
        echo "<tr><td>Tên tour:</td><td>" . htmlspecialchars($tour['tour_name']) . "</td></tr>";
        echo "<tr><td>Mã tour:</td><td>" . htmlspecialchars($tour['code']) . "</td></tr>";
        echo "<tr><td>Khởi hành:</td><td>" . $schedule['departure_date'] . "</td></tr>";
        echo "<tr><td>Kết thúc:</td><td>" . ($schedule['return_date'] ?? $schedule['departure_date']) . "</td></tr>";
        echo "</table><br>";

        echo "<table border='1'>";
        echo "<tr><td><b>Ngày</b></td><td><b>Hoạt động</b></td><td><b>Mô tả</b></td><td><b>Nơi ở</b></td></tr>";
        foreach ($itineraries as $iti) {
            echo "<tr>";
            echo "<td>Ngày " . $iti['day_number'] . "</td>";
            echo "<td>" . htmlspecialchars($iti['title']) . "</td>";
            echo "<td>" . htmlspecialchars($iti['description']) . "</td>";
            echo "<td>" . htmlspecialchars($iti['accommodation']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit();
    }

    // ==================== HỒ SƠ HƯỚNG DẪN VIÊN ====================

    public function GuideProfile()
    {
        requireGuideRole();

        $staff_id = $_SESSION['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Không tìm thấy thông tin hướng dẫn viên!';
            header('Location: ?act=home-guide');
            exit();
        }

        $modelStaff = new Staff();

        // Lấy thông tin HDV
        $guide = $modelStaff->getById($staff_id);

        // Lấy chứng chỉ
        $certificates = $modelStaff->getCertificates($staff_id);

        // Lấy lịch sử tour
        $tour_history = $modelStaff->getTourHistory($staff_id);

        // Thống kê
        $today = date('Y-m-d');
        $upcoming_tours = $this->modelSchedule->countUpcomingToursForStaff($staff_id, $today);

        $currentMonth = date('Y-m');
        $completed_this_month = $this->modelSchedule->countCompletedToursForStaff($staff_id, $currentMonth);

        require_once './views/schedule/guide_profile.php';
    }

    // ==================== THÔNG BÁO HƯỚNG DẪN VIÊN ====================

    public function GuideNotifications()
    {
        requireGuideRole();

        $staff_id = $_SESSION['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Không tìm thấy thông tin hướng dẫn viên!';
            header('Location: ?act=home-guide');
            exit();
        }

        // Lấy tất cả thông báo (tạm thời mock data, sau này sẽ lấy từ DB)
        $notifications = $this->getGuideNotifications($staff_id);

        // Đếm số thông báo chưa đọc
        $unread_count = count(array_filter($notifications, function ($n) {
            return !$n['is_read'];
        }));

        require_once './views/schedule/guide_notifications.php';
    }

    public function MarkNotificationsRead()
    {
        requireGuideRole();

        $staff_id = $_SESSION['staff_id'] ?? null;
        if (!$staff_id) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin hướng dẫn viên!']);
            exit();
        }

        // Đánh dấu tất cả thông báo là đã đọc
        // TODO: Implement database update khi có bảng notifications
        $result = $this->markAllNotificationsAsRead($staff_id);

        echo json_encode(['success' => $result, 'message' => $result ? 'Đã đánh dấu tất cả là đã đọc' : 'Có lỗi xảy ra']);
        exit();
    }

    // ==================== HELPER METHODS ====================

    /**
     * Lấy danh sách thông báo của HDV (tạm thời mock data)
     * TODO: Thay thế bằng query database khi có bảng notifications
     */
    private function getGuideNotifications($staff_id)
    {
        // Mock data - sẽ thay thế bằng query database thực tế
        $notifications = [];

        // Lấy tour sắp khởi hành trong 3 ngày tới
        $upcoming = $this->modelSchedule->getUpcomingToursForStaff($staff_id, 3);
        foreach ($upcoming as $tour) {
            $days_until = (strtotime($tour['departure_date']) - time()) / 86400;
            $notifications[] = [
                'id' => 'tour_' . $tour['schedule_id'],
                'type' => 'tour',
                'title' => 'Tour sắp khởi hành',
                'message' => $tour['tour_name'] . ' sẽ khởi hành vào ' . date('d/m/Y', strtotime($tour['departure_date'])) . ' (' . ceil($days_until) . ' ngày nữa)',
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . floor($days_until) . ' days')),
                'is_read' => false,
                'action_url' => '?act=hdv-chi-tiet-tour&id=' . $tour['schedule_id']
            ];
        }

        // Sắp xếp theo thời gian mới nhất
        usort($notifications, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $notifications;
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     * TODO: Implement database update khi có bảng notifications
     */
    private function markAllNotificationsAsRead($staff_id)
    {
        // Tạm thời trả về true
        // Khi có bảng notifications sẽ update database
        return true;
    }

    // ==================== NHẬT KÝ TOUR (USE CASE VIII.3) ====================

    /**
     * Xem danh sách nhật ký của tour
     */
    public function ViewTourJournal()
    {
        requireGuideRole();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu ID lịch tour!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Kiểm tra quyền xem (HDV phải được phân công cho tour này)
        if (!isOwnSchedule($schedule_id)) {
            $_SESSION['error'] = 'Bạn không có quyền xem nhật ký tour này!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch tour!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelJournal = new TourJournal();
        $journals = $modelJournal->getJournalsBySchedule($schedule_id);

        require_once './views/schedule/tour_journal_list.php';
    }

    /**
     * Form tạo nhật ký mới
     */
    public function CreateJournalEntryForm()
    {
        requireGuideRole();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id || !isOwnSchedule($schedule_id)) {
            $_SESSION['error'] = 'Không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        require_once './views/schedule/tour_journal_form.php';
    }

    /**
     * Lưu nhật ký mới
     */
    public function CreateJournalEntry()
    {
        requireGuideRole();

        try {
            $schedule_id = $_POST['schedule_id'] ?? null;
            if (!$schedule_id || !isOwnSchedule($schedule_id)) {
                throw new Exception('Không có quyền!');
            }

            $modelJournal = new TourJournal();

            $data = [
                'schedule_id' => $schedule_id,
                'journal_date' => $_POST['journal_date'] ?? date('Y-m-d'),
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? '',
                'activities' => $_POST['activities'] ?? null,
                'incidents' => $_POST['incidents'] ?? null,
                'incidents_resolved' => $_POST['incidents_resolved'] ?? null,
                'guest_feedback' => $_POST['guest_feedback'] ?? null,
                'weather' => $_POST['weather'] ?? null,
                'location' => $_POST['location'] ?? null,
                'status' => $_POST['status'] ?? 'Published',
                'created_by' => $_SESSION['staff_id']
            ];

            // Validate
            if (empty($data['title'])) {
                throw new Exception('Vui lòng nhập tiêu đề nhật ký!');
            }

            // Upload hình ảnh nếu có
            $images = [];
            if (!empty($_FILES['images']['name'][0])) {
                $images = $this->uploadJournalImages($_FILES['images']);
                $data['images'] = $images;
            }

            $journal_id = $modelJournal->create($data);

            $_SESSION['success'] = 'Tạo nhật ký thành công!';
            header('Location: ?act=view-tour-journal&schedule_id=' . $schedule_id);
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: ?act=view-tour-journal&schedule_id=' . ($schedule_id ?? ''));
            exit();
        }
    }

    /**
     * Form chỉnh sửa nhật ký
     */
    public function EditJournalEntry()
    {
        requireGuideRole();

        $journal_id = $_GET['id'] ?? null;
        if (!$journal_id) {
            $_SESSION['error'] = 'Thiếu ID nhật ký!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelJournal = new TourJournal();
        $journal = $modelJournal->getById($journal_id);

        if (!$journal) {
            $_SESSION['error'] = 'Không tìm thấy nhật ký!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Kiểm tra quyền
        if (!$modelJournal->isOwner($journal_id, $_SESSION['staff_id'])) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa nhật ký này!';
            header('Location: ?act=view-tour-journal&schedule_id=' . $journal['schedule_id']);
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($journal['schedule_id']);
        $images = $modelJournal->getImages($journal_id);

        require_once './views/schedule/tour_journal_form.php';
    }

    /**
     * Cập nhật nhật ký
     */
    public function UpdateJournalEntry()
    {
        requireGuideRole();

        try {
            $journal_id = $_POST['journal_id'] ?? null;
            if (!$journal_id) {
                throw new Exception('Thiếu ID nhật ký!');
            }

            $modelJournal = new TourJournal();

            if (!$modelJournal->isOwner($journal_id, $_SESSION['staff_id'])) {
                throw new Exception('Không có quyền!');
            }

            $journal = $modelJournal->getById($journal_id);
            $schedule_id = $journal['schedule_id'];

            $data = [
                'journal_date' => $_POST['journal_date'] ?? date('Y-m-d'),
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? '',
                'activities' => $_POST['activities'] ?? null,
                'incidents' => $_POST['incidents'] ?? null,
                'incidents_resolved' => $_POST['incidents_resolved'] ?? null,
                'guest_feedback' => $_POST['guest_feedback'] ?? null,
                'weather' => $_POST['weather'] ?? null,
                'location' => $_POST['location'] ?? null,
                'status' => $_POST['status'] ?? 'Published'
            ];

            if (empty($data['title'])) {
                throw new Exception('Vui lòng nhập tiêu đề!');
            }

            // Upload ảnh mới nếu có
            if (!empty($_FILES['images']['name'][0])) {
                $images = $this->uploadJournalImages($_FILES['images']);
                foreach ($images as $img) {
                    $modelJournal->addImage($journal_id, $img);
                }
            }

            $modelJournal->update($journal_id, $data);

            $_SESSION['success'] = 'Cập nhật nhật ký thành công!';
            header('Location: ?act=view-tour-journal&schedule_id=' . $schedule_id);
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: ?act=edit-journal-entry&id=' . ($journal_id ?? ''));
            exit();
        }
    }

    /**
     * Xóa nhật ký
     */
    public function DeleteJournalEntry()
    {
        requireGuideRole();

        $journal_id = $_GET['id'] ?? null;
        if (!$journal_id) {
            $_SESSION['error'] = 'Thiếu ID!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelJournal = new TourJournal();

        if (!$modelJournal->isOwner($journal_id, $_SESSION['staff_id'])) {
            $_SESSION['error'] = 'Không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $journal = $modelJournal->getById($journal_id);
        $schedule_id = $journal['schedule_id'];

        $modelJournal->delete($journal_id);

        $_SESSION['success'] = 'Xóa nhật ký thành công!';
        header('Location: ?act=view-tour-journal&schedule_id=' . $schedule_id);
        exit();
    }

    /**
     * Xóa ảnh nhật ký
     */
    public function DeleteJournalImage()
    {
        requireGuideRole();

        $image_id = $_GET['id'] ?? null;
        $journal_id = $_GET['journal_id'] ?? null;

        if (!$image_id || !$journal_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelJournal = new TourJournal();

        if (!$modelJournal->isOwner($journal_id, $_SESSION['staff_id'])) {
            $_SESSION['error'] = 'Không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelJournal->deleteImage($image_id);

        $_SESSION['success'] = 'Xóa ảnh thành công!';
        header('Location: ?act=edit-journal-entry&id=' . $journal_id);
        exit();
    }

    /**
     * Upload hình ảnh nhật ký
     */
    private function uploadJournalImages($files)
    {
        $uploaded = [];
        $upload_dir = '../uploads/journals/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === 0) {
                $file_name = time() . '_' . $i . '_' . basename($files['name'][$i]);
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($files['tmp_name'][$i], $target_path)) {
                    $uploaded[] = [
                        'file_path' => 'uploads/journals/' . $file_name,
                        'caption' => null,
                        'display_order' => $i
                    ];
                }
            }
        }

        return $uploaded;
    }

    /**
     * Xuất nhật ký tour (PDF)
     */
    public function ExportTourJournal()
    {
        requireGuideRole();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id || !isOwnSchedule($schedule_id)) {
            $_SESSION['error'] = 'Không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelJournal = new TourJournal();
        $journals = $modelJournal->prepareExport($schedule_id);
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);

        // Tạo PDF (tạm thời dùng HTML)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="nhat-ky-tour-' . $schedule_id . '.pdf"');

        // TODO: Implement PDF generation
        echo "Nhật ký tour - PDF sẽ được tạo tại đây";
        exit();
    }

    // ==================== USE CASE VIII.6: TOUR FEEDBACK ====================

    /**
     * Xem danh sách feedback của tour schedule
     */
    public function ViewTourFeedback()
    {
        requireGuideRole();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id || !isOwnSchedule($schedule_id)) {
            $_SESSION['error'] = 'Không có quyền xem feedback!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback = new TourFeedback();
        $feedbacks = $modelFeedback->getFeedbacksBySchedule($schedule_id);
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        $statistics = $modelFeedback->getTourStatistics($schedule['tour_id']);

        require_once './views/schedule/tour_feedback_list.php';
    }

    /**
     * Form tạo feedback mới
     */
    public function CreateFeedbackForm()
    {
        requireGuideRole();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id || !isOwnSchedule($schedule_id)) {
            $_SESSION['error'] = 'Không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        $feedback = null; // null = create mode

        require_once './views/schedule/tour_feedback_form.php';
    }

    /**
     * Lưu feedback mới
     */
    public function CreateFeedback()
    {
        requireGuideRole();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $schedule_id = $_POST['schedule_id'] ?? null;
        if (!$schedule_id || !isOwnSchedule($schedule_id)) {
            $_SESSION['error'] = 'Không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $staff_id = $_SESSION['staff_id'];

        // Validate
        if (empty($_POST['overall_rating'])) {
            $_SESSION['error'] = 'Vui lòng chọn đánh giá tổng quan!';
            header('Location: ?act=create-feedback-form&schedule_id=' . $schedule_id);
            exit();
        }

        $data = [
            'schedule_id' => $schedule_id,
            'staff_id' => $staff_id,
            'overall_rating' => intval($_POST['overall_rating']),
            'service_rating' => !empty($_POST['service_rating']) ? intval($_POST['service_rating']) : null,
            'guide_rating' => !empty($_POST['guide_rating']) ? intval($_POST['guide_rating']) : null,
            'food_rating' => !empty($_POST['food_rating']) ? intval($_POST['food_rating']) : null,
            'accommodation_rating' => !empty($_POST['accommodation_rating']) ? intval($_POST['accommodation_rating']) : null,
            'transportation_rating' => !empty($_POST['transportation_rating']) ? intval($_POST['transportation_rating']) : null,
            'feedback_text' => trim($_POST['feedback_text'] ?? ''),
            'positive_points' => trim($_POST['positive_points'] ?? ''),
            'improvement_points' => trim($_POST['improvement_points'] ?? ''),
            'recommend_to_others' => isset($_POST['recommend_to_others']) ? 1 : 0,
            'status' => $_POST['status'] ?? 'Published',
            'is_public' => isset($_POST['is_public']) ? 1 : 0
        ];

        // Upload hình ảnh
        $images = $this->uploadFeedbackImages();
        if (!empty($images)) {
            $data['images'] = $images;
        }

        $modelFeedback = new TourFeedback();
        $result = $modelFeedback->create($data);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ?act=view-tour-feedback&schedule_id=' . $schedule_id);
        exit();
    }

    /**
     * Form sửa feedback
     */
    public function EditFeedback()
    {
        requireGuideRole();

        $feedback_id = $_GET['feedback_id'] ?? null;
        if (!$feedback_id) {
            $_SESSION['error'] = 'Feedback không tồn tại!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback = new TourFeedback();
        $feedback = $modelFeedback->getById($feedback_id);

        if (!$feedback) {
            $_SESSION['error'] = 'Feedback không tồn tại!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        // Kiểm tra ownership
        $staff_id = $_SESSION['staff_id'];
        if (!$modelFeedback->isOwner($feedback_id, $staff_id)) {
            $_SESSION['error'] = 'Bạn không có quyền sửa feedback này!';
            header('Location: ?act=view-tour-feedback&schedule_id=' . $feedback['schedule_id']);
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($feedback['schedule_id']);
        $images = $modelFeedback->getImages($feedback_id);

        require_once './views/schedule/tour_feedback_form.php';
    }

    /**
     * Cập nhật feedback
     */
    public function UpdateFeedback()
    {
        requireGuideRole();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $feedback_id = $_POST['feedback_id'] ?? null;
        if (!$feedback_id) {
            $_SESSION['error'] = 'Feedback không tồn tại!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback = new TourFeedback();
        $staff_id = $_SESSION['staff_id'];

        // Kiểm tra ownership
        if (!$modelFeedback->isOwner($feedback_id, $staff_id)) {
            $_SESSION['error'] = 'Bạn không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $data = [
            'overall_rating' => intval($_POST['overall_rating']),
            'service_rating' => !empty($_POST['service_rating']) ? intval($_POST['service_rating']) : null,
            'guide_rating' => !empty($_POST['guide_rating']) ? intval($_POST['guide_rating']) : null,
            'food_rating' => !empty($_POST['food_rating']) ? intval($_POST['food_rating']) : null,
            'accommodation_rating' => !empty($_POST['accommodation_rating']) ? intval($_POST['accommodation_rating']) : null,
            'transportation_rating' => !empty($_POST['transportation_rating']) ? intval($_POST['transportation_rating']) : null,
            'feedback_text' => trim($_POST['feedback_text'] ?? ''),
            'positive_points' => trim($_POST['positive_points'] ?? ''),
            'improvement_points' => trim($_POST['improvement_points'] ?? ''),
            'recommend_to_others' => isset($_POST['recommend_to_others']) ? 1 : 0,
            'status' => $_POST['status'] ?? 'Published',
            'is_public' => isset($_POST['is_public']) ? 1 : 0
        ];

        // Upload thêm hình ảnh mới
        $new_images = $this->uploadFeedbackImages();
        if (!empty($new_images)) {
            foreach ($new_images as $image_path) {
                $modelFeedback->addImage($feedback_id, $image_path);
            }
        }

        $result = $modelFeedback->update($feedback_id, $data);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        $feedback = $modelFeedback->getById($feedback_id);
        header('Location: ?act=view-tour-feedback&schedule_id=' . $feedback['schedule_id']);
        exit();
    }

    /**
     * Xóa feedback
     */
    public function DeleteFeedback()
    {
        requireGuideRole();

        $feedback_id = $_GET['feedback_id'] ?? null;
        if (!$feedback_id) {
            $_SESSION['error'] = 'Feedback không tồn tại!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback = new TourFeedback();
        $staff_id = $_SESSION['staff_id'];

        // Kiểm tra ownership
        if (!$modelFeedback->isOwner($feedback_id, $staff_id)) {
            $_SESSION['error'] = 'Bạn không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $feedback = $modelFeedback->getById($feedback_id);
        $schedule_id = $feedback['schedule_id'];

        $result = $modelFeedback->delete($feedback_id);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ?act=view-tour-feedback&schedule_id=' . $schedule_id);
        exit();
    }

    /**
     * Xóa hình ảnh feedback
     */
    public function DeleteFeedbackImage()
    {
        requireGuideRole();

        $image_id = $_GET['image_id'] ?? null;
        $feedback_id = $_GET['feedback_id'] ?? null;

        if (!$image_id || !$feedback_id) {
            $_SESSION['error'] = 'Tham số không hợp lệ!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback = new TourFeedback();
        $staff_id = $_SESSION['staff_id'];

        // Kiểm tra ownership
        if (!$modelFeedback->isOwner($feedback_id, $staff_id)) {
            $_SESSION['error'] = 'Bạn không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback->deleteImage($image_id);
        $_SESSION['success'] = 'Đã xóa hình ảnh!';

        header('Location: ?act=edit-feedback&feedback_id=' . $feedback_id);
        exit();
    }

    /**
     * Admin phản hồi feedback
     */
    public function RespondToFeedback()
    {
        // Admin check handled by main authentication
        // requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=list-schedule');
            exit();
        }

        $feedback_id = $_POST['feedback_id'] ?? null;
        $response_text = trim($_POST['response_text'] ?? '');

        if (!$feedback_id || empty($response_text)) {
            $_SESSION['error'] = 'Vui lòng nhập nội dung phản hồi!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '?act=list-schedule'));
            exit();
        }

        $admin_id = $_SESSION['staff_id'];
        $modelFeedback = new TourFeedback();
        $result = $modelFeedback->addAdminResponse($feedback_id, $admin_id, $response_text);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '?act=list-schedule'));
        exit();
    }

    /**
     * Toggle visibility của feedback (public/private)
     */
    public function ToggleFeedbackVisibility()
    {
        requireGuideRole();

        $feedback_id = $_GET['feedback_id'] ?? null;
        if (!$feedback_id) {
            $_SESSION['error'] = 'Feedback không tồn tại!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback = new TourFeedback();
        $staff_id = $_SESSION['staff_id'];

        // Kiểm tra ownership
        if (!$modelFeedback->isOwner($feedback_id, $staff_id)) {
            $_SESSION['error'] = 'Bạn không có quyền!';
            header('Location: ?act=hdv-lich-cua-toi');
            exit();
        }

        $modelFeedback->toggleVisibility($feedback_id);
        $_SESSION['success'] = 'Đã cập nhật trạng thái hiển thị!';

        $feedback = $modelFeedback->getById($feedback_id);
        header('Location: ?act=view-tour-feedback&schedule_id=' . $feedback['schedule_id']);
        exit();
    }

    /**
     * Upload hình ảnh feedback
     */
    private function uploadFeedbackImages()
    {
        $uploaded = [];

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $upload_dir = 'uploads/feedbacks/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_count = count($_FILES['images']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_extension = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($file_extension, $allowed_extensions)) {
                        $new_filename = 'feedback_' . time() . '_' . uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;

                        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_path)) {
                            $uploaded[] = $upload_path;
                        }
                    }
                }
            }
        }

        return $uploaded;
    }

    // ==================== QUẢN LÝ DANH SÁCH THÀNH VIÊN ĐOÀN ====================

    public function ManageGroupMembers()
    {
        requireLogin();
        requireRole('ADMIN');

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        // Lấy danh sách thành viên
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM schedule_group_members WHERE schedule_id = ? ORDER BY member_id ASC");
        $stmt->execute([$schedule_id]);
        $groupMembers = $stmt->fetchAll();

        require_once './views/schedule/manage_group_members.php';
    }

    public function SaveGroupMembers()
    {
        requireLogin();
        requireRole('ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        try {
            $conn = connectDB();

            // Xóa tất cả thành viên cũ
            $stmt = $conn->prepare("DELETE FROM schedule_group_members WHERE schedule_id = ?");
            $stmt->execute([$schedule_id]);

            // Thêm lại danh sách mới
            if (!empty($_POST['members']) && is_array($_POST['members'])) {
                $stmt = $conn->prepare("
                    INSERT INTO schedule_group_members 
                    (schedule_id, full_name, phone, email, id_number, date_of_birth, note) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                foreach ($_POST['members'] as $member) {
                    if (!empty($member['full_name'])) {
                        $stmt->execute([
                            $schedule_id,
                            $member['full_name'],
                            $member['phone'] ?? null,
                            $member['email'] ?? null,
                            $member['id_number'] ?? null,
                            !empty($member['date_of_birth']) ? $member['date_of_birth'] : null,
                            $member['note'] ?? null
                        ]);
                    }
                }
            }

            $_SESSION['success'] = 'Cập nhật danh sách đoàn thành công!';
            header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=quan-ly-danh-sach-doan&schedule_id=' . $schedule_id);
            exit();
        }
    }
}

