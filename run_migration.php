<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== MIGRATION: THÊM PAYMENT_STATUS ===\n\n";

try {
    // Step 1: Migrate old status data first
    echo "BƯỚC 1: Migrate dữ liệu cũ\n";
    $updates = [
        ['Chờ xác nhận' => 'Giữ chỗ'],
        ['Hoàn tất' => 'Đã hoàn thành'],
    ];

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE status = ?");

    $stmt->execute(['Giữ chỗ', 'Chờ xác nhận']);
    echo "✓ 'Chờ xác nhận' → 'Giữ chỗ' (" . $stmt->rowCount() . " records)\n";

    $stmt->execute(['Đã hoàn thành', 'Hoàn tất']);
    echo "✓ 'Hoàn tất' → 'Đã hoàn thành' (" . $stmt->rowCount() . " records)\n";

    // Also handle 'Hủy' → 'Đã hủy'
    $stmt->execute(['Đã hủy', 'Hủy']);
    echo "✓ 'Hủy' → 'Đã hủy' (" . $stmt->rowCount() . " records)\n";

    echo "\nBƯỚC 2: Cập nhật status ENUM\n";
    // Step 2: Modify status column
    $sql = "ALTER TABLE bookings MODIFY COLUMN status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành')";
    $conn->exec($sql);
    echo "✓ Cập nhật status ENUM\n";

    echo "\nBƯỚC 3: Thêm payment_status column (5 trạng thái đồng nhất)\n";
    // Step 3: Add payment_status with the same 5 states
    $sql = "ALTER TABLE bookings ADD COLUMN payment_status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành') DEFAULT 'Giữ chỗ'";
    $conn->exec($sql);
    echo "✓ Thêm payment_status column\n";

    // Đồng bộ payment_status = status để đảm bảo 2-trong-1
    $sql = "UPDATE bookings SET payment_status = status";
    $conn->exec($sql);
    echo "✓ Đồng bộ payment_status = status\n";

    echo "\n✅ MIGRATION HOÀN THÀNH!\n";

} catch (Exception $e) {
    echo "❌ LỖI: {$e->getMessage()}\n";
}

echo "\n=== KIỂM TRA KẾT QUẢ ===\n";
$stmt = $conn->query("SHOW COLUMNS FROM bookings WHERE Field='status' OR Field='payment_status'");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . ": " . $row['Type'] . "\n";
}
