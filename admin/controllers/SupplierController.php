<?php
/**
 * SupplierController
 * Quản lý đối tác cung cấp dịch vụ
 * Use Case 2: Phân bổ dịch vụ cho lịch khởi hành
 */
class SupplierController
{
    public $modelSupplier;

    public function __construct()
    {
        $this->modelSupplier = new TourSupplier();
    }

    // ==================== DANH SÁCH ĐỐI TÁC ====================

    public function ListSuppliers()
    {
        requireLogin();
        // Cho phép ADMIN và nhân viên xem danh sách đối tác
        // requirePermission('tour.view');

        $filters = [
            'supplier_type' => $_GET['supplier_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $suppliers = $this->modelSupplier->getAll($filters);
        $statistics = $this->modelSupplier->getStatsByType();

        require_once './views/supplier/list_suppliers.php';
    }

    // ==================== THÊM ĐỐI TÁC ====================

    public function CreateSupplierForm()
    {
        requireRole('ADMIN');
        require_once './views/supplier/create_supplier.php';
    }

    public function CreateSupplier()
    {
        requireRole('ADMIN');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate required fields
                if (empty($_POST['supplier_name'])) {
                    $_SESSION['error'] = 'Vui lòng nhập tên đối tác!';
                    header('Location: ?act=them-doi-tac');
                    exit();
                }

                if (empty($_POST['supplier_type'])) {
                    $_SESSION['error'] = 'Vui lòng chọn loại đối tác!';
                    header('Location: ?act=them-doi-tac');
                    exit();
                }

                $data = [
                    'supplier_name' => trim($_POST['supplier_name']),
                    'supplier_code' => !empty($_POST['supplier_code']) ? trim($_POST['supplier_code']) : null,
                    'supplier_type' => $_POST['supplier_type'],
                    'contact_person' => !empty($_POST['contact_person']) ? trim($_POST['contact_person']) : null,
                    'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
                    'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
                    'address' => !empty($_POST['address']) ? trim($_POST['address']) : null,
                    'website' => !empty($_POST['website']) ? trim($_POST['website']) : null,
                    'contract_number' => !empty($_POST['contract_number']) ? trim($_POST['contract_number']) : null,
                    'contract_start_date' => !empty($_POST['contract_start_date']) ? $_POST['contract_start_date'] : null,
                    'contract_end_date' => !empty($_POST['contract_end_date']) ? $_POST['contract_end_date'] : null,
                    'payment_terms' => !empty($_POST['payment_terms']) ? trim($_POST['payment_terms']) : null,
                    'cancellation_policy' => !empty($_POST['cancellation_policy']) ? trim($_POST['cancellation_policy']) : null,
                    'rating' => !empty($_POST['rating']) ? floatval($_POST['rating']) : 0,
                    'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null,
                    'status' => isset($_POST['status']) ? intval($_POST['status']) : 1
                ];

                // Handle file upload for contract
                if (!empty($_FILES['contract_file']['name'])) {
                    $uploadDir = '../uploads/contracts/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . basename($_FILES['contract_file']['name']);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $targetPath)) {
                        $data['contract_file'] = 'uploads/contracts/' . $fileName;
                    }
                }

                $result = $this->modelSupplier->create($data);

                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                    header('Location: ?act=danh-sach-doi-tac');
                } else {
                    $_SESSION['error'] = $result['message'];
                    header('Location: ?act=them-doi-tac');
                }
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=them-doi-tac');
                exit();
            }
        }
    }

    // ==================== SỬA ĐỐI TÁC ====================

    public function EditSupplierForm()
    {
        requireRole('ADMIN');

        $supplier_id = $_GET['id'] ?? null;
        if (!$supplier_id) {
            $_SESSION['error'] = 'Thiếu tham số supplier_id!';
            header('Location: ?act=danh-sach-doi-tac');
            exit();
        }

        $supplier = $this->modelSupplier->getById($supplier_id);
        if (!$supplier) {
            $_SESSION['error'] = 'Không tìm thấy đối tác!';
            header('Location: ?act=danh-sach-doi-tac');
            exit();
        }

        require_once './views/supplier/edit_supplier.php';
    }

    public function UpdateSupplier()
    {
        requireRole('ADMIN');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $supplier_id = $_POST['supplier_id'] ?? null;
                if (!$supplier_id) {
                    $_SESSION['error'] = 'Thiếu supplier_id!';
                    header('Location: ?act=danh-sach-doi-tac');
                    exit();
                }

                // Validate required fields
                if (empty($_POST['supplier_name'])) {
                    $_SESSION['error'] = 'Vui lòng nhập tên đối tác!';
                    header('Location: ?act=sua-doi-tac&id=' . $supplier_id);
                    exit();
                }

                $data = [
                    'supplier_name' => trim($_POST['supplier_name']),
                    'supplier_code' => !empty($_POST['supplier_code']) ? trim($_POST['supplier_code']) : null,
                    'supplier_type' => $_POST['supplier_type'],
                    'contact_person' => !empty($_POST['contact_person']) ? trim($_POST['contact_person']) : null,
                    'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
                    'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
                    'address' => !empty($_POST['address']) ? trim($_POST['address']) : null,
                    'website' => !empty($_POST['website']) ? trim($_POST['website']) : null,
                    'contract_number' => !empty($_POST['contract_number']) ? trim($_POST['contract_number']) : null,
                    'contract_start_date' => !empty($_POST['contract_start_date']) ? $_POST['contract_start_date'] : null,
                    'contract_end_date' => !empty($_POST['contract_end_date']) ? $_POST['contract_end_date'] : null,
                    'payment_terms' => !empty($_POST['payment_terms']) ? trim($_POST['payment_terms']) : null,
                    'cancellation_policy' => !empty($_POST['cancellation_policy']) ? trim($_POST['cancellation_policy']) : null,
                    'rating' => !empty($_POST['rating']) ? floatval($_POST['rating']) : 0,
                    'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null,
                    'status' => isset($_POST['status']) ? intval($_POST['status']) : 1
                ];

                // Get existing supplier for file handling
                $existing = $this->modelSupplier->getById($supplier_id);
                $data['contract_file'] = $existing['contract_file']; // Keep old file by default

                // Handle file upload for contract
                if (!empty($_FILES['contract_file']['name'])) {
                    $uploadDir = '../uploads/contracts/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . basename($_FILES['contract_file']['name']);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $targetPath)) {
                        // Delete old file if exists
                        if (!empty($existing['contract_file']) && file_exists('../' . $existing['contract_file'])) {
                            unlink('../' . $existing['contract_file']);
                        }
                        $data['contract_file'] = 'uploads/contracts/' . $fileName;
                    }
                }

                $result = $this->modelSupplier->update($supplier_id, $data);

                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }

                header('Location: ?act=danh-sach-doi-tac');
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=sua-doi-tac&id=' . $supplier_id);
                exit();
            }
        }
    }

    // ==================== XÓA ĐỐI TÁC ====================

    public function DeleteSupplier()
    {
        requireRole('ADMIN');

        try {
            $supplier_id = $_GET['id'] ?? null;
            if (!$supplier_id) {
                $_SESSION['error'] = 'Thiếu supplier_id!';
                header('Location: ?act=danh-sach-doi-tac');
                exit();
            }

            // Get supplier info for file deletion
            $supplier = $this->modelSupplier->getById($supplier_id);

            $result = $this->modelSupplier->delete($supplier_id);

            if ($result['success']) {
                // Delete contract file if exists
                if (!empty($supplier['contract_file']) && file_exists('../' . $supplier['contract_file'])) {
                    unlink('../' . $supplier['contract_file']);
                }
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: ?act=danh-sach-doi-tac');
        exit();
    }

    // ==================== CHI TIẾT ĐỐI TÁC ====================

    public function ViewSupplier()
    {
        requireLogin();
        // Cho phép tất cả user đã login xem chi tiết đối tác
        // requirePermission('tour.view');

        $supplier_id = $_GET['id'] ?? null;
        if (!$supplier_id) {
            $_SESSION['error'] = 'Thiếu tham số supplier_id!';
            header('Location: ?act=danh-sach-doi-tac');
            exit();
        }

        $supplier = $this->modelSupplier->getById($supplier_id);
        if (!$supplier) {
            $_SESSION['error'] = 'Không tìm thấy đối tác!';
            header('Location: ?act=danh-sach-doi-tac');
            exit();
        }

        // Get usage statistics
        $usage = $this->modelSupplier->checkUsage($supplier_id);
        $tours = $this->modelSupplier->getToursBySupplier($supplier_id);

        require_once './views/supplier/view_supplier.php';
    }
}
