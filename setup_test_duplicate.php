<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== SETUP TEST DATA ===\n";

// Create another schedule for same tour
$stmt = $conn->prepare("INSERT INTO tour_schedules 
(tour_id, departure_date, return_date, meeting_point, meeting_time, price_adult, price_child, max_participants, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$newDate = '2026-01-20';
$result = $stmt->execute([
    26,  // tour_id (Cần Thơ)
    $newDate,  // departure_date
    '2026-01-22',  // return_date
    'Hà Nội',  // meeting_point
    '07:00:00',  // meeting_time
    15000000,  // price_adult
    7000000,  // price_child
    30,  // max_participants
    'Open'  // status
]);

echo "Created new schedule for date: $newDate\n\n";

echo "=== TEST KIỂM TRA TRÙNG KHI CẬP NHẬT ===\n\n";

// Get both schedules
$stmt = $conn->query("SELECT schedule_id, tour_id, departure_date FROM tour_schedules WHERE tour_id = 26 ORDER BY departure_date");
$schedules = $stmt->fetchAll();

echo "Lịch đã tạo:\n";
foreach ($schedules as $s) {
    echo "  Schedule #{$s['schedule_id']}: Tour {$s['tour_id']}, Date {$s['departure_date']}\n";
}

if (count($schedules) >= 2) {
    echo "\n";
    echo "TEST: Cập nhật Lịch #{$schedules[0]['schedule_id']} sang tour+date của Lịch #{$schedules[1]['schedule_id']}\n";

    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM tour_schedules 
        WHERE tour_id = ? AND departure_date = ? AND schedule_id != ?
    ");
    $stmt->execute([$schedules[1]['tour_id'], $schedules[1]['departure_date'], $schedules[0]['schedule_id']]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "❌ LỖI: Đã tồn tại lịch khác với tour {$schedules[1]['tour_id']} vào ngày {$schedules[1]['departure_date']}\n";
        echo "Kết luận: Không được phép cập nhật (báo lỗi)\n";
    } else {
        echo "✓ OK: Không trùng\n";
    }
}
