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
        $tour_id = $_GET['tour_id'] ?? null;

        if ($tour_id) {
            $schedules = $this->modelSchedule->getSchedulesByTour($tour_id);
            $tour = $this->modelTour->getById($tour_id);
        } else {
            $schedules = $this->modelSchedule->getAllSchedules();
            $tour = null;
        }

        $tours = $this->modelTour->getAll();
        require_once './views/schedule/list_schedule.php';
    }

    // ==================== THÊM LỊCH KHỞI HÀNH ====================

    public function AddSchedule()
    {
        $tour_id = $_GET['tour_id'] ?? null;
        $tours = $this->modelTour->getAll();
        require_once './views/schedule/add_schedule.php';
    }

    public function StoreSchedule()
    {
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
                    'return_date' => $_POST['return_date'],
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

        $staff = $this->modelSchedule->getScheduleStaff($schedule_id);
        $services = $this->modelSchedule->getScheduleServices($schedule_id);
        $allStaff = $this->modelSchedule->getAllStaff();
        $allServices = $this->modelSchedule->getAllServices();

        require_once './views/schedule/schedule_detail.php';
    }

    // ==================== SỬA LỊCH KHỞI HÀNH ====================

    public function EditSchedule()
    {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $schedule_id = $_GET['id'] ?? null;
                if (!$schedule_id) {
                    $_SESSION['error'] = 'Thiếu tham số schedule_id!';
                    header('Location: ?act=danh-sach-lich-khoi-hanh');
                    exit();
                }

                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'departure_date' => $_POST['departure_date'],
                    'return_date' => $_POST['return_date'],
                    'meeting_point' => $_POST['meeting_point'] ?? '',
                    'meeting_time' => $_POST['meeting_time'] ?? '',
                    'max_participants' => $_POST['max_participants'] ?? 0,
                    'price_adult' => $_POST['price_adult'] ?? 0,
                    'price_child' => $_POST['price_child'] ?? 0,
                    'status' => $_POST['status'] ?? 'Open',
                    'notes' => $_POST['notes'] ?? ''
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
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        $schedules = $this->modelSchedule->getCalendarView($month, $year);
        require_once './views/schedule/calendar_view.php';
    }

    // ==================== XUẤT BÁO CÁO ====================

    public function ExportSchedule()
    {
        $schedule_id = $_GET['id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu tham số schedule_id!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $report = $this->modelSchedule->getScheduleReport($schedule_id);
        require_once './views/schedule/export_schedule.php';
    }
}
