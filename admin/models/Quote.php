<?php
class Quote
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // Tạo báo giá mới
    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO tour_quotes (
                        tour_id, departure_date, customer_name, customer_email, 
                        customer_phone, customer_address, num_adults, num_children, 
                        num_infants, base_price, discount_type, discount_value, 
                        additional_fees, tax_percent, total_amount, validity_days, 
                        internal_notes, status, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['tour_id'],
                $data['departure_date'] ?? null,
                $data['customer_name'],
                $data['customer_email'] ?? null,
                $data['customer_phone'] ?? null,
                $data['customer_address'] ?? null,
                (int) ($data['num_adults'] ?? 0),
                (int) ($data['num_children'] ?? 0),
                (int) ($data['num_infants'] ?? 0),
                (float) ($data['base_price'] ?? 0),
                $data['discount_type'] ?? 'none', // 'none', 'percent', 'fixed'
                (float) ($data['discount_value'] ?? 0),
                (float) ($data['additional_fees'] ?? 0),
                (float) ($data['tax_percent'] ?? 0),
                (float) ($data['total_amount'] ?? 0),
                (int) ($data['validity_days'] ?? 7),
                $data['internal_notes'] ?? null,
                $data['status'] ?? 'Đang chờ',
                $_SESSION['user_id'] ?? null
            ]);

            $quote_id = $this->conn->lastInsertId();

            // Lưu các dịch vụ bổ sung (nếu có)
            if (!empty($data['options']) && is_array($data['options'])) {
                $sqlOpt = "INSERT INTO quote_options (quote_id, option_name, option_price, quantity) 
                           VALUES (?, ?, ?, ?)";
                $stmtOpt = $this->conn->prepare($sqlOpt);
                foreach ($data['options'] as $opt) {
                    if (!empty($opt['option_name'])) {
                        $stmtOpt->execute([
                            $quote_id,
                            $opt['option_name'],
                            (float) ($opt['option_price'] ?? 0),
                            (int) ($opt['quantity'] ?? 1)
                        ]);
                    }
                }
            }

            $this->conn->commit();
            return $quote_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Lấy danh sách báo giá
    public function getAll($filters = [])
    {
        $sql = "SELECT 
                    q.*,
                    t.tour_name,
                    t.code as tour_code,
                    u.username as creator_name
                FROM tour_quotes q
                LEFT JOIN tours t ON q.tour_id = t.tour_id
                LEFT JOIN users u ON q.created_by = u.user_id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND q.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (q.customer_name LIKE ? OR q.customer_email LIKE ? OR q.customer_phone LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY q.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy chi tiết báo giá
    public function getById($id)
    {
        $sql = "SELECT 
                    q.*,
                    t.tour_name,
                    t.code as tour_code,
                    t.duration_days,
                    u.username as creator_name
                FROM tour_quotes q
                LEFT JOIN tours t ON q.tour_id = t.tour_id
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

    // Cập nhật trạng thái báo giá
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE tour_quotes SET status = ?, updated_at = NOW() WHERE quote_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $id]);
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

    // Xóa báo giá
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa options trước
            $sql = "DELETE FROM quote_options WHERE quote_id = ?";
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

    // Lấy thống kê báo giá
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
        return $stmt->fetchAll();
    }
}
