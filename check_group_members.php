<?php
require_once 'commons/env.php';
require_once 'commons/function.php';

$conn = connectDB();

// Kiểm tra bảng schedule_group_members
$stmt = $conn->prepare('SELECT * FROM schedule_group_members LIMIT 10');
$stmt->execute();
$result = $stmt->fetchAll();

echo "=== KIỂM TRA BẢNG schedule_group_members ===\n";
echo "Tổng số bản ghi: " . count($result) . "\n\n";

if (count($result) > 0) {
    foreach ($result as $r) {
        echo "Schedule ID: " . $r['schedule_id'] . " | Name: " . $r['full_name'] . " | Phone: " . ($r['phone'] ?? 'N/A') . "\n";
    }
} else {
    echo "KHÔNG CÓ DỮ LIỆU trong bảng schedule_group_members!\n";
}

// Kiểm tra schedule nào đang được xem
echo "\n=== KIỂM TRA SCHEDULES ===\n";
$stmt2 = $conn->prepare('SELECT schedule_id, tour_id, departure_date, status FROM tour_schedules LIMIT 5');
$stmt2->execute();
$schedules = $stmt2->fetchAll();

foreach ($schedules as $s) {
    echo "Schedule ID: " . $s['schedule_id'] . " | Tour ID: " . $s['tour_id'] . " | Departure: " . $s['departure_date'] . " | Status: " . $s['status'] . "\n";
}
