<?php
/**
 * Script tự động chuyển đổi match expressions sang switch statements
 * Để tương thích với PHP 7.4
 */

$files = [
    'admin/models/StaffExpanded.php',
    'admin/views/home_guide.php',
    'admin/views/booking/guest_list.php',
    'admin/views/schedule/schedule_detail.php',
    'admin/views/quanlytour/list_tour.php',
    'admin/views/staff/list_staff.php',
    'admin/views/quote/list_quotes.php',
    'admin/views/quote/view_quote.php',
    'admin/views/quanlytour/edit_tour_advanced.php',
    'admin/views/booking/booking_detail.php',
    'admin/views/schedule/list_schedule.php',
    'admin/views/special_notes/list_notes_schedule.php',
    'admin/views/staff/staff_detail.php',
    'admin/views/schedule/guide_notifications.php',
    'admin/views/schedule/staff_assignments.php',
    'admin/views/auth/list_users.php',
    'admin/views/schedule/calendar_view.php',
];

$converted = 0;
$errors = 0;

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;

    if (!file_exists($path)) {
        echo "❌ File không tồn tại: $file\n";
        $errors++;
        continue;
    }

    $content = file_get_contents($path);
    $original = $content;

    // Pattern để tìm match expressions
    $pattern = '/\$(\w+)\s*=\s*match\s*\(([^)]+)\)\s*\{([^}]+)\};/s';

    $content = preg_replace_callback($pattern, function ($matches) {
        $varName = $matches[1];
        $matchVar = $matches[2];
        $cases = $matches[3];

        // Convert cases
        $switchCases = '';
        preg_match_all("/'([^']+)'\s*=>\s*'([^']+)',?/", $cases, $caseMatches, PREG_SET_ORDER);

        foreach ($caseMatches as $case) {
            $caseValue = $case[1];
            $returnValue = $case[2];

            if ($caseValue === 'default') {
                $switchCases .= "            default:\n";
                $switchCases .= "                \$$varName = '$returnValue';\n";
                $switchCases .= "                break;\n";
            } else {
                $switchCases .= "            case '$caseValue':\n";
                $switchCases .= "                \$$varName = '$returnValue';\n";
                $switchCases .= "                break;\n";
            }
        }

        // Build switch statement
        $switch = "switch ($matchVar) {\n$switchCases        }";

        return $switch;
    }, $content);

    // Pattern cho match với echo
    $pattern2 = '/echo\s+match\s*\(([^)]+)\)\s*\{([^}]+)\};/s';

    $content = preg_replace_callback($pattern2, function ($matches) {
        $matchVar = $matches[1];
        $cases = $matches[2];

        // Convert cases
        $switchCases = '';
        preg_match_all("/'([^']+)'\s*=>\s*'([^']+)',?/", $cases, $caseMatches, PREG_SET_ORDER);

        foreach ($caseMatches as $case) {
            $caseValue = $case[1];
            $returnValue = $case[2];

            if ($caseValue === 'default') {
                $switchCases .= "            default:\n";
                $switchCases .= "                echo '$returnValue';\n";
                $switchCases .= "                break;\n";
            } else {
                $switchCases .= "            case '$caseValue':\n";
                $switchCases .= "                echo '$returnValue';\n";
                $switchCases .= "                break;\n";
            }
        }

        return "switch ($matchVar) {\n$switchCases        }";
    }, $content);

    if ($content !== $original) {
        // Backup original
        copy($path, $path . '.bak');

        // Write converted content
        file_put_contents($path, $content);
        echo "✅ Đã chuyển đổi: $file\n";
        $converted++;
    } else {
        echo "⚠️  Không có thay đổi: $file\n";
    }
}

echo "\n=== KẾT QUẢ ===\n";
echo "Đã chuyển đổi: $converted files\n";
echo "Lỗi: $errors files\n";
echo "\nĐể khôi phục file gốc, xóa phần .bak\n";
