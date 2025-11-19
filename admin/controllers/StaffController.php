<?php
class StaffController
{
    public $modelStaff;

    public function __construct()
    {
        $this->modelStaff = new Staff();
    }

    // ==================== DANH SÁCH NHÂN SỰ ====================

    public function ListStaff()
    {
        $type = $_GET['type'] ?? null;

        if ($type) {
            $staffList = $this->modelStaff->getByType($type);
        } else {
            $staffList = $this->modelStaff->getAll();
        }

        $statistics = $this->modelStaff->getStatistics();

        require_once './views/staff/list_staff.php';
    }

    // ==================== THÊM NHÂN SỰ ====================

    public function AddStaff()
    {
        require_once './views/staff/add_staff.php';
    }

    public function StoreStaff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate
                if (empty($_POST['full_name'])) {
                    $_SESSION['error'] = 'Họ tên không được để trống!';
                    header('Location: ?act=them-nhan-su');
                    exit();
                }

                if (empty($_POST['staff_type'])) {
                    $_SESSION['error'] = 'Vui lòng chọn loại nhân sự!';
                    header('Location: ?act=them-nhan-su');
                    exit();
                }

                $data = [
                    'full_name' => $_POST['full_name'],
                    'staff_type' => $_POST['staff_type'],
                    'phone' => $_POST['phone'] ?? null,
                    'email' => $_POST['email'] ?? null,
                    'id_card' => $_POST['id_card'] ?? null,
                    'license_number' => $_POST['license_number'] ?? null,
                    'experience_years' => $_POST['experience_years'] ?? 0,
                    'languages' => $_POST['languages'] ?? null,
                    'status' => isset($_POST['status']) ? 1 : 0,
                    'notes' => $_POST['notes'] ?? null
                ];

                $result = $this->modelStaff->create($data);

                if ($result) {
                    $_SESSION['success'] = 'Thêm nhân sự thành công!';
                } else {
                    $_SESSION['error'] = 'Thêm nhân sự thất bại!';
                }

                header('Location: ?act=danh-sach-nhan-su');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=them-nhan-su');
                exit();
            }
        }
    }

    // ==================== SỬA NHÂN SỰ ====================

    public function EditStaff()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($id);
        if (!$staff) {
            $_SESSION['error'] = 'Không tìm thấy nhân sự!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        require_once './views/staff/edit_staff.php';
    }

    public function UpdateStaff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    $_SESSION['error'] = 'Thiếu tham số id!';
                    header('Location: ?act=danh-sach-nhan-su');
                    exit();
                }

                $data = [
                    'full_name' => $_POST['full_name'],
                    'staff_type' => $_POST['staff_type'],
                    'phone' => $_POST['phone'] ?? null,
                    'email' => $_POST['email'] ?? null,
                    'id_card' => $_POST['id_card'] ?? null,
                    'license_number' => $_POST['license_number'] ?? null,
                    'experience_years' => $_POST['experience_years'] ?? 0,
                    'languages' => $_POST['languages'] ?? null,
                    'status' => isset($_POST['status']) ? 1 : 0,
                    'notes' => $_POST['notes'] ?? null
                ];

                $result = $this->modelStaff->update($id, $data);

                if ($result) {
                    $_SESSION['success'] = 'Cập nhật nhân sự thành công!';
                } else {
                    $_SESSION['error'] = 'Cập nhật nhân sự thất bại!';
                }

                header('Location: ?act=danh-sach-nhan-su');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=sua-nhan-su&id=' . $id);
                exit();
            }
        }
    }

    // ==================== XÓA NHÂN SỰ ====================

    public function DeleteStaff()
    {
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $_SESSION['error'] = 'Thiếu tham số id!';
            } else {
                $result = $this->modelStaff->delete($id);
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=danh-sach-nhan-su');
        exit();
    }

    // ==================== CHI TIẾT NHÂN SỰ ====================

    public function StaffDetail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($id);
        if (!$staff) {
            $_SESSION['error'] = 'Không tìm thấy nhân sự!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        // Lấy lịch làm việc
        $from_date = $_GET['from_date'] ?? date('Y-m-01');
        $to_date = $_GET['to_date'] ?? date('Y-m-t');
        $schedules = $this->modelStaff->getSchedulesByStaff($id, $from_date, $to_date);

        require_once './views/staff/staff_detail.php';
    }
}
