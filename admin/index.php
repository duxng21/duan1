<?php
// Set UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

// Start session
session_start();

// Require toàn bộ các file khai báo môi trường, thực thi,...(không require view)

// Require file Common
require_once '../commons/env.php'; // Khai báo biến môi trường
require_once '../commons/function.php'; // Hàm hỗ trợ
require_once '../commons/permission_simple.php'; // Quyền & session helpers

// Require toàn bộ file Controllers
require_once './controllers/TourController.php';
require_once './controllers/ScheduleController.php';
require_once './controllers/StaffController.php';
require_once './controllers/StaffExtendedController.php';
require_once './controllers/AuthController.php';
require_once './controllers/BookingController.php';

// Require toàn bộ file Models
require_once './models/Tour.php';
require_once './models/Category.php';
require_once './models/TourDetail.php';
require_once './models/TourSchedule.php';
require_once './models/Staff.php';
require_once './models/Booking.php';
require_once './models/User.php'; // Needed for AuthController
// require_once './models/ProductModel.php';

// Route
$act = $_GET['act'] ?? '/';

// Bảo vệ toàn bộ trang admin (trừ trang login)
if ($act !== 'login') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Vui lòng đăng nhập.';
        header('Location: ../index.php?act=login');
        exit();
    }
}


// Để bảo bảo tính chất chỉ gọi 1 hàm Controller để xử lý request thì mình sử dụng match

