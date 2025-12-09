<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== KIỂM TRA VÀ SỬA STATUS VALUES ===" . PHP_EOL . PHP_EOL;

// Disable strict mode
$conn->exec("SET sql_mode=''");
echo "✓ Disabled strict mode" . PHP_EOL;

// Check current status values
echo PHP_EOL . "Current status values in database:" . PHP_EOL;
$stmt = $conn->query("SELECT DISTINCT status FROM bookings");
foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $status) {
    echo "  - '$status'" . PHP_EOL;
}

// Check ENUM definition
echo PHP_EOL . "Current ENUM definition:" . PHP_EOL;
$stmt = $conn->query("SHOW COLUMNS FROM bookings WHERE Field='status'");
$col = $stmt->fetch(PDO::FETCH_ASSOC);
echo "  " . $col['Type'] . PHP_EOL;

// Update any invalid status values
echo PHP_EOL . "Fixing invalid status values..." . PHP_EOL;

// Check if there are any values that don't match the new ENUM
$valid_statuses = ['Giữ chỗ', 'Đã đặt cọc', 'Đã thanh toán', 'Đã hủy', 'Đã hoàn thành'];

// Get all distinct status values
$stmt = $conn->query("SELECT DISTINCT status FROM bookings");
$current_statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($current_statuses as $status) {
    if (!in_array($status, $valid_statuses) && !empty($status)) {
        echo "  Found invalid status: '$status'" . PHP_EOL;
        echo "    Replacing with 'Giữ chỗ'..." . PHP_EOL;
        $sql = "UPDATE bookings SET status = 'Giữ chỗ' WHERE status = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$status]);
        echo "    ✓ Updated " . $stmt->rowCount() . " records" . PHP_EOL;
    }
}

// Also handle empty status values
$stmt = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE status = '' OR status IS NULL");
$empty_count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
if ($empty_count > 0) {
    echo "  Found $empty_count empty status values" . PHP_EOL;
    $sql = "UPDATE bookings SET status = 'Giữ chỗ' WHERE status = '' OR status IS NULL";
    $conn->exec($sql);
    echo "    ✓ Updated to 'Giữ chỗ'" . PHP_EOL;
}

// Verify all status values are now valid
echo PHP_EOL . "Verifying status values after fix:" . PHP_EOL;
$stmt = $conn->query("SELECT DISTINCT status FROM bookings");
foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $status) {
    $valid = in_array($status, $valid_statuses) ? "✓" : "✗";
    echo "  $valid '$status'" . PHP_EOL;
}

echo PHP_EOL . "✅ STATUS CLEANUP COMPLETED!" . PHP_EOL;
?>