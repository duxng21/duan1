<?php
class TourController
{

    public $modelTour;

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
        $tours = $this->modelTour->getAll();
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
        require_once './views/booking/add_booking.php';
    }

    public function ListBooking()
    {
        require_once './views/booking/list_booking.php';
    }
    public function AddList()
    {
        require_once './views/quanlytour/add_list.php';
    }
    public function EditList()
    {
        require_once './views/quanlytour/edit_list.php';
    }
}
