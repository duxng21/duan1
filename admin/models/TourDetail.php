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

// ==================== SEED DATA SUPPORT (Sample Itineraries & Policies) ====================
class TourDetailSeeder extends TourDetail
{
    /**
     * Seed sample itineraries and policies for a tour if empty.
     * Returns array with counts and status messages.
     */
    public function seedItinerariesAndPolicies($tour_id)
    {
        $result = [
            'itineraries_added' => 0,
            'policies_created' => false,
            'messages' => []
        ];

        $this->conn->beginTransaction();
        try {
            // Check existing itineraries
            $sql = "SELECT COUNT(*) FROM tour_itineraries WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tour_id]);
            $existingItis = (int) $stmt->fetchColumn();

            if ($existingItis === 0) {
                $sample = [
                    ['day_number' => 1, 'title' => 'Khởi hành & Tham quan sáng', 'description' => 'Di chuyển đến điểm du lịch đầu tiên, ăn trưa địa phương.', 'accommodation' => ''],
                    ['day_number' => 2, 'title' => 'Khám phá văn hóa địa phương', 'description' => 'Tham quan chợ/di tích, trải nghiệm đặc sản vùng.', 'accommodation' => ''],
                    ['day_number' => 3, 'title' => 'Tổng kết & Trở về', 'description' => 'Tự do mua sắm, di chuyển về điểm xuất phát.', 'accommodation' => '']
                ];
                $sqlInsert = "INSERT INTO tour_itineraries (tour_id, day_number, title, description, accommodation) VALUES (?,?,?,?,?)";
                $stmtInsert = $this->conn->prepare($sqlInsert);
                foreach ($sample as $row) {
                    $stmtInsert->execute([$tour_id, $row['day_number'], $row['title'], $row['description'], $row['accommodation']]);
                    $result['itineraries_added']++;
                }
                $result['messages'][] = 'Đã tạo lịch trình mẫu (3 ngày).';
            } else {
                $result['messages'][] = 'Đã có lịch trình, bỏ qua tạo mẫu.';
            }

            // Check policy
            $sql = "SELECT COUNT(*) FROM tour_policies WHERE tour_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tour_id]);
            $hasPolicy = (int) $stmt->fetchColumn() > 0;
            if (!$hasPolicy) {
                $sqlPolicy = "INSERT INTO tour_policies (tour_id, cancellation_policy, change_policy, payment_policy, note_policy) VALUES (?,?,?,?,?)";
                $stmtPolicy = $this->conn->prepare($sqlPolicy);
                $stmtPolicy->execute([
                    $tour_id,
                    'Hủy trước 7 ngày hoàn 70%, trước 3 ngày hoàn 30%, sau đó không hoàn.',
                    'Đổi tour trước 5 ngày miễn phí, sau đó phụ thu 15%.',
                    'Đặt cọc 30% khi đăng ký, thanh toán đủ trước ngày khởi hành 3 ngày.',
                    'Mang theo giấy tờ tùy thân, tuân thủ hướng dẫn an toàn.'
                ]);
                $result['policies_created'] = true;
                $result['messages'][] = 'Đã tạo chính sách mẫu.';
            } else {
                $result['messages'][] = 'Đã có chính sách, bỏ qua tạo mẫu.';
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            $result['messages'][] = 'Lỗi seed: ' . $e->getMessage();
        }
        return $result;
    }
}
