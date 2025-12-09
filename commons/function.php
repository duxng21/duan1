<?php

// Kết nối CSDL qua PDO
function connectDB()
{
    // Kết nối CSDL
    $host = DB_HOST;
    $port = DB_PORT;
    $dbname = DB_NAME;

    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", DB_USERNAME, DB_PASSWORD);

        // cài đặt chế độ báo lỗi là xử lý ngoại lệ
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // cài đặt chế độ trả dữ liệu
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Set UTF-8 encoding
        $conn->exec("SET NAMES 'utf8mb4'");
        $conn->exec("SET CHARACTER SET utf8mb4");

        return $conn;
    } catch (PDOException $e) {
        echo ("Connection failed: " . $e->getMessage());
    }
}

function uploadFile($file, $folderSave)
{
    $file_upload = $file;
    $pathStorage = $folderSave . rand(10000, 99999) . $file_upload['name'];

    $tmp_file = $file_upload['tmp_name'];
    $pathSave = PATH_ROOT . $pathStorage; // Đường dãn tuyệt đối của file

    if (move_uploaded_file($tmp_file, $pathSave)) {
        return $pathStorage;
    }
    return null;
}

function deleteFile($file)
{
    $pathDelete = PATH_ROOT . $file;
    if (file_exists($pathDelete)) {
        unlink($pathDelete); // Hàm unlink dùng để xóa file
    }
}

/**
 * Đếm số thông báo chưa đọc của HDV
 * Tạm thời trả về số tour sắp khởi hành trong 3 ngày
 * TODO: Thay thế bằng query từ bảng notifications khi có
 */
function getUnreadNotificationCount($staff_id = null)
{
    if (!$staff_id && isset($_SESSION['staff_id'])) {
        $staff_id = $_SESSION['staff_id'];
    }

    if (!$staff_id) {
        return 0;
    }

    try {
        $conn = connectDB();
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d', strtotime('+3 days'));

        $sql = "SELECT COUNT(DISTINCT ts.schedule_id) as total
                FROM tour_schedules ts
                INNER JOIN schedule_staff ss ON ts.schedule_id = ss.schedule_id
                WHERE ss.staff_id = ? 
                AND ts.departure_date BETWEEN ? AND ?
                AND ts.status != 'Cancelled'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$staff_id, $from_date, $to_date]);
        $result = $stmt->fetch();
        return $result ? (int) $result['total'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Chuyển đổi timestamp thành dạng "... ago" (ví dụ: "2 giờ trước", "3 ngày trước")
 * @param string $datetime - Datetime string hoặc timestamp
 * @return string
 */
function timeAgo($datetime)
{
    if (empty($datetime)) {
        return 'Không xác định';
    }

    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    if (!$timestamp) {
        return 'Không xác định';
    }

    $time_diff = time() - $timestamp;

    if ($time_diff < 1) {
        return 'Vừa xong';
    }

    $time_rules = [
        12 * 30 * 24 * 60 * 60 => 'năm',
        30 * 24 * 60 * 60 => 'tháng',
        24 * 60 * 60 => 'ngày',
        60 * 60 => 'giờ',
        60 => 'phút',
        1 => 'giây'
    ];

    foreach ($time_rules as $secs => $str) {
        $d = $time_diff / $secs;

        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ' trước';
        }
    }

    return 'Vừa xong';
}
