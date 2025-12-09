<?php
class SpecialNoteController
{
    public $modelNote;
    public $modelBooking;
    public $modelSchedule;
    public $modelGuest;

    public function __construct()
    {
        $this->modelNote = new SpecialNote();
        $this->modelBooking = new Booking();
        $this->modelSchedule = new TourSchedule();
        $this->modelGuest = new Guest();
    }

    // ==================== QUẢN LÝ GHI CHÚ ====================

    /**
     * Danh sách ghi chú theo booking
     */
    public function ListNotesByBooking()
    {
        requireLogin();
        $booking_id = $_GET['booking_id'] ?? null;

        if (!$booking_id) {
            $_SESSION['error'] = 'Thiếu thông tin booking!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        // Lấy thông tin booking
        $booking = $this->modelBooking->getBookingById($booking_id);
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        // Lấy filters
        $filters = [
            'priority' => $_GET['priority'] ?? '',
            'status' => $_GET['status'] ?? '',
            'note_type' => $_GET['note_type'] ?? ''
        ];

        $notes = $this->modelNote->getNotesByBooking($booking_id, $filters);
        $statistics = $this->modelNote->getNoteStatistics($booking_id);

        require_once './views/special_notes/list_notes.php';
    }

    /**
     * Danh sách ghi chú theo schedule
     */
    public function ListNotesBySchedule()
    {
        requireLogin();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu thông tin lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        // Lấy filters
        $filters = [
            'priority' => $_GET['priority'] ?? '',
            'status' => $_GET['status'] ?? '',
            'note_type' => $_GET['note_type'] ?? ''
        ];

        $notes = $this->modelNote->getNotesBySchedule($schedule_id, $filters);
        $statistics = $this->modelNote->getNoteStatistics(null, $schedule_id);

        require_once './views/special_notes/list_notes_schedule.php';
    }

    /**
     * Thêm ghi chú mới
     */
    public function CreateNote()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $guest_id = $_GET['guest_id'] ?? null;
            $booking_id = $_GET['booking_id'] ?? null;

            if (!$guest_id || !$booking_id) {
                $_SESSION['error'] = 'Thiếu thông tin!';
                header('Location: ?act=danh-sach-booking');
                exit();
            }

            // Lấy thông tin khách
            $guest = $this->modelBooking->getGuestById($guest_id);
            if (!$guest) {
                $_SESSION['error'] = 'Không tìm thấy thông tin khách!';
                header('Location: ?act=danh-sach-booking');
                exit();
            }

            require_once './views/special_notes/create_note.php';
        } else {
            // POST - Lưu ghi chú
            try {
                $data = [
                    'guest_id' => $_POST['guest_id'] ?? null,
                    'booking_id' => $_POST['booking_id'] ?? null,
                    'note_type' => $_POST['note_type'] ?? 'Other',
                    'note_content' => trim($_POST['note_content'] ?? ''),
                    'priority_level' => $_POST['priority_level'] ?? 'Medium',
                    'created_by' => $_SESSION['user_id']
                ];

                if (empty($data['note_content'])) {
                    throw new Exception("Vui lòng nhập nội dung ghi chú!");
                }

                $note_id = $this->modelNote->createNote($data);

                if ($note_id) {
                    $_SESSION['success'] = 'Thêm ghi chú đặc biệt thành công! Đã gửi thông báo đến HDV và hậu cần.';

                    $return_url = $_POST['return_url'] ?? '?act=danh-sach-khach&booking_id=' . $data['booking_id'];
                    header('Location: ' . $return_url);
                } else {
                    throw new Exception("Không thể lưu ghi chú!");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=them-ghi-chu&guest_id=' . $_POST['guest_id'] . '&booking_id=' . $_POST['booking_id']);
            }
            exit();
        }
    }

