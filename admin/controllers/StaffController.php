<?php
class StaffController
{
    public $modelStaff;

    public function __construct()
    {
        $this->modelStaff = new Staff();
    }

    // ==================== DANH SÁCH NHÂN SỰ ====================

    public function ListStaff()
    {
        // === Use Case 1: Hỗ trợ filter và search ===
        $filters = [
            'type' => $_GET['type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'category' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null,
            'language' => $_GET['language'] ?? null
        ];

        $staffList = $this->modelStaff->getAllWithFilters($filters);
        $statistics = $this->modelStaff->getStatistics();

        require_once './views/staff/list_staff.php';
    }

    // ==================== THÊM NHÂN SỰ ====================

    public function AddStaff()
    {
        requireRole('ADMIN');
        require_once './views/staff/add_staff.php';
    }

    public function StoreStaff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                requireRole('ADMIN');
                // Validate
                if (empty($_POST['full_name'])) {
                    $_SESSION['error'] = 'Họ tên không được để trống!';
                    header('Location: ?act=them-nhan-su');
                    exit();
                }

                if (empty($_POST['staff_type'])) {
                    $_SESSION['error'] = 'Vui lòng chọn loại nhân sự!';
                    header('Location: ?act=them-nhan-su');
                    exit();
                }

                $data = [
                    'full_name' => $_POST['full_name'],
                    'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                    'gender' => $_POST['gender'] ?? 'Nam',
                    'address' => $_POST['address'] ?? null,
                    'avatar' => null, // Xử lý upload ảnh bên dưới
                    'phone' => $_POST['phone'] ?? null,
                    'email' => $_POST['email'] ?? null,
                    'id_card' => $_POST['id_card'] ?? null,
                    'license_number' => $_POST['license_number'] ?? null,
                    'experience_years' => $_POST['experience_years'] ?? 0,
                    'languages' => $_POST['languages'] ?? null,
                    'staff_type' => $_POST['staff_type'],
                    'staff_category' => $_POST['staff_category'] ?? 'Nội địa',
                    'specialization' => $_POST['specialization'] ?? null,
                    'group_specialty' => $_POST['group_specialty'] ?? 'Cả hai',
                    'health_status' => $_POST['health_status'] ?? 'Tốt',
                    'health_notes' => $_POST['health_notes'] ?? null,
                    'emergency_contact' => $_POST['emergency_contact'] ?? null,
                    'emergency_phone' => $_POST['emergency_phone'] ?? null,
                    'bank_account' => $_POST['bank_account'] ?? null,
                    'bank_name' => $_POST['bank_name'] ?? null,
                    'status' => isset($_POST['status']) ? 1 : 0,
                    'notes' => $_POST['notes'] ?? null
                ];

                // Upload ảnh đại diện nếu có
                if (!empty($_FILES['avatar']['name'])) {
                    $uploadDir = '../uploads/avatars/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . basename($_FILES['avatar']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                        $data['avatar'] = 'uploads/avatars/' . $fileName;
                    }
                }

                $this->modelStaff->create($data);
                $_SESSION['success'] = 'Thêm nhân sự thành công!';

