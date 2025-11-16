<?php
class Category
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM tour_categories WHERE status = 1 ORDER BY category_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM tour_categories WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO tour_categories (category_name, category_type, description, image, status) 
                VALUES (:category_name, :category_type, :description, :image, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'category_name' => $data['category_name'] ?? '',
            'category_type' => $data['category_type'] ?? null,
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'status' => $data['status'] ?? 1
        ]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE tour_categories 
                SET category_name = :category_name, 
                    category_type = :category_type, 
                    description = :description, 
                    image = :image, 
                    status = :status 
                WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'category_name' => $data['category_name'] ?? '',
            'category_type' => $data['category_type'] ?? null,
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'status' => $data['status'] ?? 1
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tour_categories WHERE category_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}