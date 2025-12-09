<?php
class TourController
{
    public $modelTour;
    public $modelCategory;
    public $modelTourDetail;

    public function __construct()
    {
        $this->modelTour = new Tour();
        $this->modelCategory = new Category();
        $this->modelTourDetail = new TourDetail();
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

    // ==================== CHI TIẾT TOUR - LỊCH TRÌNH & QUẢN LÝ ====================

    public function TourDetail()
    {
        $tour_id = $_GET['id'] ?? null;
        if (!$tour_id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=list-tour');
            exit();
        }

        $tour = $this->modelTour->getById($tour_id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ?act=list-tour');
            exit();
        }

        // Lấy lịch trình
        $itineraries = $this->modelTourDetail->getItineraries($tour_id);

        // Lấy thư viện ảnh
        $gallery = $this->modelTourDetail->getGallery($tour_id);

        // Lấy chính sách
        $policies = $this->modelTourDetail->getPolicies($tour_id);

        // Lấy tất cả tags và tags của tour này
        $allTags = $this->modelTourDetail->getAllTags();
        $tourTags = $this->modelTourDetail->getTourTags($tour_id);

        require_once './views/quanlytour/tour_detail.php';
    }

    // ==================== LỊCH TRÌNH ====================

    public function ThemLichTrinh()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tour_id = $_POST['tour_id'] ?? null;
                if (!$tour_id) {
                    $_SESSION['error'] = 'Thiếu tour_id!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                $data = [
                    'tour_id' => $tour_id,
                    'day_number' => $_POST['day_number'] ?? 1,
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'activities' => $_POST['activities'] ?? ''
                ];

                // Nếu có ID thì update, không thì create
                if (!empty($_POST['itinerary_id'])) {
                    $this->modelTourDetail->updateItinerary($_POST['itinerary_id'], $data);
                    $_SESSION['success'] = 'Cập nhật lịch trình thành công!';
                } else {
                    $this->modelTourDetail->createItinerary($data);
                    $_SESSION['success'] = 'Thêm lịch trình thành công!';
                }

                header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=chi-tiet-tour&id=' . ($_POST['tour_id'] ?? ''));
                exit();
            }
        }
    }

    public function XoaLichTrinh()
    {
        try {
            $id = $_GET['id'] ?? null;
            $tour_id = $_GET['tour_id'] ?? null;

            if (!$id || !$tour_id) {
                $_SESSION['error'] = 'Thiếu tham số!';
            } else {
                $result = $this->modelTourDetail->deleteItinerary($id);
                if ($result) {
                    $_SESSION['success'] = 'Xóa lịch trình thành công!';
                } else {
                    $_SESSION['error'] = 'Xóa lịch trình thất bại!';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
        exit();
    }

    // ==================== THƯ VIỆN ẢNH ====================

    public function ThemAnhTour()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tour_id = $_POST['tour_id'] ?? null;
                if (!$tour_id) {
                    $_SESSION['error'] = 'Thiếu tour_id!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                // Xử lý upload ảnh
                if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === 0) {
                    $image_url = uploadFile($_FILES['image_url'], 'uploads/tours/');
                    if (!$image_url) {
                        $_SESSION['error'] = 'Upload ảnh thất bại!';
                        header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
                        exit();
                    }

                    $data = [
                        'tour_id' => $tour_id,
                        'image_url' => $image_url,
                        'caption' => $_POST['caption'] ?? '',
                        'is_featured' => isset($_POST['is_featured']) ? 1 : 0
                    ];

                    $this->modelTourDetail->addImage($data);
                    $_SESSION['success'] = 'Thêm ảnh thành công!';
                } else {
                    $_SESSION['error'] = 'Vui lòng chọn ảnh!';
                }

                header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=chi-tiet-tour&id=' . ($_POST['tour_id'] ?? ''));
                exit();
            }
        }
    }

    public function XoaAnhTour()
    {
        try {
            $id = $_GET['id'] ?? null;
            $tour_id = $_GET['tour_id'] ?? null;

            if (!$id || !$tour_id) {
                $_SESSION['error'] = 'Thiếu tham số!';
            } else {
                $result = $this->modelTourDetail->deleteImage($id);
                if ($result) {
                    $_SESSION['success'] = 'Xóa ảnh thành công!';
                } else {
                    $_SESSION['error'] = 'Xóa ảnh thất bại!';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
        exit();
    }

    // ==================== CHÍNH SÁCH ====================

    public function LuuChinhSach()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tour_id = $_POST['tour_id'] ?? null;
                if (!$tour_id) {
                    $_SESSION['error'] = 'Thiếu tour_id!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                $data = [
                    'cancellation_policy' => $_POST['cancellation_policy'] ?? '',
                    'change_policy' => $_POST['change_policy'] ?? '',
                    'payment_policy' => $_POST['payment_policy'] ?? '',
                    'notes' => $_POST['notes'] ?? ''
                ];

                $this->modelTourDetail->savePolicies($tour_id, $data);
                $_SESSION['success'] = 'Lưu chính sách thành công!';

                header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=chi-tiet-tour&id=' . ($_POST['tour_id'] ?? ''));
                exit();
            }
        }
    }

    // ==================== TAGS ====================

    public function LuuTags()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tour_id = $_POST['tour_id'] ?? null;
                if (!$tour_id) {
                    $_SESSION['error'] = 'Thiếu tour_id!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                $tag_ids = $_POST['tag_ids'] ?? [];

                $this->modelTourDetail->saveTourTags($tour_id, $tag_ids);
                $_SESSION['success'] = 'Lưu tags thành công!';

                header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=chi-tiet-tour&id=' . ($_POST['tour_id'] ?? ''));
                exit();
            }
        }
    }

    // ==================== SEED SAMPLE ITINERARIES & POLICIES ====================
    public function SeedTourData()
    {
        $tour_id = $_GET['id'] ?? null;
        if (!$tour_id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=list-tour');
            exit();
        }

        // Dùng lớp seeder chuyên biệt
        if (!isAdmin()) {
            $_SESSION['error'] = 'Chỉ ADMIN mới được seed dữ liệu.';
            header('Location: ?act=list-tour');
            exit();
        }

        $seeder = new TourDetailSeeder();
        $result = $seeder->seedItinerariesAndPolicies($tour_id);

        $msg = 'Seed tour #' . (int) $tour_id . ': ' . implode(' | ', $result['messages']);
        $_SESSION['success'] = $msg;
        header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
        exit();
    }

    // ==================== BULK SEED ALL TOURS ====================
    public function SeedAllToursData()
    {
        if (!isAdmin()) {
            $_SESSION['error'] = 'Chỉ ADMIN mới được seed dữ liệu.';
            header('Location: ?act=list-tour');
            exit();
        }

        $tours = $this->modelTour->getAll();
        $seeder = new TourDetailSeeder();
        $seeded = 0;
        $skipped = 0;
        $errors = 0;
        $messages = [];
        foreach ($tours as $t) {
            $tour_id = $t['tour_id'];
            $res = $seeder->seedItinerariesAndPolicies($tour_id);
            if ($res['itineraries_added'] > 0 || $res['policies_created']) {
                $seeded++;
            } else {
                $skipped++;
            }
            $messages[] = 'Tour #' . $tour_id . ': ' . implode(' / ', $res['messages']);
        }
        $_SESSION['success'] = 'Bulk seed hoàn tất. Seed mới: ' . $seeded . ', bỏ qua: ' . $skipped . '. Chi tiết: ' . implode(' || ', $messages);
        header('Location: ?act=list-tour');
        exit();
    }
}