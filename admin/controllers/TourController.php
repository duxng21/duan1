<?php
class TourController
{
    public $modelTour;
    public function __construct()
    {
        $this->modelTour = new Tour();
    }
    public function home()
    {
        require_once './views/home.php';
    }
    public function ListTour()
    {
        $tours = $this->modelTour->getAll();
        require_once './views/quanlytour/list_tour.php';
    }
    public function MenuTour()
    {
        require_once './views/quanlytour/menu_tour.php';
    }
    public function AddMenu()
    {
        require_once './views/quanlytour/add_menu.php';
    }
    public function AddBooking()
    {
        require_once './views/quanlytour/add_booking.php';
    }
    public function ListBooking()
    {
        require_once './views/quanlytour/list_booking.php';
    }
    public function AddList()
    {
        require_once './views/quanlytour/add_list.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate dữ liệu
                if (empty($_POST['tour_name'])) {
                    $_SESSION['error'] = 'Tên tour không được để trống!';
                    header('Location: ?act=add-list');
                    exit();
                }

                if (empty($_POST['category_id'])) {
                    $_SESSION['error'] = 'Vui lòng chọn danh mục tour!';
                    header('Location: ?act=add-list');
                    exit();
                }

                // Xử lý upload ảnh
                $tour_image = '';
                if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] === 0) {
                    $tour_image = uploadFile($_FILES['tour_image'], 'uploads/');
                    if (!$tour_image) {
                        $_SESSION['error'] = 'Upload ảnh thất bại!';
                        header('Location: ?act=add-list');
                        exit();
                    }
                }

                $data = [
                    'category_id' => $_POST['category_id'] ?? null,
                    'tour_name' => $_POST['tour_name'] ?? '',
                    'code' => $_POST['code'] ?? '',
                    'tour_image' => $tour_image,
                    'tour_price' => $_POST['tour_price'] ?? 0,
                    'description_short' => $_POST['description_short'] ?? '',
                    'description_full' => $_POST['description_full'] ?? '',
                    'duration_days' => $_POST['duration_days'] ?? null,
                    'start_location' => $_POST['start_location'] ?? '',
                    'status' => $_POST['status'] ?? 'Draft'
                ];

                $this->modelTour->create($data);
                $_SESSION['success'] = 'Thêm tour thành công!';
                header('Location: ?act=list-tour');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=add-list');
                exit();
            }
        }
    }

    public function EditList()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=list-tour');
            exit();
        }
        $tour = $this->modelTour->getById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ?act=list-tour');
            exit();
        }
        $categories = $this->modelCategory->getAll();
        require_once './views/quanlytour/edit_list.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    $_SESSION['error'] = 'Thiếu tham số id!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                // Validate dữ liệu
                if (empty($_POST['tour_name'])) {
                    $_SESSION['error'] = 'Tên tour không được để trống!';
                    header('Location: ?act=edit-list&id=' . $id);
                    exit();
                }

                // Xử lý upload ảnh mới nếu có
                $tour_image = '';
                if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] === 0) {
                    $tour_image = uploadFile($_FILES['tour_image'], 'uploads/');
                    if (!$tour_image) {
                        $_SESSION['error'] = 'Upload ảnh thất bại!';
                        header('Location: ?act=edit-list&id=' . $id);
                        exit();
                    }
                }

                $data = [
                    'category_id' => $_POST['category_id'] ?? null,
                    'tour_name' => $_POST['tour_name'] ?? '',
                    'code' => $_POST['code'] ?? '',
                    'tour_image' => $tour_image,
                    'tour_price' => $_POST['tour_price'] ?? 0,
                    'description_short' => $_POST['description_short'] ?? '',
                    'description_full' => $_POST['description_full'] ?? '',
                    'duration_days' => $_POST['duration_days'] ?? null,
                    'start_location' => $_POST['start_location'] ?? '',
                    'status' => $_POST['status'] ?? 'Draft'
                ];

                $this->modelTour->update($id, $data);
                $_SESSION['success'] = 'Cập nhật tour thành công!';
                header('Location: ?act=list-tour');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=edit-list&id=' . $id);
                exit();
            }
        }
    }

    // Delete
    public function delete()
    {
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $_SESSION['error'] = 'Thiếu tham số id!';
            } else {
                $result = $this->modelTour->delete($id);
                if ($result) {
                    $_SESSION['success'] = 'Xóa tour thành công!';
                } else {
                    $_SESSION['error'] = 'Xóa tour thất bại!';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=list-tour');
        exit();
    }
}
