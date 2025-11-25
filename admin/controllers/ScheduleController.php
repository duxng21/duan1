<?php
class ScheduleController
{
    public $modelSchedule;
    public $modelTour;

    public function __construct()
    {
        $this->modelSchedule = new TourSchedule();
        $this->modelTour = new Tour();
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

                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'departure_date' => $_POST['departure_date'],
                    'return_date' => !empty($_POST['return_date']) ? $_POST['return_date'] : null,
                    'meeting_point' => $_POST['meeting_point'] ?? '',
                    'meeting_time' => $_POST['meeting_time'] ?? '',
                    'max_participants' => $_POST['max_participants'] ?? 0,
                    'price_adult' => $_POST['price_adult'] ?? 0,
                    'price_child' => $_POST['price_child'] ?? 0,
                    'status' => $_POST['status'] ?? 'Open',
                    'notes' => $_POST['notes'] ?? ''
                ];

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
        $allServices = $this->modelSchedule->getAllServices();
        $journeyLogs = $this->modelSchedule->getJourneyLogs($schedule_id);

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
                    'max_participants' => !empty($_POST['max_participants']) ? (int) $_POST['max_participants'] : 0,
                    'price_adult' => !empty($_POST['price_adult']) ? (float) $_POST['price_adult'] : 0,
                    'price_child' => !empty($_POST['price_child']) ? (float) $_POST['price_child'] : 0,
                    'status' => !empty($_POST['status']) ? $_POST['status'] : 'Open',
                    'notes' => !empty($_POST['notes']) ? $_POST['notes'] : null
                ];

                $this->modelSchedule->updateSchedule($schedule_id, $data);
                $_SESSION['success'] = 'Cập nhật lịch khởi hành thành công!';
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
                $role = $_POST['role'] ?? '';

                if (!$schedule_id || !$staff_id) {
                    $_SESSION['error'] = 'Thiếu thông tin!';
                    header('Location: ?act=chi-tiet-lich-khoi-hanh&id=' . $schedule_id);
                    exit();
                }

                // Lấy thông tin lịch để check availability
                $schedule = $this->modelSchedule->getScheduleById($schedule_id);

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
}
