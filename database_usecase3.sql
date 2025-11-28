-- ==========================================
-- USE CASE 3: QUẢN LÝ DANH SÁCH KHÁCH VÀ CHECK-IN ĐOÀN
-- ==========================================
-- Cập nhật database cho Use Case 3 dựa trên database hiện tại
-- Database: duan1 - MySQL 8.0.30
-- 
-- BẢNG guest_list ĐÃ TỒN TẠI - CHỈ THÊM CÁC CỘT CẦN THIẾT
-- ==========================================

-- Thêm các cột cần thiết vào bảng guest_list hiện có
-- Kiểm tra từng cột trước khi thêm để tránh lỗi

-- Thêm cột payment_status
ALTER TABLE `guest_list` ADD COLUMN `payment_status` ENUM('Pending', 'Paid', 'Refunded') DEFAULT 'Pending' COMMENT 'Trạng thái thanh toán';

-- Thêm cột check_in_status
ALTER TABLE `guest_list` ADD COLUMN `check_in_status` ENUM('Pending', 'Checked-In', 'No-Show') DEFAULT 'Pending' COMMENT 'Trạng thái check-in';

-- Thêm cột check_in_time
ALTER TABLE `guest_list` ADD COLUMN `check_in_time` TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời gian check-in';

-- Thêm cột room_number
ALTER TABLE `guest_list` ADD COLUMN `room_number` VARCHAR(50) DEFAULT NULL COMMENT 'Số phòng';

-- Thêm cột room_type
ALTER TABLE `guest_list` ADD COLUMN `room_type` VARCHAR(50) DEFAULT 'Standard' COMMENT 'Loại phòng';

-- Thêm cột id_card
ALTER TABLE `guest_list` ADD COLUMN `id_card` VARCHAR(20) DEFAULT NULL COMMENT 'CMND/CCCD';

-- Thêm cột birth_date
ALTER TABLE `guest_list` ADD COLUMN `birth_date` DATE DEFAULT NULL COMMENT 'Ngày sinh';

-- Thêm cột gender
ALTER TABLE `guest_list` ADD COLUMN `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Other' COMMENT 'Giới tính';

-- Thêm cột phone
ALTER TABLE `guest_list` ADD COLUMN `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Số điện thoại';

