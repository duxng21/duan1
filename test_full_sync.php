<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== BEFORE UPDATE ===\n";
$stmt = $conn->prepare('SELECT booking_id, tour_id, tour_date, total_amount, contact_name, contact_phone, contact_email FROM bookings WHERE booking_id = 13');
$stmt->execute();
$before = $stmt->fetch();
var_dump($before);

// Update schedule with new contact info
$newContact = "Nguyễn Văn Test";
$newPhone = "0999888777";
$newEmail = "test@example.com";
$newPriceAdult = 18000000;
$newPriceChild = 9000000;

echo "\n=== UPDATING SCHEDULE #22 ===\n";
$stmt = $conn->prepare("UPDATE tour_schedules SET 
    price_adult = ?, 
    price_child = ?,
    customer_name = ?,
    customer_phone = ?,
    customer_email = ?
    WHERE schedule_id = 22");
$stmt->execute([$newPriceAdult, $newPriceChild, $newContact, $newPhone, $newEmail]);
echo "Schedule updated!\n";

// Run sync
require 'admin/models/Booking.php';
$bookingModel = new Booking();
$result = $bookingModel->syncPricesBySchedule(22);
echo "Sync result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

echo "\n=== AFTER SYNC ===\n";
$stmt = $conn->prepare('SELECT booking_id, tour_id, tour_date, total_amount, contact_name, contact_phone, contact_email FROM bookings WHERE booking_id = 13');
$stmt->execute();
$after = $stmt->fetch();
var_dump($after);

// Calculate expected
$expected = (15 * $newPriceAdult) + (10 * $newPriceChild);
echo "\n=== VERIFICATION ===\n";
echo "Expected total: $expected\n";
echo "Actual total: " . $after['total_amount'] . "\n";
echo "Total match: " . ($expected == $after['total_amount'] ? 'YES ✓' : 'NO ✗') . "\n";
echo "Contact name match: " . ($after['contact_name'] == $newContact ? 'YES ✓' : 'NO ✗') . " (expected: $newContact, got: {$after['contact_name']})\n";
echo "Contact phone match: " . ($after['contact_phone'] == $newPhone ? 'YES ✓' : 'NO ✗') . " (expected: $newPhone, got: {$after['contact_phone']})\n";
echo "Contact email match: " . ($after['contact_email'] == $newEmail ? 'YES ✓' : 'NO ✗') . " (expected: $newEmail, got: {$after['contact_email']})\n";
