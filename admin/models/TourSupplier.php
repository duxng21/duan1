<?php
/**
 * TourSupplier Model
 * Quản lý nhà cung cấp/đối tác (Khách sạn, Nhà hàng, Vận chuyển...)
 * Use Case 2: Quản lý thông tin chi tiết tour
 */
class TourSupplier
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    /**
     * Lấy tất cả nhà cung cấp
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM tour_suppliers WHERE 1=1";
        $params = [];

        // Filter theo loại
        if (!empty($filters['supplier_type'])) {
            $sql .= " AND supplier_type = ?";
            $params[] = $filters['supplier_type'];
        }

        // Filter theo trạng thái
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = ?";
            $params[] = (int) $filters['status'];
        }

        // Search theo tên
        if (!empty($filters['search'])) {
            $sql .= " AND (supplier_name LIKE ? OR supplier_code LIKE ? OR contact_person LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Filter theo rating tối thiểu
        if (!empty($filters['min_rating'])) {
            $sql .= " AND rating >= ?";
            $params[] = $filters['min_rating'];
        }

        $sql .= " ORDER BY supplier_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy nhà cung cấp theo ID
     */
    public function getById($supplier_id)
    {
        $sql = "SELECT * FROM tour_suppliers WHERE supplier_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$supplier_id]);
        return $stmt->fetch();
    }

    /**
     * Lấy nhà cung cấp theo mã
     */
    public function getByCode($supplier_code)
    {
        $sql = "SELECT * FROM tour_suppliers WHERE supplier_code = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$supplier_code]);
        return $stmt->fetch();
    }

    /**
     * Lấy nhà cung cấp theo loại
     */
    public function getByType($supplier_type, $active_only = true)
    {
        $sql = "SELECT * FROM tour_suppliers WHERE supplier_type = ?";
        if ($active_only) {
            $sql .= " AND status = 1";
        }
        $sql .= " ORDER BY supplier_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$supplier_type]);
        return $stmt->fetchAll();
    }

    /**
     * Tạo nhà cung cấp mới
     */
    public function create($data)
    {
        // Kiểm tra trùng mã (nếu có)
        if (!empty($data['supplier_code'])) {
            $existing = $this->getByCode($data['supplier_code']);
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Mã nhà cung cấp đã tồn tại!'
                ];
            }
        }

        $sql = "INSERT INTO tour_suppliers (
                    supplier_name, supplier_code, supplier_type,
                    contact_person, phone, email, address, website,
                    contract_number, contract_start_date, contract_end_date, contract_file,
                    payment_terms, cancellation_policy,
                    rating, notes, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['supplier_name'],
            $data['supplier_code'] ?? null,
            $data['supplier_type'],
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['website'] ?? null,
            $data['contract_number'] ?? null,
            $data['contract_start_date'] ?? null,
            $data['contract_end_date'] ?? null,
            $data['contract_file'] ?? null,
            $data['payment_terms'] ?? null,
            $data['cancellation_policy'] ?? null,
            $data['rating'] ?? 0,
            $data['notes'] ?? null,
            $data['status'] ?? 1
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Thêm nhà cung cấp thành công!',
                'supplier_id' => $this->conn->lastInsertId()
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể thêm nhà cung cấp!'
        ];
    }

    /**
     * Cập nhật nhà cung cấp
     */
    public function update($supplier_id, $data)
    {
        // Kiểm tra trùng mã (nếu có và khác với supplier hiện tại)
        if (!empty($data['supplier_code'])) {
            $existing = $this->getByCode($data['supplier_code']);
            if ($existing && $existing['supplier_id'] != $supplier_id) {
                return [
                    'success' => false,
                    'message' => 'Mã nhà cung cấp đã tồn tại!'
                ];
            }
        }

        $sql = "UPDATE tour_suppliers SET
                    supplier_name = ?,
                    supplier_code = ?,
                    supplier_type = ?,
                    contact_person = ?,
                    phone = ?,
                    email = ?,
                    address = ?,
                    website = ?,
                    contract_number = ?,
                    contract_start_date = ?,
                    contract_end_date = ?,
                    contract_file = ?,
                    payment_terms = ?,
                    cancellation_policy = ?,
                    rating = ?,
                    notes = ?,
                    status = ?
                WHERE supplier_id = ?";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['supplier_name'],
            $data['supplier_code'] ?? null,
            $data['supplier_type'],
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['website'] ?? null,
            $data['contract_number'] ?? null,
            $data['contract_start_date'] ?? null,
            $data['contract_end_date'] ?? null,
            $data['contract_file'] ?? null,
            $data['payment_terms'] ?? null,
            $data['cancellation_policy'] ?? null,
            $data['rating'] ?? 0,
            $data['notes'] ?? null,
            $data['status'] ?? 1,
            $supplier_id
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật nhà cung cấp thành công!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể cập nhật nhà cung cấp!'
        ];
    }

    /**
     * Xóa nhà cung cấp
     */
    public function delete($supplier_id)
    {
        // Kiểm tra có tour nào đang dùng không
        $check = $this->checkUsage($supplier_id);
        if ($check['in_use']) {
            return [
                'success' => false,
                'message' => "Không thể xóa! Nhà cung cấp đang được sử dụng trong {$check['tour_count']} tour."
            ];
        }

        $sql = "DELETE FROM tour_suppliers WHERE supplier_id = ?";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$supplier_id]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Xóa nhà cung cấp thành công!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể xóa nhà cung cấp!'
        ];
    }

    /**
     * Kiểm tra nhà cung cấp có đang được sử dụng không
     */
    public function checkUsage($supplier_id)
    {
        $sql = "SELECT COUNT(DISTINCT tour_id) as tour_count 
                FROM tour_supplier_links 
                WHERE supplier_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$supplier_id]);
        $result = $stmt->fetch();

        return [
            'in_use' => $result['tour_count'] > 0,
            'tour_count' => $result['tour_count']
        ];
    }

    // ==================== LIÊN KẾT VỚI TOUR ====================

    /**
     * Lấy danh sách nhà cung cấp của 1 tour
     */
    public function getSuppliersByTourId($tour_id)
    {
        return $this->getSuppliersByTour($tour_id);
    }

    public function getSuppliersByTour($tour_id)
    {
        $sql = "SELECT tsl.*, ts.supplier_name, ts.supplier_type, ts.phone, ts.email
                FROM tour_supplier_links tsl
                INNER JOIN tour_suppliers ts ON tsl.supplier_id = ts.supplier_id
                WHERE tsl.tour_id = ? AND tsl.status = 1
                ORDER BY tsl.service_day ASC, ts.supplier_type ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách tour của 1 nhà cung cấp
     */
    public function getToursBySupplier($supplier_id)
    {
        $sql = "SELECT tsl.*, t.tour_name, t.code
                FROM tour_supplier_links tsl
                INNER JOIN tours t ON tsl.tour_id = t.tour_id
                WHERE tsl.supplier_id = ? AND tsl.status = 1
                ORDER BY t.tour_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$supplier_id]);
        return $stmt->fetchAll();
    }

    /**
     * Liên kết nhà cung cấp với tour
     */
    public function linkToTour($tour_id, $supplier_id, $data)
    {
        $sql = "INSERT INTO tour_supplier_links (
                    tour_id, supplier_id, service_date, service_day,
                    service_type, service_description,
                    unit_price, quantity, total_price, currency,
                    cancellation_deadline, cancellation_fee,
                    emergency_contact, emergency_phone,
                    notes, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $quantity = $data['quantity'] ?? 1;
        $unit_price = $data['unit_price'] ?? 0;
        $total_price = $quantity * $unit_price;

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $tour_id,
            $supplier_id,
            $data['service_date'] ?? null,
            $data['service_day'] ?? null,
            $data['service_type'] ?? null,
            $data['service_description'] ?? null,
            $unit_price,
            $quantity,
            $total_price,
            $data['currency'] ?? 'VND',
            $data['cancellation_deadline'] ?? null,
            $data['cancellation_fee'] ?? 0,
            $data['emergency_contact'] ?? null,
            $data['emergency_phone'] ?? null,
            $data['notes'] ?? null,
            1
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Liên kết nhà cung cấp thành công!',
                'link_id' => $this->conn->lastInsertId()
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể liên kết nhà cung cấp!'
        ];
    }

    /**
     * Cập nhật liên kết
     */
    public function updateLink($link_id, $data)
    {
        $quantity = $data['quantity'] ?? 1;
        $unit_price = $data['unit_price'] ?? 0;
        $total_price = $quantity * $unit_price;

        $sql = "UPDATE tour_supplier_links SET
                    service_date = ?,
                    service_day = ?,
                    service_type = ?,
                    service_description = ?,
                    unit_price = ?,
                    quantity = ?,
                    total_price = ?,
                    currency = ?,
                    cancellation_deadline = ?,
                    cancellation_fee = ?,
                    emergency_contact = ?,
                    emergency_phone = ?,
                    notes = ?
                WHERE link_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['service_date'] ?? null,
            $data['service_day'] ?? null,
            $data['service_type'] ?? null,
            $data['service_description'] ?? null,
            $unit_price,
            $quantity,
            $total_price,
            $data['currency'] ?? 'VND',
            $data['cancellation_deadline'] ?? null,
            $data['cancellation_fee'] ?? 0,
            $data['emergency_contact'] ?? null,
            $data['emergency_phone'] ?? null,
            $data['notes'] ?? null,
            $link_id
        ]);
    }

    /**
     * Gỡ liên kết nhà cung cấp khỏi tour
     */
    public function unlinkFromTour($link_id)
    {
        $sql = "DELETE FROM tour_supplier_links WHERE link_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$link_id]);
    }

    /**
     * Lấy thông tin 1 liên kết
     */
    public function getLinkById($link_id)
    {
        $sql = "SELECT tsl.*, ts.supplier_name, ts.supplier_type
                FROM tour_supplier_links tsl
                INNER JOIN tour_suppliers ts ON tsl.supplier_id = ts.supplier_id
                WHERE tsl.link_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$link_id]);
        return $stmt->fetch();
    }

    /**
     * Thống kê theo loại nhà cung cấp
     */
    public function getStatsByType()
    {
        $sql = "SELECT 
                    supplier_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
                    AVG(rating) as avg_rating
                FROM tour_suppliers
                GROUP BY supplier_type
                ORDER BY total DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll();
    }
}