-- Thêm cột email
ALTER TABLE `guest_list` ADD COLUMN `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email';

-- Thêm cột address
ALTER TABLE `guest_list` ADD COLUMN `address` TEXT DEFAULT NULL COMMENT 'Địa chỉ';

-- Thêm cột is_adult
ALTER TABLE `guest_list` ADD COLUMN `is_adult` TINYINT(1) DEFAULT 1 COMMENT '1=Người lớn, 0=Trẻ em';

-- Thêm cột special_needs
ALTER TABLE `guest_list` ADD COLUMN `special_needs` TEXT DEFAULT NULL COMMENT 'Yêu cầu đặc biệt';

-- Thêm cột checked_in_by
ALTER TABLE `guest_list` ADD COLUMN `checked_in_by` INT DEFAULT NULL COMMENT 'ID nhân viên check-in';

-- Thêm foreign key cho booking_id
ALTER TABLE `guest_list` ADD CONSTRAINT `fk_guest_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Thêm foreign key cho checked_in_by (tham chiếu users table)
ALTER TABLE `guest_list` ADD CONSTRAINT `fk_guest_checked_by` FOREIGN KEY (`checked_in_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Tạo bảng guest_activity_logs để theo dõi hoạt động
CREATE TABLE `guest_activity_logs` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `guest_id` INT NOT NULL,
  `user_id` INT DEFAULT NULL COMMENT 'Người thực hiện',
  `action` VARCHAR(50) NOT NULL COMMENT 'Hành động: created, check_in, room_assigned, etc.',
  `details` TEXT DEFAULT NULL COMMENT 'Chi tiết hoạt động (JSON format)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Foreign keys
  CONSTRAINT `fk_activity_guest` FOREIGN KEY (`guest_id`) REFERENCES `guest_list`(`guest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm các index để tối ưu hóa truy vấn
ALTER TABLE `guest_list` ADD INDEX `idx_guest_booking` (`booking_id`);
ALTER TABLE `guest_list` ADD INDEX `idx_guest_check_in` (`check_in_status`);
ALTER TABLE `guest_list` ADD INDEX `idx_guest_room` (`room_number`);
ALTER TABLE `guest_list` ADD INDEX `idx_guest_payment` (`payment_status`);
ALTER TABLE `guest_list` ADD INDEX `idx_guest_name` (`full_name`);
ALTER TABLE `guest_list` ADD INDEX `idx_guest_checked_by` (`checked_in_by`);

ALTER TABLE `guest_activity_logs` ADD INDEX `idx_guest_activity` (`guest_id`, `created_at`);
ALTER TABLE `guest_activity_logs` ADD INDEX `idx_activity_user` (`user_id`);

-- ==========================================
-- DỮ LIỆU MẪU CHO USE CASE 3
-- ==========================================
-- Dựa trên bookings hiện tại: booking_id 1,2,3,4

-- Thêm khách cho booking_id = 1 (Tour Đà Nẵng – Hội An - 25 người lớn, 5 trẻ em)
INSERT INTO `guest_list` (`booking_id`, `full_name`, `id_card`, `birth_date`, `gender`, `phone`, `email`, `is_adult`, `payment_status`, `check_in_status`) VALUES
(1, 'Nguyễn Văn An', '123456789', '1985-03-15', 'Male', '0901234567', 'vanan@gmail.com', 1, 'Paid', 'Checked-In'),
(1, 'Trần Thị Bình', '987654321', '1987-07-22', 'Female', '0902345678', 'tbbinh@gmail.com', 1, 'Paid', 'Pending'),
(1, 'Lê Văn Cường', '456789123', '1990-12-05', 'Male', '0903456789', 'cuonglv@gmail.com', 1, 'Paid', 'Checked-In'),
(1, 'Phạm Thị Dung', '321654987', '1982-06-18', 'Female', '0904567890', 'dungpt@gmail.com', 1, 'Paid', 'Pending'),
(1, 'Hoàng Văn Em', '654321098', '1995-09-10', 'Male', '0905678901', 'emhv@gmail.com', 1, 'Paid', 'Checked-In'),
(1, 'Lý Thị Phương', '789012345', '2015-04-20', 'Female', '', '', 0, 'Paid', 'Pending'),
(1, 'Vũ Văn Giang', '012345678', '2012-08-15', 'Male', '', '', 0, 'Paid', 'Pending');

-- Thêm khách cho booking_id = 2 (Tour Nhật Bản - 10 người lớn)
INSERT INTO `guest_list` (`booking_id`, `full_name`, `id_card`, `birth_date`, `gender`, `phone`, `email`, `is_adult`, `payment_status`, `check_in_status`) VALUES
(2, 'Đỗ Văn Hùng', '234567890', '1980-05-12', 'Male', '0906789012', 'hungdv@gmail.com', 1, 'Paid', 'Checked-In'),
(2, 'Bùi Thị Lan', '345678901', '1988-11-25', 'Female', '0907890123', 'lanbt@gmail.com', 1, 'Paid', 'Checked-In'),
(2, 'Trịnh Văn Minh', '456789012', '1975-02-08', 'Male', '0908901234', 'minhtv@gmail.com', 1, 'Paid', 'Pending');

-- Thêm khách cho booking_id = 3 (Tour Sapa - 10 người lớn, 5 trẻ em)  
INSERT INTO `guest_list` (`booking_id`, `full_name`, `id_card`, `birth_date`, `gender`, `phone`, `email`, `is_adult`, `payment_status`, `check_in_status`) VALUES
(3, 'Ngô Văn Nam', '567890123', '1992-07-30', 'Male', '0909012345', 'namnv@gmail.com', 1, 'Paid', 'Pending'),
(3, 'Đinh Thị Oanh', '678901234', '1983-12-18', 'Female', '0910123456', 'oanhdt@gmail.com', 1, 'Paid', 'Pending'),
(3, 'Cao Văn Phúc', '789012346', '2013-03-22', 'Male', '', '', 0, 'Paid', 'Pending');

-- Cập nhật thông tin check-in cho những khách đã check-in
UPDATE `guest_list` SET 
    `check_in_time` = NOW() - INTERVAL FLOOR(RAND() * 120) MINUTE,
    `room_number` = CONCAT('A', 100 + guest_id),
    `room_type` = 'Standard',
    `checked_in_by` = 1
WHERE `check_in_status` = 'Checked-In';

-- Thêm log hoạt động
INSERT INTO `guest_activity_logs` (`guest_id`, `user_id`, `action`, `details`) VALUES
(1, 1, 'created', '{"guest_name": "Nguyễn Văn An", "booking_id": 1}'),
(1, 1, 'check_in', '{"room_assigned": "A101", "check_in_time": "2025-11-28 10:30:00"}'),
(3, 1, 'created', '{"guest_name": "Lê Văn Cường", "booking_id": 1}'),
(3, 1, 'check_in', '{"room_assigned": "A103", "check_in_time": "2025-11-28 10:45:00"}');

-- ==========================================
-- VIEWS HỮU ÍCH CHO USE CASE 3
-- ==========================================

-- View tổng quan khách theo booking (tương thích với database hiện tại)
DROP VIEW IF EXISTS v_guest_summary_by_booking;
CREATE VIEW v_guest_summary_by_booking AS
SELECT 
    b.booking_id,
    b.tour_id,
    COALESCE(t.tour_name, 'Unknown Tour') as tour_name,
    COALESCE(t.code, 'N/A') as tour_code,
    COALESCE(b.booking_type, 'Cá nhân') as booking_type,
    COALESCE(b.status, 'Unknown') as booking_status,
    COUNT(gl.guest_id) as total_guests,
    SUM(CASE WHEN gl.gender = 'Male' THEN 1 ELSE 0 END) as male_count,
    SUM(CASE WHEN gl.gender = 'Female' THEN 1 ELSE 0 END) as female_count,
    SUM(CASE WHEN gl.is_adult = 1 THEN 1 ELSE 0 END) as adult_count,
    SUM(CASE WHEN gl.is_adult = 0 THEN 1 ELSE 0 END) as child_count,
    SUM(CASE WHEN gl.check_in_status = 'Checked-In' THEN 1 ELSE 0 END) as checked_in_count,
    SUM(CASE WHEN gl.check_in_status = 'No-Show' THEN 1 ELSE 0 END) as no_show_count,
    SUM(CASE WHEN gl.check_in_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN gl.room_number IS NOT NULL AND gl.room_number != '' THEN 1 ELSE 0 END) as room_assigned_count,
    SUM(CASE WHEN gl.payment_status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
    SUM(CASE WHEN gl.payment_status = 'Pending' THEN 1 ELSE 0 END) as pending_payment_count
FROM bookings b
LEFT JOIN tours t ON b.tour_id = t.tour_id
LEFT JOIN guest_list gl ON b.booking_id = gl.booking_id
GROUP BY b.booking_id, b.tour_id, t.tour_name, t.code, b.booking_type, b.status;

-- View tổng quan khách theo lịch trình (dựa trên bảng tour_schedules hiện tại)
DROP VIEW IF EXISTS v_guest_summary_by_schedule;
CREATE VIEW v_guest_summary_by_schedule AS
SELECT 
    b.booking_id,
    b.tour_id,
    COALESCE(t.tour_name, 'Unknown Tour') as tour_name,
    COALESCE(t.code, 'N/A') as tour_code,
    b.tour_date as departure_date,
    COALESCE(b.status, 'Unknown') as booking_status,
    COUNT(gl.guest_id) as total_guests,
    SUM(CASE WHEN gl.gender = 'Male' THEN 1 ELSE 0 END) as male_count,
    SUM(CASE WHEN gl.gender = 'Female' THEN 1 ELSE 0 END) as female_count,
    SUM(CASE WHEN gl.is_adult = 1 THEN 1 ELSE 0 END) as adult_count,
    SUM(CASE WHEN gl.is_adult = 0 THEN 1 ELSE 0 END) as child_count,
    SUM(CASE WHEN gl.check_in_status = 'Checked-In' THEN 1 ELSE 0 END) as checked_in_count,
    SUM(CASE WHEN gl.check_in_status = 'No-Show' THEN 1 ELSE 0 END) as no_show_count,
    SUM(CASE WHEN gl.check_in_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN gl.room_number IS NOT NULL AND gl.room_number != '' THEN 1 ELSE 0 END) as room_assigned_count,
    SUM(CASE WHEN gl.payment_status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
    SUM(CASE WHEN gl.payment_status = 'Pending' THEN 1 ELSE 0 END) as pending_payment_count
FROM bookings b
LEFT JOIN tours t ON b.tour_id = t.tour_id
LEFT JOIN guest_list gl ON b.booking_id = gl.booking_id
GROUP BY b.booking_id, b.tour_id, t.tour_name, t.code, b.tour_date, b.status;

-- View danh sách phòng đã sử dụng (tương thích với database hiện tại)
DROP VIEW IF EXISTS v_room_usage;
CREATE VIEW v_room_usage AS
SELECT 
    booking_id,
    room_number,
    COALESCE(room_type, 'Standard') as room_type,
    COUNT(*) as guest_count,
    GROUP_CONCAT(full_name ORDER BY full_name SEPARATOR ', ') as guest_names,
    MIN(check_in_time) as first_check_in,
    MAX(check_in_time) as last_check_in
FROM guest_list 
WHERE room_number IS NOT NULL AND room_number != ''
GROUP BY booking_id, room_number, room_type
ORDER BY booking_id, room_number;

-- View báo cáo check-in theo thời gian (tương thích với database hiện tại)
DROP VIEW IF EXISTS v_checkin_timeline;
CREATE VIEW v_checkin_timeline AS
SELECT 
    DATE(check_in_time) as check_in_date,
    HOUR(check_in_time) as check_in_hour,
    COUNT(*) as check_in_count,
    GROUP_CONCAT(full_name ORDER BY check_in_time SEPARATOR ', ') as guests
FROM guest_list 
WHERE check_in_status = 'Checked-In' 
  AND check_in_time IS NOT NULL
GROUP BY DATE(check_in_time), HOUR(check_in_time)
ORDER BY check_in_date DESC, check_in_hour DESC;

COMMIT;