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
            header("Location: index.php?controller=tour&action=MenuTour");
            exit;
        }

        // Lấy danh sách danh mục từ model
        $categories = $this->modelTour->getCategories();

        require_once './views/quanlytour/menu_tour.php';
    }

    // ======================= ADD MENU (ADD + UPDATE FORM) ==========================
    public function AddMenu()
    {

        // Nếu có id => đang sửa => lấy dữ liệu load form
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $category = $this->modelTour->getCategoryById($id);
        }

        // Nếu submit form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

            header("Location: index.php?controller=tour&action=MenuTour");
            exit;
        }

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
        $categories = $this->modelCategory->getAll();
        require_once './views/quanlytour/add_list.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý upload ảnh
            $tour_image = '';
            if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] === 0) {
                $tour_image = uploadFile($_FILES['tour_image'], 'uploads/');
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
            header('Location: ?act=list-tour');
            exit();
        }
    }

    public function EditList()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "Thiếu tham số id";
            return;
        }
        $tour = $this->modelTour->getById($id);
        if (!$tour) {
            echo "Không tìm thấy tour";
            return;
        }
        $categories = $this->modelCategory->getAll();
        require_once './views/quanlytour/edit_list.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo "Thiếu tham số id";
                return;
            }

            // Xử lý upload ảnh mới nếu có
            $tour_image = '';
            if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] === 0) {
                $tour_image = uploadFile($_FILES['tour_image'], 'uploads/');
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
            header('Location: ?act=list-tour');
            exit();
        }
    }

    // Delete
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->modelTour->delete($id);
        }
        header('Location: ?act=list-tour');
        exit();
    }
}