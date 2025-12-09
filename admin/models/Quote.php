<?php
class Quote
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // Tạo báo giá mới - UC4 Enhanced
    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO tour_quotes (
                        tour_id, pricing_id, departure_date, 
                        customer_name, customer_email, customer_phone, customer_address, customer_company,
                        adult_count, child_count, infant_count, single_room_count,
                        subtotal, discount_type, discount_value, discount_amount,
                        tax_percent, tax_amount, service_fee, total_amount,
                        payment_method, validity_days, special_requests, internal_notes,
                        quote_status, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['tour_id'],
                $data['pricing_id'] ?? null,
                $data['departure_date'] ?? null,
                $data['customer_name'],
                $data['customer_email'] ?? null,
                $data['customer_phone'] ?? null,
                $data['customer_address'] ?? null,
                $data['customer_company'] ?? null,
                (int) ($data['adult_count'] ?? 0),
                (int) ($data['child_count'] ?? 0),
                (int) ($data['infant_count'] ?? 0),
                (int) ($data['single_room_count'] ?? 0),
                (float) ($data['subtotal'] ?? 0),
                $data['discount_type'] ?? null,
                (float) ($data['discount_value'] ?? 0),
                (float) ($data['discount_amount'] ?? 0),
                (float) ($data['tax_percent'] ?? 10),
                (float) ($data['tax_amount'] ?? 0),
                (float) ($data['service_fee'] ?? 0),
                (float) ($data['total_amount'] ?? 0),
                $data['payment_method'] ?? null,
                (int) ($data['validity_days'] ?? 7),
                $data['special_requests'] ?? null,
                $data['internal_notes'] ?? null,
                $data['quote_status'] ?? 'draft',
                $_SESSION['user_id'] ?? null
            ]);

            $quote_id = $this->conn->lastInsertId();

            // UC4: Log initial status
            $this->logStatusChange($quote_id, 'draft', $_SESSION['user_id'] ?? null, 'Báo giá được tạo');

            $this->conn->commit();
            return $quote_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // UC4: Thêm breakdown item
    public function addBreakdownItem($quote_id, $item)
    {
        $sql = "INSERT INTO quote_breakdown (
                    quote_id, item_type, item_name, quantity, unit_price, total_price
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $quote_id,
            $item['item_type'] ?? 'other',
            $item['item_name'],
            (int) ($item['quantity'] ?? 1),
            (float) ($item['unit_price'] ?? 0),
            (float) ($item['total_price'] ?? 0)
        ]);
    }

    // UC4: Lấy breakdown của báo giá
    public function getBreakdown($quote_id)
    {
        $sql = "SELECT * FROM quote_breakdown WHERE quote_id = ? ORDER BY breakdown_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$quote_id]);
        return $stmt->fetchAll();
    }

    // UC4: Lưu lịch sử thay đổi status
    public function logStatusChange($quote_id, $new_status, $changed_by, $notes = null)
    {
        $sql = "INSERT INTO quote_status_history (
                    quote_id, old_status, new_status, changed_by, notes, ip_address, user_agent
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Get current status
        $current = $this->getById($quote_id);
        $old_status = $current ? $current['quote_status'] : null;

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $quote_id,
            $old_status,
            $new_status,
            $changed_by,
            $notes,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    // UC4: Lấy lịch sử status
    public function getStatusHistory($quote_id)
    {
        $sql = "SELECT qsh.*, u.full_name as changed_by_name
                FROM quote_status_history qsh
                LEFT JOIN users u ON qsh.changed_by = u.user_id
                WHERE qsh.quote_id = ?
                ORDER BY qsh.changed_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$quote_id]);
        return $stmt->fetchAll();
    }

    // UC4: Cập nhật status sent
    public function updateSentStatus($quote_id, $method, $recipient)
    {
        $sql = "UPDATE tour_quotes 
                SET quote_status = 'sent', 
                    sent_at = NOW(), 
                    sent_via = ?,
                    sent_to = ?,
                    updated_at = NOW() 
                WHERE quote_id = ?";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$method, $recipient, $quote_id]);

        if ($result) {
            $this->logStatusChange($quote_id, 'sent', $_SESSION['user_id'] ?? null, "Gửi qua $method đến $recipient");
        }

        return $result;
    }

    // Lấy danh sách báo giá - UC4 Enhanced
    public function getAll($filters = [])
    {
        $sql = "SELECT 
                    q.*,
                    t.tour_name,
                    t.code as tour_code,
                    u.full_name as creator_name
                FROM tour_quotes q
                LEFT JOIN tours t ON q.tour_id = t.tour_id
                LEFT JOIN users u ON q.created_by = u.user_id
                WHERE 1=1";

        $params = [];

        // UC4: Filter by quote_status instead of status
        if (!empty($filters['status'])) {
            $sql .= " AND q.quote_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (q.customer_name LIKE ? OR q.customer_email LIKE ? OR q.customer_phone LIKE ? OR t.tour_name LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // UC4: Filter by date range
        if (!empty($filters['date_from'])) {
            $sql .= " AND q.created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND q.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $sql .= " ORDER BY q.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy chi tiết báo giá - UC4 Enhanced
    public function getById($id)
    {
        $sql = "SELECT 
                    q.*,
                    t.tour_name,
                    t.code as tour_code,
                    t.duration_days,
                    tp.package_name,
                    u.full_name as creator_name
                FROM tour_quotes q
                LEFT JOIN tours t ON q.tour_id = t.tour_id
                LEFT JOIN tour_pricing tp ON q.pricing_id = tp.pricing_id
                LEFT JOIN users u ON q.created_by = u.user_id
                WHERE q.quote_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Lấy các dịch vụ bổ sung của báo giá
    public function getOptions($quote_id)
    {
        $sql = "SELECT * FROM quote_options WHERE quote_id = ? ORDER BY option_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$quote_id]);
        return $stmt->fetchAll();
    }

    // Tính tổng tiền báo giá
    public function calculateTotal($data)
    {
        $base = (float) ($data['base_price'] ?? 0);
        $discount = 0;

        if ($data['discount_type'] === 'percent') {
            $discount = $base * ((float) ($data['discount_value'] ?? 0) / 100);
        } elseif ($data['discount_type'] === 'fixed') {
            $discount = (float) ($data['discount_value'] ?? 0);
        }

        $afterDiscount = $base - $discount;
        $fees = (float) ($data['additional_fees'] ?? 0);
        $tax = $afterDiscount * ((float) ($data['tax_percent'] ?? 0) / 100);

        return round($afterDiscount + $fees + $tax, 2);
    }

    // Cập nhật trạng thái báo giá - UC4 Enhanced
    public function updateStatus($id, $status, $user_id = null, $notes = null)
    {
        $sql = "UPDATE tour_quotes SET quote_status = ?, updated_at = NOW() WHERE quote_id = ?";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$status, $id]);

        if ($result) {
            $this->logStatusChange($id, $status, $user_id, $notes);
        }

        return $result;
    }

    // Lưu lịch sử gửi báo giá
    public function logSend($quote_id, $method, $recipient, $status, $notes = null)
    {
        try {
            $sql = "INSERT INTO quote_send_logs (quote_id, send_method, recipient, send_status, notes) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$quote_id, $method, $recipient, $status, $notes]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Xóa báo giá - UC4 Enhanced
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa breakdown items
            $sql = "DELETE FROM quote_breakdown WHERE quote_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            // Xóa status history
            $sql = "DELETE FROM quote_status_history WHERE quote_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            // Xóa báo giá
            $sql = "DELETE FROM tour_quotes WHERE quote_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Lấy thống kê báo giá - UC4 Enhanced
    public function getStatistics()
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as total,
                    SUM(total_amount) as total_value
                FROM tour_quotes
                GROUP BY status";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        // Add summary
        $summary = [
            'total_quotes' => 0,
            'total_value' => 0,
            'by_status' => []
        ];

        foreach ($results as $row) {
            $summary['total_quotes'] += $row['total'];
            $summary['total_value'] += $row['total_value'];
            $summary['by_status'][$row['status']] = $row;
        }

        return $summary;
    }

    // UC4: Lấy quote templates
    public function getTemplates()
    {
        $sql = "SELECT * FROM quote_templates WHERE is_active = 1 ORDER BY template_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UC4: Lấy template theo ID
    public function getTemplateById($template_id)
    {
        $sql = "SELECT * FROM quote_templates WHERE template_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$template_id]);
        return $stmt->fetch();
    }
}
