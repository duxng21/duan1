-- ===============================================
-- DATABASE UPDATE FOR USE CASE 4: GHI CHÚ ĐẶC BIỆT
-- ===============================================

-- Tạo bảng ghi chú đặc biệt cho khách hàng
CREATE TABLE IF NOT EXISTS `guest_special_notes` (
    `note_id` INT AUTO_INCREMENT PRIMARY KEY,
    `guest_id` INT NOT NULL,
    `booking_id` INT NOT NULL,
    `note_type` ENUM('Dietary', 'Medical', 'Allergy', 'Mobility', 'Other') DEFAULT 'Other' COMMENT 'Loại ghi chú: Ăn uống, Y tế, Dị ứng, Di chuyển, Khác',
    `note_content` TEXT NOT NULL COMMENT 'Nội dung ghi chú đặc biệt',
    `priority_level` ENUM('Low', 'Medium', 'High') DEFAULT 'Medium' COMMENT 'Mức độ ưu tiên',
    `status` ENUM('Pending', 'Acknowledged', 'In Progress', 'Resolved') DEFAULT 'Pending' COMMENT 'Trạng thái xử lý',
    `handler_notes` TEXT NULL COMMENT 'Ghi chú của người xử lý',
    `created_by` INT NULL COMMENT 'ID người tạo ghi chú',
    `resolved_by` INT NULL COMMENT 'ID người giải quyết',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `resolved_at` TIMESTAMP NULL,
    
    -- Indexes
    INDEX `idx_guest_special_notes_guest` (`guest_id`),
    INDEX `idx_guest_special_notes_booking` (`booking_id`),
    INDEX `idx_guest_special_notes_status` (`status`),
    INDEX `idx_guest_special_notes_priority` (`priority_level`),
    INDEX `idx_guest_special_notes_type` (`note_type`),
    
    -- Foreign Keys
    FOREIGN KEY (`guest_id`) REFERENCES `guest_list`(`guest_id`) ON DELETE CASCADE,
    FOREIGN KEY (`booking_id`) REFERENCES `tour_bookings`(`booking_id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
    FOREIGN KEY (`resolved_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi chú đặc biệt cho khách hàng';

-- Bảng thông báo ghi chú đặc biệt
CREATE TABLE IF NOT EXISTS `special_note_notifications` (
    `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
    `note_id` INT NOT NULL,
    `recipient_id` INT NOT NULL COMMENT 'ID người nhận thông báo (HDV, Admin)',
    `recipient_type` ENUM('Guide', 'Admin', 'Staff') NOT NULL COMMENT 'Loại người nhận',
    `is_read` TINYINT(1) DEFAULT 0 COMMENT '0: Chưa đọc, 1: Đã đọc',
    `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `read_at` TIMESTAMP NULL,
    
    -- Indexes
    INDEX `idx_special_notifications_note` (`note_id`),
    INDEX `idx_special_notifications_recipient` (`recipient_id`),
    INDEX `idx_special_notifications_unread` (`recipient_id`, `is_read`),
    
    -- Foreign Keys
    FOREIGN KEY (`note_id`) REFERENCES `guest_special_notes`(`note_id`) ON DELETE CASCADE,
    FOREIGN KEY (`recipient_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thông báo ghi chú đặc biệt';

-- Cập nhật bảng guest_list để thêm cột special_requirements nếu chưa có
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'guest_list' 
     AND COLUMN_NAME = 'special_requirements') = 0,
    'ALTER TABLE `guest_list` ADD COLUMN `special_requirements` TEXT NULL COMMENT ''Yêu cầu đặc biệt tổng quát''',
    'SELECT ''Column special_requirements already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật bảng users để đảm bảo có đầy đủ role
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'role') > 0,
    'ALTER TABLE `users` MODIFY COLUMN `role` ENUM(''ADMIN'', ''STAFF'', ''GUIDE'', ''USER'') DEFAULT ''USER''',
    'SELECT ''Table users or column role does not exist'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ===============================================
-- SAMPLE DATA FOR TESTING
-- ===============================================

-- Thêm dữ liệu mẫu cho guest_special_notes (chỉ chạy nếu có dữ liệu guest_list và tour_bookings)
INSERT IGNORE INTO `guest_special_notes` (`guest_id`, `booking_id`, `note_type`, `note_content`, `priority_level`, `status`, `created_by`) 
SELECT 1, 1, 'Dietary', 'Khách ăn chay nghiêm ngặt, không sử dụng hành tỏi', 'High', 'Pending', 1
WHERE EXISTS (SELECT 1 FROM guest_list WHERE guest_id = 1) 
  AND EXISTS (SELECT 1 FROM tour_bookings WHERE booking_id = 1)
UNION ALL
SELECT 2, 1, 'Medical', 'Khách bị tiểu đường, cần kiểm soát đường trong thức ăn', 'High', 'Acknowledged', 1
WHERE EXISTS (SELECT 1 FROM guest_list WHERE guest_id = 2) 
  AND EXISTS (SELECT 1 FROM tour_bookings WHERE booking_id = 1)
UNION ALL
SELECT 3, 2, 'Allergy', 'Dị ứng hải sản, đặc biệt là tôm cua', 'Medium', 'In Progress', 1
WHERE EXISTS (SELECT 1 FROM guest_list WHERE guest_id = 3) 
  AND EXISTS (SELECT 1 FROM tour_bookings WHERE booking_id = 2)
UNION ALL
SELECT 4, 2, 'Mobility', 'Khách sử dụng xe lăn, cần hỗ trợ di chuyển', 'High', 'Pending', 1
WHERE EXISTS (SELECT 1 FROM guest_list WHERE guest_id = 4) 
  AND EXISTS (SELECT 1 FROM tour_bookings WHERE booking_id = 2)
UNION ALL
SELECT 5, 3, 'Other', 'Khách yêu cầu phòng tầng thấp do sợ cao', 'Low', 'Resolved', 1
WHERE EXISTS (SELECT 1 FROM guest_list WHERE guest_id = 5) 
  AND EXISTS (SELECT 1 FROM tour_bookings WHERE booking_id = 3);

-- ===============================================
-- STORED PROCEDURES & FUNCTIONS
-- ===============================================

-- Procedure: Lấy thống kê ghi chú theo schedule
DROP PROCEDURE IF EXISTS `GetSpecialNotesStatsBySchedule`;

DELIMITER $$
CREATE PROCEDURE `GetSpecialNotesStatsBySchedule`(
    IN p_schedule_id INT
)
BEGIN
    SELECT 
        COUNT(*) as total_notes,
        SUM(CASE WHEN gsn.priority_level = 'High' THEN 1 ELSE 0 END) as high_priority_count,
        SUM(CASE WHEN gsn.priority_level = 'Medium' THEN 1 ELSE 0 END) as medium_priority_count,
        SUM(CASE WHEN gsn.priority_level = 'Low' THEN 1 ELSE 0 END) as low_priority_count,
        SUM(CASE WHEN gsn.status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN gsn.status = 'Acknowledged' THEN 1 ELSE 0 END) as acknowledged_count,
        SUM(CASE WHEN gsn.status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_count,
        SUM(CASE WHEN gsn.status = 'Resolved' THEN 1 ELSE 0 END) as resolved_count,
        SUM(CASE WHEN gsn.note_type = 'Dietary' THEN 1 ELSE 0 END) as dietary_count,
        SUM(CASE WHEN gsn.note_type = 'Medical' THEN 1 ELSE 0 END) as medical_count,
        SUM(CASE WHEN gsn.note_type = 'Allergy' THEN 1 ELSE 0 END) as allergy_count,
        SUM(CASE WHEN gsn.note_type = 'Mobility' THEN 1 ELSE 0 END) as mobility_count,
        SUM(CASE WHEN gsn.note_type = 'Other' THEN 1 ELSE 0 END) as other_count
    FROM guest_special_notes gsn
    INNER JOIN tour_bookings tb ON gsn.booking_id = tb.booking_id
    WHERE tb.schedule_id = p_schedule_id;
END$$
DELIMITER ;

-- Function: Đếm số ghi chú chưa đọc của user
DROP FUNCTION IF EXISTS `CountUnreadNotifications`;

DELIMITER $$
CREATE FUNCTION `CountUnreadNotifications`(p_user_id INT)
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE unread_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO unread_count
    FROM special_note_notifications 
    WHERE recipient_id = p_user_id AND is_read = 0;
    
    RETURN unread_count;
END$$
DELIMITER ;

-- ===============================================
-- VIEWS FOR REPORTING
-- ===============================================

-- View: Tổng quan ghi chú đặc biệt theo tour
CREATE OR REPLACE VIEW `v_special_notes_summary` AS
SELECT 
    t.tour_id,
    t.tour_name,
    ts.schedule_id,
    ts.departure_date,
    COUNT(gsn.note_id) as total_notes,
    SUM(CASE WHEN gsn.priority_level = 'High' THEN 1 ELSE 0 END) as high_priority_count,
    SUM(CASE WHEN gsn.status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN gsn.status = 'Resolved' THEN 1 ELSE 0 END) as resolved_count
FROM tours t
LEFT JOIN tour_schedules ts ON t.tour_id = ts.tour_id
LEFT JOIN tour_bookings tb ON ts.schedule_id = tb.schedule_id
LEFT JOIN guest_special_notes gsn ON tb.booking_id = gsn.booking_id
GROUP BY t.tour_id, t.tour_name, ts.schedule_id, ts.departure_date;

-- View: Danh sách khách có yêu cầu đặc biệt
CREATE OR REPLACE VIEW `v_guests_with_special_requirements` AS
SELECT 
    gl.guest_id,
    gl.full_name,
    gl.phone,
    gl.email,
    gl.room_number,
    tb.booking_id,
    tb.schedule_id,
    ts.departure_date,
    t.tour_name,
    GROUP_CONCAT(
        CONCAT(
            CASE gsn.note_type
                WHEN 'Dietary' THEN '🍽️'
                WHEN 'Medical' THEN '💊'
                WHEN 'Allergy' THEN '⚠️'
                WHEN 'Mobility' THEN '♿'
                ELSE '📝'
            END,
            ' ',
            gsn.note_content
        ) SEPARATOR ' | '
    ) as special_requirements_summary,
    MAX(CASE 
        WHEN gsn.priority_level = 'High' THEN 3
        WHEN gsn.priority_level = 'Medium' THEN 2
        ELSE 1
    END) as max_priority_level,
    COUNT(gsn.note_id) as notes_count
FROM guest_list gl
INNER JOIN tour_bookings tb ON gl.booking_id = tb.booking_id
INNER JOIN tour_schedules ts ON tb.schedule_id = ts.schedule_id
INNER JOIN tours t ON ts.tour_id = t.tour_id
LEFT JOIN guest_special_notes gsn ON gl.guest_id = gsn.guest_id
WHERE gsn.note_id IS NOT NULL
GROUP BY gl.guest_id, gl.full_name, gl.phone, gl.email, gl.room_number, 
         tb.booking_id, tb.schedule_id, ts.departure_date, t.tour_name;

-- ===============================================
-- INDEXES FOR OPTIMIZATION
-- ===============================================

-- Additional indexes for better performance (với kiểm tra tồn tại)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'guest_list' 
     AND INDEX_NAME = 'idx_guest_list_special_req') = 0,
    'CREATE INDEX `idx_guest_list_special_req` ON `guest_list`(`special_requirements`(100))',
    'SELECT ''Index idx_guest_list_special_req already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'guest_special_notes' 
     AND INDEX_NAME = 'idx_special_notes_created_at') = 0,
    'CREATE INDEX `idx_special_notes_created_at` ON `guest_special_notes`(`created_at`)',
    'SELECT ''Index idx_special_notes_created_at already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'guest_special_notes' 
     AND INDEX_NAME = 'idx_special_notes_resolved_at') = 0,
    'CREATE INDEX `idx_special_notes_resolved_at` ON `guest_special_notes`(`resolved_at`)',
    'SELECT ''Index idx_special_notes_resolved_at already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite indexes for complex queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'guest_special_notes' 
     AND INDEX_NAME = 'idx_special_notes_booking_status') = 0,
    'CREATE INDEX `idx_special_notes_booking_status` ON `guest_special_notes`(`booking_id`, `status`)',
    'SELECT ''Index idx_special_notes_booking_status already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'guest_special_notes' 
     AND INDEX_NAME = 'idx_special_notes_priority_status') = 0,
    'CREATE INDEX `idx_special_notes_priority_status` ON `guest_special_notes`(`priority_level`, `status`)',
    'SELECT ''Index idx_special_notes_priority_status already exists'' as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ===============================================
-- TRIGGERS FOR AUTOMATION
-- ===============================================

-- Trigger: Tự động tạo thông báo khi có ghi chú mới
DROP TRIGGER IF EXISTS `trg_create_special_note_notifications`;

DELIMITER $$
CREATE TRIGGER `trg_create_special_note_notifications`
AFTER INSERT ON `guest_special_notes`
FOR EACH ROW
BEGIN
    -- Tạo thông báo cho Admin
    INSERT IGNORE INTO special_note_notifications (note_id, recipient_id, recipient_type)
    SELECT NEW.note_id, user_id, 'Admin'
    FROM users 
    WHERE role = 'ADMIN' AND status = 1;
    
    -- Tạo thông báo cho HDV được phân công (nếu có)
    INSERT IGNORE INTO special_note_notifications (note_id, recipient_id, recipient_type)
    SELECT NEW.note_id, u.user_id, 'Guide'
    FROM tour_bookings tb
    INNER JOIN schedule_staff ss ON tb.schedule_id = ss.schedule_id
    INNER JOIN staff s ON ss.staff_id = s.staff_id
    INNER JOIN users u ON s.user_id = u.user_id
    WHERE tb.booking_id = NEW.booking_id 
    AND u.role = 'GUIDE' 
    AND u.status = 1;
END$$
DELIMITER ;

-- Trigger: Cập nhật thời gian resolved khi status = 'Resolved'
DROP TRIGGER IF EXISTS `trg_update_resolved_time`;

DELIMITER $$
CREATE TRIGGER `trg_update_resolved_time`
BEFORE UPDATE ON `guest_special_notes`
FOR EACH ROW
BEGIN
    IF NEW.status = 'Resolved' AND OLD.status != 'Resolved' THEN
        SET NEW.resolved_at = CURRENT_TIMESTAMP;
        IF NEW.resolved_by IS NULL THEN
            SET NEW.resolved_by = @current_user_id;
        END IF;
    END IF;
END$$
DELIMITER ;

-- ===============================================
-- PERMISSIONS & SECURITY
-- ===============================================

-- Tạo role-based permissions (nếu hệ thống hỗ trợ)
-- Admin: Full access
-- Guide: Read/Update status của ghi chú thuộc tour được phân công
-- Staff: Read-only access

COMMIT;

-- ===============================================
-- VERIFICATION QUERIES
-- ===============================================

-- Kiểm tra bảng đã tạo thành công
SELECT 
    TABLE_NAME,
    TABLE_COMMENT,
    CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('guest_special_notes', 'special_note_notifications');

-- Kiểm tra indexes
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('guest_special_notes', 'special_note_notifications')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- Kiểm tra views
SELECT 
    TABLE_NAME,
    VIEW_DEFINITION
FROM INFORMATION_SCHEMA.VIEWS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME LIKE 'v_special_%';

SELECT 'Database setup for Use Case 4 completed successfully!' as STATUS;