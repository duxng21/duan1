<?php
require_once __DIR__ . '/../commons/env.php';
require_once __DIR__ . '/../commons/function.php';

// Models
require_once __DIR__ . '/models/Tour.php';
require_once __DIR__ . '/models/TourSchedule.php';
require_once __DIR__ . '/models/Staff.php';
require_once __DIR__ . '/models/Booking.php';
require_once __DIR__ . '/models/User.php';

$conn = connectDB();

function ensureRole($code, $name)
{
    $conn = connectDB();
    $stmt = $conn->prepare('SELECT role_id FROM roles WHERE role_code = ? LIMIT 1');
    try {
        $stmt->execute([$code]);
        $id = $stmt->fetchColumn();
        if (!$id) {
            $ins = $conn->prepare('INSERT INTO roles (role_code, role_name) VALUES (?,?)');
            $ins->execute([$code, $name]);
            return (int) $conn->lastInsertId();
        }
        return (int) $id;
    } catch (Exception $e) {
        return 0;
    }
}

function ensureAdminUser()
{
    $conn = connectDB();
    try {
        $count = (int) $conn->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0)
            return;
    } catch (Exception $e) {
        return;
    }

    $adminRoleId = ensureRole('ADMIN', 'Quản trị');
    ensureRole('GUIDE', 'Hướng dẫn viên');
    if ($adminRoleId) {
        $hash = password_hash('Admin@123', PASSWORD_DEFAULT);
        $ins = $conn->prepare('INSERT INTO users (username,password,full_name,email,phone,avatar,role_id,staff_id,status,login_attempts,last_login,created_at) VALUES (?,?,?,?,?,?,?,?,?,0,NULL,NOW())');
        try {
            $ins->execute(['admin', $hash, 'Quản trị viên', 'admin@example.com', null, null, $adminRoleId, null, 'Active']);
        } catch (Exception $e) {
        }
    }
}

