<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

// First check schedule data
echo "=== KIỂM TRA DỮ LIỆU LỊCH #22 ===\n";
$stmt = $conn->prepare('SELECT schedule_id, tour_id, departure_date, num_adults, num_children, num_infants, price_adult, price_child, customer_name FROM tour_schedules WHERE schedule_id = 22');
$stmt->execute();
$schedule = $stmt->fetch();
var_dump($schedule);

echo "\n=== KIỂM TRA DỮ LIỆU BOOKING #13 ===\n";
$stmt = $conn->prepare('SELECT booking_id, tour_id, tour_date, num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id = 13');
$stmt->execute();
$booking = $stmt->fetch();
var_dump($booking);

echo "\n=== KIỂM TRA ĐIỀU KIỆN JOIN ===\n";
echo "Schedule tour_id: {$schedule['tour_id']}, departure_date: {$schedule['departure_date']}\n";
echo "Booking tour_id: {$booking['tour_id']}, tour_date: {$booking['tour_date']}\n";
echo "Match: " . ($schedule['tour_id'] == $booking['tour_id'] && $schedule['departure_date'] == $booking['tour_date'] ? 'YES' : 'NO') . "\n";

// Now test the sync with direct UPDATE to verify it works
echo "\n=== TEST CẬP NHẬT TRỰC TIẾP ===\n";
$testAdults = 25;
$testChildren = 18;
$testInfants = 5;
$testTotal = 999999;
$testContact = "Test Direct Update";

$stmt = $conn->prepare("UPDATE bookings SET 
    num_adults = ?,
    num_children = ?,
    num_infants = ?,
    total_amount = ?,
    contact_name = ?
    WHERE booking_id = 13");
$result = $stmt->execute([$testAdults, $testChildren, $testInfants, $testTotal, $testContact]);
echo "Direct update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

$stmt = $conn->prepare('SELECT num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id = 13');
$stmt->execute();
$afterDirect = $stmt->fetch();
echo "After direct update:\n";
var_dump($afterDirect);
