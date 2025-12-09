<?php
// ================================================
// PHAN QUYEN DON GIAN: ADMIN & GUIDE
// ================================================

function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        if (!isset($_SESSION['error'])) {
            $_SESSION['error'] = 'Vui lòng nhập thông tin để đăng nhập.'; // thống nhất thông báo mềm hơn
        }
        header('Location: ?act=login');
        exit();
    }
}

function isAdmin()
{
    return isset($_SESSION['role_code']) && $_SESSION['role_code'] === 'ADMIN';
}

function isGuide()
{
    return isset($_SESSION['role_code']) && $_SESSION['role_code'] === 'GUIDE';
}

function hasRole($roleCode)
{
    if (!isset($_SESSION['role_code']))
        return false;
    if (is_array($roleCode))
        return in_array($_SESSION['role_code'], $roleCode);
    return $_SESSION['role_code'] === $roleCode;
}

function requireRole($roleCode)
{
    requireLogin();
    if (!hasRole($roleCode)) {
        $_SESSION['error'] = 'Không có quyền truy cập';
        header('Location: ?act=login');
        exit();
    }
}

function requireGuideRole($permissionCode = null)
{
    requireLogin();
    if (!isGuide()) {
        $_SESSION['error'] = 'Chỉ hướng dẫn viên mới có thể truy cập!';
        header('Location: ?act=/');
        exit();
    }
    if ($permissionCode && !hasPermission($permissionCode)) {
        $_SESSION['error'] = 'Bạn không có quyền dùng chức năng này.';
        header('Location: ?act=/');
        exit();
    }
}

function hasPermission($permissionCode)
{
    if (isAdmin()) {
        return true; // Admin full quyền
    }

    if (isGuide()) {
        // Quyền cho HDV: xem tour, xem lịch của mình, check-in và cập nhật nhật ký
        $guidePerms = [
            'tour.view',
            'schedule.view_own',
            'schedule.checkin',
            'schedule.log.update',
            'guest.view',
            'guest.checkin',
            'guest.export'
        ];
        return in_array($permissionCode, $guidePerms);
    }
    return false;
}

// Kiểm tra quyền sở hữu lịch (HDV chỉ thao tác trên lịch được phân công)
function isOwnSchedule($schedule_id)
{
    if (!isGuide())
        return false;
    $staff_id = $_SESSION['staff_id'] ?? null;
    if (!$staff_id)
        return false;
    try {
        $conn = connectDB();
        $sql = "SELECT COUNT(*) FROM schedule_staff WHERE schedule_id = ? AND staff_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$schedule_id, $staff_id]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function requireOwnScheduleOrAdmin($schedule_id, $permissionCode = null)
{
    requireLogin();
    if (isAdmin())
        return true;
    if ($permissionCode && !hasPermission($permissionCode)) {
        $_SESSION['error'] = 'Bạn không có quyền.';
        header('Location: ?act=/');
        exit();
    }
    if (isGuide() && isOwnSchedule($schedule_id))
        return true;
    $_SESSION['error'] = 'Không có quyền truy cập lịch này.';
    header('Location: ?act=/');
    exit();
}

function requirePermission($permissionCode)
{
    requireLogin();
    if (!hasPermission($permissionCode)) {
        $_SESSION['error'] = 'Bạn không có quyền dùng chức năng này.';
        header('Location: ?act=/');
        exit();
    }
}

function logUserActivity($action, $module = null, $record_id = null, $details = null)
{
    if (!isset($_SESSION['user_id']))
        return;
    try {
        $conn = connectDB();
        $sql = 'INSERT INTO user_activity_logs (user_id, action, module, record_id, ip_address, user_agent, details) VALUES (?,?,?,?,?,?,?)';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $module,
            $record_id,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $details
        ]);
    } catch (Exception $e) {/* ignore */
    }
}

/**
 * Điều hướng về trang dashboard phù hợp với role
 */
function redirectToRoleDashboard()
{
    if (isGuide()) {
        header('Location: ?act=home-guide');
    } else {
        header('Location: ?act=dashboard');
    }
    exit();
}

