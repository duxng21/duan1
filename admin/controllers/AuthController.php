<?php
class AuthController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        // Tự động bootstrap dữ liệu ban đầu nếu chưa có user nào
        $this->bootstrapIfEmpty();
    }

    protected function bootstrapIfEmpty()
    {
        // Chỉ chạy 1 lần / request nếu chưa thiết lập
        if (isset($_SESSION['__BOOTSTRAP_DONE']))
            return; // tránh lặp
        try {
            $conn = connectDB();
            $count = (int) $conn->query('SELECT COUNT(*) FROM users')->fetchColumn();
            if ($count === 0) {
                // Đảm bảo roles tồn tại
                $rolesNeed = [
                    'ADMIN' => 'Quản trị',
                    'GUIDE' => 'Hướng dẫn viên'
                ];
                foreach ($rolesNeed as $code => $name) {
                    $stmt = $conn->prepare('SELECT role_id FROM roles WHERE role_code = ? LIMIT 1');
                    $stmt->execute([$code]);
                    if (!$stmt->fetch()) {
                        $ins = $conn->prepare('INSERT INTO roles (role_code, role_name) VALUES (?,?)');
                        $ins->execute([$code, $name]);
                    }
                }
                // Lấy role_id ADMIN
                $adminRoleId = (int) $conn->query("SELECT role_id FROM roles WHERE role_code='ADMIN' LIMIT 1")->fetchColumn();
                if ($adminRoleId) {
                    $defaultPass = 'Admin@123';
                    $hash = password_hash($defaultPass, PASSWORD_DEFAULT);
                    $insUser = $conn->prepare('INSERT INTO users (username,password,full_name,email,phone,avatar,role_id,staff_id,status,login_attempts,last_login,created_at) VALUES (?,?,?,?,?,?,?,?,?,0,NULL,NOW())');
                    $insUser->execute(['admin', $hash, 'Quản trị viên', 'admin@example.com', NULL, NULL, $adminRoleId, NULL, 'Active']);
                    $_SESSION['info'] = 'Đã tạo tài khoản mặc định admin/Admin@123. Vui lòng đổi mật khẩu.';
                    logUserActivity('bootstrap_admin', 'system', $conn->lastInsertId(), 'Seed default admin');
                }
            }
        } catch (Exception $e) {
            // Không làm gián đoạn luồng đăng nhập
            $_SESSION['warning'] = 'Bootstrap lỗi: ' . substr($e->getMessage(), 0, 60);
        }
        $_SESSION['__BOOTSTRAP_DONE'] = true;
    }

    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }
        // Sử dụng đường dẫn tuyệt đối dựa trên vị trí file controller để tránh lỗi khi gọi từ root index
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function processLogin()
    {
        $MAX_ATTEMPTS = 5; // Ngưỡng khóa tạm
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ.';
            header('Location: ?act=login');
            exit();
        }
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $_SESSION['error'] = 'Username không tồn tại.';
            header('Location: ?act=login');
            exit();
        }
        // Kiểm tra trạng thái trước
        if ($user['status'] !== 'Active') {
            $_SESSION['error'] = 'Tài khoản không hoạt động / đã bị khóa.';
            header('Location: ?act=login');
            exit();
        }
        if (!password_verify($password, $user['password'])) {
            // Tăng số lần sai
            $this->userModel->incrementLoginAttempts($user['user_id']);
            // Khóa nếu vượt quá
            if ($this->userModel->lockAccountIfExceeded($user['user_id'], $MAX_ATTEMPTS)) {
                $_SESSION['error'] = 'Sai quá nhiều lần. Tài khoản đã bị khóa.';
            } else {
                $_SESSION['error'] = 'Mật khẩu không đúng.';
            }
            header('Location: ?act=login');
            exit();
        }
        // Thành công: reset attempts + cập nhật last_login
        $this->userModel->updateLastLogin($user['user_id']);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_code'] = $user['role_code'];
        $_SESSION['staff_id'] = $user['staff_id'];
        logUserActivity('login', 'auth', $user['user_id'], 'Đăng nhập');
        header('Location: index.php');
        exit();
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            logUserActivity('logout', 'auth', $_SESSION['user_id'], 'Đăng xuất');
        }
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['success'] = 'Đã đăng xuất.';
        // Chuyển về trang đăng nhập gốc (root) dùng URL tuyệt đối tránh lỗi tương đối
        $loginUrl = defined('ROOT_URL') ? ROOT_URL . 'index.php?act=login' : '../index.php?act=login';
        header('Location: ' . $loginUrl);
        exit();
    }

    public function register()
    {
        requireRole('ADMIN');
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function processRegister()
    {
        requireRole('ADMIN');
        $username = trim($_POST['username'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password_confirmation'] ?? '';
        $role_code = $_POST['role_code'] ?? 'GUIDE';
        $staff_id = $_POST['staff_id'] !== '' ? $_POST['staff_id'] : null;
        if ($username === '' || $full_name === '' || $password === '') {
            $_SESSION['error'] = 'Thiếu dữ liệu.';
            header('Location: ?act=register');
            exit();
        }
        if ($password !== $password2) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            header('Location: ?act=register');
            exit();
        }
        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = 'Username đã tồn tại.';
            header('Location: ?act=register');
            exit();
        }
        if ($email !== '' && $this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'Email đã tồn tại.';
            header('Location: ?act=register');
            exit();
        }
        $conn = connectDB();
        $stmt = $conn->prepare('SELECT role_id FROM roles WHERE role_code = ? LIMIT 1');
        $stmt->execute([$role_code]);
        $role = $stmt->fetch();
        if (!$role) {
            $_SESSION['error'] = 'Role không hợp lệ.';
            header('Location: ?act=register');
            exit();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $user_id = $this->userModel->create([
                'username' => $username,
                'password' => $hash,
                'full_name' => $full_name,
                'email' => $email,
                'role_id' => $role['role_id'],
                'staff_id' => $staff_id,
                'status' => 'Active'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=register');
            exit();
        }
        logUserActivity('create_user', 'system', $user_id, 'Tạo user: ' . $username);
        $_SESSION['success'] = 'Tạo tài khoản thành công!';
        header('Location: ?act=register');
        exit();
    }

    // ============== ĐỔI MẬT KHẨU ==============
    public function changePassword()
    {
        requireLogin();
        require_once __DIR__ . '/../views/auth/change_password.php';
    }

    public function processChangePassword()
    {
        requireLogin();
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if ($old === '' || $new === '' || $confirm === '') {
            $_SESSION['error'] = 'Thiếu dữ liệu.';
            header('Location: ?act=doi-mat-khau');
            exit();
        }
        if ($new !== $confirm) {
            $_SESSION['error'] = 'Xác nhận mật khẩu không khớp.';
            header('Location: ?act=doi-mat-khau');
            exit();
        }
        if (strlen($new) < 8) {
            $_SESSION['error'] = 'Mật khẩu mới phải >= 8 ký tự.';
            header('Location: ?act=doi-mat-khau');
            exit();
        }
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user || !password_verify($old, $user['password'])) {
            $_SESSION['error'] = 'Mật khẩu cũ không đúng.';
            header('Location: ?act=doi-mat-khau');
            exit();
        }
        $hash = password_hash($new, PASSWORD_DEFAULT);
        if ($this->userModel->updatePassword($user['user_id'], $hash)) {
            logUserActivity('change_password', 'auth', $user['user_id'], 'Đổi mật khẩu');
            $_SESSION['success'] = 'Đổi mật khẩu thành công!';
            header('Location: ?act=doi-mat-khau');
            exit();
        } else {
            $_SESSION['error'] = 'Không thể cập nhật mật khẩu.';
            header('Location: ?act=doi-mat-khau');
            exit();
        }
    }

    // ============== QUẢN LÝ USER ADMIN ==============
    public function listUsers()
    {
        requireRole('ADMIN');
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../views/auth/list_users.php';
    }

    public function createUserForm()
    {
        requireRole('ADMIN');
        // Lấy roles cho select
        $conn = connectDB();
        $roles = $conn->query('SELECT role_id, role_code FROM roles ORDER BY role_id ASC')->fetchAll();
        require_once __DIR__ . '/../views/auth/create_user.php';
    }

    public function processCreateUser()
    {
        requireRole('ADMIN');
        $username = trim($_POST['username'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password_confirmation'] ?? '';
        $role_id = (int) ($_POST['role_id'] ?? 0);
        $staff_id = $_POST['staff_id'] !== '' ? $_POST['staff_id'] : null;
        if ($username === '' || $full_name === '' || $password === '') {
            $_SESSION['error'] = 'Thiếu dữ liệu.';
            header('Location: ?act=tao-user');
            exit();
        }
        if ($password !== $password2) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            header('Location: ?act=tao-user');
            exit();
        }
        if ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = 'Username đã tồn tại.';
            header('Location: ?act=tao-user');
            exit();
        }
        if ($email !== '' && $this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'Email đã tồn tại.';
            header('Location: ?act=tao-user');
            exit();
        }
        // Kiểm tra role_id hợp lệ
        $conn = connectDB();
        $stmt = $conn->prepare('SELECT role_code FROM roles WHERE role_id = ? LIMIT 1');
        $stmt->execute([$role_id]);
        $role = $stmt->fetch();
        if (!$role) {
            $_SESSION['error'] = 'Role không hợp lệ.';
            header('Location: ?act=tao-user');
            exit();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $user_id = $this->userModel->create([
                'username' => $username,
                'password' => $hash,
                'full_name' => $full_name,
                'email' => $email,
                'role_id' => $role_id,
                'staff_id' => $staff_id,
                'status' => 'Active'
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=tao-user');
            exit();
        }
        logUserActivity('create_user_admin', 'user', $user_id, 'Admin tạo user: ' . $username);
        $_SESSION['success'] = 'Tạo tài khoản thành công!';
        header('Location: ?act=danh-sach-user');
        exit();
    }

    public function toggleUserStatus()
    {
        requireRole('ADMIN');
        $user_id = (int) ($_GET['user_id'] ?? 0);
        $action = $_GET['action'] ?? '';
        if ($user_id <= 0 || !in_array($action, ['lock', 'unlock'])) {
            $_SESSION['error'] = 'Tham số không hợp lệ.';
            header('Location: ?act=danh-sach-user');
            exit();
        }
        // Không cho tự khóa bản thân
        if ($user_id == ($_SESSION['user_id'] ?? 0) && $action === 'lock') {
            $_SESSION['error'] = 'Không thể tự khóa tài khoản của bạn.';
            header('Location: ?act=danh-sach-user');
            exit();
        }
        $target = $this->userModel->findById($user_id);
        if (!$target) {
            $_SESSION['error'] = 'User không tồn tại.';
            header('Location: ?act=danh-sach-user');
            exit();
        }
        $newStatus = $action === 'lock' ? 'Locked' : 'Active';
        if ($this->userModel->setStatus($user_id, $newStatus)) {
            logUserActivity($action === 'lock' ? 'lock_user' : 'unlock_user', 'user', $user_id, 'Thay đổi trạng thái user');
            $_SESSION['success'] = ($action === 'lock' ? 'Đã khóa user.' : 'Đã mở khóa user.');
        } else {
            $_SESSION['error'] = 'Không thể cập nhật trạng thái.';
        }
        header('Location: ?act=danh-sach-user');
        exit();
    }
}