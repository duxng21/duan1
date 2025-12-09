<?php
require_once __DIR__ . '/../commons/env.php';
require_once __DIR__ . '/../commons/function.php';

$conn = connectDB();

try {
    // Drop foreign key constraint from tour_logs
    $conn->exec('ALTER TABLE tour_logs DROP FOREIGN KEY tour_logs_ibfk_2');
    echo "✓ Removed foreign key tour_logs_ibfk_2" . PHP_EOL;
} catch (Exception $e) {
    echo "⚠ Could not remove tour_logs_ibfk_2: " . $e->getMessage() . PHP_EOL;
}

try {
    // Drop guides table
    $conn->exec('DROP TABLE IF EXISTS guides');
    echo "✓ Dropped guides table" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Error dropping guides: " . $e->getMessage() . PHP_EOL;
}

try {
    // Drop foreign key constraint from tour_services
    $conn->exec('ALTER TABLE tour_services DROP FOREIGN KEY tour_services_ibfk_2');
    echo "✓ Removed foreign key tour_services_ibfk_2" . PHP_EOL;
} catch (Exception $e) {
    echo "⚠ Could not remove tour_services_ibfk_2: " . $e->getMessage() . PHP_EOL;
}

try {
    // Drop partners table
    $conn->exec('DROP TABLE IF EXISTS partners');
    echo "✓ Dropped partners table" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Error dropping partners: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "✓ Cleanup hoàn tất!" . PHP_EOL;
