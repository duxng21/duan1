<?php
class SpecialNoteController
{
    public $modelNote;
    public $modelBooking;

    public function __construct()
    {
        $this->modelNote = new SpecialNote();
        $this->modelBooking = new Booking();
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

        // Lấy thông tin schedule
        require_once './models/TourSchedule.php';
        $modelSchedule = new TourSchedule();
        $schedule = $modelSchedule->getScheduleById($schedule_id);

        require_once './views/special_notes/export_special_requirements_pdf.php';
    }
}
