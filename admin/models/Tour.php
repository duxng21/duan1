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
                    tm.file_path AS tour_image,
                    tp.price_adult AS tour_price,
                    -- Tổng số lịch trình (itinerary)
                    (SELECT COUNT(*) FROM tour_itineraries iti WHERE iti.tour_id = t.tour_id) AS itinerary_count,
                    -- Tổng số ảnh
                    (SELECT COUNT(*) FROM tour_media m WHERE m.tour_id = t.tour_id) AS image_count,
                    -- Tổng số lịch khởi hành (schedules)
                    (SELECT COUNT(*) FROM tour_schedules ts WHERE ts.tour_id = t.tour_id AND ts.status != 'Cancelled') AS schedule_count,
                    -- Danh sách tags (group concat)
                    (SELECT GROUP_CONCAT(tt.tag_name SEPARATOR ', ') FROM tour_tag_relations r JOIN tour_tags tt ON r.tag_id = tt.tag_id WHERE r.tour_id = t.tour_id) AS tag_list,
                    -- Đã có chính sách chưa
                    (SELECT COUNT(*) FROM tour_policies p WHERE p.tour_id = t.tour_id) AS has_policies
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
                    tm.file_path AS tour_image,
                    tp.price_adult AS tour_price,
                    (SELECT COUNT(*) FROM tour_itineraries iti WHERE iti.tour_id = t.tour_id) AS itinerary_count,
                    (SELECT COUNT(*) FROM tour_media m WHERE m.tour_id = t.tour_id) AS image_count,
                    (SELECT COUNT(*) FROM tour_schedules ts WHERE ts.tour_id = t.tour_id AND ts.status != 'Cancelled') AS schedule_count,
                    (SELECT GROUP_CONCAT(tt.tag_name SEPARATOR ', ') FROM tour_tag_relations r JOIN tour_tags tt ON r.tag_id = tt.tag_id WHERE r.tour_id = t.tour_id) AS tag_list,
                    (SELECT COUNT(*) FROM tour_policies p WHERE p.tour_id = t.tour_id) AS has_policies
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_media tm ON t.tour_id = tm.tour_id AND tm.is_featured = 1
                LEFT JOIN tour_prices tp ON t.tour_id = tp.tour_id
                WHERE t.category_id = ?
                ORDER BY t.tour_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$category_id]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT 
                    t.*,
                    tc.category_name,
                    tm.file_path AS tour_image,
                    tp.price_adult AS tour_price,
                    (SELECT COUNT(*) FROM tour_itineraries iti WHERE iti.tour_id = t.tour_id) AS itinerary_count,
                    (SELECT COUNT(*) FROM tour_media m WHERE m.tour_id = t.tour_id) AS image_count,
                    (SELECT COUNT(*) FROM tour_schedules ts WHERE ts.tour_id = t.tour_id AND ts.status != 'Cancelled') AS schedule_count,
                    (SELECT GROUP_CONCAT(tt.tag_name SEPARATOR ', ') FROM tour_tag_relations r JOIN tour_tags tt ON r.tag_id = tt.tag_id WHERE r.tour_id = t.tour_id) AS tag_list,
                    (SELECT COUNT(*) FROM tour_policies p WHERE p.tour_id = t.tour_id) AS has_policies
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_media tm ON t.tour_id = tm.tour_id AND tm.is_featured = 1
                LEFT JOIN tour_prices tp ON t.tour_id = tp.tour_id
                WHERE t.tour_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            // Insert vào bảng tours
            $sql = "INSERT INTO tours (category_id, tour_name, code) 
                    VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['category_id'],
                $data['tour_name'],
                $data['code']
            ]);

            $tour_id = $this->conn->lastInsertId();

            // Insert ảnh vào tour_media
            if (!empty($data['tour_image'])) {
                $sql = "INSERT INTO tour_media (tour_id, file_path, is_featured) VALUES (?, ?, 1)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$tour_id, $data['tour_image']]);
            }

            // Insert giá vào tour_prices
            if (!empty($data['tour_price'])) {
                $sql = "INSERT INTO tour_prices (tour_id, package_name, price_adult) VALUES (?, 'Standard', ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$tour_id, $data['tour_price']]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        try {
            $this->conn->beginTransaction();

            // Update bảng tours
            $sql = "UPDATE tours SET 
                    category_id = ?,
                    tour_name = ?,
                    code = ?
                    WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['category_id'],
                $data['tour_name'],
                $data['code'],
                $id
            ]);

            // Update ảnh nếu có upload mới
            if (!empty($data['tour_image'])) {
                // Xóa ảnh cũ
                $sql = "DELETE FROM tour_media WHERE tour_id = ? AND is_featured = 1";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id]);

                // Insert ảnh mới
                $sql = "INSERT INTO tour_media (tour_id, file_path, is_featured) VALUES (?, ?, 1)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id, $data['tour_image']]);
            }

            // Update giá
            if (!empty($data['tour_price'])) {
                // Check xem đã có giá chưa
                $sql = "SELECT COUNT(*) FROM tour_prices WHERE tour_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $sql = "UPDATE tour_prices SET price_adult = ? WHERE tour_id = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([$data['tour_price'], $id]);
                } else {
                    $sql = "INSERT INTO tour_prices (tour_id, package_name, price_adult) VALUES (?, 'Standard', ?)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([$id, $data['tour_price']]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa tour_media
            $sql = "DELETE FROM tour_media WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            // Xóa tour_prices
            $sql = "DELETE FROM tour_prices WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            // Xóa tour
            $sql = "DELETE FROM tours WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
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

    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM tours";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int) $result['total'] : 0;
    }

}
