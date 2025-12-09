<?php
// Migration: Add payment status to bookings table
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== MIGRATION: THÊM PAYMENT_STATUS VÀO BOOKINGS ===\n\n";

try {
    // Add payment_status column with unified 5 states
    $sql = "ALTER TABLE bookings ADD COLUMN payment_status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành') DEFAULT 'Giữ chỗ'";
    $conn->exec($sql);
    echo "✓ Thêm cột payment_status thành công\n\n";

    // Update status ENUM to new values
    $sql = "ALTER TABLE bookings MODIFY COLUMN status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành')";
    $conn->exec($sql);
    echo "✓ Cập nhật status ENUM thành công\n\n";

    // Migrate old data
    echo "=== MIGRATION DỮ LIỆU CŨ ===\n";
    $migrations = [
        ['old' => 'Chờ xác nhận', 'new' => 'Giữ chỗ'],
        ['old' => 'Đã đặt cọc', 'new' => 'Đã đặt cọc'],
        ['old' => 'Hoàn tất', 'new' => 'Đã hoàn thành'],
        ['old' => 'Hủy', 'new' => 'Đã hủy'],
    ];

    foreach ($migrations as $m) {
        $sql = "UPDATE bookings SET status = ? WHERE status = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$m['new'], $m['old']]);
        $count = $stmt->rowCount();
        echo "✓ '{$m['old']}' → '{$m['new']}' ($count records)\n";
    }

    // Đồng bộ payment_status = status (2-trong-1)
    $stmt = $conn->prepare("UPDATE bookings SET payment_status = status");
    $stmt->execute();
    echo "✓ Đồng bộ payment_status = status ({$stmt->rowCount()} records)\n";

    echo "\n✅ MIGRATION HOÀN THÀNH!\n";

} catch (Exception $e) {
    echo "❌ LỖI: {$e->getMessage()}\n";
    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column đã tồn tại, bỏ qua\n";
    }
}

// Verify
echo "\n=== KIỂM TRA KẾT QUẢ ===\n";
$stmt = $conn->query("DESCRIBE bookings WHERE Field IN ('status', 'payment_status')");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . ": " . $row['Type'] . "\n";
}
