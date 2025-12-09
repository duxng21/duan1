<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== HOÀN THÀNH MIGRATION STATUS - PHƯƠNG ÁP NGẶT ===\n\n";

try {
    echo "BƯỚC 1: Disable strict mode tạm thời\n";
    $conn->exec("SET sql_mode=''");
    echo "✓ Strict mode disabled\n";

    echo "\nBƯỚC 2: Kiểm tra tất cả status hiện tại trong DB\n";
    $stmt = $conn->query("SELECT booking_id, status FROM bookings ORDER BY booking_id");
    $current_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Tất cả bookings:\n";
    foreach ($current_data as $row) {
        echo "  Booking #{$row['booking_id']}: status='{$row['status']}'\n";
    }

    echo "\nBƯỚC 3: Cập nhật tất cả status cũ\n";

    // Update 'Chờ xác nhận' → 'Giữ chỗ'
    $sql = "UPDATE bookings SET status = 'Giữ chỗ' WHERE status = 'Chờ xác nhận'";
    $conn->exec($sql);
    echo "✓ Updated 'Chờ xác nhận' → 'Giữ chỗ'\n";

    // Update 'Hủy' → 'Đã hủy'
    $sql = "UPDATE bookings SET status = 'Đã hủy' WHERE status = 'Hủy'";
    $conn->exec($sql);
    echo "✓ Updated 'Hủy' → 'Đã hủy'\n";

    echo "\nBƯỚC 4: Kiểm tra lại status sau migration\n";
    $stmt = $conn->query("SELECT booking_id, status FROM bookings ORDER BY booking_id");
    $final_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Tất cả bookings sau migration:\n";
    foreach ($final_data as $row) {
        echo "  Booking #{$row['booking_id']}: status='{$row['status']}'\n";
    }

    echo "\nBƯỚC 5: Cập nhật status ENUM\n";
    $sql = "ALTER TABLE bookings MODIFY COLUMN status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành') NOT NULL";
    $conn->exec($sql);
    echo "✓ Cập nhật status ENUM thành công\n";

    echo "\nBƯỚC 6: Cập nhật payment_status ENUM và đồng bộ với status\n";
    $sql = "ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành') DEFAULT 'Giữ chỗ'";
    $conn->exec($sql);
    echo "✓ Cập nhật payment_status ENUM thành công\n";

    $sql = "UPDATE bookings SET payment_status = status";
    $conn->exec($sql);
    echo "✓ Đồng bộ payment_status = status\n";

    echo "\n✅ MIGRATION HOÀN THÀNH!\n";

} catch (Exception $e) {
    echo "❌ LỖI: {$e->getMessage()}\n";
    die();
}

echo "\n=== KIỂM TRA KẾT QUẢ CUỐI CÙNG ===\n";
$stmt = $conn->query("SHOW COLUMNS FROM bookings WHERE Field='status' OR Field='payment_status'");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . ": " . $row['Type'] . "\n";
}

echo "\n=== KIỂM TRA DỮ LIỆU ===\n";
$stmt = $conn->query("SELECT booking_id, status, payment_status FROM bookings LIMIT 5");
echo "Sample bookings:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $booking) {
    echo "  Booking #{$booking['booking_id']}: status='{$booking['status']}', payment_status='{$booking['payment_status']}'\n";
}
?>