function tableExists($name)
{
    $conn = connectDB();
    try {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$name]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function upsertCategory($name)
{
    if (!tableExists('tour_categories'))
        return null;
    $conn = connectDB();
    $sel = $conn->prepare('SELECT category_id FROM tour_categories WHERE category_name = ? LIMIT 1');
    $sel->execute([$name]);
    $id = $sel->fetchColumn();
    if ($id)
        return (int) $id;
    $ins = $conn->prepare('INSERT INTO tour_categories (category_name, status) VALUES (?,1)');
    $ins->execute([$name]);
    return (int) $conn->lastInsertId();
}

function upsertSupplier($name, $type)
{
    if (!tableExists('tour_suppliers'))
        return null;
    $conn = connectDB();
    $sel = $conn->prepare('SELECT supplier_id FROM tour_suppliers WHERE supplier_name = ? LIMIT 1');
    $sel->execute([$name]);
    $id = $sel->fetchColumn();
    if ($id)
        return (int) $id;
    $ins = $conn->prepare('INSERT INTO tour_suppliers (supplier_name, supplier_type, phone, email, status) VALUES (?,?,?,?,1)');
    $ins->execute([$name, $type, '0900000000', 'supplier@example.com']);
    return (int) $conn->lastInsertId();
}

function seedTours()
{
    $tourModel = new Tour();
    $conn = connectDB();
    try {
        $count = (int) $conn->query('SELECT COUNT(*) FROM tours')->fetchColumn();
    } catch (Exception $e) {
        return ['created' => 0];
    }
    if ($count > 0)
        return ['created' => 0];

    $catVN = upsertCategory('Nội địa');
    $catQT = upsertCategory('Quốc tế');

    $created = 0;
    $samples = [
        ['category_id' => $catVN, 'tour_name' => 'Hà Nội - Hạ Long 3N2Đ', 'code' => 'HNHL3N2D', 'tour_price' => 3990000, 'tour_image' => 'uploads/sample/halong.jpg'],
        ['category_id' => $catQT, 'tour_name' => 'Bangkok - Pattaya 4N3Đ', 'code' => 'BKPP4N3D', 'tour_price' => 5990000, 'tour_image' => 'uploads/sample/bangkok.jpg']
    ];

    foreach ($samples as $s) {
        try {
            $tourModel->create($s);
            $created++;
        } catch (Exception $e) {
        }
    }
    return ['created' => $created];
}

function seedSchedules()
{
    if (!tableExists('tour_schedules'))
        return ['created' => 0];
    $conn = connectDB();
    $count = (int) $conn->query('SELECT COUNT(*) FROM tour_schedules')->fetchColumn();
    if ($count > 0)
        return ['created' => 0];

    $tours = $conn->query('SELECT tour_id FROM tours ORDER BY tour_id ASC')->fetchAll();
    if (!$tours)
        return ['created' => 0];
    $sched = new TourSchedule();
    $created = 0;
    $i = 0;
    foreach ($tours as $t) {
        $i++;
        $dep = date('Y-m-d', strtotime("+{$i} week"));
        $ret = date('Y-m-d', strtotime($dep . ' +3 days'));
        try {
            $id = $sched->createSchedule([
                'tour_id' => $t['tour_id'],
                'departure_date' => $dep,
                'return_date' => $ret,
                'meeting_point' => 'Văn phòng công ty',
                'meeting_time' => '07:30',
                'max_participants' => 30,
                'price_adult' => 3990000,
                'price_child' => 2990000,
                'status' => 'Open',
                'notes' => null
            ]);
            if ($id)
                $created++;
        } catch (Exception $e) {
        }
    }
    return ['created' => $created];
}

function seedStaff()
{
    $conn = connectDB();
    if (!tableExists('staff'))
        return ['created' => 0];
    $count = (int) $conn->query('SELECT COUNT(*) FROM staff')->fetchColumn();
    if ($count > 0)
        return ['created' => 0];
    $staff = new Staff();
    $created = 0;
    $rows = [
        ['full_name' => 'Nguyễn Văn A', 'gender' => 'Nam', 'phone' => '0911111111', 'email' => 'a@example.com', 'staff_type' => 'Guide', 'languages' => 'Vietnamese, English', 'experience_years' => 3, 'status' => 1],
        ['full_name' => 'Trần Thị B', 'gender' => 'Nữ', 'phone' => '0922222222', 'email' => 'b@example.com', 'staff_type' => 'Manager', 'languages' => 'Vietnamese', 'experience_years' => 5, 'status' => 1]
    ];
    foreach ($rows as $r) {
        try {
            if ($staff->create($r))
                $created++;
        } catch (Exception $e) {
        }
    }
    return ['created' => $created];
}

function seedAssignments()
{
    if (!tableExists('schedule_staff'))
        return ['created' => 0];
    $conn = connectDB();
    $count = (int) $conn->query('SELECT COUNT(*) FROM schedule_staff')->fetchColumn();
    if ($count > 0)
        return ['created' => 0];
    $sched = new TourSchedule();
    $schedules = $conn->query('SELECT schedule_id FROM tour_schedules ORDER BY schedule_id ASC')->fetchAll();
    $guides = $conn->query("SELECT staff_id FROM staff WHERE staff_type='Guide' AND status=1 ORDER BY staff_id ASC")->fetchAll();
    if (!$schedules || !$guides)
        return ['created' => 0];
    $created = 0;
    $i = 0;
    foreach ($schedules as $sc) {
        $guide = $guides[$i % count($guides)];
        try {
            if ($sched->assignStaff($sc['schedule_id'], $guide['staff_id'], 'Hướng dẫn viên'))
                $created++;
        } catch (Exception $e) {
        }
        $i++;
    }
    return ['created' => $created];
}

function seedBookings()
{
    if (!tableExists('bookings'))
        return ['created' => 0];
    $conn = connectDB();
    $count = (int) $conn->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    if ($count > 0)
        return ['created' => 0];
    $booking = new Booking();
    $tours = $conn->query('SELECT tour_id FROM tours ORDER BY tour_id ASC')->fetchAll();
    if (!$tours)
        return ['created' => 0];
    $created = 0;
    foreach ($tours as $t) {
        try {
            $id = $booking->create([
                'tour_id' => $t['tour_id'],
                'tour_date' => date('Y-m-d', strtotime('+10 days')),
                'booking_type' => 'Cá nhân',
                'contact_name' => 'Khách demo',
                'contact_phone' => '0900000000',
                'contact_email' => 'guest@example.com',
                'num_adults' => 2,
                'num_children' => 1,
                'num_infants' => 0,
                'special_requests' => null,
                'status' => 'Chờ xác nhận',
                'total_amount' => 4990000,
                'details' => [
                    ['service_name' => 'Vé tham quan', 'quantity' => 3, 'unit_price' => 200000],
                    ['service_name' => 'Khách sạn', 'quantity' => 1, 'unit_price' => 1500000]
                ]
            ]);
            if ($id)
                $created++;
        } catch (Exception $e) {
        }
    }
    return ['created' => $created];
}

// Run seeding
$results = [];
ensureAdminUser();
$results['tours'] = seedTours();
$results['schedules'] = seedSchedules();
$results['staff'] = seedStaff();
$results['assign'] = seedAssignments();
$results['bookings'] = seedBookings();

// Output
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Run Seeder</title><style>body{font-family:Arial;padding:20px;background:#f5f5f5}.box{max-width:800px;margin:0 auto;background:#fff;padding:20px;border-radius:8px}h1{margin-top:0}.ok{color:green}.muted{color:#666}</style></head><body><div class='box'>";
echo "<h1>🚀 Database Seeder</h1>";
echo "<p class='muted'>Chạy seeding dữ liệu mẫu cho hệ thống.</p>";
echo "<ul>";
echo "<li>Users/Roles: đảm bảo có admin nếu trống</li>";
echo "<li>Tours: tạo mới: " . $results['tours']['created'] . "</li>";
echo "<li>Schedules: tạo mới: " . $results['schedules']['created'] . "</li>";
echo "<li>Staff: tạo mới: " . $results['staff']['created'] . "</li>";
echo "<li>Assignments: tạo mới: " . $results['assign']['created'] . "</li>";
echo "<li>Bookings: tạo mới: " . $results['bookings']['created'] . "</li>";
echo "</ul>";
echo "<p><a href='index.php'>← Về trang Admin</a></p>";
echo "</div></body></html>";
