<?php
class TourController
{
    public $modelTour;
    public $modelCategory;
    public $modelTourDetail;

    public $modelTourPricing;
    public $modelTourSupplier;
    public $modelTourVersion;

    public function __construct()
    {
        $this->modelTour = new Tour();
        $this->modelCategory = new Category();
        $this->modelTourDetail = new TourDetail();
        $this->modelTourPricing = new TourPricing();
        $this->modelTourSupplier = new TourSupplier();
        if (class_exists('TourVersion')) {
            $this->modelTourVersion = new TourVersion();
        }
    }

    public function home()
    {
        // Kiểm tra role và điều hướng đến trang home phù hợp
        if (function_exists('isGuide') && isGuide()) {
            // HDV: Hiển thị dashboard cho hướng dẫn viên
            $this->GuideHome();
        } else {
            // Admin/Manager: Hiển thị dashboard quản trị
            require_once './views/home.php';
        }
    }

    private function GuideHome()
    {
        requireGuideRole();

        $staff_id = $_SESSION['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Không tìm thấy thông tin hướng dẫn viên!';
            header('Location: ?act=logout');
            exit();
        }

        $modelSchedule = new TourSchedule();

        // Lấy thống kê
        $today = date('Y-m-d');
        $upcoming_count = $modelSchedule->countUpcomingToursForStaff($staff_id, $today);

        $inprogress = $modelSchedule->getInProgressToursForStaff($staff_id);
        $inprogress_count = count($inprogress);

        $completed_count = $modelSchedule->countCompletedToursForStaff($staff_id, date('Y-m'));

        // Lấy danh sách tour sắp tới (7 ngày tới)
        $upcoming_tours = $modelSchedule->getUpcomingToursForStaff($staff_id, 7);

        // Lấy danh sách tour đang diễn ra
        $in_progress_tours = $inprogress;

        // Lấy rating trung bình (giả định - sẽ implement sau)
        $avg_rating = 4.5;

        // Tạo array stats cho view
        $stats = [
            'upcoming_tours' => $upcoming_count,
            'in_progress_tours' => $inprogress_count,
            'completed_this_month' => $completed_count,
            'avg_rating' => $avg_rating
        ];

        require_once './views/home_guide.php';
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

    // ==================== MENU TOUR (LIST + DELETE + LOAD FOR EDIT) ==========================
    public function MenuTour()
    {
        // Xử lý xoá
        if (isset($_GET['delete_id'])) {
            $id = $_GET['delete_id'];
            $result = $this->modelCategory->delete($id);
            if (!$result['success']) {
                $_SESSION['error'] = $result['message'];
            } else {
                $_SESSION['success'] = $result['message'];
            }
            header("Location: ?act=menu-tour");
            exit;
        }

        // UC1: Lấy danh sách với số lượng tour
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category_type' => $_GET['category_type'] ?? '',
            'status' => isset($_GET['status']) ? $_GET['status'] : ''
        ];
        $categories = $this->modelCategory->getAllWithTourCount($filters);
        require_once './views/quanlytour/menu_tour.php';
    }

    // ==================== ADD MENU (ADD + UPDATE FORM) ==========================
    public function AddMenu()
    {
        $category = null;

        if (isset($_GET['id'])) {
            $category = $this->modelCategory->getById($_GET['id']);
            if (!$category) {
                $_SESSION['error'] = 'Danh mục không tồn tại!';
                header("Location: ?act=menu-tour");
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate
            if (empty(trim($_POST['category_name']))) {
                $_SESSION['error'] = 'Tên danh mục không được để trống!';
                header("Location: ?act=them-danh-muc" . (isset($_POST['id']) ? "&id=" . $_POST['id'] : ""));
                exit;
            }

            $data = [
                'category_name' => trim($_POST['category_name']),
                'category_type' => (!empty($_POST['category_type']) && strlen(trim($_POST['category_type'])) <= 50) ? trim($_POST['category_type']) : null,
                'description' => trim($_POST['description'] ?? ''),
                'status' => isset($_POST['status']) ? 1 : 0
            ];

            // Giữ lại hình ảnh cũ nếu đang update
            if (!empty($_POST['id']) && $category) {
                $data['image'] = $category['image'] ?? '';
            } else {
                $data['image'] = '';
            }

            // Xử lý upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/categories/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'category_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        // Xóa ảnh cũ nếu có
                        if (!empty($data['image']) && file_exists($data['image'])) {
                            unlink($data['image']);
                        }
                        $data['image'] = $upload_path;
                    }
                }
            }

