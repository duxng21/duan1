<?php
class QuoteController
{
    protected $quoteModel;
    protected $tourModel;

    public function __construct()
    {
        requireRole('ADMIN');
        $this->quoteModel = new Quote();
        $this->tourModel = new Tour();
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

    // Form tạo báo giá mới
    public function CreateQuoteForm()
    {
        $tours = $this->tourModel->getAll();
        $tour_id = $_GET['tour_id'] ?? '';
        $selectedTour = null;
        if ($tour_id) {
            $selectedTour = $this->tourModel->getById($tour_id);
        }
        require_once __DIR__ . '/../views/quote/create_quote.php';
    }

    // Lưu báo giá mới
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

            // Validate base_price
            if (empty($_POST['base_price']) || (float) $_POST['base_price'] <= 0) {
                throw new Exception('Vui lòng nhập giá căn bản hợp lệ.');
            }

            // Tính toán giá
            $data = [
                'tour_id' => (int) $_POST['tour_id'],
                'departure_date' => $_POST['departure_date'] ?? null,
                'customer_name' => trim($_POST['customer_name']),
                'customer_email' => trim($_POST['customer_email'] ?? ''),
                'customer_phone' => trim($_POST['customer_phone'] ?? ''),
                'customer_address' => trim($_POST['customer_address'] ?? ''),
                'num_adults' => (int) ($_POST['num_adults'] ?? 0),
                'num_children' => (int) ($_POST['num_children'] ?? 0),
                'num_infants' => (int) ($_POST['num_infants'] ?? 0),
                'base_price' => (float) ($_POST['base_price'] ?? 0),
                'discount_type' => $_POST['discount_type'] ?? 'none',
                'discount_value' => (float) ($_POST['discount_value'] ?? 0),
                'additional_fees' => (float) ($_POST['additional_fees'] ?? 0),
                'tax_percent' => (float) ($_POST['tax_percent'] ?? 0),
                'validity_days' => (int) ($_POST['validity_days'] ?? 7),
                'internal_notes' => trim($_POST['internal_notes'] ?? ''),
                'status' => 'Đang chờ'
            ];

            // Tính total
            $data['total_amount'] = $this->quoteModel->calculateTotal($data);

            // Lấy options nếu có
            $options = [];
            if (!empty($_POST['option_name'])) {
                foreach ($_POST['option_name'] as $i => $name) {
                    if (!empty($name)) {
                        $options[] = [
                            'option_name' => $name,
                            'option_price' => (float) ($_POST['option_price'][$i] ?? 0),
                            'quantity' => (int) ($_POST['option_quantity'][$i] ?? 1)
                        ];
                    }
                }
            }
            $data['options'] = $options;

            $quote_id = $this->quoteModel->create($data);

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

    // Xem chi tiết báo giá
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

        $options = $this->quoteModel->getOptions($id);
        require_once __DIR__ . '/../views/quote/view_quote.php';
    }

    // Xuất báo giá (PDF/Excel)
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

        $options = $this->quoteModel->getOptions($id);

        if ($format === 'excel' || $format === 'csv') {
            $filename = 'quote_' . $id . '_' . date('Ymd') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Mục', 'Chi tiết']);
            fputcsv($out, ['Báo giá số', $id]);
            fputcsv($out, ['Tour', $quote['tour_name']]);
            fputcsv($out, ['Khách hàng', $quote['customer_name']]);
            fputcsv($out, ['Email', $quote['customer_email']]);
            fputcsv($out, ['Số điện thoại', $quote['customer_phone']]);
            fputcsv($out, ['Ngày khởi hành', $quote['departure_date']]);
            fputcsv($out, ['Người lớn', $quote['num_adults']]);
            fputcsv($out, ['Trẻ em', $quote['num_children']]);
            fputcsv($out, ['Em bé', $quote['num_infants']]);
            fputcsv($out, ['Giá căn bản', number_format($quote['base_price'], 0, ',', '.')]);
            fputcsv($out, ['Chiết khấu', $quote['discount_type'] . ' ' . $quote['discount_value']]);
            fputcsv($out, ['Phụ phí', number_format($quote['additional_fees'], 0, ',', '.')]);
            fputcsv($out, ['Thuế', $quote['tax_percent'] . '%']);
            fputcsv($out, ['Tổng cộng', number_format($quote['total_amount'], 0, ',', '.')]);
            fputcsv($out, []);
            fputcsv($out, ['Dịch vụ bổ sung']);
            foreach ($options as $opt) {
                fputcsv($out, [$opt['option_name'], $opt['quantity'], number_format($opt['option_price'], 0, ',', '.')]);
            }
            fclose($out);
            logUserActivity('export_quote', 'quote', $id, 'Xuất CSV báo giá');
            exit();
        }

        // PDF/Print view
        logUserActivity('view_quote_print', 'quote', $id, 'Xem print báo giá');
        require_once __DIR__ . '/../views/quote/print_quote.php';
    }

    // Cập nhật trạng thái báo giá
    public function UpdateQuoteStatus()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? '';

        if ($id <= 0 || !in_array($status, ['Đang chờ', 'Đã chấp nhận', 'Đã từ chối', 'Hết hạn'])) {
            $_SESSION['error'] = 'Tham số không hợp lệ.';
            header('Location: ?act=danh-sach-bao-gia');
            exit();
        }

        if ($this->quoteModel->updateStatus($id, $status)) {
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
