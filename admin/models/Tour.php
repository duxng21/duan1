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

    // ===================== Danh mục Tour =====================

    public function getCategories(){
        $sql = "SELECT * FROM tour_categories ORDER BY category_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addCategory($name){
        $sql = "INSERT INTO tour_categories(category_name, status) VALUES(?, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name]);
    }

    public function deleteCategory($id){
        $sql = "DELETE FROM tour_categories WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getCategoryById($id){
        $sql = "SELECT * FROM tour_categories WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateCategory($id, $name){
        $sql = "UPDATE tour_categories SET category_name = ? WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $id]);
    }

}
