<?php
require_once __DIR__ . '/../commons/env.php';
require_once __DIR__ . '/../commons/function.php';

$conn = connectDB();

function getAllTables()
{
    $conn = connectDB();
    $rows = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
    return array_map(function ($r) {
        return $r[0];
    }, $rows);
}

function isAllowed($table)
{
    $allowedExact = [
        'users',
        'roles',
        'customers',
        'services',
        'schedule_services',
        'schedule_journey_logs',
        'permissions',
        'role_permissions',
        'user_activity_logs'
    ];
    if (in_array($table, $allowedExact, true))
        return true;
    $prefixes = [
        'tour_',
        'tours',
        'staff',
        'schedule_',
        'booking',
        'bookings',
        'quote',
        'quotes',
        'notification_',
        'report',
        'reports',
        'guest_',
        'tourj_',
        'special_',
        'category',
        'categories'
    ];
    foreach ($prefixes as $p) {
        if (strpos($table, $p) === 0)
            return true;
    }
    return false;
}

$tables = getAllTables();
$toDrop = [];
foreach ($tables as $t) {
    if (!isAllowed($t))
        $toDrop[] = $t;
}

$confirm = isset($_GET['confirm']) ? (int) $_GET['confirm'] : 0;

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Cleanup Tables</title><style>body{font-family:Arial;padding:20px;background:#f5f5f5}.box{max-width:800px;margin:0 auto;background:#fff;padding:20px;border-radius:8px}code{background:#eee;padding:2px 6px;border-radius:4px}</style></head><body><div class='box'>";
echo "<h1>🧹 Cleanup Tables</h1>";

if (empty($toDrop)) {
    echo "<p>Không có bảng thừa theo tiêu chí whitelist.</p>";
} else {
    echo "<p>Các bảng sẽ bị coi là không cần thiết (không khớp whitelist tiền tố):</p><ul>";
    foreach ($toDrop as $t)
        echo "<li><code>" . htmlspecialchars($t) . "</code></li>";
    echo "</ul>";

    if ($confirm === 1) {
        $dropped = 0;
        $errors = 0;
        foreach ($toDrop as $t) {
            try {
                $conn->exec("DROP TABLE IF EXISTS `" . $t . "`");
                $dropped++;
            } catch (Exception $e) {
                $errors++;
            }
        }
        echo "<p>Đã xóa <strong>$dropped</strong> bảng. Lỗi: <strong>$errors</strong>.</p>";
    } else {
        echo "<p><strong>Chế độ xem trước</strong>. Không xóa gì cả. Để xóa thật, thêm tham số <code>?confirm=1</code>.</p>";
        echo "<p><a href='cleanup_tables.php?confirm=1'>Thực hiện xóa ngay (không thể hoàn tác)</a></p>";
    }
}

echo "<p><a href='index.php'>← Về trang Admin</a></p>";
echo "</div></body></html>";