/**
 * Kiểm tra và chặn HDV truy cập route admin-only
 */
function blockGuideFromAdminRoutes($act)
{
    if (!isGuide()) {
        return; // Không phải HDV, cho phép
    }

    // Danh sách các route admin-only
    $adminOnlyRoutes = [
        'list-tour',
        // 'menu-tour',
        // 'them-danh-muc',
        'add-list',
        'luu-tour',
        'edit-list',
        'cap-nhat-tour',
        'xoa-tour',
        'chi-tiet-tour',
        'them-lich-trinh',
        'xoa-lich-trinh',
        'them-anh-tour',
        'xoa-anh-tour',
        'luu-chinh-sach',
        'luu-tags',
        'seed-tour-data',
        'seed-all-tours',
        'danh-sach-lich-khoi-hanh',
        'them-lich-khoi-hanh',
        'luu-lich-khoi-hanh',
        'sua-lich-khoi-hanh',
        'cap-nhat-lich-khoi-hanh',
        'xoa-lich-khoi-hanh',
        'phan-cong-nhan-su',
        'xoa-nhan-su-khoi-lich',
        'phan-bo-dich-vu',
        'xoa-dich-vu-khoi-lich',
        'xem-lich-theo-thang',
        'xuat-bao-cao-lich',
        'tong-quan-phan-cong',
        'danh-sach-nhan-su',
        'them-nhan-su',
        'luu-nhan-su',
        'sua-nhan-su',
        'cap-nhat-nhan-su',
        'xoa-nhan-su',
        'thong-ke-nhan-su',
        'xuat-excel-nhan-su',
        'xuat-pdf-nhan-su',
        'tao-tai-khoan-hdv',
        'tao-tai-khoan-hang-loat',
        'list-booking',
        'danh-sach-booking',
        'them-booking',
        'luu-booking',
        'chi-tiet-booking',
        'sua-booking',
        'cap-nhat-booking',
        'cap-nhat-trang-thai-booking',
        'huy-booking',
        'in-phieu-booking',
        'danh-sach-khach',
        'check-in-khach',
        'phan-phong-khach',
        'xuat-danh-sach-doan',
        'bao-cao-doan',
        'xuat-danh-sach-da-check-in',
        'ghi-chu-dac-biet',
        'them-ghi-chu',
        'sua-ghi-chu',
        'cap-nhat-ghi-chu',
        'xoa-ghi-chu',
        'cap-nhat-trang-thai-ghi-chu',
        'bao-cao-yeu-cau-dac-biet',
        'xuat-bao-cao-yeu-cau-dac-biet',
        'bao-cao-tour',
        'xuat-bao-cao-tour',
        'danh-sach-bao-gia',
        'tao-bao-gia',
        'luu-bao-gia',
        'xem-bao-gia',
        'xuat-bao-gia',
        'cap-nhat-trang-thai-bao-gia',
        'xoa-bao-gia',
        'danh-sach-user',
        'tao-user',
        'luu-user',
        'doi-trang-thai-user',
        'dashboard-hieu-suat',
        'quan-ly-chung-chi',
        'them-chung-chi',
        'xoa-chung-chi',
        'quan-ly-ngon-ngu',
        'them-ngon-ngu',
        'xoa-ngon-ngu',
        'quan-ly-lich-nghi',
        'them-lich-nghi',
        'duyet-lich-nghi',
        'tu-choi-lich-nghi',
        'lich-su-tour',
        'cap-nhat-lich-su-tour',
        'quan-ly-danh-gia',
        'them-danh-gia'
    ];

    // UC3: Quản lý phiên bản tour (admin only)
    $adminOnlyRoutes = array_merge($adminOnlyRoutes, [
        'quan-ly-phien-ban',
        'tao-phien-ban',
        'kich-hoat-phien-ban',
        'tam-dung-phien-ban',
        'luu-tru-phien-ban',
    ]);

    if (in_array($act, $adminOnlyRoutes)) {
        $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này!';
        redirectToRoleDashboard();
    }
}
