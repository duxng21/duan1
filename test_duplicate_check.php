<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TEST KIỂM TRA TRÙNG LỊCH TRÌNH ===\n\n";

// Lấy 1 tour đã có schedule
$stmt = $conn->query("SELECT t.tour_id, t.tour_name, t.code, ts.departure_date 
                     FROM tours t 
                     JOIN tour_schedules ts ON t.tour_id = ts.tour_id 
                     LIMIT 1");
$existing = $stmt->fetch();

echo "Tour đã có lịch:\n";
echo "  Tour ID: {$existing['tour_id']}\n";
echo "  Tên: {$existing['tour_name']}\n";
echo "  Mã: {$existing['code']}\n";
echo "  Ngày: {$existing['departure_date']}\n\n";

// Test query kiểm tra trùng
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tour_schedules WHERE tour_id = ? AND departure_date = ?");
$stmt->execute([$existing['tour_id'], $existing['departure_date']]);
$result = $stmt->fetch();

echo "Kết quả kiểm tra trùng:\n";
echo "  Số lịch trùng: {$result['count']}\n";
echo "  Kết luận: " . ($result['count'] > 0 ? "✓ BỊ TRÙNG - Nên báo lỗi" : "✗ KHÔNG TRÙNG") . "\n\n";

// Test với tour + ngày chưa có
$newDate = date('Y-m-d', strtotime('+100 days'));
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tour_schedules WHERE tour_id = ? AND departure_date = ?");
$stmt->execute([$existing['tour_id'], $newDate]);
$result = $stmt->fetch();

echo "Kiểm tra với ngày mới ($newDate):\n";
echo "  Số lịch trùng: {$result['count']}\n";
echo "  Kết luận: " . ($result['count'] > 0 ? "✗ BỊ TRÙNG" : "✓ KHÔNG TRÙNG - Nên cho phép tạo") . "\n";
