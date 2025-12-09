<?php
/**
 * Model xử lý tạo và quản lý tài liệu từ booking
 * - Báo giá (Quote)
 * - Hợp đồng (Contract)
 * - Hóa đơn VAT (Invoice)
 */
class BookingDocument
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== TẠO TÀI LIỆU ====================

    /**
     * Tạo báo giá từ booking
     * @param int $booking_id
     * @return array Document data with HTML content
     */
    public function generateQuote($booking_id)
    {
        $booking = $this->getBookingData($booking_id);
        if (!$booking) {
            throw new Exception('Không tìm thấy booking!');
        }

        $tour = $this->getTourData($booking['tour_id']);
        $pricing = $this->getTourPricing($booking['tour_id'], $booking['tour_date']);
        $services = $this->getScheduleServices($booking['schedule_id'] ?? null);

        // Calculate unit prices from pricing or estimate from total
        $booking['adult_count'] = $booking['num_adults'];
        $booking['child_count'] = $booking['num_children'];
        $booking['infant_count'] = $booking['num_infants'];

        if ($pricing) {
            $booking['adult_price'] = $pricing['adult_price'];
            $booking['child_price'] = $pricing['child_price'];
        } else {
            // Estimate: divide total by number of people
            $total_people = $booking['num_adults'] + $booking['num_children'];
            if ($total_people > 0) {
                $avg_price = $booking['total_amount'] / $total_people;
                $booking['adult_price'] = $avg_price;
                $booking['child_price'] = $avg_price * 0.7; // 70% for children
            } else {
                $booking['adult_price'] = 0;
                $booking['child_price'] = 0;
            }
        }

        // Template data
        $data = [
            'document_type' => 'QUOTE',
            'document_number' => $this->generateDocumentNumber('QT', $booking_id),
            'document_date' => date('d/m/Y'),
            'booking' => $booking,
            'tour' => $tour,
            'pricing' => $pricing,
            'services' => $services,
            'company_info' => $this->getCompanyInfo(),
            'subtotal' => $booking['total_amount'],
            'vat_rate' => 10, // 10% VAT
            'vat_amount' => $booking['total_amount'] * 0.10,
            'total' => $booking['total_amount'] * 1.10
        ];

        // Generate HTML content
        $data['html_content'] = $this->renderQuoteTemplate($data);

        // Save to database
        $document_id = $this->saveDocument([
            'booking_id' => $booking_id,
            'document_type' => 'QUOTE',
            'document_number' => $data['document_number'],
            'file_name' => "bao-gia-{$booking_id}-" . date('Ymd') . ".pdf",
            'content_html' => $data['html_content'],
            'amount' => $data['total'],
            'status' => 'Draft'
        ]);

        $data['document_id'] = $document_id;
        return $data;
    }

    /**
     * Tạo hợp đồng từ booking
     * @param int $booking_id
     * @return array Document data with HTML content
     */
    public function generateContract($booking_id)
    {
        $booking = $this->getBookingData($booking_id);
        if (!$booking) {
            throw new Exception('Không tìm thấy booking!');
        }

        $tour = $this->getTourData($booking['tour_id']);
        $pricing = $this->getTourPricing($booking['tour_id'], $booking['tour_date']);
        $itineraries = $this->getTourItineraries($booking['tour_id']);

        // Calculate unit prices
        $booking['adult_count'] = $booking['num_adults'];
        $booking['child_count'] = $booking['num_children'];
        $booking['infant_count'] = $booking['num_infants'];

        if ($pricing) {
            $booking['adult_price'] = $pricing['adult_price'];
            $booking['child_price'] = $pricing['child_price'];
        } else {
            $total_people = $booking['num_adults'] + $booking['num_children'];
            if ($total_people > 0) {
                $avg_price = $booking['total_amount'] / $total_people;
                $booking['adult_price'] = $avg_price;
                $booking['child_price'] = $avg_price * 0.7;
            } else {
                $booking['adult_price'] = 0;
                $booking['child_price'] = 0;
            }
        }

        $data = [
            'document_type' => 'CONTRACT',
            'document_number' => $this->generateDocumentNumber('HD', $booking_id),
            'document_date' => date('d/m/Y'),
            'booking' => $booking,
            'tour' => $tour,
            'pricing' => $pricing,
            'itineraries' => $itineraries,
            'company_info' => $this->getCompanyInfo(),
            'terms_conditions' => $this->getContractTerms(),
            'total' => $booking['total_amount'] * 1.10
        ];
        $data['html_content'] = $this->renderContractTemplate($data);

        $document_id = $this->saveDocument([
            'booking_id' => $booking_id,
            'document_type' => 'CONTRACT',
            'document_number' => $data['document_number'],
            'file_name' => "hop-dong-{$booking_id}-" . date('Ymd') . ".pdf",
            'content_html' => $data['html_content'],
            'amount' => $data['total'],
            'status' => 'Draft'
        ]);

        $data['document_id'] = $document_id;
        return $data;
    }

    /**
     * Tạo hóa đơn VAT từ booking
     * @param int $booking_id
     * @return array Document data with HTML content
     */
    public function generateInvoice($booking_id)
    {
        $booking = $this->getBookingData($booking_id);
        if (!$booking) {
            throw new Exception('Không tìm thấy booking!');
        }

        // Only generate invoice for confirmed bookings
        if ($booking['status'] !== 'Confirmed') {
            throw new Exception('Chỉ tạo hóa đơn cho booking đã xác nhận!');
        }

        $tour = $this->getTourData($booking['tour_id']);

        $data = [
            'document_type' => 'INVOICE',
            'document_number' => $this->generateDocumentNumber('HD', $booking_id),
            'invoice_series' => 'KH/25E', // Invoice series for electronic invoice
            'invoice_date' => date('d/m/Y'),
            'booking' => $booking,
            'tour' => $tour,
            'company_info' => $this->getCompanyInfo(),
            'customer_tax_info' => $this->getCustomerTaxInfo($booking['customer_id']),
            'items' => $this->prepareInvoiceItems($booking, $tour),
            'subtotal' => $booking['total_amount'],
            'vat_rate' => 10,
            'vat_amount' => $booking['total_amount'] * 0.10,
            'total' => $booking['total_amount'] * 1.10,
            'total_text' => $this->numberToText($booking['total_amount'] * 1.10)
        ];

        $data['html_content'] = $this->renderInvoiceTemplate($data);

        $document_id = $this->saveDocument([
            'booking_id' => $booking_id,
            'document_type' => 'INVOICE',
            'document_number' => $data['document_number'],
            'invoice_series' => $data['invoice_series'],
            'file_name' => "hoa-don-{$booking_id}-" . date('Ymd') . ".pdf",
            'content_html' => $data['html_content'],
            'amount' => $data['total'],
            'status' => 'Draft'
        ]);

        $data['document_id'] = $document_id;
        return $data;
    }

    // ==================== TEMPLATE RENDERING ====================

    private function renderQuoteTemplate($data)
    {
        ob_start();
        include './views/booking/templates/quote_template.php';
        return ob_get_clean();
    }

    private function renderContractTemplate($data)
    {
        ob_start();
        include './views/booking/templates/contract_template.php';
        return ob_get_clean();
    }

    private function renderInvoiceTemplate($data)
    {
        ob_start();
        include './views/booking/templates/invoice_template.php';
        return ob_get_clean();
    }

    // ==================== DATA HELPERS ====================

    private function getBookingData($booking_id)
    {
        $sql = "SELECT 
                    b.*,
                    t.tour_name,
                    t.code as tour_code,
                    t.duration_days,
                    c.full_name as customer_name,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    c.address as customer_address,
                    ts.departure_date,
                    ts.return_date,
                    ts.guide_name
                FROM bookings b
                JOIN tours t ON b.tour_id = t.tour_id
                LEFT JOIN customers c ON b.customer_id = c.customer_id
                LEFT JOIN tour_schedules ts ON b.schedule_id = ts.schedule_id
                WHERE b.booking_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetch();
    }

    private function getTourData($tour_id)
    {
        $sql = "SELECT t.*, tc.category_name
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                WHERE t.tour_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetch();
    }

    private function getTourPricing($tour_id, $tour_date = null)
    {
        $sql = "SELECT * FROM tour_pricing 
                WHERE tour_id = ? 
                AND status = 'Active'";

        $params = [$tour_id];

        if ($tour_date) {
            $sql .= " AND (valid_from IS NULL OR valid_from <= ?)
                      AND (valid_to IS NULL OR valid_to >= ?)";
            $params[] = $tour_date;
            $params[] = $tour_date;
        }

        $sql .= " ORDER BY pricing_id DESC LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    private function getScheduleServices($schedule_id)
    {
        if (!$schedule_id) {
            return [];
        }

        $sql = "SELECT ss.*, s.service_name, s.service_type
                FROM schedule_services ss
                JOIN services s ON ss.service_id = s.service_id
                WHERE ss.schedule_id = ?
                ORDER BY s.service_type, s.service_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    private function getTourItineraries($tour_id)
    {
        $sql = "SELECT * FROM tour_itinerary 
                WHERE tour_id = ? 
                ORDER BY day_number";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    private function getCompanyInfo()
    {
        return [
            'name' => 'CÔNG TY DU LỊCH ABC',
            'name_en' => 'ABC TRAVEL COMPANY',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'phone' => '(028) 3822 xxxx',
            'hotline' => '1900 xxxx',
            'email' => 'info@abctravel.vn',
            'website' => 'www.abctravel.vn',
            'tax_code' => '0123456789',
            'bank_account' => '123456789',
            'bank_name' => 'Vietcombank - Chi nhánh TP.HCM',
            'logo_url' => '/admin/views/assetz/app-assets/images/logo/logo.png'
        ];
    }

    private function getCustomerTaxInfo($customer_id)
    {
        $sql = "SELECT tax_code, company_name, company_address 
                FROM customers 
                WHERE customer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$customer_id]);
        return $stmt->fetch();
    }

    private function getContractTerms()
    {
        return [
            'payment_terms' => '- Đặt cọc 30% trong vòng 3 ngày sau khi ký hợp đồng<br>- Thanh toán 70% còn lại trước ngày khởi hành 7 ngày',
            'cancellation_policy' => '- Hủy trước 15 ngày: hoàn 80%<br>- Hủy trước 7 ngày: hoàn 50%<br>- Hủy trước 3 ngày: hoàn 30%<br>- Hủy trong 3 ngày: không hoàn',
            'responsibilities' => '- Công ty chịu trách nhiệm về chất lượng dịch vụ theo chương trình<br>- Khách hàng chịu trách nhiệm về giấy tờ, sức khỏe cá nhân',
            'force_majeure' => 'Hai bên được miễn trừ trách nhiệm trong trường hợp thiên tai, chiến tranh, dịch bệnh...'
        ];
    }

    private function prepareInvoiceItems($booking, $tour)
    {
        return [
            [
                'stt' => 1,
                'description' => "Dịch vụ tour du lịch: {$tour['tour_name']}",
                'unit' => 'Người',
                'quantity' => $booking['adult_count'] + $booking['child_count'],
                'price' => $booking['total_amount'] / ($booking['adult_count'] + $booking['child_count']),
                'amount' => $booking['total_amount']
            ]
        ];
    }

    // ==================== DOCUMENT MANAGEMENT ====================

    /**
     * Lưu thông tin document vào database
     */
    private function saveDocument($data)
    {
        $sql = "INSERT INTO booking_documents (
                    booking_id, document_type, document_number, invoice_series,
                    file_name, content_html, amount, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['booking_id'],
            $data['document_type'],
            $data['document_number'],
            $data['invoice_series'] ?? null,
            $data['file_name'],
            $data['content_html'],
            $data['amount'],
            $data['status']
        ]);

        return $this->conn->lastInsertId();
    }

    /**
     * Lấy danh sách tài liệu của booking
     */
    public function getDocumentsByBooking($booking_id)
    {
        $sql = "SELECT * FROM booking_documents 
                WHERE booking_id = ? 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin document
     */
    public function getDocumentById($document_id)
    {
        $sql = "SELECT * FROM booking_documents WHERE document_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$document_id]);
        return $stmt->fetch();
    }

    /**
     * Cập nhật trạng thái document (Draft -> Sent -> Paid)
     */
    public function updateDocumentStatus($document_id, $status)
    {
        $sql = "UPDATE booking_documents 
                SET status = ?, updated_at = NOW() 
                WHERE document_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $document_id]);
    }

    /**
     * Đánh dấu đã gửi email
     */
    public function markAsSent($document_id, $email_to)
    {
        $sql = "UPDATE booking_documents 
                SET sent_at = NOW(), 
                    sent_to_email = ?,
                    status = 'Sent'
                WHERE document_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$email_to, $document_id]);
    }

    /**
     * Lưu file path sau khi generate PDF
     */
    public function updateFilePath($document_id, $file_path)
    {
        $sql = "UPDATE booking_documents 
                SET file_path = ? 
                WHERE document_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$file_path, $document_id]);
    }

    // ==================== UTILITIES ====================

    /**
     * Generate số chứng từ (QT001, HD001, ...)
     */
    private function generateDocumentNumber($prefix, $booking_id)
    {
        $year = date('y');
        return $prefix . $year . str_pad($booking_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Chuyển số thành chữ (tiếng Việt)
     */
    private function numberToText($number)
    {
        $units = ['', 'nghìn', 'triệu', 'tỷ'];
        $digits = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];

        if ($number == 0)
            return 'Không đồng';

        $result = '';
        $unitIndex = 0;

        while ($number > 0) {
            $temp = $number % 1000;
            if ($temp > 0) {
                $str = '';

                $hundreds = floor($temp / 100);
                if ($hundreds > 0) {
                    $str .= $digits[$hundreds] . ' trăm ';
                }

                $tens = floor(($temp % 100) / 10);
                $ones = $temp % 10;

                if ($tens > 1) {
                    $str .= $digits[$tens] . ' mươi ';
                    if ($ones > 0) {
                        $str .= $digits[$ones] . ' ';
                    }
                } elseif ($tens == 1) {
                    $str .= 'mười ';
                    if ($ones > 0) {
                        $str .= $digits[$ones] . ' ';
                    }
                } else {
                    if ($ones > 0 && $hundreds > 0) {
                        $str .= 'lẻ ';
                    }
                    if ($ones > 0) {
                        $str .= $digits[$ones] . ' ';
                    }
                }

                $result = $str . $units[$unitIndex] . ' ' . $result;
            }

            $number = floor($number / 1000);
            $unitIndex++;
        }

        return ucfirst(trim($result)) . ' đồng';
    }

    /**
     * Kiểm tra quyền truy cập document
     */
    public function canAccess($document_id, $user_id = null)
    {
        if (!$user_id) {
            $user_id = $_SESSION['user_id'] ?? null;
        }

        $sql = "SELECT bd.* 
                FROM booking_documents bd
                WHERE bd.document_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$document_id]);
        $doc = $stmt->fetch();

        if (!$doc)
            return false;

        // Admin có full access
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN') {
            return true;
        }

        // Các role khác cũng có thể xem (vì bookings không track created_by)
        // Trong tương lai có thể thêm logic phức tạp hơn
        return true;
    }
}
