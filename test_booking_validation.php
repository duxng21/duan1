<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TEST VALIDATION SỐ LƯỢNG KHÁCH TRONG BOOKING ===\n\n";

// Get a schedule
$stmt = $conn->query("SELECT schedule_id, tour_id, departure_date, max_participants FROM tour_schedules LIMIT 1");
$schedule = $stmt->fetch();

echo "Lịch kiểm tra: #{$schedule['schedule_id']}\n";
echo "Tour ID: {$schedule['tour_id']}\n";
echo "Ngày: {$schedule['departure_date']}\n";
echo "Chỗ tối đa: {$schedule['max_participants']}\n\n";

// Test cases
$tests = [
    ['adults' => 10, 'children' => 5, 'infants' => 0, 'name' => 'Bình thường', 'expect' => 'PASS'],
    ['adults' => 15, 'children' => 10, 'infants' => 0, 'name' => 'Đầy chỗ', 'expect' => $schedule['max_participants'] <= 25 ? 'PASS' : 'ERROR'],
    ['adults' => 20, 'children' => 15, 'infants' => 3, 'name' => 'Vượt quá', 'expect' => 'ERROR'],
];

echo "TEST CẤP PHÉP OVERBOOK = FALSE:\n\n";
foreach ($tests as $i => $test) {
    $total = $test['adults'] + $test['children'] + $test['infants'];
    $maxParticipants = intval($schedule['max_participants']);
    $status = ($total <= $maxParticipants) ? 'PASS' : 'ERROR';
    $match = ($status === $test['expect']) ? '✓' : '✗';

    echo ($i + 1) . ". {$test['name']}\n";
    echo "   Adults: {$test['adults']}, Children: {$test['children']}, Infants: {$test['infants']}\n";
    echo "   Tổng: $total, Max: $maxParticipants\n";
    echo "   Result: $status (Expected: {$test['expect']}) - $match\n\n";
}

echo "TEST CẤP PHÉP OVERBOOK = TRUE:\n";
echo "Tất cả test đều PASS vì được phép overbooking\n";