try {
    match ($act) {
        // Trang chủ
        '/' => (new TourController())->Home(),
        'list-tour' => (new TourController())->ListTour(),
        'menu-tour' => (new TourController())->MenuTour(),
        'them-danh-muc' => (new TourController())->AddMenu(),
        'add-list' => (new TourController())->AddList(),
        'luu-tour' => (new TourController())->store(),
        'edit-list' => (new TourController())->EditList(),
        'cap-nhat-tour' => (new TourController())->update(),
        'xoa-tour' => (new TourController())->delete(),

        // Chi tiết tour - Lịch trình & Thư viện ảnh
        'chi-tiet-tour' => (new TourController())->TourDetail(),
        'them-lich-trinh' => (new TourController())->ThemLichTrinh(),
        'xoa-lich-trinh' => (new TourController())->XoaLichTrinh(),
        'them-anh-tour' => (new TourController())->ThemAnhTour(),
        'xoa-anh-tour' => (new TourController())->XoaAnhTour(),
        'luu-chinh-sach' => (new TourController())->LuuChinhSach(),
        'luu-tags' => (new TourController())->LuuTags(),
        'seed-tour-data' => (new TourController())->SeedTourData(),
        'seed-all-tours' => (new TourController())->SeedAllToursData(),

        // Lịch khởi hành & Phân bổ nhân sự
        'danh-sach-lich-khoi-hanh' => (new ScheduleController())->ListSchedule(),
        'them-lich-khoi-hanh' => (new ScheduleController())->AddSchedule(),
        'luu-lich-khoi-hanh' => (new ScheduleController())->StoreSchedule(),
        'chi-tiet-lich-khoi-hanh' => (new ScheduleController())->ScheduleDetail(),
        'sua-lich-khoi-hanh' => (new ScheduleController())->EditSchedule(),
        'cap-nhat-lich-khoi-hanh' => (new ScheduleController())->UpdateSchedule(),
        'xoa-lich-khoi-hanh' => (new ScheduleController())->DeleteSchedule(),
        'phan-cong-nhan-su' => (new ScheduleController())->AssignStaff(),
        'xoa-nhan-su-khoi-lich' => (new ScheduleController())->RemoveStaff(),
        'phan-bo-dich-vu' => (new ScheduleController())->AssignService(),
        'xoa-dich-vu-khoi-lich' => (new ScheduleController())->RemoveService(),
        'xem-lich-theo-thang' => (new ScheduleController())->CalendarView(),
        'xuat-bao-cao-lich' => (new ScheduleController())->ExportSchedule(),
        'tong-quan-phan-cong' => (new ScheduleController())->StaffAssignments(),
        // HDV actions
        'hdv-checkin' => (new ScheduleController())->GuideCheckIn(),
        'hdv-luu-nhat-ky' => (new ScheduleController())->GuideSaveJourneyLog(),

        // Quản lý nhân sự
        'danh-sach-nhan-su' => (new StaffController())->ListStaff(),
        'them-nhan-su' => (new StaffController())->AddStaff(),
        'luu-nhan-su' => (new StaffController())->StoreStaff(),
        'chi-tiet-nhan-su' => (new StaffController())->StaffDetail(),
        'sua-nhan-su' => (new StaffController())->EditStaff(),
        'cap-nhat-nhan-su' => (new StaffController())->UpdateStaff(),
        'xoa-nhan-su' => (new StaffController())->DeleteStaff(),

        // Quản lý chứng chỉ nhân sự
        'quan-ly-chung-chi' => (new StaffExtendedController())->ManageCertificates(),
        'them-chung-chi' => (new StaffExtendedController())->AddCertificate(),
        'xoa-chung-chi' => (new StaffExtendedController())->DeleteCertificate(),

        // Quản lý ngôn ngữ nhân sự
        'quan-ly-ngon-ngu' => (new StaffExtendedController())->ManageLanguages(),
        'them-ngon-ngu' => (new StaffExtendedController())->AddLanguage(),
        'xoa-ngon-ngu' => (new StaffExtendedController())->DeleteLanguage(),

        // Quản lý lịch nghỉ
        'quan-ly-lich-nghi' => (new StaffExtendedController())->ManageTimeOff(),
        'them-lich-nghi' => (new StaffExtendedController())->AddTimeOff(),
        'duyet-lich-nghi' => (new StaffExtendedController())->ApproveTimeOff(),
        'tu-choi-lich-nghi' => (new StaffExtendedController())->RejectTimeOff(),

        // Lịch sử tour & đánh giá
        'lich-su-tour' => (new StaffExtendedController())->TourHistory(),
        'cap-nhat-lich-su-tour' => (new StaffExtendedController())->UpdateTourHistory(),
        'quan-ly-danh-gia' => (new StaffExtendedController())->ManageEvaluations(),
        'them-danh-gia' => (new StaffExtendedController())->AddEvaluation(),

        // Dashboard hiệu suất
        'dashboard-hieu-suat' => (new StaffExtendedController())->PerformanceDashboard(),

        // Quản lý Booking
        'danh-sach-booking' => (new BookingController())->ListBooking(),
        'them-booking' => (new BookingController())->AddBooking(),
        'luu-booking' => (new BookingController())->StoreBooking(),
        'chi-tiet-booking' => (new BookingController())->BookingDetail(),
        'sua-booking' => (new BookingController())->EditBooking(),
        'cap-nhat-booking' => (new BookingController())->UpdateBooking(),
        'cap-nhat-trang-thai-booking' => (new BookingController())->UpdateStatus(),
        'huy-booking' => (new BookingController())->CancelBooking(),
        'in-phieu-booking' => (new BookingController())->PrintBooking(),

        //auth
        'login' => (new AuthController())->login(),
        'doi-mat-khau' => (new AuthController())->changePassword(),
        'luu-mat-khau-moi' => (new AuthController())->processChangePassword(),
        'logout' => (new AuthController())->logout(),
        // Quản lý user
        'danh-sach-user' => (new AuthController())->listUsers(),
        'tao-user' => (new AuthController())->createUserForm(),
        'luu-user' => (new AuthController())->processCreateUser(),
        'doi-trang-thai-user' => (new AuthController())->toggleUserStatus(),
        default => print '404 - Không tìm thấy trang',
    };
} catch (UnhandledMatchError $e) {
    http_response_code(404);
    echo '404 - Không tìm thấy trang';
}
