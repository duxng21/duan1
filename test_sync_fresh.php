<?php
require 'commons/env.php';
require 'commons/function.php';

// Clear any opcode cache
if (function_exists('opcache_reset')) {
    opcache_reset();
}

$conn = connectDB();

echo "=== CHẠY SYNC VỚI DEBUG ===\n";

// Load model fresh
require_once 'admin/models/Booking.php';
$bookingModel = new Booking();

// Get current values
$stmt = $conn->prepare('SELECT num_adults, num_children, num_infants, total_amount FROM bookings WHERE booking_id = 13');
$stmt->execute();
$before = $stmt->fetch();
echo "TRƯỚC: adults={$before['num_adults']}, children={$before['num_children']}, infants={$before['num_infants']}, total={$before['total_amount']}\n\n";

// Run sync
$result = $bookingModel->syncPricesBySchedule(22);
echo "Kết quả sync: " . ($result ? 'TRUE' : 'FALSE') . "\n\n";

// Check after
$stmt = $conn->prepare('SELECT num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id = 13');
$stmt->execute();
$after = $stmt->fetch();
echo "SAU: adults={$after['num_adults']}, children={$after['num_children']}, infants={$after['num_infants']}, total={$after['total_amount']}, contact={$after['contact_name']}\n";

// Get schedule values for comparison
$stmt = $conn->prepare('SELECT num_adults, num_children, num_infants, price_adult, price_child, customer_name FROM tour_schedules WHERE schedule_id = 22');
$stmt->execute();
$schedule = $stmt->fetch();
$expectedTotal = ($schedule['num_adults'] * $schedule['price_adult']) + ($schedule['num_children'] * $schedule['price_child']) + ($schedule['num_infants'] * $schedule['price_child'] * 0.1);

echo "\nKỲ VỌNG TỪ LỊCH: adults={$schedule['num_adults']}, children={$schedule['num_children']}, infants={$schedule['num_infants']}, total=$expectedTotal, contact={$schedule['customer_name']}\n";
