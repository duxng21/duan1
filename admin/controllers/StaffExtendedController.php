<?php
// CONTROLLER MỞ RỘNG CHO QUẢN LÝ NHÂN SỰ CHI TIẾT
// Bao gồm: chứng chỉ, ngôn ngữ, lịch nghỉ, lịch sử tour, đánh giá

class StaffExtendedController
{
    public $modelStaff;

    public function __construct()
    {
        $this->modelStaff = new Staff();
    }

    // ==================== QUẢN LÝ CHỨNG CHỈ ====================

    public function ManageCertificates()
    {
        $staff_id = $_GET['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Thiếu tham số staff_id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($staff_id);
        $certificates = $this->modelStaff->getCertificates($staff_id);

        // Lấy chứng chỉ sắp hết hạn (30 ngày)
        $expiringCerts = $this->modelStaff->getExpiringCertificates(30);

        require_once './views/staff/manage_certificates.php';
    }

    public function AddCertificate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'staff_id' => $_POST['staff_id'],
                    'certificate_name' => $_POST['certificate_name'],
                    'certificate_type' => $_POST['certificate_type'],
                    'certificate_number' => $_POST['certificate_number'] ?? null,
                    'issued_by' => $_POST['issued_by'] ?? null,
                    'issued_date' => $_POST['issued_date'] ?? null,
                    'expiry_date' => $_POST['expiry_date'] ?? null,
                    'status' => $_POST['status'] ?? 'Còn hạn',
                    'notes' => $_POST['notes'] ?? null
                ];

