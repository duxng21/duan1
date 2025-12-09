<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TRƯỚC KHI CẬP NHẬT ===\n";
$stmt = $conn->prepare('SELECT booking_id, num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id = 13');
$stmt->execute();
$before = $stmt->fetch();
echo "Booking #13:\n";
echo "  - Số người lớn: {$before['num_adults']}\n";
echo "  - Số trẻ em: {$before['num_children']}\n";
echo "  - Số em bé: {$before['num_infants']}\n";
echo "  - Tổng tiền: {$before['total_amount']}\n";
echo "  - Tên liên hệ: {$before['contact_name']}\n";

// Thay đổi số lượng khách và giá trong lịch
$newAdults = 20;
$newChildren = 15;
$newInfants = 3;
$newPriceAdult = 12000000;
$newPriceChild = 6000000;
$newContact = "Trần Thị B";

echo "\n=== CẬP NHẬT LỊCH #22 ===\n";
echo "Số lượng mới: $newAdults người lớn, $newChildren trẻ em, $newInfants em bé\n";
echo "Giá mới: $newPriceAdult/người lớn, $newPriceChild/trẻ em\n";
echo "Liên hệ mới: $newContact\n";

$stmt = $conn->prepare("UPDATE tour_schedules SET 
    num_adults = ?,
    num_children = ?,
    num_infants = ?,
    price_adult = ?, 
    price_child = ?,
    customer_name = ?
    WHERE schedule_id = 22");
$stmt->execute([$newAdults, $newChildren, $newInfants, $newPriceAdult, $newPriceChild, $newContact]);

// Chạy sync
require 'admin/models/Booking.php';
$bookingModel = new Booking();
$result = $bookingModel->syncPricesBySchedule(22);
echo "\nKết quả đồng bộ: " . ($result ? '✓ THÀNH CÔNG' : '✗ THẤT BẠI') . "\n";

echo "\n=== SAU KHI ĐỒNG BỘ ===\n";
$stmt = $conn->prepare('SELECT booking_id, num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id = 13');
$stmt->execute();
$after = $stmt->fetch();
echo "Booking #13:\n";
echo "  - Số người lớn: {$after['num_adults']}\n";
echo "  - Số trẻ em: {$after['num_children']}\n";
echo "  - Số em bé: {$after['num_infants']}\n";
echo "  - Tổng tiền: {$after['total_amount']}\n";
echo "  - Tên liên hệ: {$after['contact_name']}\n";

// Tính toán kỳ vọng
$expectedTotal = ($newAdults * $newPriceAdult) + ($newChildren * $newPriceChild) + ($newInfants * $newPriceChild * 0.1);
echo "\n=== KIỂM TRA ===\n";
echo "Tổng tiền kỳ vọng: $expectedTotal\n";
echo "Tổng tiền thực tế: {$after['total_amount']}\n";
echo "Số người lớn: " . ($after['num_adults'] == $newAdults ? '✓' : '✗') . " (kỳ vọng: $newAdults)\n";
echo "Số trẻ em: " . ($after['num_children'] == $newChildren ? '✓' : '✗') . " (kỳ vọng: $newChildren)\n";
echo "Số em bé: " . ($after['num_infants'] == $newInfants ? '✓' : '✗') . " (kỳ vọng: $newInfants)\n";
echo "Tổng tiền: " . ($after['total_amount'] == $expectedTotal ? '✓' : '✗') . "\n";
echo "Liên hệ: " . ($after['contact_name'] == $newContact ? '✓' : '✗') . " (kỳ vọng: $newContact)\n";
