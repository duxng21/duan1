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

        $tour = $modelTour->getById($schedule['tour_id']);
        $itineraries = $modelTourDetail->getItineraries($schedule['tour_id']);
        $gallery = $modelTourDetail->getGallery($schedule['tour_id']);
        $policies = $modelTourDetail->getPolicies($schedule['tour_id']);
        $assigned_staff = $staff;

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

        require_once './views/schedule/my_tasks.php';
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
}