<?php
require 'commons/env.php';
require 'commons/function.php';

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['tour_id'] = '26';  // Tour đã có lịch
$_POST['departure_date'] = '2025-12-08';  // Ngày đã có lịch (sẽ trùng)
$_POST['meeting_point'] = 'Test Point';
$_POST['meeting_time'] = '08:00:00';
$_POST['max_participants'] = 30;
$_POST['num_adults'] = 15;
$_POST['num_children'] = 10;
$_POST['num_infants'] = 0;
$_POST['price_adult'] = 5000000;
$_POST['price_child'] = 2500000;
$_POST['status'] = 'Open';
$_POST['notes'] = 'Test';

// Start session
session_start();

// Load controller
require_once 'admin/models/Tour.php';
require_once 'admin/models/TourSchedule.php';
require_once 'admin/controllers/ScheduleController.php';

$controller = new ScheduleController();

// Simulate the request
ob_start();
$controller->StoreSchedule();
$output = ob_get_clean();

// Check session error
echo "=== KẾT QUẢ TEST THÊM LỊCH TRÙNG ===\n";
if (isset($_SESSION['error'])) {
    echo "✓ LỖI ĐƯỢC BÁO: {$_SESSION['error']}\n";
} else {
    echo "✗ KHÔNG CÓ LỖI: Hệ thống không phát hiện trùng!\n";
}