            // Nếu có id => Cập nhật
            if (!empty($_POST['id'])) {
                $result = $this->modelCategory->update($_POST['id'], $data);
            }
            // Không có id => thêm mới
            else {
                $result = $this->modelCategory->create($data);
            }

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header("Location: ?act=menu-tour");
            exit;
        }

        require_once __DIR__ . '/../views/quanlytour/add_menu.php';
    }

    // ==================== CLONE CATEGORY ====================
    public function CloneCategory()
    {
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $_SESSION['error'] = 'Thiếu ID danh mục cần nhân bản!';
                header('Location: ?act=menu-tour');
                exit();
            }

            $category = $this->modelCategory->getById($id);
            if (!$category) {
                $_SESSION['error'] = 'Không tìm thấy danh mục!';
                header('Location: ?act=menu-tour');
                exit();
            }

            $newName = ($category['category_name'] ?? 'Danh mục') . ' (Copy)';
            $data = [
                'category_name' => $newName,
                'category_type' => $category['category_type'] ?? null,
                'description' => $category['description'] ?? '',
                'image' => $category['image'] ?? '',
                'status' => (int) ($category['status'] ?? 1)
            ];

            $result = $this->modelCategory->create($data);
            if ($result && is_array($result) && !empty($result['success'])) {
                $_SESSION['success'] = 'Nhân bản danh mục thành công!';
            } else {
                $_SESSION['error'] = is_array($result) ? ($result['message'] ?? 'Không thể nhân bản') : 'Không thể nhân bản';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=menu-tour');
        exit();
    }

    // ==================== SEED CATEGORIES ====================
    public function SeedCategories()
    {
        try {
            $defaults = [
                ['category_name' => 'Tour Miền Bắc', 'category_type' => 'Trong nước', 'description' => 'Các tour khu vực miền Bắc', 'status' => 1],
                ['category_name' => 'Tour Miền Trung', 'category_type' => 'Trong nước', 'description' => 'Các tour khu vực miền Trung', 'status' => 1],
                ['category_name' => 'Tour Miền Nam', 'category_type' => 'Trong nước', 'description' => 'Các tour khu vực miền Nam', 'status' => 1],
                ['category_name' => 'Tour Quốc Tế', 'category_type' => 'Quốc tế', 'description' => 'Các tour nước ngoài', 'status' => 1],
            ];

            $added = 0;
            $skipped = 0;
            foreach ($defaults as $cat) {
                if ($this->modelCategory->checkNameExists($cat['category_name'])) {
                    $skipped++;
                    continue;
                }
                $res = $this->modelCategory->create($cat);
                if (is_array($res) && !empty($res['success']))
                    $added++;
                else
                    $skipped++;
            }
            $_SESSION['success'] = "Seed danh mục: thêm {$added}, bỏ qua {$skipped}.";
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi seed: ' . $e->getMessage();
        }
        header('Location: ?act=menu-tour');
        exit();
    }

    // ==================== UC1: BULK IMPORT CATEGORIES ====================
    public function BulkImportCategories()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== 0) {
                    $_SESSION['error'] = 'Vui lòng chọn file CSV!';
                    header('Location: ?act=menu-tour');
                    exit();
                }

                $file = $_FILES['csv_file']['tmp_name'];
                $categories = [];

                if (($handle = fopen($file, 'r')) !== false) {
                    // Skip header row
                    $header = fgetcsv($handle);

                    while (($row = fgetcsv($handle)) !== false) {
                        if (count($row) >= 2) {
                            $categories[] = [
                                'category_name' => $row[0],
                                'category_type' => $row[1] ?? null,
                                'description' => $row[2] ?? '',
                                'status' => 1
                            ];
                        }
                    }
                    fclose($handle);
                }

                if (empty($categories)) {
                    $_SESSION['error'] = 'File CSV không có dữ liệu hợp lệ!';
                    header('Location: ?act=menu-tour');
                    exit();
                }

                $result = $this->modelCategory->bulkImport($categories);

                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                    if (!empty($result['errors'])) {
                        $_SESSION['warning'] = implode('<br>', $result['errors']);
                    }
                } else {
                    $_SESSION['error'] = $result['message'];
                }

            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }

        header('Location: ?act=menu-tour');
        exit();
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

        // UC2: Lấy pricing packages
        $pricingPackages = $this->modelTourPricing->getPricingByTour($tour_id);

        // UC2: Lấy suppliers
        $suppliers = $this->modelTourSupplier->getSuppliersByTour($tour_id);
        $allSuppliers = $this->modelTourSupplier->getAll(['status' => 1]);

        require_once './views/quanlytour/tour_detail.php';
    }

    // ==================== UC2: PRICING MANAGEMENT ====================

    public function AddPricingPackage()
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
                    'package_name' => $_POST['package_name'] ?? '',
                    'base_price_adult' => $_POST['base_price_adult'] ?? 0,
                    'base_price_child' => $_POST['base_price_child'] ?? 0,
                    'base_price_infant' => $_POST['base_price_infant'] ?? 0,
                    'single_room_surcharge' => $_POST['single_room_surcharge'] ?? 0,
                    'min_group_size' => $_POST['min_group_size'] ?? 1,
                    'max_group_size' => $_POST['max_group_size'] ?? null,
                    'discount_type' => $_POST['discount_type'] ?? null,
                    'discount_value' => $_POST['discount_value'] ?? 0,
                    'season_start' => $_POST['season_start'] ?? null,
                    'season_end' => $_POST['season_end'] ?? null,
                    'holiday_surcharge_percent' => $_POST['holiday_surcharge_percent'] ?? 0,
                    'description' => $_POST['description'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                $result = $this->modelTourPricing->addPackage($data);

                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
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

    public function UpdatePricingPackage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pricing_id = $_POST['pricing_id'] ?? null;
                $tour_id = $_POST['tour_id'] ?? null;

                if (!$pricing_id || !$tour_id) {
                    $_SESSION['error'] = 'Thiếu tham số!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                $data = [
                    'package_name' => $_POST['package_name'] ?? '',
                    'base_price_adult' => $_POST['base_price_adult'] ?? 0,
                    'base_price_child' => $_POST['base_price_child'] ?? 0,
                    'base_price_infant' => $_POST['base_price_infant'] ?? 0,
                    'single_room_surcharge' => $_POST['single_room_surcharge'] ?? 0,
                    'min_group_size' => $_POST['min_group_size'] ?? 1,
                    'max_group_size' => $_POST['max_group_size'] ?? null,
                    'discount_type' => $_POST['discount_type'] ?? null,
                    'discount_value' => $_POST['discount_value'] ?? 0,
                    'season_start' => $_POST['season_start'] ?? null,
                    'season_end' => $_POST['season_end'] ?? null,
                    'holiday_surcharge_percent' => $_POST['holiday_surcharge_percent'] ?? 0,
                    'description' => $_POST['description'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                $result = $this->modelTourPricing->updatePackage($pricing_id, $data);

                if (is_array($result) && $result['success']) {
                    $_SESSION['success'] = $result['message'];
                } elseif (is_array($result)) {
                    $_SESSION['error'] = $result['message'];
                } elseif ($result) {
                    $_SESSION['success'] = 'Cập nhật thành công!';
                } else {
                    $_SESSION['error'] = 'Cập nhật thất bại!';
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

    public function DeletePricingPackage()
    {
        try {
            $pricing_id = $_GET['pricing_id'] ?? null;
            $tour_id = $_GET['tour_id'] ?? null;

            if (!$pricing_id || !$tour_id) {
                $_SESSION['error'] = 'Thiếu tham số!';
            } else {
                $result = $this->modelTourPricing->deletePackage($pricing_id);
                if (is_array($result) && $result['success']) {
                    $_SESSION['success'] = $result['message'];
                } elseif (is_array($result)) {
                    $_SESSION['error'] = $result['message'];
                } elseif ($result) {
                    $_SESSION['success'] = 'Xóa thành công!';
                } else {
                    $_SESSION['error'] = 'Xóa thất bại!';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
        exit();
    }

    // ==================== UC2: SUPPLIER MANAGEMENT ====================

    public function LinkSupplierToTour()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tour_id = $_POST['tour_id'] ?? null;
                $supplier_id = $_POST['supplier_id'] ?? null;

                if (!$tour_id || !$supplier_id) {
                    $_SESSION['error'] = 'Thiếu tham số!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                $data = [
                    'service_date' => $_POST['service_date'] ?? null,
                    'service_day' => $_POST['service_day'] ?? null,
                    'service_type' => $_POST['service_type'] ?? '',
                    'service_description' => $_POST['service_description'] ?? '',
                    'unit_price' => $_POST['unit_price'] ?? 0,
                    'quantity' => $_POST['quantity'] ?? 1,
                    'currency' => $_POST['currency'] ?? 'VND',
                    'cancellation_deadline' => $_POST['cancellation_deadline'] ?? null,
                    'cancellation_fee' => $_POST['cancellation_fee'] ?? 0,
                    'emergency_contact' => $_POST['emergency_contact'] ?? '',
                    'emergency_phone' => $_POST['emergency_phone'] ?? '',
                    'notes' => $_POST['notes'] ?? ''
                ];

                $result = $this->modelTourSupplier->linkToTour($tour_id, $supplier_id, $data);

                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
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

    public function UpdateSupplierLink()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $link_id = $_POST['link_id'] ?? null;
                $tour_id = $_POST['tour_id'] ?? null;

                if (!$link_id || !$tour_id) {
                    $_SESSION['error'] = 'Thiếu tham số!';
                    header('Location: ?act=list-tour');
                    exit();
                }

                $data = [
                    'service_date' => $_POST['service_date'] ?? null,
                    'service_day' => $_POST['service_day'] ?? null,
                    'service_type' => $_POST['service_type'] ?? '',
                    'service_description' => $_POST['service_description'] ?? '',
                    'unit_price' => $_POST['unit_price'] ?? 0,
                    'quantity' => $_POST['quantity'] ?? 1,
                    'currency' => $_POST['currency'] ?? 'VND',
                    'cancellation_deadline' => $_POST['cancellation_deadline'] ?? null,
                    'cancellation_fee' => $_POST['cancellation_fee'] ?? 0,
                    'emergency_contact' => $_POST['emergency_contact'] ?? '',
                    'emergency_phone' => $_POST['emergency_phone'] ?? '',
                    'notes' => $_POST['notes'] ?? ''
                ];

                $result = $this->modelTourSupplier->updateLink($link_id, $data);

                if ($result) {
                    $_SESSION['success'] = 'Cập nhật liên kết thành công!';
                } else {
                    $_SESSION['error'] = 'Không thể cập nhật liên kết!';
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

    public function UnlinkSupplierFromTour()
    {
        try {
            $link_id = $_GET['link_id'] ?? null;
            $tour_id = $_GET['tour_id'] ?? null;

            if (!$link_id || !$tour_id) {
                $_SESSION['error'] = 'Thiếu tham số!';
            } else {
                $result = $this->modelTourSupplier->unlinkFromTour($link_id);
                if ($result) {
                    $_SESSION['success'] = 'Gỡ liên kết thành công!';
                } else {
                    $_SESSION['error'] = 'Không thể gỡ liên kết!';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=chi-tiet-tour&id=' . $tour_id);
        exit();
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

    // ==================== CLONE TOUR ====================

    /**
     * Hiển thị modal/form clone tour với tùy chọn các phần cần clone
     */
    public function CloneTourForm()
    {
        $tour_id = $_GET['id'] ?? null;
        if (!$tour_id) {
            $_SESSION['error'] = 'Thiếu ID tour cần clone!';
            header('Location: ?act=list-tour');
            exit();
        }

        $tour = $this->modelTour->getById($tour_id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ?act=list-tour');
            exit();
        }

        // Lấy thông tin chi tiết để hiển thị
        $itineraries = $this->modelTourDetail->getItineraries($tour_id);
        $images = $this->modelTourDetail->getImages($tour_id);
        $pricing = $this->modelTourPricing->getPackagesByTourId($tour_id);
        $suppliers = $this->modelTourSupplier->getSuppliersByTourId($tour_id);
        $policies = $this->modelTourDetail->getPolicies($tour_id);

        require_once './views/quanlytour/clone_tour_form.php';
    }

    /**
     * Thực hiện clone tour với các tùy chọn đã chọn
     */
    public function CloneTour()
    {
        try {
            $tour_id = $_POST['tour_id'] ?? null;
            if (!$tour_id) {
                $_SESSION['error'] = 'Thiếu ID tour gốc!';
                header('Location: ?act=list-tour');
                exit();
            }

            // Lấy thông tin tour gốc
            $original_tour = $this->modelTour->getById($tour_id);
            if (!$original_tour) {
                $_SESSION['error'] = 'Không tìm thấy tour gốc!';
                header('Location: ?act=list-tour');
                exit();
            }

            // Các tùy chọn clone
            $clone_options = [
                'itinerary' => isset($_POST['clone_itinerary']),
                'pricing' => isset($_POST['clone_pricing']),
                'images' => isset($_POST['clone_images']),
                'suppliers' => isset($_POST['clone_suppliers']),
                'policies' => isset($_POST['clone_policies']),
                'tags' => isset($_POST['clone_tags'])
            ];

            // Tên tour mới
            $new_name = $_POST['new_tour_name'] ?? ($original_tour['tour_name'] . ' (Bản sao)');
            $new_code = $_POST['new_tour_code'] ?? ($original_tour['code'] . '_COPY_' . time());

            $conn = connectDB();
            $conn->beginTransaction();

            // Cảnh báo trùng thời gian áp dụng (E1)
            $__start_date = $_POST['start_date'] ?? null;
            $__end_date = $_POST['end_date'] ?? null;
            if (!empty($__start_date) || !empty($__end_date)) {
                $start = $__start_date ?: '0001-01-01';
                $end = $__end_date ?: '9999-12-31';
                $chk = $conn->prepare("SELECT COUNT(*) FROM tour_versions WHERE tour_id = ? AND status <> 'archived' AND ((start_date IS NULL OR start_date <= ?) AND (end_date IS NULL OR end_date >= ?))");
                $chk->execute([$tour_id, $end, $start]);
                $overlap = (int) $chk->fetchColumn();
                if ($overlap > 0) {
                    $_SESSION['warning'] = 'Cảnh báo: Thời gian áp dụng có thể trùng với phiên bản khác.';
                }
            }



            // 1. Tạo tour mới (bản sao) - chỉ chèn các cột tối thiểu có trong schema hiện tại
            $sql = "INSERT INTO tours (category_id, tour_name, code) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $original_tour['category_id'],
                $new_name,
                $new_code
            ]);

            $new_tour_id = $conn->lastInsertId();

            // 2. Clone lịch trình (itineraries)
            if ($clone_options['itinerary']) {
                // Clone các cột phù hợp với model hiện tại (day_number, title, description, accommodation)
                $sql = "INSERT INTO tour_itineraries (tour_id, day_number, title, description, accommodation)
                        SELECT ?, day_number, title, description, accommodation
                        FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$new_tour_id, $tour_id]);
            }

            // 3. Clone giá (pricing packages)
            if ($clone_options['pricing']) {
                // Schema hiện tại sử dụng tối thiểu package_name, price_adult
                $sql = "INSERT INTO tour_prices (tour_id, package_name, price_adult)
                        SELECT ?, package_name, price_adult
                        FROM tour_prices WHERE tour_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$new_tour_id, $tour_id]);
            }

            // 4. Clone hình ảnh (chỉ copy link, không copy file vật lý)
            if ($clone_options['images']) {
                // Chỉ clone các cột an toàn: file_path, media_type (nếu có), is_featured
                // Nếu media_type không tồn tại trong bản ghi cũ, ép về 'image' khi thêm ảnh khác.
                $sql = "INSERT INTO tour_media (tour_id, file_path, media_type, is_featured)
                        SELECT ?, file_path, media_type, is_featured
                        FROM tour_media WHERE tour_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$new_tour_id, $tour_id]);
            }

            // 5. Clone nhà cung cấp (suppliers)
            // Bỏ qua clone suppliers để tránh sai khác schema; sẽ bổ sung sau nếu cần

            // 6. Clone chính sách (policies)
            if ($clone_options['policies']) {
                // Schema hiện tại: cancellation_policy, change_policy, payment_policy, note_policy
                $sql = "INSERT INTO tour_policies (tour_id, cancellation_policy, change_policy, payment_policy, note_policy)
                        SELECT ?, cancellation_policy, change_policy, payment_policy, note_policy
                        FROM tour_policies WHERE tour_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$new_tour_id, $tour_id]);
            }

            // 7. Clone tags
            if ($clone_options['tags']) {
                $sql = "INSERT INTO tour_tag_relations (tour_id, tag_id)
                        SELECT ?, tag_id
                        FROM tour_tag_relations WHERE tour_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$new_tour_id, $tour_id]);
            }

            // Ghi log activity
            logUserActivity('clone_tour', 'tours', $new_tour_id, "Cloned from tour ID: {$tour_id}");

            $conn->commit();

            $_SESSION['success'] = "Clone tour thành công! Tour mới: {$new_name} (ID: {$new_tour_id})";
            header('Location: ?act=edit-list&id=' . $new_tour_id);
            exit();

        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            $_SESSION['error'] = 'Lỗi khi clone tour: ' . $e->getMessage();
            header('Location: ?act=list-tour');
            exit();
        }
    }

    /**
     * Clone nhiều tour cùng lúc (bulk clone)
     */
    public function BulkCloneTours()
    {
        try {
            $tour_ids = $_POST['tour_ids'] ?? [];
            if (empty($tour_ids)) {
                $_SESSION['error'] = 'Chưa chọn tour nào để clone!';
                header('Location: ?act=list-tour');
                exit();
            }

            $success_count = 0;
            $error_count = 0;
            $messages = [];

            foreach ($tour_ids as $tour_id) {
                try {
                    $original_tour = $this->modelTour->getById($tour_id);
                    if (!$original_tour) {
                        $messages[] = "Tour ID {$tour_id}: Không tìm thấy";
                        $error_count++;
                        continue;
                    }

                    // Clone với tất cả tùy chọn
                    $new_name = $original_tour['tour_name'] . ' (Copy)';
                    $new_code = $original_tour['code'] . '_C' . time() . '_' . $tour_id;

                    $conn = connectDB();
                    $conn->beginTransaction();

                    // Insert tour mới
                    $sql = "INSERT INTO tours (category_id, tour_name, code, status, created_at)
                            VALUES (?, ?, ?, 'Draft', NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        $original_tour['category_id'],
                        $new_name,
                        $new_code
                    ]);

                    $new_tour_id = $conn->lastInsertId();

                    // Clone tất cả
                    $conn->exec("INSERT INTO tour_itineraries (tour_id, day_number, title, description) 
                                SELECT {$new_tour_id}, day_number, title, description FROM tour_itineraries WHERE tour_id = {$tour_id}");

                    $conn->exec("INSERT INTO tour_prices (tour_id, package_name, price_adult) 
                                SELECT {$new_tour_id}, package_name, price_adult FROM tour_prices WHERE tour_id = {$tour_id}");

                    $conn->commit();

                    $success_count++;
                    $messages[] = "Tour #{$tour_id} → #{$new_tour_id} OK";

                } catch (Exception $e) {
                    if (isset($conn)) {
                        $conn->rollBack();
                    }
                    $error_count++;
                    $messages[] = "Tour #{$tour_id}: Lỗi - " . $e->getMessage();
                }
            }

            $summary = "Clone xong {$success_count} tour, lỗi {$error_count}. Chi tiết: " . implode(' | ', $messages);
            $_SESSION['success'] = $summary;
            header('Location: ?act=list-tour');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi bulk clone: ' . $e->getMessage();
            header('Location: ?act=list-tour');
            exit();
        }
    }

    // ==================== UC3: QUẢN LÝ PHIÊN BẢN TOUR ====================
    public function ManageVersions()
    {
        $tour_id = $_GET['tour_id'] ?? ($_GET['id'] ?? null);
        if (!$tour_id) {
            $_SESSION['error'] = 'Thiếu tour_id!';
            header('Location: ?act=list-tour');
            exit();
        }

        $tour = $this->modelTour->getById($tour_id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ?act=list-tour');
            exit();
        }

        $versions = $this->modelTourVersion->getVersionsByTour($tour_id);
        require_once './views/quanlytour/versions_list.php';
    }

    public function CreateVersionForm()
    {
        $tour_id = $_GET['tour_id'] ?? ($_GET['id'] ?? null);
        if (!$tour_id) {
            $_SESSION['error'] = 'Thiếu tour_id!';
            header('Location: ?act=list-tour');
            exit();
        }

        $tour = $this->modelTour->getById($tour_id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ?act=list-tour');
            exit();
        }

        $versions = $this->modelTourVersion->getVersionsByTour($tour_id);
        require_once './views/quanlytour/version_form.php';
    }

    public function StoreVersion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: ?act=list-tour');
            exit();
        }

        $tour_id = $_POST['tour_id'] ?? null;
        if (!$tour_id) {
            $_SESSION['error'] = 'Thiếu tour_id!';
            header('Location: ?act=list-tour');
            exit();
        }

        try {
            $data = [
                'tour_id' => $tour_id,
                'version_name' => trim($_POST['version_name'] ?? ''),
                'version_type' => $_POST['version_type'] ?? 'season',
                'start_date' => $_POST['start_date'] ?? null,
                'end_date' => $_POST['end_date'] ?? null,
                'description' => $_POST['description'] ?? null,
                'status' => isset($_POST['status']) ? 'visible' : 'hidden',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'activation_mode' => $_POST['activation_mode'] ?? 'manual',
                'scheduled_at' => $_POST['scheduled_at'] ?? null,
                'source_type' => null,
                'source_id' => null,
            ];

            if ($data['version_name'] === '') {
                $_SESSION['error'] = 'Tên phiên bản không được để trống!';
                header('Location: ?act=tao-phien-ban&tour_id=' . $tour_id);
                exit();
            }

            $clone_mode = $_POST['clone_mode'] ?? 'none'; // none|tour|version
            $clone_source_id = null;
            if ($clone_mode === 'tour') {
                $data['source_type'] = 'tour';
                $data['source_id'] = $tour_id;
                $clone_source_id = $tour_id;
            } elseif ($clone_mode === 'version' && !empty($_POST['source_version_id'])) {
                $data['source_type'] = 'version';
                $data['source_id'] = (int) $_POST['source_version_id'];
                $clone_source_id = (int) $_POST['source_version_id'];
            }

            $conn = connectDB();
            $conn->beginTransaction();

            $version_id = $this->modelTourVersion->createVersion($data);

            if ($clone_mode !== 'none') {
                $options = [
                    'itinerary' => isset($_POST['clone_itinerary']),
                    'pricing' => isset($_POST['clone_pricing']),
                    'images' => isset($_POST['clone_images'])
                ];
                $this->modelTourVersion->cloneFromSource($version_id, $data['source_type'], $clone_source_id, $options);
            }

            $conn->commit();
            $_SESSION['success'] = 'Tạo phiên bản thành công!';
            header('Location: ?act=quan-ly-phien-ban&tour_id=' . $tour_id);
            exit();

        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            $_SESSION['error'] = 'Lỗi tạo phiên bản: ' . $e->getMessage();
            header('Location: ?act=tao-phien-ban&tour_id=' . $tour_id);
            exit();
        }
    }

    public function ActivateVersion()
    {
        $version_id = $_GET['version_id'] ?? null;
        $tour_id = $_GET['tour_id'] ?? null;
        if (!$version_id || !$tour_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=list-tour');
            exit();
        }
        $this->modelTourVersion->activateVersion($version_id);
        $_SESSION['success'] = 'Đã kích hoạt phiên bản';
        header('Location: ?act=quan-ly-phien-ban&tour_id=' . $tour_id);
        exit();
    }

    public function PauseVersion()
    {
        $version_id = $_GET['version_id'] ?? null;
        $tour_id = $_GET['tour_id'] ?? null;
        if (!$version_id || !$tour_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=list-tour');
            exit();
        }
        $this->modelTourVersion->pauseVersion($version_id);
        $_SESSION['success'] = 'Đã tạm dừng phiên bản';
        header('Location: ?act=quan-ly-phien-ban&tour_id=' . $tour_id);
        exit();
    }

    public function ArchiveVersion()
    {
        $version_id = $_GET['version_id'] ?? null;
        $tour_id = $_GET['tour_id'] ?? null;
        if (!$version_id || !$tour_id) {
            $_SESSION['error'] = 'Thiếu tham số!';
            header('Location: ?act=list-tour');
            exit();
        }
        $this->modelTourVersion->archiveVersion($version_id);
        $_SESSION['success'] = 'Đã lưu trữ phiên bản';
        header('Location: ?act=quan-ly-phien-ban&tour_id=' . $tour_id);
        exit();
    }
}