                // Upload file nếu có
                if (!empty($_FILES['attachment']['name'])) {
                    $uploadDir = '../uploads/certificates/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadFile)) {
                        $data['attachment'] = 'uploads/certificates/' . $fileName;
                    }
                }

                $result = $this->modelStaff->addCertificate($data);

                if ($result) {
                    $_SESSION['success'] = 'Thêm chứng chỉ thành công!';
                } else {
                    $_SESSION['error'] = 'Thêm chứng chỉ thất bại!';
                }

                header('Location: ?act=quan-ly-chung-chi&staff_id=' . $data['staff_id']);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=quan-ly-chung-chi&staff_id=' . $_POST['staff_id']);
                exit();
            }
        }
    }

    public function DeleteCertificate()
    {
        $certificate_id = $_GET['id'] ?? null;
        $staff_id = $_GET['staff_id'] ?? null;

        if ($certificate_id) {
            $result = $this->modelStaff->deleteCertificate($certificate_id);
            if ($result) {
                $_SESSION['success'] = 'Xóa chứng chỉ thành công!';
            } else {
                $_SESSION['error'] = 'Xóa chứng chỉ thất bại!';
            }
        }

        header('Location: ?act=quan-ly-chung-chi&staff_id=' . $staff_id);
        exit();
    }

    // ==================== QUẢN LÝ NGÔN NGỮ ====================

    public function ManageLanguages()
    {
        $staff_id = $_GET['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Thiếu tham số staff_id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($staff_id);
        $languages = $this->modelStaff->getLanguages($staff_id);

        require_once './views/staff/manage_languages.php';
    }

    public function AddLanguage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'staff_id' => $_POST['staff_id'],
                    'language_name' => $_POST['language_name'],
                    'proficiency_level' => $_POST['proficiency_level'],
                    'certificate_name' => $_POST['certificate_name'] ?? null,
                    'certificate_score' => $_POST['certificate_score'] ?? null,
                    'notes' => $_POST['notes'] ?? null
                ];

                $result = $this->modelStaff->addLanguage($data);

                if ($result) {
                    $_SESSION['success'] = 'Thêm ngôn ngữ thành công!';
                } else {
                    $_SESSION['error'] = 'Thêm ngôn ngữ thất bại!';
                }

                header('Location: ?act=quan-ly-ngon-ngu&staff_id=' . $data['staff_id']);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=quan-ly-ngon-ngu&staff_id=' . $_POST['staff_id']);
                exit();
            }
        }
    }

    public function DeleteLanguage()
    {
        $language_id = $_GET['id'] ?? null;
        $staff_id = $_GET['staff_id'] ?? null;

        if ($language_id) {
            $result = $this->modelStaff->deleteLanguage($language_id);
            if ($result) {
                $_SESSION['success'] = 'Xóa ngôn ngữ thành công!';
            } else {
                $_SESSION['error'] = 'Xóa ngôn ngữ thất bại!';
            }
        }

        header('Location: ?act=quan-ly-ngon-ngu&staff_id=' . $staff_id);
        exit();
    }

    // ==================== QUẢN LÝ LỊCH NGHỈ ====================

    public function ManageTimeOff()
    {
        $staff_id = $_GET['staff_id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($staff_id) {
            $staff = $this->modelStaff->getById($staff_id);
            $timeOffList = $this->modelStaff->getTimeOff($staff_id, $status);
        } else {
            // Xem tất cả lịch nghỉ (dành cho quản lý)
            $staff = null;
            $timeOffList = $this->modelStaff->getTimeOff(null, $status);
        }

        require_once './views/staff/manage_time_off.php';
    }

    public function AddTimeOff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'staff_id' => $_POST['staff_id'],
                    'timeoff_type' => $_POST['timeoff_type'],
                    'from_date' => $_POST['from_date'],
                    'to_date' => $_POST['to_date'],
                    'reason' => $_POST['reason'] ?? null,
                    'status' => $_POST['status'] ?? 'Chờ duyệt',
                    'notes' => $_POST['notes'] ?? null
                ];

                // Upload file nếu có
                if (!empty($_FILES['attachment']['name'])) {
                    $uploadDir = '../uploads/timeoff/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadFile)) {
                        $data['attachment'] = 'uploads/timeoff/' . $fileName;
                    }
                }

                $result = $this->modelStaff->addTimeOff($data);

                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }

                header('Location: ?act=quan-ly-lich-nghi&staff_id=' . $data['staff_id']);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=quan-ly-lich-nghi&staff_id=' . $_POST['staff_id']);
                exit();
            }
        }
    }

    public function ApproveTimeOff()
    {
        $timeoff_id = $_GET['id'] ?? null;
        $staff_id = $_GET['staff_id'] ?? null;
        $approved_by = 1; // TODO: Lấy từ session user đăng nhập

        if ($timeoff_id) {
            $result = $this->modelStaff->approveTimeOff($timeoff_id, $approved_by);
            if ($result) {
                $_SESSION['success'] = 'Duyệt lịch nghỉ thành công!';
            } else {
                $_SESSION['error'] = 'Duyệt lịch nghỉ thất bại!';
            }
        }

        header('Location: ?act=quan-ly-lich-nghi' . ($staff_id ? '&staff_id=' . $staff_id : ''));
        exit();
    }

    public function RejectTimeOff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timeoff_id = $_POST['timeoff_id'];
            $staff_id = $_POST['staff_id'] ?? null;
            $notes = $_POST['notes'] ?? 'Từ chối';

            $result = $this->modelStaff->rejectTimeOff($timeoff_id, $notes);

            if ($result) {
                $_SESSION['success'] = 'Từ chối lịch nghỉ thành công!';
            } else {
                $_SESSION['error'] = 'Từ chối lịch nghỉ thất bại!';
            }

            header('Location: ?act=quan-ly-lich-nghi' . ($staff_id ? '&staff_id=' . $staff_id : ''));
            exit();
        }
    }

    // ==================== LỊCH SỬ TOUR & ĐÁNH GIÁ ====================

    public function TourHistory()
    {
        $staff_id = $_GET['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Thiếu tham số staff_id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($staff_id);
        $tourHistory = $this->modelStaff->getTourHistory($staff_id);
        $performanceStats = $this->modelStaff->getPerformanceStats($staff_id);

        require_once './views/staff/tour_history.php';
    }

    public function UpdateTourHistory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $history_id = $_POST['history_id'];
                $staff_id = $_POST['staff_id'];

                $data = [
                    'customer_feedback' => $_POST['customer_feedback'] ?? null,
                    'customer_rating' => $_POST['customer_rating'] ?? 0.0,
                    'manager_feedback' => $_POST['manager_feedback'] ?? null,
                    'manager_rating' => $_POST['manager_rating'] ?? 0.0,
                    'issues' => $_POST['issues'] ?? null,
                    'completed_status' => $_POST['completed_status'] ?? 'Hoàn thành',
                    'salary_paid' => $_POST['salary_paid'] ?? 0,
                    'bonus' => $_POST['bonus'] ?? 0
                ];

                $result = $this->modelStaff->updateTourHistory($history_id, $data);

                if ($result) {
                    $_SESSION['success'] = 'Cập nhật đánh giá thành công!';
                } else {
                    $_SESSION['error'] = 'Cập nhật đánh giá thất bại!';
                }

                header('Location: ?act=lich-su-tour&staff_id=' . $staff_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=lich-su-tour&staff_id=' . $_POST['staff_id']);
                exit();
            }
        }
    }

    // ==================== ĐÁNH GIÁ ĐỊNH KỲ ====================

    public function ManageEvaluations()
    {
        $staff_id = $_GET['staff_id'] ?? null;
        if (!$staff_id) {
            $_SESSION['error'] = 'Thiếu tham số staff_id!';
            header('Location: ?act=danh-sach-nhan-su');
            exit();
        }

        $staff = $this->modelStaff->getById($staff_id);
        $evaluations = $this->modelStaff->getEvaluations($staff_id);

        require_once './views/staff/manage_evaluations.php';
    }

    public function AddEvaluation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'staff_id' => $_POST['staff_id'],
                    'evaluation_period' => $_POST['evaluation_period'],
                    'evaluator_name' => $_POST['evaluator_name'] ?? null,
                    'professional_skill' => $_POST['professional_skill'] ?? 0,
                    'communication_skill' => $_POST['communication_skill'] ?? 0,
                    'responsibility' => $_POST['responsibility'] ?? 0,
                    'problem_solving' => $_POST['problem_solving'] ?? 0,
                    'customer_service' => $_POST['customer_service'] ?? 0,
                    'teamwork' => $_POST['teamwork'] ?? 0,
                    'strengths' => $_POST['strengths'] ?? null,
                    'weaknesses' => $_POST['weaknesses'] ?? null,
                    'improvement_plan' => $_POST['improvement_plan'] ?? null,
                    'notes' => $_POST['notes'] ?? null,
                    'evaluation_date' => $_POST['evaluation_date'] ?? date('Y-m-d')
                ];

                $result = $this->modelStaff->addEvaluation($data);

                if ($result) {
                    $_SESSION['success'] = 'Thêm đánh giá thành công!';
                } else {
                    $_SESSION['error'] = 'Thêm đánh giá thất bại!';
                }

                header('Location: ?act=quan-ly-danh-gia&staff_id=' . $data['staff_id']);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                header('Location: ?act=quan-ly-danh-gia&staff_id=' . $_POST['staff_id']);
                exit();
            }
        }
    }

    // ==================== DASHBOARD HIỆU SUẤT ====================

    public function PerformanceDashboard()
    {
        $staff_id = $_GET['staff_id'] ?? null;

        if ($staff_id) {
            // Dashboard của 1 nhân viên cụ thể
            $staff = $this->modelStaff->getById($staff_id);
            $performanceStats = $this->modelStaff->getPerformanceStats($staff_id);
            $tourHistory = $this->modelStaff->getTourHistory($staff_id, 10);
            $evaluations = $this->modelStaff->getEvaluations($staff_id);
            $certificates = $this->modelStaff->getCertificates($staff_id);
            $expiringCerts = array_filter($certificates, function ($cert) {
                return $cert['status'] == 'Sắp hết hạn';
            });

            require_once './views/staff/performance_dashboard.php';
        } else {
            // Dashboard tổng quan tất cả nhân viên
            $staffList = $this->modelStaff->getAll();
            $expiringCerts = $this->modelStaff->getExpiringCertificates(30);

            // TODO: Thêm thống kê tổng quan
            require_once './views/staff/performance_overview.php';
        }
    }
}
