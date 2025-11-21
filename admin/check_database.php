<?php
// Script kiểm tra database đã mở rộng chưa
require_once '../commons/env.php';
require_once '../commons/function.php';

try {
    $conn = connectDB();

    // Kiểm tra cột date_of_birth trong bảng staff
    $result = $conn->query("SHOW COLUMNS FROM staff LIKE 'date_of_birth'");
    $isExpanded = $result->rowCount() > 0;

    if ($isExpanded) {
        echo "✓ Database đã được mở rộng\n\n";

        // Kiểm tra các bảng mới
        $tables = [
            'staff_certificates',
            'staff_languages',
            'staff_tour_history',
            'staff_time_off',
            'staff_evaluations',
            'staff_experiences',
            'staff_notifications'
        ];

        echo "Kiểm tra các bảng mới:\n";
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            $exists = $result->rowCount() > 0;
            echo ($exists ? "✓" : "✗") . " $table\n";
        }
    } else {
        echo "✗ Database chưa được mở rộng. Cần chạy expand_staff_management.sql\n";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
