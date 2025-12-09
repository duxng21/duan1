<?php
class TourVersion
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getVersionsByTour($tour_id)
    {
        // Một số DB có thể chưa có cột created_at; sắp xếp theo thời điểm kích hoạt/đặt lịch rồi tới version_id
        $sql = "SELECT * FROM tour_versions WHERE tour_id = ? ORDER BY activated_at DESC, scheduled_at DESC, version_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function getVersionById($version_id)
    {
        $sql = "SELECT * FROM tour_versions WHERE version_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$version_id]);
        return $stmt->fetch();
    }

    public function createVersion($data)
    {
        $sql = "INSERT INTO tour_versions (tour_id, version_name, version_type, start_date, end_date, description, status, is_active, activation_mode, scheduled_at, source_type, source_id)
                VALUES (:tour_id, :version_name, :version_type, :start_date, :end_date, :description, :status, :is_active, :activation_mode, :scheduled_at, :source_type, :source_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':tour_id' => $data['tour_id'],
            ':version_name' => $data['version_name'],
            ':version_type' => $data['version_type'],
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':description' => $data['description'] ?? null,
            ':status' => $data['status'] ?? 'hidden',
            ':is_active' => !empty($data['is_active']) ? 1 : 0,
            ':activation_mode' => $data['activation_mode'] ?? 'manual',
            ':scheduled_at' => $data['scheduled_at'] ?? null,
            ':source_type' => $data['source_type'] ?? null,
            ':source_id' => $data['source_id'] ?? null,
        ]);
        return (int) $this->conn->lastInsertId();
    }

    public function cloneFromSource($version_id, $source_type, $source_id, $options)
    {
        // Clone itineraries
        if (!empty($options['itinerary'])) {
            if ($source_type === 'tour') {
                $sql = "INSERT INTO tour_version_itineraries (version_id, day_number, title, description, accommodation)
                        SELECT ?, day_number, title, description, accommodation
                        FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number";
            } else { // source: version
                $sql = "INSERT INTO tour_version_itineraries (version_id, day_number, title, description, accommodation)
                        SELECT ?, day_number, title, description, accommodation
                        FROM tour_version_itineraries WHERE version_id = ? ORDER BY day_number";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$version_id, $source_id]);
        }

        // Clone prices
        if (!empty($options['pricing'])) {
            if ($source_type === 'tour') {
                $sql = "INSERT INTO tour_version_prices (version_id, package_name, price_adult, price_child, price_infant, description)
                        SELECT ?, package_name, price_adult, price_child, price_infant, description
                        FROM tour_prices WHERE tour_id = ?";
            } else {
                $sql = "INSERT INTO tour_version_prices (version_id, package_name, price_adult, price_child, price_infant, description)
                        SELECT ?, package_name, price_adult, price_child, price_infant, description
                        FROM tour_version_prices WHERE version_id = ?";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$version_id, $source_id]);
        }

        // Clone media
        if (!empty($options['images'])) {
            if ($source_type === 'tour') {
                $sql = "INSERT INTO tour_version_media (version_id, file_path, media_type, is_featured)
                        SELECT ?, file_path, media_type, is_featured
                        FROM tour_media WHERE tour_id = ?";
            } else {
                $sql = "INSERT INTO tour_version_media (version_id, file_path, media_type, is_featured)
                        SELECT ?, file_path, media_type, is_featured
                        FROM tour_version_media WHERE version_id = ?";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$version_id, $source_id]);
        }
    }

    public function getVersionDetails($version_id)
    {
        $details = [
            'version' => $this->getVersionById($version_id),
            'itineraries' => [],
            'prices' => [],
            'media' => []
        ];
        $stmt = $this->conn->prepare("SELECT * FROM tour_version_itineraries WHERE version_id = ? ORDER BY day_number");
        $stmt->execute([$version_id]);
        $details['itineraries'] = $stmt->fetchAll();

        $stmt = $this->conn->prepare("SELECT * FROM tour_version_prices WHERE version_id = ?");
        $stmt->execute([$version_id]);
        $details['prices'] = $stmt->fetchAll();

        $stmt = $this->conn->prepare("SELECT * FROM tour_version_media WHERE version_id = ?");
        $stmt->execute([$version_id]);
        $details['media'] = $stmt->fetchAll();

        return $details;
    }

    public function activateVersion($version_id)
    {
        $sql = "UPDATE tour_versions SET is_active = 1, status = 'visible', activated_at = NOW() WHERE version_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$version_id]);
    }

    public function pauseVersion($version_id)
    {
        $sql = "UPDATE tour_versions SET is_active = 0, status = 'hidden' WHERE version_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$version_id]);
    }

    public function archiveVersion($version_id)
    {
        $sql = "UPDATE tour_versions SET is_active = 0, status = 'archived' WHERE version_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$version_id]);
    }
}
