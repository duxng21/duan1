-- ==========================================
-- USE CASE 2: QUẢN LÝ LỊCH KHỞI HÀNH & PHÂN BỔ NHÂN SỰ, DỊCH VỤ
-- ==========================================
-- Cập nhật database cho Use Case 2 dựa trên cấu trúc có sẵn
-- 
-- LƯU Ý QUAN TRỌNG:
-- - Nếu cột/index đã tồn tại, MySQL sẽ báo lỗi "Duplicate column name" - BỎ QUA LỖI NÀY
-- - Chỉ chạy câu lệnh tạo bảng mới hoặc insert dữ liệu mẫu nếu cần
-- - File này tương thích với MySQL 5.7+ và MariaDB 10.0+
-- ==========================================

-- Bảng partners đã tồn tại, thêm các cột mới (an toàn với syntax đơn giản)
-- Nếu cột đã tồn tại sẽ báo lỗi nhưng không ảnh hưởng đến quá trình
ALTER TABLE `partners` ADD COLUMN `partner_type` ENUM('Restaurant', 'Hotel', 'Transportation', 'Airline', 'Other') NOT NULL DEFAULT 'Other' AFTER `partner_name`;
ALTER TABLE `partners` ADD COLUMN `contact_person` VARCHAR(100) DEFAULT NULL AFTER `email`;
ALTER TABLE `partners` ADD COLUMN `address` TEXT DEFAULT NULL;
ALTER TABLE `partners` ADD COLUMN `tax_code` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `partners` ADD COLUMN `bank_account` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `partners` ADD COLUMN `bank_name` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `partners` ADD COLUMN `rating` DECIMAL(3,2) DEFAULT 0.00;
ALTER TABLE `partners` ADD COLUMN `status` TINYINT(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive';
ALTER TABLE `partners` ADD COLUMN `notes` TEXT DEFAULT NULL;

-- Bảng services đã tồn tại, thêm các cột mới
ALTER TABLE `services` ADD COLUMN `partner_id` INT DEFAULT NULL AFTER `service_id`;
ALTER TABLE `services` ADD COLUMN `description` TEXT DEFAULT NULL;
ALTER TABLE `services` ADD COLUMN `unit_price` DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE `services` ADD COLUMN `unit` VARCHAR(50) DEFAULT 'pax' COMMENT 'pax, room, vehicle, ticket, etc.';
ALTER TABLE `services` ADD COLUMN `capacity` INT DEFAULT NULL COMMENT 'Số lượng tối đa';
ALTER TABLE `services` ADD COLUMN `location` VARCHAR(255) DEFAULT NULL;

-- Thêm foreign key
ALTER TABLE `services` ADD CONSTRAINT `fk_service_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners`(`partner_id`) ON DELETE SET NULL ON UPDATE CASCADE;


-- Bảng schedule_services đã có cấu trúc phù hợp
-- Thêm index cần thiết (bỏ qua lỗi nếu đã tồn tại)
ALTER TABLE schedule_services ADD INDEX idx_schedule_services_schedule (schedule_id);
ALTER TABLE schedule_services ADD INDEX idx_schedule_services_service (service_id);

-- Bảng schedule_staff đã có cấu trúc phù hợp  
-- Thêm index cần thiết (bỏ qua lỗi nếu đã tồn tại)
ALTER TABLE schedule_staff ADD INDEX idx_schedule_staff_schedule (schedule_id);
ALTER TABLE schedule_staff ADD INDEX idx_schedule_staff_staff (staff_id);


-- Bảng lưu nhật ký hành trình của HDV (bảng mới)
CREATE TABLE `schedule_journey_logs` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `schedule_id` INT NOT NULL,
  `staff_id` INT NOT NULL,
  `log_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`schedule_id`) REFERENCES `tour_schedules`(`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`staff_id`) REFERENCES `staff`(`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng notification_logs đã tồn tại với cấu trúc phù hợp
-- Thêm service_id nếu chưa có
ALTER TABLE `notification_logs` ADD COLUMN `service_id` INT DEFAULT NULL;

-- Thêm index (bỏ qua lỗi nếu đã tồn tại)
ALTER TABLE notification_logs ADD INDEX idx_notification_logs_type (notification_type);
ALTER TABLE notification_logs ADD INDEX idx_notification_logs_recipient (recipient_type, recipient_id);


-- Thêm index để tăng hiệu suất truy vấn (bỏ qua lỗi nếu đã tồn tại)
ALTER TABLE partners ADD INDEX idx_partner_type (partner_type);
ALTER TABLE partners ADD INDEX idx_partner_status (status);
ALTER TABLE services ADD INDEX idx_service_type (service_type);
ALTER TABLE services ADD INDEX idx_service_status (status);
ALTER TABLE services ADD INDEX idx_service_partner (partner_id);

-- ==========================================
-- SEED DATA MẪU CHO USE CASE 2
-- ==========================================

-- Xóa dữ liệu mẫu cũ nếu có (an toàn)
DELETE FROM `services` WHERE partner_id IN (
    SELECT partner_id FROM partners WHERE partner_name IN (
        'Nhà hàng Hương Sen', 'Khách sạn Mường Thanh Grand', 
        'Công ty Vận tải Phương Trang', 'Vietnam Airlines', 'Nhà hàng Quê Nhà'
    )
);
DELETE FROM `partners` WHERE partner_name IN (
    'Nhà hàng Hương Sen', 'Khách sạn Mường Thanh Grand', 
    'Công ty Vận tải Phương Trang', 'Vietnam Airlines', 'Nhà hàng Quê Nhà'
);


-- Thêm dữ liệu đối tác mẫu
INSERT INTO `partners` (`partner_name`, `partner_type`, `contact_person`, `phone`, `email`, `address`, `rating`, `status`) VALUES
('Nhà hàng Hương Sen', 'Restaurant', 'Nguyễn Văn A', '0901234567', 'huongsen@gmail.com', '123 Đường Lê Lợi, Q.1, TP.HCM', 4.50, 1),
('Khách sạn Mường Thanh Grand', 'Hotel', 'Trần Thị B', '0902345678', 'muongthanh@hotel.com', '456 Đường Trần Phú, Q.5, TP.HCM', 4.20, 1),
('Công ty Vận tải Phương Trang', 'Transportation', 'Lê Văn C', '0903456789', 'phuongtrang@transport.vn', '789 Đường Nguyễn Văn Linh, Q.7, TP.HCM', 4.80, 1),
('Vietnam Airlines', 'Airline', 'Phạm Thị D', '1900123456', 'booking@vietnamairlines.com', 'Tân Sơn Nhất, TP.HCM', 4.70, 1),
('Nhà hàng Quê Nhà', 'Restaurant', 'Hoàng Văn E', '0904567890', 'quenha@restaurant.vn', '321 Đường Võ Văn Tần, Q.3, TP.HCM', 4.30, 1);

-- Thêm dữ liệu dịch vụ mẫu (sau khi đã có partners)
-- Sử dụng subquery để lấy partner_id từ partner_name
INSERT INTO `services` (`partner_id`, `service_name`, `service_type`, `description`, `unit_price`, `unit`, `capacity`, `location`, `provider_name`, `status`) VALUES
-- Nhà hàng Hương Sen
((SELECT partner_id FROM partners WHERE partner_name = 'Nhà hàng Hương Sen' LIMIT 1), 'Buffet sáng - Hương Sen', 'Restaurant', 'Buffet sáng đa dạng món Việt và Âu', 150000, 'pax', 200, '123 Đường Lê Lợi, Q.1', 'Nhà hàng Hương Sen', 1),
((SELECT partner_id FROM partners WHERE partner_name = 'Nhà hàng Hương Sen' LIMIT 1), 'Set menu trưa - Hương Sen', 'Restaurant', 'Set menu 4 món đặc sản miền Trung', 250000, 'pax', 200, '123 Đường Lê Lợi, Q.1', 'Nhà hàng Hương Sen', 1),

-- Nhà hàng Quê Nhà  
((SELECT partner_id FROM partners WHERE partner_name = 'Nhà hàng Quê Nhà' LIMIT 1), 'Buffet tối - Quê Nhà', 'Restaurant', 'Buffet hải sản và lẩu', 350000, 'pax', 150, '321 Đường Võ Văn Tần, Q.3', 'Nhà hàng Quê Nhà', 1),

-- Khách sạn Mường Thanh
((SELECT partner_id FROM partners WHERE partner_name = 'Khách sạn Mường Thanh Grand' LIMIT 1), 'Phòng Standard - Mường Thanh', 'Hotel', 'Phòng 2 giường đơn hoặc 1 giường đôi', 800000, 'room', 50, '456 Đường Trần Phú, Q.5', 'Khách sạn Mường Thanh', 1),
((SELECT partner_id FROM partners WHERE partner_name = 'Khách sạn Mường Thanh Grand' LIMIT 1), 'Phòng Deluxe - Mường Thanh', 'Hotel', 'Phòng cao cấp view biển', 1200000, 'room', 30, '456 Đường Trần Phú, Q.5', 'Khách sạn Mường Thanh', 1),

-- Phương Trang Transport
((SELECT partner_id FROM partners WHERE partner_name = 'Công ty Vận tải Phương Trang' LIMIT 1), 'Xe 16 chỗ', 'Vehicle', 'Xe du lịch 16 chỗ có điều hòa', 2000000, 'vehicle', 16, 'TP.HCM', 'Phương Trang', 1),
((SELECT partner_id FROM partners WHERE partner_name = 'Công ty Vận tải Phương Trang' LIMIT 1), 'Xe 29 chỗ', 'Vehicle', 'Xe du lịch 29 chỗ có điều hòa', 3000000, 'vehicle', 29, 'TP.HCM', 'Phương Trang', 1),
((SELECT partner_id FROM partners WHERE partner_name = 'Công ty Vận tải Phương Trang' LIMIT 1), 'Xe 45 chỗ', 'Vehicle', 'Xe du lịch 45 chỗ có điều hòa, wifi', 4500000, 'vehicle', 45, 'TP.HCM', 'Phương Trang', 1),

-- Vietnam Airlines
((SELECT partner_id FROM partners WHERE partner_name = 'Vietnam Airlines' LIMIT 1), 'Vé máy bay HCM - Đà Nẵng', 'Flight', 'Vé khứ hồi hạng phổ thông', 2500000, 'ticket', 180, 'Sân bay Tân Sơn Nhất', 'Vietnam Airlines', 1),
((SELECT partner_id FROM partners WHERE partner_name = 'Vietnam Airlines' LIMIT 1), 'Vé máy bay HCM - Hà Nội', 'Flight', 'Vé khứ hồi hạng phổ thông', 3000000, 'ticket', 180, 'Sân bay Tân Sơn Nhất', 'Vietnam Airlines', 1);

-- ==========================================
-- VIEWS HỮU ÍCH CHO USE CASE 2
-- ==========================================

-- View xem tổng quan lịch khởi hành kèm nhân sự và dịch vụ
CREATE OR REPLACE VIEW v_schedule_overview AS
SELECT 
    ts.schedule_id,
    ts.tour_id,
    t.tour_name,
    t.code AS tour_code,
    ts.departure_date,
    ts.return_date,
    ts.meeting_point,
    ts.meeting_time,
    ts.max_participants,
    ts.current_participants,
    ts.status,
    COUNT(DISTINCT ss.staff_id) AS staff_count,
    COUNT(DISTINCT sserv.service_id) AS service_count,
    COALESCE(SUM(sserv.quantity * sserv.unit_price), 0) AS total_service_cost
FROM tour_schedules ts
LEFT JOIN tours t ON ts.tour_id = t.tour_id
LEFT JOIN schedule_staff ss ON ts.schedule_id = ss.schedule_id
LEFT JOIN schedule_services sserv ON ts.schedule_id = sserv.schedule_id
GROUP BY ts.schedule_id;

-- View kiểm tra tình trạng sẵn sàng của nhân sự
CREATE OR REPLACE VIEW v_staff_availability AS
SELECT 
    s.staff_id,
    s.full_name,
    s.staff_type,
    s.phone,
    s.status,
    COUNT(ss.schedule_id) AS upcoming_schedules,
    MIN(ts.departure_date) AS next_departure
FROM staff s
LEFT JOIN schedule_staff ss ON s.staff_id = ss.staff_id
LEFT JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id 
    AND ts.departure_date >= CURDATE() 
    AND ts.status NOT IN ('Cancelled', 'Completed')
WHERE s.status = 1
GROUP BY s.staff_id;

-- View thống kê dịch vụ theo đối tác
CREATE OR REPLACE VIEW v_partner_services AS
SELECT 
    p.partner_id,
    p.partner_name,
    p.partner_type,
    p.status AS partner_status,
    COUNT(s.service_id) AS total_services,
    COUNT(CASE WHEN s.status = 1 THEN 1 END) AS active_services,
    COALESCE(AVG(s.rating), 0) AS avg_service_rating
FROM partners p
LEFT JOIN services s ON p.partner_id = s.partner_id
GROUP BY p.partner_id;

COMMIT;