                header('Location: ?act=danh-sach-nhan-su');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=them-nhan-su');
                exit();
            }
        }
    }

    // ==================== SỬA NHÂN SỰ ====================

    public function EditStaff()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($id);
        if (!$staff) {
            $_SESSION['error'] = 'Không tìm thấy nhân sự!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        require_once './views/staff/edit_staff.php';
    }

    public function UpdateStaff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    $_SESSION['error'] = 'Thiếu tham số id!';
                    header('Location: ?act=danh-sach-nhan-su');
                    exit();
                }

                $data = [
                    'full_name' => $_POST['full_name'],
                    'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                    'gender' => $_POST['gender'] ?? 'Nam',
                    'address' => $_POST['address'] ?? null,
                    'avatar' => $_POST['current_avatar'] ?? null, // Giữ ảnh cũ
                    'phone' => $_POST['phone'] ?? null,
                    'email' => $_POST['email'] ?? null,
                    'id_card' => $_POST['id_card'] ?? null,
                    'license_number' => $_POST['license_number'] ?? null,
                    'experience_years' => $_POST['experience_years'] ?? 0,
                    'languages' => $_POST['languages'] ?? null,
                    'staff_type' => $_POST['staff_type'],
                    'staff_category' => $_POST['staff_category'] ?? 'Nội địa',
                    'specialization' => $_POST['specialization'] ?? null,
                    'group_specialty' => $_POST['group_specialty'] ?? 'Cả hai',
                    'health_status' => $_POST['health_status'] ?? 'Tốt',
                    'health_notes' => $_POST['health_notes'] ?? null,
                    'emergency_contact' => $_POST['emergency_contact'] ?? null,
                    'emergency_phone' => $_POST['emergency_phone'] ?? null,
                    'bank_account' => $_POST['bank_account'] ?? null,
                    'bank_name' => $_POST['bank_name'] ?? null,
                    'status' => isset($_POST['status']) ? 1 : 0,
                    'notes' => $_POST['notes'] ?? null
                ];

                // Upload ảnh đại diện mới nếu có
                if (!empty($_FILES['avatar']['name'])) {
                    $uploadDir = '../uploads/avatars/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . basename($_FILES['avatar']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                        $data['avatar'] = 'uploads/avatars/' . $fileName;
                    }
                }

                $result = $this->modelStaff->update($id, $data);

                if ($result) {
                    $_SESSION['success'] = 'Cập nhật nhân sự thành công!';
                } else {
                    $_SESSION['error'] = 'Cập nhật nhân sự thất bại!';
                }

                header('Location: ?act=danh-sach-nhan-su');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=sua-nhan-su&id=' . $id);
                exit();
            }
        }
    }

    // ==================== XÓA NHÂN SỰ ====================

    public function DeleteStaff()
    {
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $_SESSION['error'] = 'Thiếu tham số id!';
            } else {
                $result = $this->modelStaff->delete($id);
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ?act=danh-sach-nhan-su');
        exit();
    }

    // ==================== CHI TIẾT NHÂN SỰ ====================

    public function StaffDetail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Thiếu tham số id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($id);
        if (!$staff) {
            $_SESSION['error'] = 'Không tìm thấy nhân sự!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        // Lấy lịch làm việc
        $from_date = $_GET['from_date'] ?? date('Y-m-01');
        $to_date = $_GET['to_date'] ?? date('Y-m-t');
        $schedules = $this->modelStaff->getSchedulesByStaff($id, $from_date, $to_date);

        require_once './views/staff/staff_detail.php';
    }

    // ==================== THỐNG KÊ VÀ BÁO CÁO (Use Case 1) ====================

    /**
     * Hiển thị trang thống kê tổng quan HDV
     */
    public function Statistics()
    {
        // Thống kê tổng quan
        $overview = $this->modelStaff->getStatistics();

        // Thống kê theo loại (nội địa, quốc tế)
        $byCategory = $this->modelStaff->getStatisticsByCategory();

        // Top HDV theo số tour
        $topGuides = $this->modelStaff->getTopGuidesByTours(10);

        // Thống kê theo tháng
        $year = $_GET['year'] ?? date('Y');
        $monthlyStats = $this->modelStaff->getMonthlyStatistics($year);

        // Thống kê theo ngôn ngữ
        $languageStats = $this->modelStaff->getStatisticsByLanguage();

        require_once './views/staff/statistics.php';
    }

    /**
     * Xuất báo cáo Excel
     */
    public function ExportExcel()
    {
        try {
            $filters = [
                'type' => $_GET['type'] ?? null,
                'status' => $_GET['status'] ?? null,
                'category' => $_GET['category'] ?? null
            ];

            $staffList = $this->modelStaff->getAllWithFilters($filters);

            // Tạo file Excel
            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="Danh_sach_HDV_' . date('Y-m-d') . '.xls"');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr style="background-color: #4CAF50; color: white; font-weight: bold;">';
            echo '<th>Mã HDV</th>';
            echo '<th>Họ tên</th>';
            echo '<th>Loại</th>';
            echo '<th>Phân loại</th>';
            echo '<th>Ngôn ngữ</th>';
            echo '<th>Chuyên môn</th>';
            echo '<th>Kinh nghiệm (năm)</th>';
            echo '<th>Số tour đã dẫn</th>';
            echo '<th>Đánh giá</th>';
            echo '<th>Trạng thái</th>';
            echo '<th>Điện thoại</th>';
            echo '<th>Email</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($staffList as $staff) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($staff['staff_id']) . '</td>';
                echo '<td>' . htmlspecialchars($staff['full_name']) . '</td>';
                echo '<td>' . htmlspecialchars($staff['staff_type']) . '</td>';
                echo '<td>' . htmlspecialchars($staff['staff_category'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($staff['languages'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($staff['specialization'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($staff['experience_years'] ?? 0) . '</td>';
                echo '<td>' . htmlspecialchars($staff['total_tours'] ?? 0) . '</td>';
                echo '<td>' . htmlspecialchars($staff['performance_rating'] ?? 0) . '</td>';
                echo '<td>' . ($staff['status'] ? 'Hoạt động' : 'Nghỉ việc') . '</td>';
                echo '<td>' . htmlspecialchars($staff['phone'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($staff['email'] ?? '') . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';

            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi xuất file: ' . $e->getMessage();
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }
    }

    /**
     * Xuất báo cáo PDF (đơn giản)
     */
    public function ExportPDF()
    {
        try {
            $filters = [
                'type' => $_GET['type'] ?? null,
                'status' => $_GET['status'] ?? null,
                'category' => $_GET['category'] ?? null
            ];

            $staffList = $this->modelStaff->getAllWithFilters($filters);
            $statistics = $this->modelStaff->getStatistics();

            // Tạo HTML cho PDF
            ob_start();
            require_once './views/staff/report_pdf.php';
            $html = ob_get_clean();

            // Output PDF headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Bao_cao_HDV_' . date('Y-m-d') . '.pdf"');

            // Nếu có thư viện PDF như mPDF, TCPDF, hoặc DomPDF:
            // require_once '../vendor/autoload.php';
            // $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
            // $mpdf->WriteHTML($html);
            // $mpdf->Output();

            // Tạm thời: xuất HTML (có thể dùng browser Print to PDF)
            echo $html;

            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi xuất PDF: ' . $e->getMessage();
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }
    }
}
