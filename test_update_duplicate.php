<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TEST KIỂM TRA TRÙNG KHI CẬP NHẬT LỊCH ===\n\n";

// Get an existing schedule
$stmt = $conn->query("SELECT schedule_id, tour_id, departure_date FROM tour_schedules LIMIT 1");
$schedule1 = $stmt->fetch();
echo "Lịch #1 (hiện tại): schedule_id={$schedule1['schedule_id']}, tour={$schedule1['tour_id']}, date={$schedule1['departure_date']}\n";

// Get another schedule
$stmt = $conn->query("SELECT schedule_id, tour_id, departure_date FROM tour_schedules LIMIT 1,1");
$schedule2 = $stmt->fetch();
echo "Lịch #2: schedule_id={$schedule2['schedule_id']}, tour={$schedule2['tour_id']}, date={$schedule2['departure_date']}\n\n";

// Test 1: Cố gắng cập nhật schedule1 thành trùng với schedule2
echo "TEST 1: Cập nhật Lịch #1 sang tour+date của Lịch #2\n";
$stmt = $conn->prepare("
    SELECT COUNT(*) as count FROM tour_schedules 
    WHERE tour_id = ? AND departure_date = ? AND schedule_id != ?
");
$stmt->execute([$schedule2['tour_id'], $schedule2['departure_date'], $schedule1['schedule_id']]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    echo "❌ LỖI: Đã tồn tại lịch khác với tour {$schedule2['tour_id']} vào ngày {$schedule2['departure_date']}\n\n";
} else {
    echo "✓ OK: Không trùng, được phép cập nhật\n\n";
}

// Test 2: Cập nhật về tour+date của chính nó (không trùng)
echo "TEST 2: Cập nhật Lịch #1 giữ nguyên tour+date\n";
$stmt = $conn->prepare("
    SELECT COUNT(*) as count FROM tour_schedules 
    WHERE tour_id = ? AND departure_date = ? AND schedule_id != ?
");
$stmt->execute([$schedule1['tour_id'], $schedule1['departure_date'], $schedule1['schedule_id']]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    echo "❌ Có lịch khác trùng\n";
} else {
    echo "✓ OK: Không trùng, được phép cập nhật\n";
}
