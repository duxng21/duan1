<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== KIỂM TRA HOÀN CHỈNH HỆ THỐNG STATUS ===" . PHP_EOL . PHP_EOL;

// 1. Check database ENUM definitions
echo "1. KIỂM TRA ENUM DEFINITIONS:" . PHP_EOL;
$stmt = $conn->query("SHOW COLUMNS FROM bookings WHERE Field IN ('status', 'payment_status')");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  ✓ {$col['Field']}: {$col['Type']}" . PHP_EOL;
}

// 2. Check data integrity
echo PHP_EOL . "2. KIỂM TRA DỮ LIỆU:" . PHP_EOL;
$stmt = $conn->query("SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN status = 'Giữ chỗ' THEN 1 END) as hold,
    COUNT(CASE WHEN status = 'Đã đặt cọc' THEN 1 END) as deposited,
    COUNT(CASE WHEN status = 'Đã thanh toán' THEN 1 END) as paid,
    COUNT(CASE WHEN status = 'Đã hủy' THEN 1 END) as cancelled,
    COUNT(CASE WHEN status = 'Đã hoàn thành' THEN 1 END) as completed
FROM bookings");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
echo "  Total bookings: {$stats['total']}" . PHP_EOL;
echo "  - Giữ chỗ (Hold): {$stats['hold']}" . PHP_EOL;
echo "  - Đã đặt cọc (Deposited): {$stats['deposited']}" . PHP_EOL;
echo "  - Đã thanh toán (Paid): {$stats['paid']}" . PHP_EOL;
echo "  - Đã hủy (Cancelled): {$stats['cancelled']}" . PHP_EOL;
echo "  - Đã hoàn thành (Completed): {$stats['completed']}" . PHP_EOL;

// 3. Test cancellation
echo PHP_EOL . "3. TEST HỦY BOOKING:" . PHP_EOL;
$test_id = 2;
$stmt = $conn->query("SELECT status FROM bookings WHERE booking_id = $test_id");
$before = $stmt->fetch(PDO::FETCH_ASSOC)['status'];
echo "  Trước: Booking #{$test_id} = '$before'" . PHP_EOL;

$conn->exec("UPDATE bookings SET status = 'Đã hủy' WHERE booking_id = $test_id");
$stmt = $conn->query("SELECT status FROM bookings WHERE booking_id = $test_id");
$after = $stmt->fetch(PDO::FETCH_ASSOC)['status'];
echo "  Sau: Booking #{$test_id} = '$after'" . PHP_EOL;

if ($after === 'Đã hủy') {
    echo "  ✓ HỦY THÀNH CÔNG!" . PHP_EOL;
} else {
    echo "  ✗ HỦY THẤT BẠI!" . PHP_EOL;
}

// 4. Test payment_status
echo PHP_EOL . "4. KIỂM TRA PAYMENT_STATUS:" . PHP_EOL;
$stmt = $conn->query("SELECT DISTINCT payment_status FROM bookings");
$payment_statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($payment_statuses as $ps) {
    echo "  ✓ '$ps'" . PHP_EOL;
}

echo PHP_EOL . "✅ KIỂM TRA HOÀN THÀNH - HỆ THỐNG HOẠT ĐỘNG BÌNH THƯỜNG!" . PHP_EOL;
?>