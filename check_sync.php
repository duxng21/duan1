<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

// Get booking #13 info
$stmt = $conn->prepare('SELECT booking_id, tour_id, tour_date, num_adults, num_children, num_infants, total_amount FROM bookings WHERE booking_id = 13');
$stmt->execute();
$booking = $stmt->fetch();
echo "=== BOOKING #13 ===\n";
var_dump($booking);

// Get schedule for this tour/date
$stmt = $conn->prepare('SELECT schedule_id, tour_id, departure_date, price_adult, price_child FROM tour_schedules WHERE tour_id = ? AND departure_date = ?');
$stmt->execute([$booking['tour_id'], $booking['tour_date']]);
$schedule = $stmt->fetch();
echo "\n=== SCHEDULE ===\n";
var_dump($schedule);

// Calculate what total should be
if ($schedule) {
    $expected = ($booking['num_adults'] * $schedule['price_adult']) + ($booking['num_children'] * $schedule['price_child']) + ($booking['num_infants'] * $schedule['price_child'] * 0.1);
    echo "\n=== CALCULATION ===\n";
    echo "num_adults: " . $booking['num_adults'] . " × " . $schedule['price_adult'] . " = " . ($booking['num_adults'] * $schedule['price_adult']) . "\n";
    echo "num_children: " . $booking['num_children'] . " × " . $schedule['price_child'] . " = " . ($booking['num_children'] * $schedule['price_child']) . "\n";
    echo "num_infants: " . $booking['num_infants'] . " × " . ($schedule['price_child'] * 0.1) . " = " . ($booking['num_infants'] * $schedule['price_child'] * 0.1) . "\n";
    echo "Expected total: $expected\n";
    echo "Current total: " . $booking['total_amount'] . "\n";
    echo "Match: " . ($expected == $booking['total_amount'] ? 'YES' : 'NO') . "\n";
}
