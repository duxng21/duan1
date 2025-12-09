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
require_once '../commons/notification.php'; // Hệ thống thông báo

// Require toàn bộ file Controllers
require_once './controllers/TourController.php';
require_once './controllers/ScheduleController.php';
require_once './controllers/StaffController.php';
require_once './controllers/StaffExtendedController.php';
require_once './controllers/SupplierController.php';
require_once './controllers/AuthController.php';
require_once './controllers/BookingController.php';
require_once './controllers/ReportController.php';
require_once './controllers/QuoteController.php';
require_once './controllers/SpecialNoteController.php';

// Require toàn bộ file Models
require_once './models/Tour.php';
require_once './models/Category.php';
require_once './models/TourDetail.php';
require_once './models/TourSchedule.php';
require_once './models/Staff.php';
require_once './models/Booking.php';
require_once './models/User.php'; // Needed for AuthController
require_once './models/Report.php';
require_once './models/Quote.php';
require_once './models/SpecialNote.php';
require_once './models/TourPricing.php';
require_once './models/TourSupplier.php';
require_once './models/TourJournal.php';
require_once './models/TourFeedback.php';
require_once './models/TaskCheck.php';
require_once './models/TourVersion.php';
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

    // Điều hướng thông minh: nếu truy cập root, chuyển đến dashboard phù hợp
    if ($act === '/' || $act === '') {
        $role = $_SESSION['role_code'] ?? '';
        if ($role === 'GUIDE') {
            header('Location: ?act=home-guide');
            exit();
        } else {
            header('Location: ?act=dashboard');
            exit();
        }
    }

    // Chặn HDV truy cập các route admin-only
    blockGuideFromAdminRoutes($act);
}
// Để bảo bảo tính chất chỉ gọi 1 hàm Controller để xử lý request thì mình sử dụng switch

