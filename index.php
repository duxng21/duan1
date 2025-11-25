<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './commons/env.php';
require_once './commons/function.php';
require_once './commons/permission_simple.php';

require_once './controllers/ProductController.php';
require_once './admin/controllers/AuthController.php';

require_once './models/ProductModel.php';
require_once './admin/models/User.php';

$act = isset($_GET['act']) ? trim($_GET['act']) : '/';

switch ($act) {
    case '/':
        // Chỉ hiển thị trang chủ sau khi đăng nhập.
        // Nếu chưa đăng nhập -> chuyển sang form login.
        // Nếu đã đăng nhập và là ADMIN -> vào khu vực admin.
        if (!isset($_SESSION['user_id'])) {
            (new AuthController())->login();
            break;
        }
        if (isAdmin()) {
            header('Location: admin/index.php');
            exit();
        }
        (new ProductController())->Home();
        break;
    case 'login':
        (new AuthController())->login();
        break;
    case 'do-login':
        (new AuthController())->processLogin();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'register':
        (new AuthController())->register();
        break;
    case 'do-register':
        (new AuthController())->processRegister();
        break;
    default:
        http_response_code(404);
        echo '404 - Không tìm thấy trang';
        break;
}