    /**
     * Sửa ghi chú
     */
    public function EditNote()
    {
        requireLogin();

        $note_id = $_GET['id'] ?? null;
        if (!$note_id) {
            $_SESSION['error'] = 'Thiếu thông tin ghi chú!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        $note = $this->modelNote->getNoteById($note_id);
        if (!$note) {
            $_SESSION['error'] = 'Không tìm thấy ghi chú!';
            header('Location: ?act=danh-sach-booking');
            exit();
        }

        require_once './views/special_notes/edit_note.php';
    }

    /**
     * Cập nhật ghi chú
     */
    public function UpdateNote()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $note_id = $_POST['note_id'] ?? null;
                if (!$note_id) {
                    throw new Exception("Thiếu thông tin ghi chú!");
                }

                $data = [
                    'note_type' => $_POST['note_type'] ?? 'Other',
                    'note_content' => trim($_POST['note_content'] ?? ''),
                    'priority_level' => $_POST['priority_level'] ?? 'Medium'
                ];

                if (empty($data['note_content'])) {
                    throw new Exception("Vui lòng nhập nội dung ghi chú!");
                }

                $result = $this->modelNote->updateNote($note_id, $data);

                if ($result) {
                    $_SESSION['success'] = 'Cập nhật ghi chú thành công! Đã gửi thông báo cập nhật.';
                } else {
                    throw new Exception("Không thể cập nhật ghi chú!");
                }

                $return_url = $_POST['return_url'] ?? '?act=danh-sach-booking';
                header('Location: ' . $return_url);
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ?act=sua-ghi-chu&id=' . $_POST['note_id']);
            }
            exit();
        }
    }

    /**
     * Cập nhật trạng thái ghi chú (HDV xử lý)
     */
    public function UpdateNoteStatus()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $note_id = $_POST['note_id'] ?? null;
                $status = $_POST['status'] ?? null;
                $handler_notes = trim($_POST['handler_notes'] ?? '');

                if (!$note_id || !$status) {
                    throw new Exception("Thiếu thông tin!");
                }

                $result = $this->modelNote->updateStatus($note_id, $status, $handler_notes);

                if ($result) {
                    $statusNames = [
                        'Pending' => 'Chờ xử lý',
                        'Acknowledged' => 'Đã nhận',
                        'In Progress' => 'Đang xử lý',
                        'Resolved' => 'Đã hoàn thành'
                    ];
                    $_SESSION['success'] = 'Cập nhật trạng thái: ' . ($statusNames[$status] ?? $status);
                } else {
                    throw new Exception("Không thể cập nhật trạng thái!");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            $return_url = $_POST['return_url'] ?? $_SERVER['HTTP_REFERER'] ?? '?act=danh-sach-booking';
            header('Location: ' . $return_url);
            exit();
        }
    }

    /**
     * Xóa ghi chú
     */
    public function DeleteNote()
    {
        requireRole('ADMIN');

        try {
            $note_id = $_GET['id'] ?? null;
            if (!$note_id) {
                throw new Exception("Thiếu thông tin ghi chú!");
            }

            $result = $this->modelNote->deleteNote($note_id);

            if ($result) {
                $_SESSION['success'] = 'Xóa ghi chú thành công!';
            } else {
                throw new Exception("Không thể xóa ghi chú!");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $return_url = $_GET['return_url'] ?? $_SERVER['HTTP_REFERER'] ?? '?act=danh-sach-booking';
        header('Location: ' . $return_url);
        exit();
    }

    /**
     * Báo cáo yêu cầu đặc biệt (trước tour)
     */
    public function SpecialRequirementsReport()
    {
        requireLogin();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu thông tin lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $guests = $this->modelNote->getSpecialRequirementsReport($schedule_id);
        $statistics = $this->modelNote->getNoteStatistics(null, $schedule_id);

        require_once './views/special_notes/special_requirements_report.php';
    }

    /**
     * Xuất PDF báo cáo yêu cầu đặc biệt
     */
    public function ExportSpecialRequirementsPDF()
    {
        requireLogin();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu thông tin lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $guests = $this->modelNote->getSpecialRequirementsReport($schedule_id);
        $statistics = $this->modelNote->getNoteStatistics(null, $schedule_id);
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);

        require_once './views/special_notes/export_special_requirements_pdf.php';
    }

    /**
     * Dashboard ghi chú đặc biệt - Tổng quan
     */
    public function Dashboard()
    {
        requireLogin();

        // Lấy thống kê tổng quan
        $overallStats = $this->modelNote->getOverallStatistics();
        
        // Lấy ghi chú ưu tiên cao chưa xử lý
        $urgentNotes = $this->modelNote->getUrgentNotes();
        
        // Lấy thông báo chưa đọc
        $unreadNotifications = $this->modelNote->getUnreadNotifications($_SESSION['user_id']);
        
        // Lấy báo cáo hiệu quả xử lý theo tháng
        $monthlyEfficiency = $this->modelNote->getMonthlyEfficiencyReport();

        require_once './views/special_notes/dashboard.php';
    }

    /**
     * Quản lý ghi chú đặc biệt - Menu chính
     */
    public function ManageSpecialNotes()
    {
        requireLogin();

        // Lấy danh sách tour đang có lịch khởi hành
        $tours = $this->modelSchedule->getActiveTours();
        
        require_once './views/special_notes/manage_notes.php';
    }

    /**
     * Báo cáo hiệu quả phục vụ đặc biệt (sau tour)
     */
    public function ServiceEfficiencyReport()
    {
        requireLogin();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu thông tin lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        // Lấy báo cáo hiệu quả
        $efficiency = $this->modelNote->getServiceEfficiencyReport($schedule_id);
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        $notes = $this->modelNote->getNotesBySchedule($schedule_id);

        require_once './views/special_notes/service_efficiency_report.php';
    }

    /**
     * Gửi thông báo nhắc nhở trước tour
     */
    public function SendPreTourReminder()
    {
        requireLogin();
        requireRole(['ADMIN', 'STAFF']);

        $schedule_id = $_POST['schedule_id'] ?? null;
        if (!$schedule_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin lịch khởi hành']);
            exit();
        }

        try {
            $result = $this->modelNote->sendPreTourReminder($schedule_id);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã gửi thông báo nhắc nhở đến tất cả HDV và nhân viên liên quan'
                ]);
            } else {
                throw new Exception('Không thể gửi thông báo');
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function MarkNotificationRead()
    {
        requireLogin();

        $notification_id = $_POST['notification_id'] ?? null;
        if (!$notification_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
            exit();
        }

        try {
            $result = $this->modelNote->markNotificationAsRead($notification_id, $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Lấy số lượng thông báo chưa đọc (AJAX)
     */
    public function GetUnreadCount()
    {
        requireLogin();
        
        $count = $this->modelNote->getUnreadNotificationCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
        exit();
    }

    /**
     * In danh sách khách có yêu cầu riêng
     */
    public function PrintSpecialRequirements()
    {
        requireLogin();

        $schedule_id = $_GET['schedule_id'] ?? null;
        if (!$schedule_id) {
            $_SESSION['error'] = 'Thiếu thông tin lịch khởi hành!';
            header('Location: ?act=danh-sach-lich-khoi-hanh');
            exit();
        }

        $guests = $this->modelNote->getSpecialRequirementsReport($schedule_id);
        $schedule = $this->modelSchedule->getScheduleById($schedule_id);
        $statistics = $this->modelNote->getNoteStatistics(null, $schedule_id);

        require_once './views/special_notes/print_special_requirements.php';
    }

    /**
     * Copy ghi chú từ booking trước (cho khách quen)
     */
    public function CopyNotesFromPreviousBooking()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $guest_id = $_POST['guest_id'] ?? null;
                $current_booking_id = $_POST['current_booking_id'] ?? null;
                $previous_booking_id = $_POST['previous_booking_id'] ?? null;

                if (!$guest_id || !$current_booking_id || !$previous_booking_id) {
                    throw new Exception('Thiếu thông tin!');
                }

                $result = $this->modelNote->copyNotesFromPreviousBooking(
                    $guest_id, 
                    $current_booking_id, 
                    $previous_booking_id, 
                    $_SESSION['user_id']
                );

                if ($result) {
                    $_SESSION['success'] = 'Đã sao chép thành công ' . $result . ' ghi chú từ booking trước!';
                } else {
                    throw new Exception('Không tìm thấy ghi chú nào để sao chép!');
                }

                header('Location: ?act=danh-sach-khach&booking_id=' . $current_booking_id);
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '?act=danh-sach-booking'));
            }
            exit();
        }
    }
}
