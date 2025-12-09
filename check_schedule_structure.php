<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TOUR_SCHEDULES TABLE STRUCTURE ===\n";
$stmt = $conn->query("DESCRIBE tour_schedules");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== TOURS TABLE STRUCTURE ===\n";
$stmt = $conn->query("DESCRIBE tours");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
