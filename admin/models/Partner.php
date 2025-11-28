<?php
class Partner
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== DANH SÁCH ĐỐI TÁC ====================

    public function getAll()
    {
        $sql = "SELECT * FROM partners ORDER BY partner_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActive()
    {
        $sql = "SELECT * FROM partners WHERE status = 1 ORDER BY partner_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM partners WHERE partner_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByType($type)
    {
        $sql = "SELECT * FROM partners WHERE partner_type = ? AND status = 1 ORDER BY partner_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    // ==================== THÊM ĐỐI TÁC ====================

    public function create($data)
    {
        // Validate
        if (empty($data['partner_name'])) {
            throw new Exception('Tên đối tác không được để trống!');
        }

        // Kiểm tra trùng tên
        $checkSql = "SELECT COUNT(*) FROM partners WHERE partner_name = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$data['partner_name']]);
        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception('Tên đối tác đã tồn tại!');
        }

        $sql = "INSERT INTO partners (
                    partner_name, partner_type, contact_person, phone, email, 
                    address, tax_code, bank_account, bank_name, rating, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['partner_name'],
            $data['partner_type'] ?? 'Other',
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['tax_code'] ?? null,
            $data['bank_account'] ?? null,
            $data['bank_name'] ?? null,
            $data['rating'] ?? 0.00,
            $data['status'] ?? 1,
            $data['notes'] ?? null
        ]);

        if (!$result) {
            throw new Exception('Thêm đối tác thất bại!');
        }

        return $this->conn->lastInsertId();
    }

    // ==================== CẬP NHẬT ĐỐI TÁC ====================

    public function update($id, $data)
    {
        // Validate
        if (empty($data['partner_name'])) {
            throw new Exception('Tên đối tác không được để trống!');
        }

        // Kiểm tra trùng tên (trừ chính nó)
        $checkSql = "SELECT COUNT(*) FROM partners WHERE partner_name = ? AND partner_id != ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$data['partner_name'], $id]);
        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception('Tên đối tác đã tồn tại!');
        }

        $sql = "UPDATE partners SET 
                    partner_name = ?, 
                    partner_type = ?, 
                    contact_person = ?, 
                    phone = ?, 
                    email = ?, 
                    address = ?, 
                    tax_code = ?, 
                    bank_account = ?, 
                    bank_name = ?, 
                    rating = ?, 
                    status = ?, 
                    notes = ?
                WHERE partner_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['partner_name'],
            $data['partner_type'] ?? 'Other',
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['tax_code'] ?? null,
            $data['bank_account'] ?? null,
            $data['bank_name'] ?? null,
            $data['rating'] ?? 0.00,
            $data['status'] ?? 1,
            $data['notes'] ?? null,
            $id
        ]);
    }

    // ==================== XÓA ĐỐI TÁC ====================

    public function delete($id)
    {
        // Kiểm tra đối tác có dịch vụ không
        $checkSql = "SELECT COUNT(*) FROM services WHERE partner_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$id]);

        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception('Không thể xóa đối tác đã có dịch vụ! Vui lòng xóa dịch vụ trước.');
        }

        $sql = "DELETE FROM partners WHERE partner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ==================== THỐNG KÊ ====================

    public function getStatistics()
    {
        $sql = "SELECT 
                    partner_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_count,
                    AVG(rating) as avg_rating
                FROM partners
                GROUP BY partner_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getServicesCount($partner_id)
    {
        $sql = "SELECT COUNT(*) FROM services WHERE partner_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$partner_id]);
        return $stmt->fetchColumn();
    }

    // ==================== TÌM KIẾM ====================

    public function search($filters = [])
    {
        $sql = "SELECT * FROM partners WHERE 1=1";
        $params = [];

        if (!empty($filters['partner_type'])) {
            $sql .= " AND partner_type = ?";
            $params[] = $filters['partner_type'];
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = ?";
            $params[] = (int)$filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (partner_name LIKE ? OR contact_person LIKE ? OR phone LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY partner_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ==================== CẬP NHẬT ĐÁNH GIÁ ====================

    public function updateRating($partner_id, $rating)
    {
        if ($rating < 0 || $rating > 5) {
            throw new Exception('Đánh giá phải từ 0 đến 5!');
        }

        $sql = "UPDATE partners SET rating = ? WHERE partner_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$rating, $partner_id]);
    }
}
