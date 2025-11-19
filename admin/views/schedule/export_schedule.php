<?php
// Export báo cáo lịch khởi hành ra Excel/CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Bao_cao_lich_khoi_hanh_' . date('Y-m-d') . '.csv');

// Tạo output stream
$output = fopen('php://output', 'w');

// Thêm BOM để Excel nhận diện UTF-8
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header của file CSV
fputcsv($output, [
    'Mã lịch',
    'Tên tour',
    'Mã tour',
    'Ngày khởi hành',
    'Ngày kết thúc',
    'Điểm tập trung',
    'Giờ tập trung',
    'Số người tối đa',
    'Đã đăng ký',
    'Còn trống',
    'Giá người lớn',
    'Giá trẻ em',
    'Trạng thái',
    'Tổng booking',
    'Tổng khách',
    'Ghi chú'
]);

// Dữ liệu
if (!empty($schedules)) {
    foreach ($schedules as $schedule) {
        $slots_available = ($schedule['max_participants'] ?? 0) - ($schedule['current_participants'] ?? 0);

        fputcsv($output, [
            $schedule['schedule_id'] ?? '',
            $schedule['tour_name'] ?? '',
            $schedule['tour_code'] ?? '',
            $schedule['departure_date'] ?? '',
            $schedule['return_date'] ?? '',
            $schedule['meeting_point'] ?? '',
            $schedule['meeting_time'] ?? '',
            $schedule['max_participants'] ?? 0,
            $schedule['current_participants'] ?? 0,
            $slots_available,
            number_format($schedule['price_adult'] ?? 0, 0, ',', '.') . ' VNĐ',
            number_format($schedule['price_child'] ?? 0, 0, ',', '.') . ' VNĐ',
            $schedule['status'] ?? '',
            $schedule['total_bookings'] ?? 0,
            $schedule['total_guests'] ?? 0,
            $schedule['notes'] ?? ''
        ]);
    }
}

fclose($output);
exit();