try {
    switch ($act) {
        // Trang chủ
        case '/':
            (new TourController())->Home();
            break;
        case 'home-guide': // Alias cho HDV
            (new TourController())->Home();
            break;
        case 'dashboard': // Alias cho admin
            (new TourController())->Home();
            break;
        case 'list-tour':
            (new TourController())->ListTour();
            break;
        case 'menu-tour':
            (new TourController())->MenuTour();
            break;
        case 'them-danh-muc':
            (new TourController())->AddMenu();
            break;
        case 'clone-category':
            (new TourController())->CloneCategory();
            break;
        case 'seed-categories':
            (new TourController())->SeedCategories();
            break;
        case 'add-list':
            (new TourController())->AddList();
            break;
        case 'luu-tour':
            (new TourController())->store();
            break;
        case 'edit-list':
            (new TourController())->EditList();
            break;
        case 'cap-nhat-tour':
            (new TourController())->update();
            break;
        case 'xoa-tour':
            (new TourController())->delete();
            break;

        // Chi tiết tour - Lịch trình & Thư viện ảnh
        case 'chi-tiet-tour':
            (new TourController())->TourDetail();
            break;
        case 'them-lich-trinh':
            (new TourController())->ThemLichTrinh();
            break;
        case 'xoa-lich-trinh':
            (new TourController())->XoaLichTrinh();
            break;
        case 'them-anh-tour':
            (new TourController())->ThemAnhTour();
            break;
        case 'xoa-anh-tour':
            (new TourController())->XoaAnhTour();
            break;
        case 'luu-chinh-sach':
            (new TourController())->LuuChinhSach();
            break;
        case 'luu-tags':
            (new TourController())->LuuTags();
            break;
        case 'seed-tour-data':
            (new TourController())->SeedTourData();
            break;
        case 'seed-all-tours':
            (new TourController())->SeedAllToursData();
            break;

        // Clone tour
        case 'clone-tour-form':
            (new TourController())->CloneTourForm();
            break;
        case 'clone-tour':
            (new TourController())->CloneTour();
            break;
        case 'bulk-clone-tours':
            (new TourController())->BulkCloneTours();
            break;

        // Quản lý phiên bản tour
        case 'quan-ly-phien-ban':
            (new TourController())->ManageVersions();
            break;
        case 'tao-phien-ban':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new TourController())->StoreVersion();
            } else {
                (new TourController())->CreateVersionForm();
            }
            break;
        case 'kich-hoat-phien-ban':
            (new TourController())->ActivateVersion();
            break;
        case 'tam-dung-phien-ban':
            (new TourController())->PauseVersion();
            break;
        case 'luu-tru-phien-ban':
            (new TourController())->ArchiveVersion();
            break;

        // Lịch khởi hành & Phân bổ nhân sự
        case 'danh-sach-lich-khoi-hanh':
            (new ScheduleController())->ListSchedule();
            break;
        case 'them-lich-khoi-hanh':
            (new ScheduleController())->AddSchedule();
            break;
        case 'luu-lich-khoi-hanh':
            (new ScheduleController())->StoreSchedule();
            break;
        case 'api-guest-summary':
            (new ScheduleController())->ApiGuestSummary();
            break;
        case 'api-tour-itineraries':
            (new ScheduleController())->ApiTourItineraries();
            break;
        case 'chi-tiet-lich-khoi-hanh':
            (new ScheduleController())->ScheduleDetail();
            break;
        case 'quan-ly-danh-sach-doan':
            (new ScheduleController())->ManageGroupMembers();
            break;
        case 'luu-danh-sach-doan':
            (new ScheduleController())->SaveGroupMembers();
            break;
        case 'sua-lich-khoi-hanh':
            (new ScheduleController())->EditSchedule();
            break;
        case 'cap-nhat-lich-khoi-hanh':
            (new ScheduleController())->UpdateSchedule();
            break;
        case 'api-sync-booking-from-schedule':
            (new ScheduleController())->ApiSyncBookingFromSchedule();
            break;
        // Seed lịch cho tất cả tour hiện có
        case 'seed-lich-khoi-hanh':
            (new ScheduleController())->SeedSchedulesForAllTours();
            break;
        case 'xoa-lich-khoi-hanh':
            (new ScheduleController())->DeleteSchedule();
            break;
        case 'thay-doi-trang-thai-tour':
            (new ScheduleController())->ChangeScheduleStatus();
            break;
        case 'phan-cong-nhan-su':
            (new ScheduleController())->AssignStaff();
            break;
        case 'xoa-nhan-su-khoi-lich':
            (new ScheduleController())->RemoveStaff();
            break;
        case 'phan-bo-dich-vu':
            (new ScheduleController())->AssignService();
            break;
        case 'xoa-dich-vu-khoi-lich':
            (new ScheduleController())->RemoveService();
            break;
        case 'xem-lich-theo-thang':
            (new ScheduleController())->CalendarView();
            break;
        case 'xuat-bao-cao-lich':
            (new ScheduleController())->ExportSchedule();
            break;
        case 'tong-quan-phan-cong':
            (new ScheduleController())->StaffAssignments();
            break;
        // HDV actions
        case 'hdv-checkin':
            (new ScheduleController())->GuideCheckIn();
            break;
        case 'hdv-luu-nhat-ky':
            (new ScheduleController())->GuideSaveJourneyLog();
            break;

        // === Use Case 1: HDV Xem lịch trình tour và lịch làm việc ===
        case 'hdv-lich-cua-toi':
            (new ScheduleController())->MyTours();
            break;
        case 'hdv-chi-tiet-tour':
            (new ScheduleController())->MyTourDetail();
            break;
        case 'hdv-nhiem-vu-cua-toi':
            (new ScheduleController())->MyTasks();
            break;
        case 'hdv-xem-lich-thang':
            (new ScheduleController())->MyCalendarView();
            break;
        case 'luu-nhiem-vu':
            (new ScheduleController())->SaveTasks();
            break;
        case 'hdv-xuat-lich':
            (new ScheduleController())->ExportMySchedule();
            break;

        // === HDV Hồ sơ và Thông báo ===
        case 'hdv-ho-so':
            (new ScheduleController())->GuideProfile();
            break;
        case 'hdv-thong-bao':
            (new ScheduleController())->GuideNotifications();
            break;
        case 'hdv-danh-dau-da-doc':
            (new ScheduleController())->MarkNotificationsRead();
            break;
        case 'hdv-tro-giup':
            require_once './views/schedule/guide_help.php';
            break;

        // === Use Case 2: HDV Xem danh sách khách ===
        case 'hdv-danh-sach-khach':
            (new ScheduleController())->GuestList();
            break;
        case 'xuat-danh-sach-khach':
            (new ScheduleController())->ExportGuestList();
            break;

        // === Use Case 4: HDV Điểm danh khách ===
        case 'hdv-diem-danh':
            (new ScheduleController())->GuestCheckIn();
            break;
        case 'luu-diem-danh':
            (new ScheduleController())->SaveCheckInBatch();
            break;
        case 'xuat-bao-cao-diem-danh':
            (new ScheduleController())->ExportCheckInReport();
            break;

        // === Use Case 3: HDV Nhật ký tour ===
        case 'view-tour-journal':
            (new ScheduleController())->ViewTourJournal();
            break;
        case 'create-journal-entry-form':
            (new ScheduleController())->CreateJournalEntryForm();
            break;
        case 'create-journal-entry':
            (new ScheduleController())->CreateJournalEntry();
            break;
        case 'edit-journal-entry':
            (new ScheduleController())->EditJournalEntry();
            break;
        case 'update-journal-entry':
            (new ScheduleController())->UpdateJournalEntry();
            break;
        case 'delete-journal-entry':
            (new ScheduleController())->DeleteJournalEntry();
            break;
        case 'delete-journal-image':
            (new ScheduleController())->DeleteJournalImage();
            break;
        case 'export-tour-journal':
            (new ScheduleController())->ExportTourJournal();
            break;

        // === Use Case 6: HDV Tour Feedback ===
        case 'view-tour-feedback':
            (new ScheduleController())->ViewTourFeedback();
            break;
        case 'create-feedback-form':
            (new ScheduleController())->CreateFeedbackForm();
            break;
        case 'create-feedback':
            (new ScheduleController())->CreateFeedback();
            break;
        case 'edit-feedback':
            (new ScheduleController())->EditFeedback();
            break;
        case 'update-feedback':
            (new ScheduleController())->UpdateFeedback();
            break;
        case 'delete-feedback':
            (new ScheduleController())->DeleteFeedback();
            break;
        case 'delete-feedback-image':
            (new ScheduleController())->DeleteFeedbackImage();
            break;
        case 'respond-to-feedback':
            (new ScheduleController())->RespondToFeedback();
            break;
        case 'toggle-feedback-visibility':
            (new ScheduleController())->ToggleFeedbackVisibility();
            break;

        // Quản lý nhân sự
        case 'danh-sach-nhan-su':
            (new StaffController())->ListStaff();
            break;
        case 'them-nhan-su':
            (new StaffController())->AddStaff();
            break;
        case 'luu-nhan-su':
            (new StaffController())->StoreStaff();
            break;
        case 'chi-tiet-nhan-su':
            (new StaffController())->StaffDetail();
            break;
        case 'sua-nhan-su':
            (new StaffController())->EditStaff();
            break;
        case 'cap-nhat-nhan-su':
            (new StaffController())->UpdateStaff();
            break;
        case 'xoa-nhan-su':
            (new StaffController())->DeleteStaff();
            break;

        // === Use Case 1: Thống kê và báo cáo HDV ===
        case 'thong-ke-nhan-su':
            (new StaffController())->Statistics();
            break;
        case 'xuat-excel-nhan-su':
            (new StaffController())->ExportExcel();
            break;
        case 'xuat-pdf-nhan-su':
            (new StaffController())->ExportPDF();
            break;

        // Tạo tài khoản HDV
        case 'tao-tai-khoan-hdv':
            (new StaffController())->CreateAccountForStaff();
            break;
        case 'tao-tai-khoan-hang-loat':
            (new StaffController())->CreateAccountsForAllGuides();
            break;

        // Quản lý chứng chỉ nhân sự
        case 'quan-ly-chung-chi':
            (new StaffExtendedController())->ManageCertificates();
            break;
        case 'them-chung-chi':
            (new StaffExtendedController())->AddCertificate();
            break;
        case 'xoa-chung-chi':
            (new StaffExtendedController())->DeleteCertificate();
            break;

        // Quản lý ngôn ngữ nhân sự
        case 'quan-ly-ngon-ngu':
            (new StaffExtendedController())->ManageLanguages();
            break;
        case 'them-ngon-ngu':
            (new StaffExtendedController())->AddLanguage();
            break;
        case 'xoa-ngon-ngu':
            (new StaffExtendedController())->DeleteLanguage();
            break;

        // Quản lý lịch nghỉ
        case 'quan-ly-lich-nghi':
            (new StaffExtendedController())->ManageTimeOff();
            break;
        case 'them-lich-nghi':
            (new StaffExtendedController())->AddTimeOff();
            break;
        case 'duyet-lich-nghi':
            (new StaffExtendedController())->ApproveTimeOff();
            break;
        case 'tu-choi-lich-nghi':
            (new StaffExtendedController())->RejectTimeOff();
            break;

        // Lịch sử tour & đánh giá
        case 'lich-su-tour':
            (new StaffExtendedController())->TourHistory();
            break;
        case 'cap-nhat-lich-su-tour':
            (new StaffExtendedController())->UpdateTourHistory();
            break;
        case 'quan-ly-danh-gia':
            (new StaffExtendedController())->ManageEvaluations();
            break;
        case 'them-danh-gia':
            (new StaffExtendedController())->AddEvaluation();
            break;

        // Dashboard hiệu suất
        case 'dashboard-hieu-suat':
            (new StaffExtendedController())->PerformanceDashboard();
            break;

        // === Use Case 2: Quản lý đối tác cung cấp dịch vụ ===
        case 'danh-sach-doi-tac':
            (new SupplierController())->ListSuppliers();
            break;
        case 'them-doi-tac':
            (new SupplierController())->CreateSupplierForm();
            break;
        case 'luu-doi-tac':
            (new SupplierController())->CreateSupplier();
            break;
        case 'xem-doi-tac':
            (new SupplierController())->ViewSupplier();
            break;
        case 'sua-doi-tac':
            (new SupplierController())->EditSupplierForm();
            break;
        case 'cap-nhat-doi-tac':
            (new SupplierController())->UpdateSupplier();
            break;
        case 'xoa-doi-tac':
            (new SupplierController())->DeleteSupplier();
            break;

        // Quản lý Booking
        case 'list-booking':
            (new BookingController())->ListBooking();
            break;
        case 'danh-sach-booking':
            (new BookingController())->ListBooking();
            break;
        case 'them-booking':
        case 'tao-booking': // alias để tránh nhầm lẫn
            (new BookingController())->AddBooking();
            break;
        case 'tao-booking-tu-lich':
            (new BookingController())->AddBookingFromSchedule();
            break;
        case 'luu-booking':
            (new BookingController())->StoreBooking();
            break;
        case 'chi-tiet-booking':
            (new BookingController())->BookingDetail();
            break;
        case 'sua-booking':
            (new BookingController())->EditBooking();
            break;
        case 'cap-nhat-booking':
            (new BookingController())->UpdateBooking();
            break;
        case 'cap-nhat-trang-thai-booking':
            (new BookingController())->UpdateStatus();
            break;
        case 'huy-booking':
            (new BookingController())->CancelBooking();
            break;
        case 'in-phieu-booking':
            (new BookingController())->PrintBooking();
            break;

        // Document Generation từ Booking
        case 'tao-bao-gia-pdf':
            (new BookingController())->GenerateQuotePDF();
            break;
        case 'tao-hop-dong-pdf':
            (new BookingController())->GenerateContractPDF();
            break;
        case 'tao-hoa-don-pdf':
            (new BookingController())->GenerateInvoicePDF();
            break;
        case 'xem-tai-lieu':
            (new BookingController())->ViewDocuments();
            break;
        case 'tai-tai-lieu':
            (new BookingController())->DownloadDocument();
            break;
        case 'in-tai-lieu':
            (new BookingController())->PrintDocument();
            break;
        case 'gui-tai-lieu-email':
            (new BookingController())->SendDocumentEmail();
            break;

        // Use Case 3: Quản lý danh sách khách & Check-in
        case 'danh-sach-khach':
            (new BookingController())->ViewGuestList();
            break;
        case 'check-in-khach':
            (new BookingController())->CheckInGuest();
            break;
        case 'phan-phong-khach':
            (new BookingController())->AssignRoom();
            break;
        case 'xuat-danh-sach-doan':
            (new BookingController())->ExportGuestListPDF();
            break;
        case 'bao-cao-doan':
            (new BookingController())->GuestSummaryReport();
            break;
        case 'xuat-danh-sach-da-check-in':
            (new BookingController())->ExportCheckedInGuests();
            break;

        // Use Case 4: Quản lý ghi chú đặc biệt
        case 'ghi-chu-dac-biet':
            (new SpecialNoteController())->ListNotesBySchedule();
            break;
        case 'them-ghi-chu':
            (new SpecialNoteController())->CreateNote();
            break;
        case 'sua-ghi-chu':
            (new SpecialNoteController())->EditNote();
            break;
        case 'cap-nhat-ghi-chu':
            (new SpecialNoteController())->UpdateNote();
            break;
        case 'xoa-ghi-chu':
            (new SpecialNoteController())->DeleteNote();
            break;
        case 'cap-nhat-trang-thai-ghi-chu':
            (new SpecialNoteController())->UpdateNoteStatus();
            break;
        case 'bao-cao-yeu-cau-dac-biet':
            (new SpecialNoteController())->SpecialRequirementsReport();
            break;
        case 'xuat-bao-cao-yeu-cau-dac-biet':
            (new SpecialNoteController())->ExportSpecialRequirementsPDF();
            break;

        // Báo cáo tài chính tour
        case 'bao-cao-tour':
            (new ReportController())->TourFinanceReport();
            break;
        case 'xuat-bao-cao-tour':
            (new ReportController())->ExportTourFinance();
            break;

        // Use Case III.5: Financial Reports Enhanced
        case 'financial-dashboard':
            (new ReportController())->FinancialDashboard();
            break;
        case 'export-dashboard':
            (new ReportController())->ExportDashboard();
            break;
        case 'tour-detail-report':
            (new ReportController())->TourDetailReport();
            break;
        case 'comparison-report':
            (new ReportController())->ComparisonReport();
            break;
        case 'monthly-report':
            (new ReportController())->MonthlyReport();
            break;
        // Báo cáo doanh thu từ bookings
        case 'bao-cao-doanh-thu':
            (new ReportController())->revenueReport();
            break;

        // Quản lý báo giá
        case 'danh-sach-bao-gia':
            (new QuoteController())->ListQuotes();
            break;
        case 'tao-bao-gia':
            (new QuoteController())->CreateQuoteForm();
            break;
        case 'luu-bao-gia':
            (new QuoteController())->StoreQuote();
            break;
        case 'xem-bao-gia':
            (new QuoteController())->ViewQuote();
            break;
        case 'xuat-bao-gia':
            (new QuoteController())->ExportQuote();
            break;
        case 'cap-nhat-trang-thai-bao-gia':
            (new QuoteController())->UpdateQuoteStatus();
            break;
        case 'xoa-bao-gia':
            (new QuoteController())->DeleteQuote();
            break;

        //auth
        case 'login':
            (new AuthController())->login();
            break;
        case 'doi-mat-khau':
            (new AuthController())->changePassword();
            break;
        case 'luu-mat-khau-moi':
            (new AuthController())->processChangePassword();
            break;
        case 'logout':
            (new AuthController())->logout();
            break;
        // Quản lý user
        case 'danh-sach-user':
            (new AuthController())->listUsers();
            break;
        case 'tao-user':
            (new AuthController())->createUserForm();
            break;
        case 'luu-user':
            (new AuthController())->processCreateUser();
            break;
        case 'doi-trang-thai-user':
            (new AuthController())->toggleUserStatus();
            break;
        default:
            print '404 - Không tìm thấy trang';
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo '<h3>Lỗi hệ thống</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
