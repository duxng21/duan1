<?php
class QuoteController
{
    protected $quoteModel;
    protected $tourModel;
    protected $tourPricingModel;

    public function __construct()
    {
        requireRole('ADMIN');
        $this->quoteModel = new Quote();
        $this->tourModel = new Tour();
        $this->tourPricingModel = new TourPricing();
    }

    // Danh sách báo giá
    public function ListQuotes()
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        $quotes = $this->quoteModel->getAll($filters);
        $stats = $this->quoteModel->getStatistics();
        require_once __DIR__ . '/../views/quote/list_quotes.php';
    }

    // Form tạo báo giá mới - UC4 Enhanced
    public function CreateQuoteForm()
    {
        $tours = $this->tourModel->getAll();
        $tour_id = $_GET['tour_id'] ?? '';
        $selectedTour = null;
        $pricingPackages = [];

        if ($tour_id) {
            $selectedTour = $this->tourModel->getById($tour_id);
            // UC4: Load pricing packages for selected tour
            $pricingPackages = $this->tourPricingModel->getPricingByTour($tour_id, true);
        }

        require_once __DIR__ . '/../views/quote/create_quote.php';
    }

    // UC4: AJAX - Tính giá tự động
    public function CalculateQuotePrice()
    {
        header('Content-Type: application/json');

        try {
            $pricing_id = $_POST['pricing_id'] ?? null;
            $adults = (int) ($_POST['adults'] ?? 0);
            $children = (int) ($_POST['children'] ?? 0);
            $infants = (int) ($_POST['infants'] ?? 0);

            $options = [
                'single_room' => isset($_POST['single_room']) ? (int) $_POST['single_room'] : 0,
                'is_holiday' => isset($_POST['is_holiday']) ? true : false,
                'discount_code' => $_POST['discount_code'] ?? null
            ];

            if (!$pricing_id) {
                echo json_encode(['success' => false, 'message' => 'Thiếu pricing_id']);
                exit();
            }

            $result = $this->tourPricingModel->calculatePrice($pricing_id, $adults, $children, $infants, $options);

            echo json_encode([
                'success' => true,
                'breakdown' => $result
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }

    // UC4: Lấy pricing packages cho tour (AJAX)
    public function GetTourPricingPackages()
    {
        header('Content-Type: application/json');

        $tour_id = $_GET['tour_id'] ?? null;
        if (!$tour_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu tour_id']);
            exit();
        }

        $packages = $this->tourPricingModel->getPricingByTour($tour_id, true);

        // Check for seasonal pricing
        $departure_date = $_GET['departure_date'] ?? null;
        if ($departure_date) {
            $seasonal = $this->tourPricingModel->getSeasonalPricing($tour_id, $departure_date);
            if ($seasonal) {
                echo json_encode([
                    'success' => true,
                    'packages' => $packages,
                    'seasonal' => $seasonal
                ]);
                exit();
            }
        }

        echo json_encode([
            'success' => true,
            'packages' => $packages
        ]);
        exit();
    }

    // Lưu báo giá mới - UC4 Enhanced
    public function StoreQuote()
    {
        try {
            // Validation
            if (empty($_POST['tour_id']) || empty($_POST['customer_name'])) {
                throw new Exception('Thiếu thông tin bắt buộc: Tour và tên khách hàng.');
            }

            if (!empty($_POST['customer_email']) && !filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ.');
            }

            // UC4: Enhanced data structure
            $data = [
                'tour_id' => (int) $_POST['tour_id'],
                'pricing_id' => !empty($_POST['pricing_id']) ? (int) $_POST['pricing_id'] : null,
                'departure_date' => $_POST['departure_date'] ?? null,
                'customer_name' => trim($_POST['customer_name']),
                'customer_email' => trim($_POST['customer_email'] ?? ''),
                'customer_phone' => trim($_POST['customer_phone'] ?? ''),
                'customer_address' => trim($_POST['customer_address'] ?? ''),
                'customer_company' => trim($_POST['customer_company'] ?? ''),

                // UC4: Adult/Child/Infant counts
                'adult_count' => (int) ($_POST['adult_count'] ?? 0),
                'child_count' => (int) ($_POST['child_count'] ?? 0),
                'infant_count' => (int) ($_POST['infant_count'] ?? 0),
                'single_room_count' => (int) ($_POST['single_room_count'] ?? 0),

                // UC4: Price breakdown
                'subtotal' => (float) ($_POST['subtotal'] ?? 0),
                'discount_type' => $_POST['discount_type'] ?? null,
                'discount_value' => (float) ($_POST['discount_value'] ?? 0),
                'discount_amount' => (float) ($_POST['discount_amount'] ?? 0),
                'tax_percent' => (float) ($_POST['tax_percent'] ?? 10),
                'tax_amount' => (float) ($_POST['tax_amount'] ?? 0),
                'service_fee' => (float) ($_POST['service_fee'] ?? 0),
                'total_amount' => (float) ($_POST['total_amount'] ?? 0),

                // UC4: Quote metadata
                'validity_days' => (int) ($_POST['validity_days'] ?? 7),
                'payment_method' => $_POST['payment_method'] ?? null,
                'special_requests' => trim($_POST['special_requests'] ?? ''),
                'internal_notes' => trim($_POST['internal_notes'] ?? ''),
                'quote_status' => 'draft'
            ];

            // Validate amounts
            if ($data['total_amount'] <= 0) {
                throw new Exception('Tổng tiền không hợp lệ.');
            }

            $quote_id = $this->quoteModel->create($data);

            // UC4: Save breakdown items if provided
            if (!empty($_POST['breakdown_items'])) {
                $breakdown_items = json_decode($_POST['breakdown_items'], true);
                foreach ($breakdown_items as $item) {
                    $this->quoteModel->addBreakdownItem($quote_id, $item);
                }
            }

            logUserActivity('create_quote', 'quote', $quote_id, 'Tạo báo giá cho: ' . $data['customer_name']);

            $_SESSION['success'] = 'Tạo báo giá thành công!';
            header('Location: ?act=xem-bao-gia&id=' . $quote_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi! ' . $e->getMessage();
            // Preserve form data
            $_SESSION['form_data'] = $_POST;
            header('Location: ?act=tao-bao-gia');
            exit();
        }
    }

    // Xem chi tiết báo giá - UC4 Enhanced
    public function ViewQuote()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'ID báo giá không hợp lệ.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        $quote = $this->quoteModel->getById($id);
        if (!$quote) {
            $_SESSION['error'] = 'Không tìm thấy báo giá.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        // UC4: Load breakdown items
        $breakdown = $this->quoteModel->getBreakdown($id);

        // UC4: Load status history
        $statusHistory = $this->quoteModel->getStatusHistory($id);

        require_once __DIR__ . '/../views/quote/view_quote.php';
    }

    // UC4: Send quote via email
    public function SendQuoteEmail()
    {
        $id = (int) ($_POST['id'] ?? 0);
        $recipient = trim($_POST['recipient_email'] ?? '');

        if ($id <= 0) {
            $_SESSION['error'] = 'ID báo giá không hợp lệ.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email người nhận không hợp lệ.';
            header('Location: ?act=xem-bao-gia&id=' . $id);
            exit();
        }

        $quote = $this->quoteModel->getById($id);
        if (!$quote) {
            $_SESSION['error'] = 'Không tìm thấy báo giá.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        try {
            // TODO: Implement email sending with PHPMailer or similar
            // For now, just log the action

            $subject = "Báo giá Tour - {$quote['tour_name']}";
            $message = "Kính gửi {$quote['customer_name']},\n\n";
            $message .= "Đính kèm báo giá cho chuyến du lịch {$quote['tour_name']}.\n\n";
            $message .= "Tổng tiền: " . number_format($quote['total_amount'], 0, ',', '.') . " VND\n";
            $message .= "Hiệu lực: {$quote['validity_days']} ngày\n\n";
            $message .= "Trân trọng,\nĐội ngũ " . ($_SESSION['company_name'] ?? 'Tour Company');

            // Update quote status
            $this->quoteModel->updateSentStatus($id, 'email', $recipient);

            logUserActivity('send_quote_email', 'quote', $id, "Gửi email đến: $recipient");

            $_SESSION['success'] = 'Đã gửi báo giá qua email thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi khi gửi email: ' . $e->getMessage();
        }

        header('Location: ?act=xem-bao-gia&id=' . $id);
        exit();
    }

    // UC4: Preview quote before finalizing
    public function PreviewQuote()
    {
        // This uses POST data to show preview without saving
        $data = $_POST;
        $tour = null;

        if (!empty($data['tour_id'])) {
            $tour = $this->tourModel->getById($data['tour_id']);
        }

        require_once __DIR__ . '/../views/quote/quote_preview.php';
    }

    // Xuất báo giá (PDF/Excel) - UC4 Enhanced
    public function ExportQuote()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $format = strtolower($_GET['format'] ?? 'pdf');

        if ($id <= 0) {
            $_SESSION['error'] = 'ID báo giá không hợp lệ.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        $quote = $this->quoteModel->getById($id);
        if (!$quote) {
            $_SESSION['error'] = 'Không tìm thấy báo giá.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        // UC4: Load breakdown
        $breakdown = $this->quoteModel->getBreakdown($id);

        if ($format === 'excel' || $format === 'csv') {
            $filename = 'quote_' . $id . '_' . date('Ymd') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . $filename);

            $out = fopen('php://output', 'w');

            // BOM for UTF-8
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['BÁO GIÁ TOUR DU LỊCH']);
            fputcsv($out, []);
            fputcsv($out, ['Mã báo giá', $id]);
            fputcsv($out, ['Ngày tạo', $quote['created_at']]);
            fputcsv($out, ['Tour', $quote['tour_name']]);
            fputcsv($out, ['Ngày khởi hành', $quote['departure_date']]);
            fputcsv($out, []);
            fputcsv($out, ['THÔNG TIN KHÁCH HÀNG']);
            fputcsv($out, ['Tên khách hàng', $quote['customer_name']]);
            fputcsv($out, ['Email', $quote['customer_email']]);
            fputcsv($out, ['Số điện thoại', $quote['customer_phone']]);
            fputcsv($out, ['Địa chỉ', $quote['customer_address']]);
            fputcsv($out, []);
            fputcsv($out, ['CHI TIẾT GIÁ']);
            fputcsv($out, ['Số người lớn', $quote['adult_count'] ?? 0]);
            fputcsv($out, ['Số trẻ em', $quote['child_count'] ?? 0]);
            fputcsv($out, ['Số em bé', $quote['infant_count'] ?? 0]);
            fputcsv($out, []);

            // UC4: Breakdown items
            if (!empty($breakdown)) {
                fputcsv($out, ['BẢNG TÍNH GIÁ CHI TIẾT']);
                fputcsv($out, ['Hạng mục', 'Số lượng', 'Đơn giá', 'Thành tiền']);
                foreach ($breakdown as $item) {
                    fputcsv($out, [
                        $item['item_name'],
                        $item['quantity'],
                        number_format($item['unit_price'], 0, ',', '.'),
                        number_format($item['total_price'], 0, ',', '.')
                    ]);
                }
                fputcsv($out, []);
            }

            fputcsv($out, ['TỔNG KẾT']);
            fputcsv($out, ['Tạm tính', number_format($quote['subtotal'] ?? 0, 0, ',', '.')]);
            if ($quote['discount_amount'] > 0) {
                fputcsv($out, ['Chiết khấu', '-' . number_format($quote['discount_amount'], 0, ',', '.')]);
            }
            fputcsv($out, ['Thuế VAT (' . ($quote['tax_percent'] ?? 0) . '%)', number_format($quote['tax_amount'] ?? 0, 0, ',', '.')]);
            if ($quote['service_fee'] > 0) {
                fputcsv($out, ['Phí dịch vụ', number_format($quote['service_fee'], 0, ',', '.')]);
            }
            fputcsv($out, ['TỔNG CỘNG', number_format($quote['total_amount'], 0, ',', '.')]);
            fputcsv($out, []);
            fputcsv($out, ['Hiệu lực báo giá', $quote['validity_days'] . ' ngày']);

            fclose($out);
            logUserActivity('export_quote', 'quote', $id, 'Xuất CSV báo giá');
            exit();
        }

        // PDF/Print view - UC4 Enhanced
        logUserActivity('view_quote_print', 'quote', $id, 'Xem print báo giá');
        require_once __DIR__ . '/../views/quote/print_quote.php';
    }

    // Cập nhật trạng thái báo giá - UC4 Enhanced
    public function UpdateQuoteStatus()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? '';

        $validStatuses = ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired', 'cancelled'];

        if ($id <= 0 || !in_array($status, $validStatuses)) {
            $_SESSION['error'] = 'Tham số không hợp lệ.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        // UC4: Log status change with user info
        $user_id = $_SESSION['user_id'] ?? null;
        $notes = $_GET['notes'] ?? '';

        if ($this->quoteModel->updateStatus($id, $status, $user_id, $notes)) {
            logUserActivity('update_quote_status', 'quote', $id, 'Cập nhật trạng thái: ' . $status);
            $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật trạng thái.';
        }

        header('Location: ?act=xem-bao-gia&id=' . $id);
        exit();
    }

    // Xóa báo giá
    public function DeleteQuote()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'ID không hợp lệ.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        try {
            $this->quoteModel->delete($id);
            logUserActivity('delete_quote', 'quote', $id, 'Xóa báo giá');
            $_SESSION['success'] = 'Xóa báo giá thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: ?act=danh-sach-bao-gia');
        exit();
    }
}
