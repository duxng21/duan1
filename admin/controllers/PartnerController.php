<?php
class PartnerController
{
    public $modelPartner;

    public function __construct()
    {
        $this->modelPartner = new Partner();
    }

    // ==================== DANH SÁCH ĐỐI TÁC ====================

    public function ListPartner()
    {
        requireRole('ADMIN');
        
        $filters = [
            'partner_type' => $_GET['partner_type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        if (!empty($filters['partner_type']) || !empty($filters['search']) || isset($filters['status'])) {
            $partners = $this->modelPartner->search($filters);
        } else {
            $partners = $this->modelPartner->getAll();
        }

        require_once './views/partner/list_partner.php';
    }

    // ==================== THÊM ĐỐI TÁC ====================

    public function AddPartner()
    {
        requireRole('ADMIN');
        require_once './views/partner/add_partner.php';
    }

    public function StorePartner()
    {
        requireRole('ADMIN');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate dữ liệu
                if (empty($_POST['partner_name'])) {
                    $_SESSION['error'] = 'Tên đối tác không được để trống!';
                    header('Location: ?act=them-doi-tac');
                    exit();
                }

                $data = [
                    'partner_name' => trim($_POST['partner_name']),
                    'partner_type' => $_POST['partner_type'] ?? 'Other',
                    'contact_person' => trim($_POST['contact_person'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'tax_code' => trim($_POST['tax_code'] ?? ''),
                    'bank_account' => trim($_POST['bank_account'] ?? ''),
                    'bank_name' => trim($_POST['bank_name'] ?? ''),
                    'rating' => !empty($_POST['rating']) ? (float)$_POST['rating'] : 0.00,
                    'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                $partner_id = $this->modelPartner->create($data);
                $_SESSION['success'] = 'Thêm đối tác thành công!';
                header('Location: ?act=danh-sach-doi-tac');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=them-doi-tac');
                exit();
            }
        }
    }

    // ==================== SỬA ĐỐI TÁC ====================

    public function EditPartner()
    {
        requireRole('ADMIN');
        
        $partner_id = $_GET['id'] ?? null;
        if (!$partner_id) {
            $_SESSION['error'] = 'Thiếu tham số partner_id!';
            header('Location: ?act=danh-sach-doi-tac');
            exit();
        }

        $partner = $this->modelPartner->getById($partner_id);
        if (!$partner) {
            $_SESSION['error'] = 'Không tìm thấy đối tác!';
            header('Location: ?act=danh-sach-doi-tac');
            exit();
        }

        require_once './views/partner/edit_partner.php';
    }

    public function UpdatePartner()
    {
        requireRole('ADMIN');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $partner_id = $_GET['id'] ?? null;
                if (!$partner_id) {
                    $_SESSION['error'] = 'Thiếu tham số partner_id!';
                    header('Location: ?act=danh-sach-doi-tac');
                    exit();
                }

                // Validate dữ liệu
                if (empty($_POST['partner_name'])) {
                    $_SESSION['error'] = 'Tên đối tác không được để trống!';
                    header('Location: ?act=sua-doi-tac&id=' . $partner_id);
                    exit();
                }

                $data = [
                    'partner_name' => trim($_POST['partner_name']),
                    'partner_type' => $_POST['partner_type'] ?? 'Other',
                    'contact_person' => trim($_POST['contact_person'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'tax_code' => trim($_POST['tax_code'] ?? ''),
                    'bank_account' => trim($_POST['bank_account'] ?? ''),
                    'bank_name' => trim($_POST['bank_name'] ?? ''),
                    'rating' => !empty($_POST['rating']) ? (float)$_POST['rating'] : 0.00,
                    'status' => isset($_POST['status']) ? (int)$_POST['status'] : 1,
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                $this->modelPartner->update($partner_id, $data);
                $_SESSION['success'] = 'Cập nhật đối tác thành công!';
                header('Location: ?act=danh-sach-doi-tac');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=sua-doi-tac&id=' . $partner_id);
                exit();
            }
        }
    }

    // ==================== XÓA ĐỐI TÁC ====================

    public function DeletePartner()
    {
        requireRole('ADMIN');
        
        try {
            $partner_id = $_GET['id'] ?? null;
            if (!$partner_id) {
                $_SESSION['error'] = 'Thiếu tham số partner_id!';
            } else {
                $this->modelPartner->delete($partner_id);
                $_SESSION['success'] = 'Xóa đối tác thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ?act=danh-sach-doi-tac');
        exit();
    }

    // ==================== THỐNG KÊ ĐỐI TÁC ====================

    public function PartnerStatistics()
    {
        requireRole('ADMIN');
        
        $statistics = $this->modelPartner->getStatistics();
        require_once './views/partner/statistics.php';
    }
}
