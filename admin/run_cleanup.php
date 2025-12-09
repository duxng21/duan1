<?php
require_once __DIR__ . '/../commons/env.php';
require_once __DIR__ . '/../commons/function.php';

$conn = connectDB();

function getAllTables()
{
    $conn = connectDB();
    $rows = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
    return array_map(function ($r) {
        return $r[0]; }, $rows);
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

echo "Danh sách bảng sẽ xóa:" . PHP_EOL;
foreach ($toDrop as $t) {
    echo "  - $t" . PHP_EOL;
}

$dropped = 0;
$errors = 0;
foreach ($toDrop as $t) {
    try {
        $conn->exec("DROP TABLE IF EXISTS `$t`");
        echo "✓ Đã xóa: $t" . PHP_EOL;
        $dropped++;
    } catch (Exception $e) {
        echo "✗ Lỗi khi xóa $t: " . $e->getMessage() . PHP_EOL;
        $errors++;
    }
}

echo PHP_EOL . "Kết quả: Đã xóa $dropped bảng, $errors lỗi." . PHP_EOL;
