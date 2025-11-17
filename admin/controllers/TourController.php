<?php
class TourController
{
    public $modelTour;
    public $modelCategory;

    public function __construct()
    {
        $this->modelTour = new Tour();
        $this->modelCategory = new Category();
    }

    public function home()
    {
        require_once './views/home.php';
    }

    public function ListTour()
    {
        $category_id = $_GET['category_id'] ?? null;

        if ($category_id) {
            $tours = $this->modelTour->getByCategory($category_id);
        } else {
            $tours = $this->modelTour->getAll();
        }

        $categories = $this->modelCategory->getAll();
        require_once './views/quanlytour/list_tour.php';
    }

    // ======================= MENU TOUR (LIST + DELETE + LOAD FOR EDIT) ==========================
    public function MenuTour()
    {
        // Xử lý xoá
        if (isset($_GET['delete_id'])) {
            $id = $_GET['delete_id'];
            $this->modelTour->deleteCategory($id);
            header("Location: ?act=menu-tour");
            exit;
        }

        $categories = $this->modelTour->getCategories();
        require_once './views/quanlytour/menu_tour.php';
    }

    // ======================= ADD MENU (ADD + UPDATE FORM) ==========================
    public function AddMenu()
    {
        $category = null;

        if (isset($_GET['id'])) {
            $category = $this->modelTour->getCategoryById($_GET['id']);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['category_name'] ?? null;

            if (!$name) {
                die("Tên danh mục không được để trống");
            }

            // Nếu có id => Cập nhật
            if (!empty($_POST['id'])) {
                $this->modelTour->updateCategory($_POST['id'], $name);
            }
            // Không có id => thêm mới
            else {
                $this->modelTour->addCategory($name);
            }

            header("Location: ?act=menu-tour");
            exit;
        }

        require_once './views/quanlytour/add_menu.php';
    }

    public function AddBooking()
    {
        require_once './views/booking/add_booking.php';
    }

    public function ListBooking()
    {
        require_once './views/booking/list_booking.php';
    }

    public function AddList()
    {
        $categories = $this->modelCategory->getAll();
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
                    'tour_price' => $_POST['tour_price'] ?? 0
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
                    'tour_price' => $_POST['tour_price'] ?? 0
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