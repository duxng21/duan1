<?php
class TourDetail
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== ITINERARY (Lịch trình) ====================

    public function getItineraries($tour_id)
    {
        $sql = "SELECT * FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function getItineraryById($id)
    {
        $sql = "SELECT * FROM tour_itineraries WHERE itinerary_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createItinerary($data)
    {
        $sql = "INSERT INTO tour_itineraries (tour_id, day_number, title, description, accommodation) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['day_number'],
            $data['title'],
            $data['description'] ?? '',
            $data['accommodation'] ?? ''
        ]);
    }

    public function updateItinerary($id, $data)
    {
        $sql = "UPDATE tour_itineraries 
                SET day_number = ?, title = ?, description = ?, accommodation = ? 
                WHERE itinerary_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['day_number'],
            $data['title'],
            $data['description'] ?? '',
            $data['accommodation'] ?? '',
            $id
        ]);
    }

    public function deleteItinerary($id)
    {
        $sql = "DELETE FROM tour_itineraries WHERE itinerary_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ==================== GALLERY (Thư viện ảnh) ====================

    public function getGallery($tour_id)
    {
        $sql = "SELECT * FROM tour_media WHERE tour_id = ? ORDER BY is_featured DESC, media_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function addImage($data)
    {
        $sql = "INSERT INTO tour_media (tour_id, file_path, media_type, is_featured) 
                VALUES (?, ?, 'image', ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['file_path'],
            $data['is_featured'] ?? 0
        ]);
    }

    public function deleteImage($id)
    {
        // Lấy thông tin ảnh trước khi xóa
        $sql = "SELECT file_path FROM tour_media WHERE media_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $image = $stmt->fetch();

        if ($image) {
            // Xóa file vật lý
            $filePath = '../' . $image['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Xóa record trong database
            $sql = "DELETE FROM tour_media WHERE media_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        }
        return false;
    }

    // ==================== POLICIES (Chính sách) ====================

    public function getPolicies($tour_id)
    {
        $sql = "SELECT * FROM tour_policies WHERE tour_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetch();
    }

    public function savePolicies($tour_id, $data)
    {
        // Check xem đã có chính sách chưa
        $existing = $this->getPolicies($tour_id);

        if ($existing) {
            // Update
            $sql = "UPDATE tour_policies 
                    SET cancellation_policy = ?, change_policy = ?, payment_policy = ?, note_policy = ? 
                    WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $data['cancellation_policy'] ?? '',
                $data['change_policy'] ?? '',
                $data['payment_policy'] ?? '',
                $data['note_policy'] ?? '',
                $tour_id
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO tour_policies (tour_id, cancellation_policy, change_policy, payment_policy, note_policy) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $tour_id,
                $data['cancellation_policy'] ?? '',
                $data['change_policy'] ?? '',
                $data['payment_policy'] ?? '',
                $data['note_policy'] ?? ''
            ]);
        }
    }

    // ==================== TAGS ====================

    public function getAllTags()
    {
        $sql = "SELECT * FROM tour_tags WHERE status = 1 ORDER BY tag_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTourTags($tour_id)
    {
        $sql = "SELECT tag_id FROM tour_tag_relations WHERE tour_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveTourTags($tour_id, $tags)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa tags cũ
            $sql = "DELETE FROM tour_tag_relations WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tour_id]);

            // Thêm tags mới
            if (!empty($tags)) {
                $sql = "INSERT INTO tour_tag_relations (tour_id, tag_id) VALUES (?, ?)";
                $stmt = $this->conn->prepare($sql);
                foreach ($tags as $tag_id) {
                    $stmt->execute([$tour_id, $tag_id]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function createTag($tag_name)
    {
        $sql = "INSERT INTO tour_tags (tag_name, status) VALUES (?, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$tag_name]);
    }

    public function deleteTag($tag_id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa quan hệ
            $sql = "DELETE FROM tour_tag_relations WHERE tag_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tag_id]);

            // Xóa tag
            $sql = "DELETE FROM tour_tags WHERE tag_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tag_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
