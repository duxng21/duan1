-- ================================================
-- INSERT DỮ LIỆU MẪU CHO CÁC BẢNG TOUR DETAIL
-- ================================================

-- 1. Tạo các bảng cần thiết (nếu chưa có)
-- ================================================

-- Bảng lịch trình tour theo ngày
CREATE TABLE IF NOT EXISTS `tour_itineraries` (
  `itinerary_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tour_id` INT NOT NULL,
  `day_number` INT NOT NULL COMMENT 'Ngày thứ mấy',
  `title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề của ngày',
  `description` TEXT COMMENT 'Mô tả chi tiết hoạt động trong ngày',
  `accommodation` VARCHAR(255) COMMENT 'Khách sạn lưu trú',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`tour_id`) REFERENCES `tours`(`tour_id`) ON DELETE CASCADE,
  INDEX `idx_tour_day` (`tour_id`, `day_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chính sách tour (hủy/đổi)
CREATE TABLE IF NOT EXISTS `tour_policies` (
  `policy_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tour_id` INT NOT NULL UNIQUE,
  `cancellation_policy` TEXT COMMENT 'Chính sách hủy tour',
  `change_policy` TEXT COMMENT 'Chính sách đổi tour',
  `payment_policy` TEXT COMMENT 'Chính sách thanh toán',
  `note_policy` TEXT COMMENT 'Lưu ý khác',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`tour_id`) REFERENCES `tours`(`tour_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng tags/loại tour
CREATE TABLE IF NOT EXISTS `tour_tags` (
  `tag_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tag_name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) COMMENT 'URL friendly name',
  `description` TEXT,
  `status` TINYINT(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng quan hệ tour và tags (nhiều-nhiều)
CREATE TABLE IF NOT EXISTS `tour_tag_relations` (
  `relation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tour_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`tour_id`) REFERENCES `tours`(`tour_id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tour_tags`(`tag_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_tour_tag` (`tour_id`, `tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. INSERT DỮ LIỆU MẪU
-- ================================================

-- INSERT TAGS
INSERT INTO `tour_tags` (`tag_name`, `slug`, `description`, `status`) VALUES
('Du lịch biển', 'du-lich-bien', 'Tour du lịch biển đảo', 1),
('Du lịch núi', 'du-lich-nui', 'Tour leo núi, khám phá núi rừng', 1),
('Du lịch văn hóa', 'du-lich-van-hoa', 'Tour tham quan di tích lịch sử, văn hóa', 1),
('Du lịch sinh thái', 'du-lich-sinh-thai', 'Tour khám phá thiên nhiên, sinh thái', 1),
('Du lịch mạo hiểm', 'du-lich-mao-hiem', 'Tour thể thao mạo hiểm', 1),
('Du lịch nghỉ dưỡng', 'du-lich-nghi-duong', 'Tour nghỉ dưỡng, thư giãn', 1),
('Du lịch gia đình', 'du-lich-gia-dinh', 'Tour phù hợp gia đình có trẻ em', 1),
('Du lịch tâm linh', 'du-lich-tam-linh', 'Tour tham quan chùa chiền, đền miếu', 1),
('Du lịch ẩm thực', 'du-lich-am-thuc', 'Tour khám phá ẩm thực địa phương', 1),
('Du lịch giá rẻ', 'du-lich-gia-re', 'Tour tiết kiệm, giá cả phải chăng', 1),
('Tour cao cấp', 'tour-cao-cap', 'Tour sang trọng, đẳng cấp', 1),
('Du lịch cộng đồng', 'du-lich-cong-dong', 'Tour homestay, trải nghiệm cộng đồng', 1);

-- INSERT LỊCH TRÌNH MẪU (giả sử tour_id = 1 tồn tại)
-- Bạn cần thay thế tour_id phù hợp với dữ liệu thực tế

-- Lịch trình cho Tour Hạ Long (giả sử tour_id = 1)
INSERT INTO `tour_itineraries` (`tour_id`, `day_number`, `title`, `description`, `accommodation`) VALUES
(1, 1, 'Hà Nội - Hạ Long - Lên tàu', 
'08:00 - Xe và HDV đón quý khách tại điểm hẹn, khởi hành đi Hạ Long.\n12:00 - Dùng bữa trưa tại nhà hàng.\n13:30 - Đến bến tàu Hạ Long, làm thủ tục lên tàu.\n14:00 - Tàu khởi hành, ngắm cảnh vịnh Hạ Long.\n15:30 - Thăm hang Sửng Sốt.\n17:00 - Tự do bơi lội, chèo kayak.\n19:00 - Bữa tối trên tàu.\n20:30 - Câu mực đêm, karaoke.', 
'Du thuyền 4 sao'),

(1, 2, 'Vịnh Bái Tử Long - Làng Chài Vung Viêng', 
'06:30 - Tập Thái Cực Quyền trên boong tàu.\n07:00 - Dùng điểm tâm sáng.\n08:00 - Thăm làng chài Vung Viêng, trải nghiệm đi thuyền kayak.\n10:00 - Trả phòng, nghỉ ngơi trên tàu.\n11:00 - Buffet trưa trên tàu.\n12:00 - Tàu về bến, trả phòng.\n13:00 - Xe đưa quý khách về Hà Nội.\n17:00 - Về đến Hà Nội, kết thúc chương trình.', 
NULL);

-- Lịch trình cho Tour Đà Nẵng (giả sử tour_id = 2)
INSERT INTO `tour_itineraries` (`tour_id`, `day_number`, `title`, `description`, `accommodation`) VALUES
(2, 1, 'Hà Nội - Đà Nẵng - Bà Nà Hills', 
'06:00 - Khởi hành từ Hà Nội đi sân bay Nội Bài.\n08:30 - Bay đến Đà Nẵng (chuyến bay 1h30).\n10:00 - Đến Đà Nẵng, xe đưa đoàn đi Bà Nà Hills.\n11:00 - Chinh phục Bà Nà bằng cáp treo dài nhất thế giới.\n12:00 - Dùng buffet trưa tại Bà Nà.\n14:00 - Tham quan Cầu Vàng, Vườn Hoa Le Jardin D''Amour.\n17:00 - Về khách sạn nhận phòng.\n19:00 - Tự do khám phá ẩm thực Đà Nẵng.', 
'Khách sạn 4 sao gần biển'),

(2, 2, 'Hội An - Phố Cổ', 
'08:00 - Dùng buffet sáng tại khách sạn.\n09:00 - Khởi hành đi Hội An.\n10:00 - Tham quan phố cổ Hội An: Chùa Cầu, Hội quán Phúc Kiến.\n12:00 - Dùng cơm trưa với đặc sản Hội An.\n14:00 - Tự do mua sắm, chụp ảnh tại phố cổ.\n17:00 - Về Đà Nẵng, nghỉ ngơi.\n19:00 - Tự do dạo biển Mỹ Khê về đêm.', 
'Khách sạn 4 sao gần biển'),

(2, 3, 'Đà Nẵng - Hà Nội', 
'08:00 - Dùng buffet sáng, trả phòng.\n09:00 - Tự do tắm biển Mỹ Khê.\n11:00 - Dùng cơm trưa.\n13:00 - Ra sân bay Đà Nẵng.\n15:00 - Bay về Hà Nội.\n17:00 - Về đến Hà Nội, kết thúc chương trình.', 
NULL);

-- INSERT CHÍNH SÁCH MẪU
INSERT INTO `tour_policies` (`tour_id`, `cancellation_policy`, `change_policy`, `payment_policy`, `note_policy`) VALUES
(1, 
'- Hủy tour trước 15 ngày: hoàn lại 70% tổng giá trị tour.\n- Hủy tour từ 7-14 ngày: hoàn lại 50% tổng giá trị tour.\n- Hủy tour từ 3-6 ngày: hoàn lại 30% tổng giá trị tour.\n- Hủy tour trong vòng 2 ngày: không hoàn lại tiền.\n- Trong trường hợp bất khả kháng (thiên tai, dịch bệnh): hoàn lại 90% giá trị tour.',

'- Đổi tour phải trước 10 ngày khởi hành.\n- Mỗi lần đổi tour phải chịu phí 10% giá trị tour.\n- Chỉ được đổi tour 1 lần duy nhất.\n- Tour đổi phải có giá trị tương đương hoặc cao hơn.\n- Nếu giá trị tour mới thấp hơn, không hoàn lại phần chênh lệch.',

'- Đặt cọc 30% khi đăng ký tour.\n- Thanh toán 70% còn lại trước 3 ngày khởi hành.\n- Chấp nhận thanh toán: tiền mặt, chuyển khoản, thẻ tín dụng.\n- Hóa đơn VAT theo yêu cầu (cộng thêm 10% thuế).',

'- Giá tour đã bao gồm: xe, khách sạn, ăn uống theo chương trình, vé tham quan, HDV.\n- Giá tour chưa bao gồm: đồ uống có cồn, chi phí cá nhân.\n- Trẻ em dưới 5 tuổi: miễn phí (ngủ chung với bố mẹ).\n- Trẻ em từ 5-10 tuổi: 50% giá tour người lớn.\n- Trẻ em trên 10 tuổi: tính như người lớn.'),

(2,
'- Hủy tour trước 20 ngày: hoàn lại 80% tổng giá trị tour.\n- Hủy tour từ 10-19 ngày: hoàn lại 60% tổng giá trị tour.\n- Hủy tour từ 5-9 ngày: hoàn lại 40% tổng giá trị tour.\n- Hủy tour trong vòng 4 ngày: không hoàn lại tiền.',

'- Đổi tour phải trước 15 ngày khởi hành.\n- Mỗi lần đổi tour phải chịu phí 5% giá trị tour.\n- Được đổi tour tối đa 2 lần.',

'- Đặt cọc 40% khi đăng ký tour.\n- Thanh toán 60% còn lại trước 5 ngày khởi hành.\n- Hỗ trợ trả góp 0% qua thẻ tín dụng.',

'- Giá tour đã bao gồm vé máy bay khứ hồi.\n- Yêu cầu hộ chiếu còn hạn trên 6 tháng (tour quốc tế).\n- Khách có nhu cầu ăn chay, kiêng cần báo trước.');

-- INSERT QUAN HỆ TOUR-TAGS MẪU
INSERT INTO `tour_tag_relations` (`tour_id`, `tag_id`) VALUES
-- Tour Hạ Long (tour_id = 1) có tags: Du lịch biển, Du lịch nghỉ dưỡng, Du lịch gia đình
(1, 1),  -- Du lịch biển
(1, 6),  -- Du lịch nghỉ dưỡng  
(1, 7),  -- Du lịch gia đình

-- Tour Đà Nẵng (tour_id = 2) có tags: Du lịch biển, Du lịch văn hóa, Du lịch ẩm thực
(2, 1),  -- Du lịch biển
(2, 3),  -- Du lịch văn hóa
(2, 9);  -- Du lịch ẩm thực

-- ================================================
-- LƯU Ý QUAN TRỌNG:
-- ================================================
-- 1. Thay đổi tour_id trong các câu INSERT phù hợp với dữ liệu thực tế của bạn
-- 2. Chạy lần lượt từng phần để tránh lỗi
-- 3. Kiểm tra foreign key constraints trước khi insert
-- 4. Backup database trước khi chạy script

-- Kiểm tra dữ liệu đã insert:
-- SELECT * FROM tour_tags;
-- SELECT * FROM tour_itineraries WHERE tour_id = 1;
-- SELECT * FROM tour_policies WHERE tour_id IN (1, 2);
-- SELECT * FROM tour_tag_relations;
