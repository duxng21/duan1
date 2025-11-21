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
            'schedule.log.update'
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

