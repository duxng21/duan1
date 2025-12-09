<?php
class ServiceController
{
    public $modelService;
    public $modelPartner;

    public function __construct()
    {
        $this->modelService = new Service();
        $this->modelPartner = new Partner();
    }

    // ==================== DANH SÁCH DỊCH VỤ ====================

    public function ListService()
    {
        requireRole('ADMIN');
        
        $filters = [
            'service_type' => $_GET['service_type'] ?? null,
            'partner_id' => $_GET['partner_id'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        if (!empty($filters['service_type']) || !empty($filters['partner_id']) || !empty($filters['search']) || isset($filters['status'])) {
            $services = $this->modelService->search($filters);
        } else {
            $services = $this->modelService->getAll();
        }

        $partners = $this->modelPartner->getActive();
        require_once './views/service/list_service.php';
    }

    // ==================== THÊM DỊCH VỤ ====================

    public function AddService()
    {
        requireRole('ADMIN');
        
        $partners = $this->modelPartner->getActive();
        require_once './views/service/add_service.php';
    }

    public function StoreService()
    {
        requireRole('ADMIN');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate dữ liệu
                if (empty($_POST['service_name'])) {
                    $_SESSION['error'] = 'Tên dịch vụ không được để trống!';
                    header('Location: ?act=them-dich-vu');
                    exit();
                }

                if (empty($_POST['partner_id'])) {
                    $_SESSION['error'] = 'Vui lòng chọn đối tác cung cấp!';
                    header('Location: ?act=them-dich-vu');
                    exit();
                }

                $data = [
                    'partner_id' => (int)$_POST['partner_id'],
                    'service_name' => trim($_POST['service_name']),
                    'service_type' => $_POST['service_type'] ?? 'Other',
                    'description' => trim($_POST['description'] ?? ''),
                    'unit_price' => !empty($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.00,
                    'unit' => trim($_POST['unit'] ?? 'pax'),
                    'capacity' => !empty($_POST['capacity']) ? (int)$_POST['capacity'] : null,
                    'location' => trim($_POST['location'] ?? ''),
                    'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                    'provider_name' => trim($_POST['provider_name'] ?? ''),
                    'rating' => !empty($_POST['rating']) ? (float)$_POST['rating'] : 0.00,
                    'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                $service_id = $this->modelService->create($data);
                $_SESSION['success'] = 'Thêm dịch vụ thành công!';
                header('Location: ?act=danh-sach-dich-vu');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=them-dich-vu');
                exit();
            }
        }
    }

    // ==================== SỬA DỊCH VỤ ====================

    public function EditService()
    {
        requireRole('ADMIN');
        
        $service_id = $_GET['id'] ?? null;
        if (!$service_id) {
            $_SESSION['error'] = 'Thiếu tham số service_id!';
            header('Location: ?act=danh-sach-dich-vu');
            exit();
        }

        $service = $this->modelService->getById($service_id);
        if (!$service) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ!';
            header('Location: ?act=danh-sach-dich-vu');
            exit();
        }

        $partners = $this->modelPartner->getActive();
        require_once './views/service/edit_service.php';
    }

    public function UpdateService()
    {
        requireRole('ADMIN');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $service_id = $_GET['id'] ?? null;
                if (!$service_id) {
                    $_SESSION['error'] = 'Thiếu tham số service_id!';
                    header('Location: ?act=danh-sach-dich-vu');
                    exit();
                }

                // Validate dữ liệu
                if (empty($_POST['service_name'])) {
                    $_SESSION['error'] = 'Tên dịch vụ không được để trống!';
                    header('Location: ?act=sua-dich-vu&id=' . $service_id);
                    exit();
                }

                if (empty($_POST['partner_id'])) {
                    $_SESSION['error'] = 'Vui lòng chọn đối tác cung cấp!';
                    header('Location: ?act=sua-dich-vu&id=' . $service_id);
                    exit();
                }

                $data = [
                    'partner_id' => (int)$_POST['partner_id'],
                    'service_name' => trim($_POST['service_name']),
                    'service_type' => $_POST['service_type'] ?? 'Other',
                    'description' => trim($_POST['description'] ?? ''),
                    'unit_price' => !empty($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.00,
                    'unit' => trim($_POST['unit'] ?? 'pax'),
                    'capacity' => !empty($_POST['capacity']) ? (int)$_POST['capacity'] : null,
                    'location' => trim($_POST['location'] ?? ''),
                    'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                    'provider_name' => trim($_POST['provider_name'] ?? ''),
                    'rating' => !empty($_POST['rating']) ? (float)$_POST['rating'] : 0.00,
                    'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                $this->modelService->update($service_id, $data);
                $_SESSION['success'] = 'Cập nhật dịch vụ thành công!';
                header('Location: ?act=danh-sach-dich-vu');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=sua-dich-vu&id=' . $service_id);
                exit();
            }
        }
    }

    // ==================== XÓA DỊCH VỤ ====================

    public function DeleteService()
    {
        requireRole('ADMIN');
        
        try {
            $service_id = $_GET['id'] ?? null;
            if (!$service_id) {
                $_SESSION['error'] = 'Thiếu tham số service_id!';
            } else {
                $this->modelService->delete($service_id);
                $_SESSION['success'] = 'Xóa dịch vụ thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ?act=danh-sach-dich-vu');
        exit();
    }

    // ==================== CHI TIẾT DỊCH VỤ ====================

    public function ServiceDetail()
    {
        requireRole('ADMIN');
        
        $service_id = $_GET['id'] ?? null;
        if (!$service_id) {
            $_SESSION['error'] = 'Thiếu tham số service_id!';
            header('Location: ?act=danh-sach-dich-vu');
            exit();
        }

        $service = $this->modelService->getById($service_id);
        if (!$service) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ!';
            header('Location: ?act=danh-sach-dich-vu');
            exit();
        }

        $usage_count = $this->modelService->getUsageCount($service_id);
        $total_revenue = $this->modelService->getTotalRevenue($service_id);

        require_once './views/service/service_detail.php';
    }

    // ==================== THỐNG KÊ DỊCH VỤ ====================

    public function ServiceStatistics()
    {
        requireRole('ADMIN');
        
        $statistics = $this->modelService->getStatistics();
        require_once './views/service/statistics.php';
    }

    // ==================== KIỂM TRA KHẢ DỤNG (AJAX) ====================

    public function CheckAvailability()
    {
        requireRole('ADMIN');
        
        header('Content-Type: application/json');
        
        $service_id = $_GET['service_id'] ?? null;
        $quantity = $_GET['quantity'] ?? 1;
        $date = $_GET['date'] ?? null;

        if (!$service_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu service_id']);
            exit();
        }

        $result = $this->modelService->checkAvailability($service_id, $quantity, $date);
        echo json_encode($result);
        exit();
    }
}
