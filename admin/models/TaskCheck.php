<?php
class TaskCheck
{
    private $conn;
    private $ensured = false;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    private function ensureTable()
    {
        if ($this->ensured)
            return;
        $sql = "CREATE TABLE IF NOT EXISTS `schedule_task_checks` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `schedule_id` INT UNSIGNED NOT NULL,
            `task_key` VARCHAR(191) NOT NULL,
            `title` VARCHAR(255) NULL,
            `is_done` TINYINT(1) NOT NULL DEFAULT 0,
            `done_at` DATETIME NULL,
            `done_by` INT UNSIGNED NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_schedule_task` (`schedule_id`,`task_key`),
            KEY `idx_schedule_id` (`schedule_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->conn->exec($sql);
        $this->ensured = true;
    }

    public function getDoneMap($schedule_id)
    {
        try {
            $this->ensureTable();
            $sql = "SELECT task_key, is_done FROM schedule_task_checks WHERE schedule_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$schedule_id]);
            $map = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $map[$row['task_key']] = (int) $row['is_done'] === 1;
            }
            return $map;
        } catch (Exception $e) {
            // Table might not exist yet; fail gracefully
            return [];
        }
    }

    public function saveBulk($schedule_id, array $doneKeys, $userId, array $taskTitles = [])
    {
        $this->ensureTable();
        $this->conn->beginTransaction();
        try {
            // Mark all existing to not done
            $stmtReset = $this->conn->prepare("UPDATE schedule_task_checks SET is_done = 0 WHERE schedule_id = ?");
            $stmtReset->execute([$schedule_id]);

            // Upsert done items
            $sql = "INSERT INTO schedule_task_checks (schedule_id, task_key, title, is_done, done_at, done_by)
                    VALUES (?, ?, ?, 1, NOW(), ?)
                    ON DUPLICATE KEY UPDATE is_done = VALUES(is_done), done_at = VALUES(done_at), done_by = VALUES(done_by), title = VALUES(title)";
            $stmt = $this->conn->prepare($sql);
            foreach ($doneKeys as $key) {
                $title = $taskTitles[$key] ?? null;
                $stmt->execute([$schedule_id, $key, $title, $userId]);
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // If table missing for any reason, ensure then retry once
            try {
                $this->conn->rollBack();
            } catch (Exception $ie) { /* ignore */
            }
            try {
                $this->ensureTable();
                $this->conn->beginTransaction();
                $stmtReset = $this->conn->prepare("UPDATE schedule_task_checks SET is_done = 0 WHERE schedule_id = ?");
                $stmtReset->execute([$schedule_id]);

                $sql = "INSERT INTO schedule_task_checks (schedule_id, task_key, title, is_done, done_at, done_by)
                        VALUES (?, ?, ?, 1, NOW(), ?)
                        ON DUPLICATE KEY UPDATE is_done = VALUES(is_done), done_at = VALUES(done_at), done_by = VALUES(done_by), title = VALUES(title)";
                $stmt = $this->conn->prepare($sql);
                foreach ($doneKeys as $key) {
                    $title = $taskTitles[$key] ?? null;
                    $stmt->execute([$schedule_id, $key, $title, $userId]);
                }
                $this->conn->commit();
                return true;
            } catch (Exception $e2) {
                try {
                    $this->conn->rollBack();
                } catch (Exception $ie2) { /* ignore */
                }
                return false;
            }
        }
    }
}
