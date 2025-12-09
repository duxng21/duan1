<?php
/**
 * TourFeedback Model
 * Use Case VIII.6: Gửi phản hồi đánh giá tour, dịch vụ
 */
class TourFeedback
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    /**
     * Lấy tất cả feedback của một tour schedule
     */
    public function getFeedbacksBySchedule($schedule_id, $filters = [])
    {
        $sql = "SELECT 
                    tf.*,
                    s.full_name as author_name,
                    s.email as author_email,
                    ts.departure_date,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_feedbacks tf
                INNER JOIN staff s ON tf.staff_id = s.staff_id
                INNER JOIN tour_schedules ts ON tf.schedule_id = ts.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                WHERE tf.schedule_id = :schedule_id";

        $params = ['schedule_id' => $schedule_id];

        // Filter theo trạng thái
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND tf.status = :status";
            $params['status'] = $filters['status'];
        }

        // Filter theo visibility (cho frontend)
        if (isset($filters['is_public'])) {
            $sql .= " AND tf.is_public = :is_public";
            $params['is_public'] = $filters['is_public'];
        }

        $sql .= " ORDER BY tf.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy feedback theo ID
     */
    public function getById($feedback_id)
    {
        $sql = "SELECT 
                    tf.*,
                    s.full_name as author_name,
                    s.email as author_email,
                    s.staff_type as author_role,
                    ts.departure_date,
                    ts.return_date,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_feedbacks tf
                INNER JOIN staff s ON tf.staff_id = s.staff_id
                INNER JOIN tour_schedules ts ON tf.schedule_id = ts.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                WHERE tf.feedback_id = :feedback_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['feedback_id' => $feedback_id]);
        return $stmt->fetch();
    }

    /**
     * Tạo feedback mới
     */
    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO tour_feedbacks (
                        schedule_id, staff_id, overall_rating, service_rating, 
                        guide_rating, food_rating, accommodation_rating, 
                        transportation_rating, feedback_text, positive_points, 
                        improvement_points, recommend_to_others, status, is_public
                    ) VALUES (
                        :schedule_id, :staff_id, :overall_rating, :service_rating,
                        :guide_rating, :food_rating, :accommodation_rating,
                        :transportation_rating, :feedback_text, :positive_points,
                        :improvement_points, :recommend_to_others, :status, :is_public
                    )";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                'schedule_id' => $data['schedule_id'],
                'staff_id' => $data['staff_id'],
                'overall_rating' => $data['overall_rating'] ?? 5,
                'service_rating' => $data['service_rating'] ?? null,
                'guide_rating' => $data['guide_rating'] ?? null,
                'food_rating' => $data['food_rating'] ?? null,
                'accommodation_rating' => $data['accommodation_rating'] ?? null,
                'transportation_rating' => $data['transportation_rating'] ?? null,
                'feedback_text' => $data['feedback_text'] ?? '',
                'positive_points' => $data['positive_points'] ?? null,
                'improvement_points' => $data['improvement_points'] ?? null,
                'recommend_to_others' => $data['recommend_to_others'] ?? 1,
                'status' => $data['status'] ?? 'Published',
                'is_public' => $data['is_public'] ?? 1
            ]);

            if (!$result) {
                throw new Exception('Không thể tạo feedback!');
            }

            $feedback_id = $this->conn->lastInsertId();

            // Thêm hình ảnh nếu có
            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $image_path) {
                    $this->addImage($feedback_id, $image_path);
                }
            }

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Tạo feedback thành công!',
                'feedback_id' => $feedback_id
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật feedback
     */
    public function update($feedback_id, $data)
    {
        $sql = "UPDATE tour_feedbacks SET
                    overall_rating = :overall_rating,
                    service_rating = :service_rating,
                    guide_rating = :guide_rating,
                    food_rating = :food_rating,
                    accommodation_rating = :accommodation_rating,
                    transportation_rating = :transportation_rating,
                    feedback_text = :feedback_text,
                    positive_points = :positive_points,
                    improvement_points = :improvement_points,
                    recommend_to_others = :recommend_to_others,
                    status = :status,
                    is_public = :is_public,
                    updated_at = CURRENT_TIMESTAMP
                WHERE feedback_id = :feedback_id";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            'feedback_id' => $feedback_id,
            'overall_rating' => $data['overall_rating'] ?? 5,
            'service_rating' => $data['service_rating'] ?? null,
            'guide_rating' => $data['guide_rating'] ?? null,
            'food_rating' => $data['food_rating'] ?? null,
            'accommodation_rating' => $data['accommodation_rating'] ?? null,
            'transportation_rating' => $data['transportation_rating'] ?? null,
            'feedback_text' => $data['feedback_text'] ?? '',
            'positive_points' => $data['positive_points'] ?? null,
            'improvement_points' => $data['improvement_points'] ?? null,
            'recommend_to_others' => $data['recommend_to_others'] ?? 1,
            'status' => $data['status'] ?? 'Published',
            'is_public' => $data['is_public'] ?? 1
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật feedback thành công!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể cập nhật feedback!'
        ];
    }

    /**
     * Xóa feedback (soft delete)
     */
    public function delete($feedback_id)
    {
        $sql = "UPDATE tour_feedbacks SET status = 'Deleted', updated_at = CURRENT_TIMESTAMP 
                WHERE feedback_id = :feedback_id";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute(['feedback_id' => $feedback_id]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Xóa feedback thành công!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể xóa feedback!'
        ];
    }

    /**
     * Toggle visibility (public/private)
     */
    public function toggleVisibility($feedback_id)
    {
        $sql = "UPDATE tour_feedbacks 
                SET is_public = NOT is_public, updated_at = CURRENT_TIMESTAMP 
                WHERE feedback_id = :feedback_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['feedback_id' => $feedback_id]);
    }

    /**
     * Admin phản hồi lại feedback
     */
    public function addAdminResponse($feedback_id, $admin_id, $response_text)
    {
        $sql = "UPDATE tour_feedbacks SET
                    admin_response = :response_text,
                    responded_by = :admin_id,
                    responded_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE feedback_id = :feedback_id";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            'feedback_id' => $feedback_id,
            'admin_id' => $admin_id,
            'response_text' => $response_text
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Đã phản hồi feedback!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể phản hồi!'
        ];
    }

    /**
     * Thêm hình ảnh feedback
     */
    public function addImage($feedback_id, $file_path, $caption = null)
    {
        $sql = "INSERT INTO tour_feedback_images (feedback_id, file_path, caption) 
                VALUES (:feedback_id, :file_path, :caption)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'feedback_id' => $feedback_id,
            'file_path' => $file_path,
            'caption' => $caption
        ]);
    }

    /**
     * Xóa hình ảnh
     */
    public function deleteImage($image_id)
    {
        // Lấy đường dẫn file trước khi xóa
        $sql = "SELECT file_path FROM tour_feedback_images WHERE image_id = :image_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['image_id' => $image_id]);
        $image = $stmt->fetch();

        if ($image) {
            // Xóa file vật lý
            if (file_exists($image['file_path'])) {
                unlink($image['file_path']);
            }

            // Xóa record
            $deleteSql = "DELETE FROM tour_feedback_images WHERE image_id = :image_id";
            $deleteStmt = $this->conn->prepare($deleteSql);
            return $deleteStmt->execute(['image_id' => $image_id]);
        }

        return false;
    }

    /**
     * Lấy hình ảnh của feedback
     */
    public function getImages($feedback_id)
    {
        $sql = "SELECT * FROM tour_feedback_images 
                WHERE feedback_id = :feedback_id 
                ORDER BY display_order, uploaded_at";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['feedback_id' => $feedback_id]);
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra quyền sở hữu feedback
     */
    public function isOwner($feedback_id, $staff_id)
    {
        $sql = "SELECT COUNT(*) FROM tour_feedbacks 
                WHERE feedback_id = :feedback_id AND staff_id = :staff_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'feedback_id' => $feedback_id,
            'staff_id' => $staff_id
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy thống kê feedback của tour
     */
    public function getTourStatistics($tour_id)
    {
        $sql = "SELECT 
                    COUNT(*) as total_feedbacks,
                    AVG(overall_rating) as avg_overall_rating,
                    AVG(service_rating) as avg_service_rating,
                    AVG(guide_rating) as avg_guide_rating,
                    AVG(food_rating) as avg_food_rating,
                    AVG(accommodation_rating) as avg_accommodation_rating,
                    AVG(transportation_rating) as avg_transportation_rating,
                    SUM(CASE WHEN recommend_to_others = 1 THEN 1 ELSE 0 END) as recommend_count,
                    COUNT(CASE WHEN tf.status = 'Published' AND is_public = 1 THEN 1 END) as public_count
                FROM tour_feedbacks tf
                INNER JOIN tour_schedules ts ON tf.schedule_id = ts.schedule_id
                WHERE ts.tour_id = :tour_id AND tf.status != 'Deleted'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        return $stmt->fetch();
    }

    /**
     * Lấy feedback của staff (HDV)
     */
    public function getFeedbacksByStaff($staff_id, $limit = 10)
    {
        $sql = "SELECT 
                    tf.*,
                    ts.departure_date,
                    ts.return_date,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_feedbacks tf
                INNER JOIN tour_schedules ts ON tf.schedule_id = ts.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                WHERE tf.staff_id = :staff_id AND tf.status != 'Deleted'
                ORDER BY tf.created_at DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':staff_id', $staff_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lưu nháp feedback
     */
    public function saveDraft($data)
    {
        $data['status'] = 'Draft';
        return $this->create($data);
    }

    /**
     * Publish draft
     */
    public function publishDraft($feedback_id)
    {
        $sql = "UPDATE tour_feedbacks 
                SET status = 'Published', updated_at = CURRENT_TIMESTAMP 
                WHERE feedback_id = :feedback_id AND status = 'Draft'";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['feedback_id' => $feedback_id]);
    }

    /**
     * Lấy top positive/negative feedbacks
     */
    public function getTopFeedbacks($type = 'positive', $limit = 5)
    {
        $orderBy = $type === 'positive' ? 'DESC' : 'ASC';

        $sql = "SELECT 
                    tf.*,
                    s.full_name as author_name,
                    t.tour_name,
                    ts.departure_date
                FROM tour_feedbacks tf
                INNER JOIN staff s ON tf.staff_id = s.staff_id
                INNER JOIN tour_schedules ts ON tf.schedule_id = ts.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                WHERE tf.status = 'Published' AND tf.is_public = 1
                ORDER BY tf.overall_rating {$orderBy}, tf.created_at DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Sentiment analysis - Phân loại feedback
     */
    public function analyzeSentiment($feedback_text)
    {
        // Từ khóa tích cực
        $positive_words = ['tuyệt vời', 'xuất sắc', 'tốt', 'đẹp', 'thích', 'hài lòng', 'chuyên nghiệp', 'nhiệt tình', 'chu đáo'];
        // Từ khóa tiêu cực
        $negative_words = ['tệ', 'kém', 'không tốt', 'thất vọng', 'chán', 'tồi', 'lỗi', 'chậm trễ'];

        $text_lower = mb_strtolower($feedback_text, 'UTF-8');
        $positive_count = 0;
        $negative_count = 0;

        foreach ($positive_words as $word) {
            if (strpos($text_lower, $word) !== false) {
                $positive_count++;
            }
        }

        foreach ($negative_words as $word) {
            if (strpos($text_lower, $word) !== false) {
                $negative_count++;
            }
        }

        if ($positive_count > $negative_count) {
            return 'Positive';
        } elseif ($negative_count > $positive_count) {
            return 'Negative';
        } else {
            return 'Neutral';
        }
    }
}
