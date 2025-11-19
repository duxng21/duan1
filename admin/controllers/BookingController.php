<?php

class BookingController
{
    public $bookingModel;
    public $tourModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->tourModel = new Tour();
    }

    // ==================== DANH SÁCH BOOKING ====================

    public function ListBooking()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $bookings = $this->bookingModel->getAll($filters);
        $statistics = $this->bookingModel->getStatistics();
        $tours = $this->tourModel->getAll();

        require_once './views/booking/list_booking.php';
    }

    // ==================== THÊM BOOKING ====================

    public function AddBooking()
    {
        // Lấy tours với giá mẫu từ schedule gần nhất
        $conn = connectDB();
        $stmt = $conn->query("
            SELECT 
                t.tour_id,
                t.tour_name,
                t.code,
                t.duration_days,
                COALESCE(MIN(ts.price_adult), 0) as price_adult,
                COALESCE(MIN(ts.price_child), 0) as price_child
            FROM tours t
            LEFT JOIN tour_schedules ts ON t.tour_id = ts.tour_id
            WHERE t.status = 'Public'
            GROUP BY t.tour_id
            ORDER BY t.tour_name
        ");
        $tours = $stmt->fetchAll();

        // Lấy customers
        $stmt = $conn->query("SELECT * FROM customers ORDER BY full_name");
        $customers = $stmt->fetchAll();

        require_once './views/booking/add_booking.php';
    }

    // ==================== LƯU BOOKING ====================

    public function StoreBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=them-booking');
            exit;
        }

        try {
            // Validate
            if (empty($_POST['tour_id'])) {
                throw new Exception('Vui lòng chọn tour!');
            }

            // Validate theo loại booking
            $bookingType = $_POST['booking_type'] ?? 'Cá nhân';

            if ($bookingType == 'Đoàn') {
                // Đoàn: bắt buộc có tên tổ chức và thông tin liên hệ
                if (empty($_POST['organization_name'])) {
                    throw new Exception('Vui lòng nhập tên công ty/tổ chức!');
                }
                if (empty($_POST['contact_name']) || empty($_POST['contact_phone'])) {
                    throw new Exception('Vui lòng nhập đầy đủ thông tin liên hệ!');
                }
            } else {
                // Cá nhân: bắt buộc chọn khách hàng
                if (empty($_POST['customer_id'])) {
                    throw new Exception('Vui lòng chọn khách hàng!');
                }
            }

            if (empty($_POST['num_adults']) || $_POST['num_adults'] < 1) {
                throw new Exception('Số người lớn phải >= 1!');
            }

            $data = [
                'tour_id' => $_POST['tour_id'],
                'tour_date' => $_POST['tour_date'] ?? null,
                'customer_id' => $_POST['customer_id'] ?? null,
                'booking_type' => $bookingType,
                'organization_name' => $_POST['organization_name'] ?? null,
                'contact_name' => $_POST['contact_name'] ?? null,
                'contact_phone' => $_POST['contact_phone'] ?? null,
                'contact_email' => $_POST['contact_email'] ?? null,
                'num_adults' => intval($_POST['num_adults']),
                'num_children' => intval($_POST['num_children'] ?? 0),
                'num_infants' => intval($_POST['num_infants'] ?? 0),
                'special_requests' => $_POST['special_requests'] ?? null,
                'total_amount' => floatval($_POST['total_amount']),
                'status' => $_POST['status'] ?? 'Chờ xác nhận'
            ];

            // Lấy booking details từ form nếu có
            if (!empty($_POST['service_name'])) {
                $data['details'] = [];
                foreach ($_POST['service_name'] as $index => $service_name) {
                    if (!empty($service_name)) {
                        $data['details'][] = [
                            'service_name' => $service_name,
                            'quantity' => intval($_POST['quantity'][$index] ?? 1),
                            'unit_price' => floatval($_POST['unit_price'][$index] ?? 0)
                        ];
                    }
                }
            }

            $booking_id = $this->bookingModel->create($data);

            $_SESSION['success'] = 'Tạo booking thành công! ' . ($bookingType == 'Đoàn' ? 'Đã xác nhận tạm thời cho đoàn.' : '');
            header('Location: ?act=chi-tiet-booking&id=' . $booking_id);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=them-booking');
            exit;
        }
    }

    // ==================== CHI TIẾT BOOKING ====================

    public function BookingDetail()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $bookingDetails = $this->bookingModel->getBookingDetails($id);

        require_once './views/booking/booking_detail.php';
    }

    // ==================== SỬA BOOKING ====================

    public function EditBooking()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $tours = $this->tourModel->getAll();

        // Lấy customers
        $customerModel = new stdClass();
        $customerModel->conn = connectDB();
        $stmt = $customerModel->conn->query("SELECT * FROM customers ORDER BY full_name");
        $customers = $stmt->fetchAll();

        $bookingDetails = $this->bookingModel->getBookingDetails($id);

        require_once './views/booking/edit_booking.php';
    }

    // ==================== CẬP NHẬT BOOKING ====================

    public function UpdateBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        try {
            $data = [
                'tour_id' => $_POST['tour_id'],
                'customer_id' => $_POST['customer_id'],
                'num_adults' => intval($_POST['num_adults']),
                'num_children' => intval($_POST['num_children'] ?? 0),
                'num_infants' => intval($_POST['num_infants'] ?? 0),
                'total_amount' => floatval($_POST['total_amount']),
                'status' => $_POST['status']
            ];

            $this->bookingModel->update($id, $data);

            $_SESSION['success'] = 'Cập nhật booking thành công!';
            header('Location: ?act=chi-tiet-booking&id=' . $id);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ?act=sua-booking&id=' . $id);
            exit;
        }
    }

    // ==================== CẬP NHẬT TRẠNG THÁI ====================

    public function UpdateStatus()
    {
        $id = $_POST['booking_id'] ?? 0;
        $status = $_POST['status'] ?? '';

        try {
            $this->bookingModel->updateStatus($id, $status);
            $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ?act=chi-tiet-booking&id=' . $id);
        exit;
    }

    // ==================== HỦY BOOKING ====================

    public function CancelBooking()
    {
        $id = $_GET['id'] ?? 0;

        try {
            $this->bookingModel->cancel($id);
            $_SESSION['success'] = 'Đã hủy booking!';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ?act=danh-sach-booking');
        exit;
    }

    // ==================== IN PHIẾU BOOKING ====================

    public function PrintBooking()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Booking không tồn tại!';
            header('Location: ?act=danh-sach-booking');
            exit;
        }

        $bookingDetails = $this->bookingModel->getBookingDetails($id);

        require_once './views/booking/print_booking.php';
    }
}
