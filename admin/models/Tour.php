<?php 
class Tour{
    public $conn;
    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getAll()
    {
        $sql="SELECT*FROM tours";
        $stmt=$this->conn->prepare($sql);
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