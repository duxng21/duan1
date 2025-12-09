-- Migration 9: Schedule Task Checks
CREATE TABLE IF NOT EXISTS `schedule_task_checks` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
