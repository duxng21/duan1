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

        // Lấy thông tin tài khoản nếu có
        $userAccount = null;
        if ($staff['staff_type'] === 'Guide') {
            $conn = connectDB();
            $userStmt = $conn->prepare(
                'SELECT u.*, r.role_name, r.role_code 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                WHERE u.staff_id = ? LIMIT 1'
            );
            $userStmt->execute([$id]);
            $userAccount = $userStmt->fetch();
        }

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

    // ==================== TẠO TÀI KHOẢN HDV ====================

    public function CreateAccountForStaff()
    {
        try {
            requireRole('ADMIN');

            $staff_id = $_GET['id'] ?? null;
            if (!$staff_id) {
                $_SESSION['error'] = 'Thiếu tham số staff_id!';
                header('Location: ?act=danh-sach-nhan-su');
                exit();
            }

            $staff = $this->modelStaff->getById($staff_id);
            if (!$staff) {
                $_SESSION['error'] = 'Không tìm thấy nhân sự!';
                header('Location: ?act=danh-sach-nhan-su');
                exit();
            }

            if ($staff['staff_type'] !== 'Guide') {
                $_SESSION['error'] = 'Chỉ có thể tạo tài khoản cho Hướng dẫn viên!';
                header('Location: ?act=danh-sach-nhan-su');
                exit();
            }

            $conn = connectDB();
            $checkStmt = $conn->prepare('SELECT user_id, username FROM users WHERE staff_id = ? LIMIT 1');
            $checkStmt->execute([$staff_id]);
            $existingUser = $checkStmt->fetch();

            if ($existingUser) {
                $_SESSION['warning'] = 'Nhân sự này đã có tài khoản: ' . $existingUser['username'];
                header('Location: ?act=danh-sach-nhan-su');
                exit();
            }

            $username = $this->generateUsername($staff);
            $defaultPassword = 'Guide@' . date('Y');
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

            $roleStmt = $conn->prepare('SELECT role_id FROM roles WHERE role_code = ? LIMIT 1');
            $roleStmt->execute(['GUIDE']);
            $role = $roleStmt->fetch();

            if (!$role) {
                $insertRoleStmt = $conn->prepare('INSERT INTO roles (role_code, role_name) VALUES (?, ?)');
                $insertRoleStmt->execute(['GUIDE', 'Hướng dẫn viên']);
                $role_id = $conn->lastInsertId();
            } else {
                $role_id = $role['role_id'];
            }

            $userModel = new User();
            $user_id = $userModel->create([
                'username' => $username,
                'password' => $hashedPassword,
                'full_name' => $staff['full_name'],
                'email' => $staff['email'] ?? null,
                'phone' => $staff['phone'] ?? null,
                'role_id' => $role_id,
                'staff_id' => $staff_id,
                'status' => 'Active'
            ]);

            logUserActivity('create_guide_account', 'staff', $user_id, "Tạo tài khoản cho HDV: {$staff['full_name']}");

            $_SESSION['success'] = "Tạo tài khoản thành công!<br>Username: <strong>{$username}</strong><br>Mật khẩu: <strong>{$defaultPassword}</strong><br><em class='text-warning'>Vui lòng lưu lại thông tin này!</em>";
            header('Location: ?act=danh-sach-nhan-su');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tạo tài khoản: ' . $e->getMessage();
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }
    }

    public function CreateAccountsForAllGuides()
    {
        try {
            requireRole('ADMIN');

            $conn = connectDB();

            $sql = "SELECT s.* FROM staff s 
                    LEFT JOIN users u ON s.staff_id = u.staff_id 
                    WHERE s.staff_type = 'Guide' 
                    AND s.status = 1 
                    AND u.user_id IS NULL";
            $stmt = $conn->query($sql);
            $guidesWithoutAccount = $stmt->fetchAll();

            if (empty($guidesWithoutAccount)) {
                $_SESSION['info'] = 'Tất cả HDV đã có tài khoản!';
                header('Location: ?act=danh-sach-nhan-su');
                exit();
            }

            $userModel = new User();
            $created = [];
            $failed = [];

            $roleStmt = $conn->prepare('SELECT role_id FROM roles WHERE role_code = ? LIMIT 1');
            $roleStmt->execute(['GUIDE']);
            $role = $roleStmt->fetch();

            if (!$role) {
                $insertRoleStmt = $conn->prepare('INSERT INTO roles (role_code, role_name) VALUES (?, ?)');
                $insertRoleStmt->execute(['GUIDE', 'Hướng dẫn viên']);
                $role_id = $conn->lastInsertId();
            } else {
                $role_id = $role['role_id'];
            }

            foreach ($guidesWithoutAccount as $staff) {
                try {
                    $username = $this->generateUsername($staff);
                    $defaultPassword = 'Guide@' . date('Y');
                    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

                    $user_id = $userModel->create([
                        'username' => $username,
                        'password' => $hashedPassword,
                        'full_name' => $staff['full_name'],
                        'email' => $staff['email'] ?? null,
                        'phone' => $staff['phone'] ?? null,
                        'role_id' => $role_id,
                        'staff_id' => $staff['staff_id'],
                        'status' => 'Active'
                    ]);

                    $created[] = [
                        'name' => $staff['full_name'],
                        'username' => $username,
                        'password' => $defaultPassword
                    ];

                } catch (Exception $e) {
                    $failed[] = $staff['full_name'] . ': ' . $e->getMessage();
                }
            }

            $message = "<strong>Tạo tài khoản hàng loạt hoàn tất:</strong><br>";
            $message .= "✅ Thành công: " . count($created) . " tài khoản<br>";

            if (!empty($created)) {
                $message .= "<br><strong>Danh sách tài khoản đã tạo:</strong><br>";
                foreach ($created as $acc) {
                    $message .= "- {$acc['name']}: <code>{$acc['username']}</code> / <code>{$acc['password']}</code><br>";
                }
                $message .= "<br><em class='text-warning'>Vui lòng lưu lại thông tin này!</em>";
            }

            if (!empty($failed)) {
                $message .= "<br><br>❌ Thất bại: " . count($failed) . "<br>";
                foreach ($failed as $err) {
                    $message .= "- {$err}<br>";
                }
            }

            $_SESSION['success'] = $message;
            header('Location: ?act=danh-sach-nhan-su');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tạo tài khoản hàng loạt: ' . $e->getMessage();
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }
    }

    private function generateUsername($staff)
    {
        $username = null;
        if (!empty($staff['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $staff['phone']);
            if (strlen($phone) >= 9) {
                $username = 'guide' . substr($phone, -6);
            }
        }

        if (!$username && !empty($staff['email'])) {
            $emailParts = explode('@', $staff['email']);
            $username = preg_replace('/[^a-z0-9]/', '', strtolower($emailParts[0]));
        }

        if (!$username) {
            $name = strtolower($staff['full_name']);
            $name = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $name);
            $name = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $name);
            $name = preg_replace('/[ìíịỉĩ]/u', 'i', $name);
            $name = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $name);
            $name = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $name);
            $name = preg_replace('/[ỳýỵỷỹ]/u', 'y', $name);
            $name = preg_replace('/đ/u', 'd', $name);
            $name = preg_replace('/[^a-z0-9]/', '', $name);
            $username = 'guide' . $name . rand(100, 999);
        }

        $originalUsername = $username;
        $counter = 1;
        $userModel = new User();
        while ($userModel->usernameExists($username)) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
