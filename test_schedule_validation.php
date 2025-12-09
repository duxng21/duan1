<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TEST LOGIC KIỂM TRA TRÙNG (MOCK CONTROLLER) ===\n\n";

// Scenario 1: Thêm lịch trùng
echo "SCENARIO 1: Cố gắng thêm lịch TRÙNG\n";
echo "Tour ID: 26 (Cần Thơ - VN010)\n";
echo "Departure Date: 2025-12-08\n\n";

$tourId = 26;
$departureDate = '2025-12-08';

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tour_schedules WHERE tour_id = ? AND departure_date = ?");
$stmt->execute([$tourId, $departureDate]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    $stmt = $conn->prepare("SELECT tour_name, code FROM tours WHERE tour_id = ?");
    $stmt->execute([$tourId]);
    $tour = $stmt->fetch();
    $tourName = $tour['tour_name'];
    $tourCode = $tour['code'];

    echo "❌ LỖI ĐƯỢC BÁO:\n";
    echo "Lỗi! Đã tồn tại lịch trình cho tour <strong>$tourCode - $tourName</strong> vào ngày <strong>$departureDate</strong>. Không được lên lịch trình trùng!\n\n";
} else {
    echo "✓ Được phép thêm (không trùng)\n\n";
}

// Scenario 2: Thêm lịch không trùng
echo "SCENARIO 2: Cố gắng thêm lịch KHÔNG TRÙNG\n";
echo "Tour ID: 26 (Cần Thơ - VN010)\n";
$newDate = '2026-05-15';
echo "Departure Date: $newDate\n\n";

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tour_schedules WHERE tour_id = ? AND departure_date = ?");
$stmt->execute([$tourId, $newDate]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    $stmt = $conn->prepare("SELECT tour_name, code FROM tours WHERE tour_id = ?");
    $stmt->execute([$tourId]);
    $tour = $stmt->fetch();
    echo "❌ Có lỗi: Lịch đã tồn tại\n";
} else {
    echo "✓ ĐƯỢC PHÉP THÊM\n";
    echo "Sẽ tạo lịch mới cho tour 26 vào ngày $newDate\n";
}
