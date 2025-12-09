<?php
/**
 * Category Model - Enhanced
 * Use Case 1: Quản lý danh mục tour
 */
class Category
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    /**
     * Lấy tất cả danh mục (có đếm số tour)
     * UC1: Hiển thị danh sách với số lượng tour
     */
    public function getAllWithTourCount($filters = [])
    {
        $sql = "SELECT tc.*, 
                COUNT(t.tour_id) as tour_count
                FROM tour_categories tc
                LEFT JOIN tours t ON tc.category_id = t.category_id AND t.status != 0
                WHERE 1=1";

        $params = [];

        // Filter theo trạng thái
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND tc.status = :status";
            $params['status'] = (int) $filters['status'];
        } else {
            $sql .= " AND tc.status = 1"; // Mặc định chỉ lấy active
        }

        // Filter theo loại
        if (!empty($filters['category_type'])) {
            $sql .= " AND tc.category_type = :category_type";
            $params['category_type'] = $filters['category_type'];
        }

        // Search theo tên
        if (!empty($filters['search'])) {
            $sql .= " AND tc.category_name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " GROUP BY tc.category_id ORDER BY tc.category_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả (phương thức cũ, giữ lại để tương thích)
     */
    public function getAll()
    {
        $sql = "SELECT * FROM tour_categories WHERE status = 1 ORDER BY category_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM tour_categories WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Kiểm tra tên danh mục đã tồn tại chưa
     * UC1: Validate trùng tên khi thêm/sửa
     */
    public function checkNameExists($category_name, $exclude_id = null)
    {
        $sql = "SELECT category_id FROM tour_categories 
                WHERE category_name = :category_name";

        $params = ['category_name' => $category_name];

        if ($exclude_id) {
            $sql .= " AND category_id != :exclude_id";
            $params['exclude_id'] = $exclude_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Tạo danh mục mới
     */
    public function create($data)
    {
        // Kiểm tra trùng tên
        if ($this->checkNameExists($data['category_name'])) {
            return [
                'success' => false,
                'message' => 'Tên danh mục đã tồn tại!'
            ];
        }

        $sql = "INSERT INTO tour_categories (category_name, category_type, description, image, status) 
                VALUES (:category_name, :category_type, :description, :image, :status)";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            'category_name' => $data['category_name'] ?? '',
            'category_type' => $data['category_type'] ?? null,
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'status' => $data['status'] ?? 1
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Thêm danh mục thành công!',
                'category_id' => $this->conn->lastInsertId()
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể thêm danh mục!'
        ];
    }

    /**
     * Cập nhật danh mục
     */
    public function update($id, $data)
    {
        // Kiểm tra trùng tên (trừ chính nó)
        if ($this->checkNameExists($data['category_name'], $id)) {
            return [
                'success' => false,
                'message' => 'Tên danh mục đã tồn tại!'
            ];
        }

        $sql = "UPDATE tour_categories 
                SET category_name = :category_name, 
                    category_type = :category_type, 
                    description = :description, 
                    image = :image, 
                    status = :status 
                WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            'id' => $id,
            'category_name' => $data['category_name'] ?? '',
            'category_type' => $data['category_type'] ?? null,
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'status' => $data['status'] ?? 1
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể cập nhật danh mục!'
        ];
    }

    /**
     * Kiểm tra có thể xóa danh mục không
     * UC1: Validate trước khi xóa
     */
    public function canDelete($id)
    {
        $sql = "SELECT COUNT(*) as tour_count 
                FROM tours 
                WHERE category_id = :id AND status != 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return [
            'can_delete' => $result['tour_count'] == 0,
            'tour_count' => $result['tour_count']
        ];
    }

    /**
     * Xóa danh mục
     * UC1: Xóa với kiểm tra ràng buộc
     */
    public function delete($id)
    {
        // Kiểm tra trước khi xóa
        $check = $this->canDelete($id);
        if (!$check['can_delete']) {
            return [
                'success' => false,
                'message' => "Không thể xóa! Danh mục đang có {$check['tour_count']} tour."
            ];
        }

        $sql = "DELETE FROM tour_categories WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Xóa danh mục thành công!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể xóa danh mục!'
        ];
    }

    /**
     * Tìm kiếm danh mục
     * UC1: Tìm kiếm theo tên, loại
     */
    public function searchCategories($keyword, $filters = [])
    {
        $sql = "SELECT tc.*, 
                COUNT(t.tour_id) as tour_count
                FROM tour_categories tc
                LEFT JOIN tours t ON tc.category_id = t.category_id AND t.status != 0
                WHERE (tc.category_name LIKE :keyword OR tc.description LIKE :keyword)";

        $params = ['keyword' => '%' . $keyword . '%'];

        // Filter theo loại
        if (!empty($filters['category_type'])) {
            $sql .= " AND tc.category_type = :category_type";
            $params['category_type'] = $filters['category_type'];
        }

        // Filter theo trạng thái
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND tc.status = :status";
            $params['status'] = (int) $filters['status'];
        }

        $sql .= " GROUP BY tc.category_id ORDER BY tc.category_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Import danh mục từ CSV/Excel
     * UC1: Nhập hàng loạt danh mục
     */
    public function bulkImport($categories)
    {
        $success_count = 0;
        $error_count = 0;
        $errors = [];

        foreach ($categories as $index => $category) {
            $row_number = $index + 2; // +2 vì dòng 1 là header, index từ 0

            // Validate dữ liệu
            if (empty($category['category_name'])) {
                $errors[] = "Dòng {$row_number}: Thiếu tên danh mục";
                $error_count++;
                continue;
            }

            // Kiểm tra trùng tên
            if ($this->checkNameExists($category['category_name'])) {
                $errors[] = "Dòng {$row_number}: Danh mục '{$category['category_name']}' đã tồn tại";
                $error_count++;
                continue;
            }

            // Thêm danh mục
            $result = $this->create($category);
            if ($result['success']) {
                $success_count++;
            } else {
                $errors[] = "Dòng {$row_number}: {$result['message']}";
                $error_count++;
            }
        }

        return [
            'success' => $success_count > 0,
            'message' => "Import thành công {$success_count} danh mục, lỗi {$error_count}",
            'success_count' => $success_count,
            'error_count' => $error_count,
            'errors' => $errors
        ];
    }

    /**
     * Lấy thống kê danh mục
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total_categories,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_categories,
                    (SELECT COUNT(*) FROM tours WHERE status != 0) as total_tours
                FROM tour_categories";

        $stmt = $this->conn->query($sql);
        return $stmt->fetch();
    }

    /**
     * Lấy danh mục theo loại
     */
    public function getByType($category_type)
    {
        $sql = "SELECT * FROM tour_categories 
                WHERE category_type = :category_type AND status = 1 
                ORDER BY category_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['category_type' => $category_type]);
        return $stmt->fetchAll();
    }
}