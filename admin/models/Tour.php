<?php
class Tour
{
    public $conn;
    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getAll()
    {
        $sql = "SELECT 
                    t.*,
                    tc.category_name,
                    tm.file_path as tour_image,
                    tp.price_adult as tour_price
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_media tm ON t.tour_id = tm.tour_id AND tm.is_featured = 1
                LEFT JOIN tour_prices tp ON t.tour_id = tp.tour_id
                ORDER BY t.tour_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByCategory($category_id)
    {
        $sql = "SELECT 
                    t.*,
                    tc.category_name,
                    tm.file_path as tour_image,
                    tp.price_adult as tour_price
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_media tm ON t.tour_id = tm.tour_id AND tm.is_featured = 1
                LEFT JOIN tour_prices tp ON t.tour_id = tp.tour_id
                WHERE t.category_id = :category_id
                ORDER BY t.tour_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT 
                    t.*,
                    tc.category_name,
                    tm.file_path as tour_image,
                    tp.price_adult as tour_price
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_media tm ON t.tour_id = tm.tour_id AND tm.is_featured = 1
                LEFT JOIN tour_prices tp ON t.tour_id = tp.tour_id
                WHERE t.tour_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        // Insert vào bảng tours (chỉ các cột tồn tại trong bảng)
        $sql = "INSERT INTO tours (category_id, tour_name, code, description_short, description_full, duration_days, start_location, status, created_at)
                VALUES (:category_id, :tour_name, :code, :description_short, :description_full, :duration_days, :start_location, :status, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'category_id' => $data['category_id'] ?? null,
            'tour_name' => $data['tour_name'] ?? '',
            'code' => $data['code'] ?? '',
            'description_short' => $data['description_short'] ?? '',
            'description_full' => $data['description_full'] ?? '',
            'duration_days' => $data['duration_days'] ?? null,
            'start_location' => $data['start_location'] ?? '',
            'status' => $data['status'] ?? 'Draft'
        ]);
        $tour_id = $this->conn->lastInsertId();

        // Nếu có hình ảnh, insert vào bảng tour_media
        if (!empty($data['tour_image'])) {
            $sqlMedia = "INSERT INTO tour_media (tour_id, file_path, is_featured) VALUES (:tour_id, :file_path, 1)";
            $stmtMedia = $this->conn->prepare($sqlMedia);
            $stmtMedia->execute([
                'tour_id' => $tour_id,
                'file_path' => $data['tour_image']
            ]);
        }

        // Nếu có giá, insert vào bảng tour_prices
        if (!empty($data['tour_price'])) {
            $sqlPrice = "INSERT INTO tour_prices (tour_id, package_name, price_adult, valid_from, valid_to)
                         VALUES (:tour_id, 'Standard', :price_adult, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR))";
            $stmtPrice = $this->conn->prepare($sqlPrice);
            $stmtPrice->execute([
                'tour_id' => $tour_id,
                'price_adult' => $data['tour_price']
            ]);
        }

        return $tour_id;
    }

    // ===================== Danh mục Tour =====================

    public function getCategories()
    {
        $sql = "SELECT * FROM tour_categories ORDER BY category_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addCategory($name)
    {
        $sql = "INSERT INTO tour_categories(category_name, status) VALUES(?, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name]);
    }

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM tour_categories WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM tour_categories WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateCategory($id, $name)
    {
        $sql = "UPDATE tour_categories SET category_name = ? WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $id]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE tours 
                SET category_id = :category_id,
                    tour_name = :tour_name,
                    code = :code,
                    description_short = :description_short,
                    description_full = :description_full,
                    duration_days = :duration_days,
                    start_location = :start_location,
                    status = :status
                WHERE tour_id = :id";
        $stmt = $this->conn->prepare($sql);

        $result = $stmt->execute([
            'category_id' => $data['category_id'] ?? null,
            'tour_name' => $data['tour_name'] ?? '',
            'code' => $data['code'] ?? '',
            'description_short' => $data['description_short'] ?? '',
            'description_full' => $data['description_full'] ?? '',
            'duration_days' => $data['duration_days'] ?? null,
            'start_location' => $data['start_location'] ?? '',
            'status' => $data['status'] ?? 'Draft',
            'id' => $id
        ]);

        // Cập nhật ảnh nếu có
        if (!empty($data['tour_image'])) {
            // Xóa ảnh cũ
            $sqlDeleteMedia = "DELETE FROM tour_media WHERE tour_id = :tour_id AND is_featured = 1";
            $stmtDeleteMedia = $this->conn->prepare($sqlDeleteMedia);
            $stmtDeleteMedia->execute(['tour_id' => $id]);

            // Thêm ảnh mới
            $sqlMedia = "INSERT INTO tour_media (tour_id, file_path, is_featured) VALUES (:tour_id, :file_path, 1)";
            $stmtMedia = $this->conn->prepare($sqlMedia);
            $stmtMedia->execute([
                'tour_id' => $id,
                'file_path' => $data['tour_image']
            ]);
        }

        // Cập nhật giá nếu có
        if (!empty($data['tour_price'])) {
            // Xóa giá cũ
            $sqlDeletePrice = "DELETE FROM tour_prices WHERE tour_id = :tour_id";
            $stmtDeletePrice = $this->conn->prepare($sqlDeletePrice);
            $stmtDeletePrice->execute(['tour_id' => $id]);

            // Thêm giá mới
            $sqlPrice = "INSERT INTO tour_prices (tour_id, package_name, price_adult, valid_from, valid_to)
                         VALUES (:tour_id, 'Standard', :price_adult, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR))";
            $stmtPrice = $this->conn->prepare($sqlPrice);
            $stmtPrice->execute([
                'tour_id' => $id,
                'price_adult' => $data['tour_price']
            ]);
        }

        return $result;
    }

    public function delete($id)
    {
        // Xóa các bản ghi liên quan trước
        $sqlMedia = "DELETE FROM tour_media WHERE tour_id = :id";
        $stmtMedia = $this->conn->prepare($sqlMedia);
        $stmtMedia->execute(['id' => $id]);

        $sqlPrice = "DELETE FROM tour_prices WHERE tour_id = :id";
        $stmtPrice = $this->conn->prepare($sqlPrice);
        $stmtPrice->execute(['id' => $id]);

        // Xóa tour
        $sql = "DELETE FROM tours WHERE tour_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

}

