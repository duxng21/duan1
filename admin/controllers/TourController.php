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
        header("Location: ?act=menu-tour");
        exit;
    }

    $categories = $this->modelTour->getCategories();
    require_once './views/quanlytour/menu_tour.php';
}


    // ======================= ADD MENU (ADD + UPDATE FORM) ==========================
    public function AddMenu()
    {

        $category = null; // default

        if (isset($_GET['id'])) {
            $category = $this->modelTour->getCategoryById($_GET['id']);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // Nếu có id => Cập nhật
                if (!empty($_POST['id'])) {
                    $this->modelTour->updateCategory($_POST['id'], $name);
                }
                // Không có id => thêm mới
                else {
                    $this->modelTour->addCategory($name);
                }

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $this->modelTour->updateCategory($_POST['id'], $name);
            } else {
                $this->modelTour->addCategory($name);
            }

            header("Location: ?act=menu-tour");
            exit;
        }
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