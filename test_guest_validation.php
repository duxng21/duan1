<?php
require 'commons/env.php';
require 'commons/function.php';

echo "=== TEST VALIDATION SỐ LƯỢNG KHÁCH ===\n\n";

// Test cases
$testCases = [
    ['name' => 'Hop le', 'max' => 30, 'adults' => 15, 'children' => 10, 'infants' => 0, 'expect' => 'PASS'],
    ['name' => 'Hop le - Day cho', 'max' => 30, 'adults' => 20, 'children' => 10, 'infants' => 0, 'expect' => 'PASS'],
    ['name' => 'Loi - Vuot qua 1', 'max' => 30, 'adults' => 20, 'children' => 11, 'infants' => 0, 'expect' => 'ERROR'],
    ['name' => 'Loi - Vuot qua nhieu', 'max' => 30, 'adults' => 25, 'children' => 20, 'infants' => 10, 'expect' => 'ERROR'],
    ['name' => 'Hop le - Co em be', 'max' => 50, 'adults' => 30, 'children' => 15, 'infants' => 5, 'expect' => 'PASS'],
];

foreach ($testCases as $i => $test) {
    $total = $test['adults'] + $test['children'] + $test['infants'];
    $status = ($total <= $test['max']) ? 'PASS' : 'ERROR';
    $match = ($status === $test['expect']) ? 'YES' : 'NO';

    echo ($i + 1) . ". {$test['name']}\n";
    echo "   Max: {$test['max']}, Adults: {$test['adults']}, Children: {$test['children']}, Infants: {$test['infants']}\n";
    echo "   Total: $total (Expecting: {$test['expect']}, Got: $status) Match: $match\n";
    echo "\n";
}
