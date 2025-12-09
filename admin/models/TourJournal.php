<?php
/**
 * TourJournal Model
 * Quản lý nhật ký tour - HDV ghi nhận diễn biến hàng ngày
 * Use Case VIII.3: Xem / Thêm / Cập nhật nhật ký tour
 */
class TourJournal
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    /**
     * Lấy tất cả nhật ký của 1 schedule
     */
    public function getJournalsBySchedule($schedule_id)
    {
        $sql = "SELECT tj.*, 
                       s.full_name as author_name,
                       ts.tour_name,
                       (SELECT COUNT(*) FROM tour_journal_images WHERE journal_id = tj.journal_id) as image_count
                FROM tour_journals tj
                LEFT JOIN staff s ON tj.created_by = s.staff_id
                LEFT JOIN tour_schedules tsc ON tj.schedule_id = tsc.schedule_id
                LEFT JOIN tours ts ON tsc.tour_id = ts.tour_id
                WHERE tj.schedule_id = ?
                ORDER BY tj.journal_date DESC, tj.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy nhật ký theo ID
     */
    public function getById($journal_id)
    {
        $sql = "SELECT tj.*, 
                       s.full_name as author_name,
                       ts.tour_name,
                       tsc.departure_date,
                       tsc.return_date
                FROM tour_journals tj
                LEFT JOIN staff s ON tj.created_by = s.staff_id
                LEFT JOIN tour_schedules tsc ON tj.schedule_id = tsc.schedule_id
                LEFT JOIN tours ts ON tsc.tour_id = ts.tour_id
                WHERE tj.journal_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$journal_id]);
        return $stmt->fetch();
    }

    /**
     * Tạo nhật ký mới
     */
    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO tour_journals (
                        schedule_id, journal_date, title, content,
                        activities, incidents, incidents_resolved,
                        guest_feedback, weather, location,
                        status, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['schedule_id'],
                $data['journal_date'],
                $data['title'],
                $data['content'] ?? null,
                $data['activities'] ?? null,
                $data['incidents'] ?? null,
                $data['incidents_resolved'] ?? null,
                $data['guest_feedback'] ?? null,
                $data['weather'] ?? null,
                $data['location'] ?? null,
                $data['status'] ?? 'Published',
                $data['created_by'] ?? $_SESSION['staff_id'] ?? null
            ]);

            $journal_id = $this->conn->lastInsertId();

            // Lưu hình ảnh nếu có
            if (!empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $this->addImage($journal_id, $image);
                }
            }

            $this->conn->commit();
            return $journal_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật nhật ký
     */
    public function update($journal_id, $data)
    {
        $sql = "UPDATE tour_journals SET
                    journal_date = ?,
                    title = ?,
                    content = ?,
                    activities = ?,
                    incidents = ?,
                    incidents_resolved = ?,
                    guest_feedback = ?,
                    weather = ?,
                    location = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE journal_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['journal_date'],
            $data['title'],
            $data['content'] ?? null,
            $data['activities'] ?? null,
            $data['incidents'] ?? null,
            $data['incidents_resolved'] ?? null,
            $data['guest_feedback'] ?? null,
            $data['weather'] ?? null,
            $data['location'] ?? null,
            $data['status'] ?? 'Published',
            $journal_id
        ]);
    }

    /**
     * Xóa nhật ký (soft delete - chuyển vào trash)
     */
    public function delete($journal_id)
    {
        $sql = "UPDATE tour_journals SET status = 'Deleted', deleted_at = NOW() WHERE journal_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$journal_id]);
    }

    /**
     * Xóa vĩnh viễn
     */
    public function permanentDelete($journal_id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa hình ảnh
            $images = $this->getImages($journal_id);
            foreach ($images as $img) {
                $this->deleteImage($img['image_id']);
            }

            // Xóa nhật ký
            $sql = "DELETE FROM tour_journals WHERE journal_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$journal_id]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Khôi phục từ trash
     */
    public function restore($journal_id)
    {
        $sql = "UPDATE tour_journals SET status = 'Published', deleted_at = NULL WHERE journal_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$journal_id]);
    }

    /**
     * Lưu bản nháp
     */
    public function saveDraft($data)
    {
        $data['status'] = 'Draft';
        if (!empty($data['journal_id'])) {
            return $this->update($data['journal_id'], $data);
        } else {
            return $this->create($data);
        }
    }

    /**
     * Publish nhật ký
     */
    public function publish($journal_id)
    {
        $sql = "UPDATE tour_journals SET status = 'Published', updated_at = NOW() WHERE journal_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$journal_id]);
    }

    // ==================== IMAGES ====================

    /**
     * Lấy hình ảnh của nhật ký
     */
    public function getImages($journal_id)
    {
        $sql = "SELECT * FROM tour_journal_images WHERE journal_id = ? ORDER BY display_order ASC, image_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$journal_id]);
        return $stmt->fetchAll();
    }

    /**
     * Thêm hình ảnh
     */
    public function addImage($journal_id, $image_data)
    {
        $sql = "INSERT INTO tour_journal_images (journal_id, file_path, caption, display_order)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $journal_id,
            $image_data['file_path'],
            $image_data['caption'] ?? null,
            $image_data['display_order'] ?? 0
        ]);
    }

    /**
     * Xóa hình ảnh
     */
    public function deleteImage($image_id)
    {
        // Lấy thông tin ảnh
        $sql = "SELECT file_path FROM tour_journal_images WHERE image_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$image_id]);
        $image = $stmt->fetch();

        if ($image) {
            // Xóa file vật lý
            $file_path = '../' . $image['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Xóa record
            $sql = "DELETE FROM tour_journal_images WHERE image_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$image_id]);
        }
        return false;
    }

    // ==================== STATISTICS ====================

    /**
     * Thống kê nhật ký của HDV
     */
    public function getStatsByStaff($staff_id, $from_date = null, $to_date = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total_journals,
                    COUNT(DISTINCT schedule_id) as total_tours,
                    SUM(CASE WHEN status = 'Published' THEN 1 ELSE 0 END) as published_count,
                    SUM(CASE WHEN status = 'Draft' THEN 1 ELSE 0 END) as draft_count
                FROM tour_journals
                WHERE created_by = ?";

        $params = [$staff_id];

        if ($from_date) {
            $sql .= " AND journal_date >= ?";
            $params[] = $from_date;
        }

        if ($to_date) {
            $sql .= " AND journal_date <= ?";
            $params[] = $to_date;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Lấy nhật ký gần đây của HDV
     */
    public function getRecentByStaff($staff_id, $limit = 10)
    {
        $sql = "SELECT tj.*, 
                       ts.tour_name,
                       tsc.departure_date,
                       (SELECT COUNT(*) FROM tour_journal_images WHERE journal_id = tj.journal_id) as image_count
                FROM tour_journals tj
                LEFT JOIN tour_schedules tsc ON tj.schedule_id = tsc.schedule_id
                LEFT JOIN tours ts ON tsc.tour_id = ts.tour_id
                WHERE tj.created_by = ? AND tj.status != 'Deleted'
                ORDER BY tj.created_at DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra quyền sở hữu
     */
    public function isOwner($journal_id, $staff_id)
    {
        $sql = "SELECT COUNT(*) FROM tour_journals WHERE journal_id = ? AND created_by = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$journal_id, $staff_id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Export nhật ký (chuẩn bị dữ liệu)
     */
    public function prepareExport($schedule_id)
    {
        $journals = $this->getJournalsBySchedule($schedule_id);

        foreach ($journals as &$journal) {
            $journal['images'] = $this->getImages($journal['journal_id']);
        }

        return $journals;
    }
}
