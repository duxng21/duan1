-- ================================================
-- MỞ RỘNG HỆ THỐNG QUẢN LÝ NHÂN VIÊN HƯỚNG DẪN VIÊN
-- ================================================

-- 1. MỞ RỘNG BẢNG STAFF
-- ================================================
ALTER TABLE staff 
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL COMMENT 'Ngày sinh',
ADD COLUMN IF NOT EXISTS gender ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam' COMMENT 'Giới tính',
ADD COLUMN IF NOT EXISTS address TEXT NULL COMMENT 'Địa chỉ',
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL COMMENT 'Ảnh đại diện',
ADD COLUMN IF NOT EXISTS health_status ENUM('Tốt', 'Khá', 'Trung bình', 'Yếu') DEFAULT 'Tốt' COMMENT 'Tình trạng sức khỏe',
ADD COLUMN IF NOT EXISTS health_notes TEXT NULL COMMENT 'Ghi chú sức khỏe',
ADD COLUMN IF NOT EXISTS staff_category ENUM('Nội địa', 'Quốc tế', 'Cả hai') DEFAULT 'Nội địa' COMMENT 'Phân loại HDV',
ADD COLUMN IF NOT EXISTS specialization VARCHAR(255) NULL COMMENT 'Chuyên tuyến (VD: Miền Bắc, Châu Âu...)',
ADD COLUMN IF NOT EXISTS group_specialty ENUM('Khách lẻ', 'Khách đoàn', 'Cả hai') DEFAULT 'Cả hai' COMMENT 'Chuyên khách đoàn hay lẻ',
ADD COLUMN IF NOT EXISTS performance_rating DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Đánh giá hiệu suất (0-5)',
ADD COLUMN IF NOT EXISTS total_tours INT DEFAULT 0 COMMENT 'Tổng số tour đã dẫn',
ADD COLUMN IF NOT EXISTS emergency_contact VARCHAR(100) NULL COMMENT 'Liên hệ khẩn cấp',
ADD COLUMN IF NOT EXISTS emergency_phone VARCHAR(20) NULL COMMENT 'SĐT khẩn cấp',
ADD COLUMN IF NOT EXISTS bank_account VARCHAR(50) NULL COMMENT 'Số tài khoản ngân hàng',
ADD COLUMN IF NOT EXISTS bank_name VARCHAR(100) NULL COMMENT 'Tên ngân hàng';

-- Tạo index cho tìm kiếm nhanh
CREATE INDEX IF NOT EXISTS idx_staff_category ON staff(staff_category);
CREATE INDEX IF NOT EXISTS idx_staff_type ON staff(staff_type);
CREATE INDEX IF NOT EXISTS idx_staff_status ON staff(status);
CREATE INDEX IF NOT EXISTS idx_performance_rating ON staff(performance_rating);

-- 2. BẢNG CHỨNG CHỈ CHUYÊN MÔN
-- ================================================
CREATE TABLE IF NOT EXISTS staff_certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    certificate_name VARCHAR(255) NOT NULL COMMENT 'Tên chứng chỉ',
    certificate_type ENUM('Hướng dẫn viên', 'Ngoại ngữ', 'Chuyên môn khác', 'An toàn', 'Sơ cấp cứu') NOT NULL,
    certificate_number VARCHAR(100) NULL COMMENT 'Số chứng chỉ',
    issued_by VARCHAR(255) NULL COMMENT 'Đơn vị cấp',
    issued_date DATE NULL COMMENT 'Ngày cấp',
    expiry_date DATE NULL COMMENT 'Ngày hết hạn',
    attachment VARCHAR(255) NULL COMMENT 'File đính kèm',
    status ENUM('Còn hạn', 'Sắp hết hạn', 'Hết hạn') DEFAULT 'Còn hạn',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_cert (staff_id),
    INDEX idx_expiry (expiry_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG NGÔN NGỮ SỬ DỤNG (Chi tiết)
-- ================================================
CREATE TABLE IF NOT EXISTS staff_languages (
    language_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    language_name VARCHAR(50) NOT NULL COMMENT 'Tên ngôn ngữ (Tiếng Anh, Trung, Nhật...)',
    proficiency_level ENUM('Cơ bản', 'Trung cấp', 'Thành thạo', 'Bản ngữ') NOT NULL COMMENT 'Trình độ',
    certificate_name VARCHAR(255) NULL COMMENT 'Chứng chỉ ngoại ngữ (TOEIC, IELTS...)',
    certificate_score VARCHAR(50) NULL COMMENT 'Điểm số',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_lang (staff_id),
    INDEX idx_language (language_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG LỊCH SỬ DẪN TOUR
-- ================================================
CREATE TABLE IF NOT EXISTS staff_tour_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    schedule_id INT NOT NULL,
    tour_id INT NOT NULL,
    role VARCHAR(100) DEFAULT 'Hướng dẫn viên' COMMENT 'Vai trò (HDV chính, HDV phụ, Điều phối...)',
    departure_date DATE NOT NULL,
    return_date DATE NULL,
    number_of_guests INT DEFAULT 0 COMMENT 'Số khách',
    customer_feedback TEXT NULL COMMENT 'Phản hồi từ khách',
    customer_rating DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Đánh giá của khách (0-5)',
    manager_feedback TEXT NULL COMMENT 'Nhận xét từ quản lý',
    manager_rating DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Đánh giá từ quản lý (0-5)',
    issues TEXT NULL COMMENT 'Vấn đề phát sinh (nếu có)',
    completed_status ENUM('Hoàn thành tốt', 'Hoàn thành', 'Có vấn đề', 'Chưa hoàn thành') DEFAULT 'Hoàn thành',
    salary_paid DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Lương đã trả',
    bonus DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Thưởng',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES tour_schedules(schedule_id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    INDEX idx_staff_history (staff_id),
    INDEX idx_departure_date (departure_date),
    INDEX idx_rating (customer_rating, manager_rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG LỊCH NGHỈ / LỊCH BẬN
-- ================================================
CREATE TABLE IF NOT EXISTS staff_time_off (
    timeoff_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    timeoff_type ENUM('Nghỉ phép', 'Nghỉ ốm', 'Nghỉ không lương', 'Bận việc cá nhân', 'Đi công tác khác') NOT NULL,
    from_date DATE NOT NULL COMMENT 'Từ ngày',
    to_date DATE NOT NULL COMMENT 'Đến ngày',
    reason TEXT NULL COMMENT 'Lý do',
    attachment VARCHAR(255) NULL COMMENT 'File đính kèm (đơn xin nghỉ, giấy bác sĩ...)',
    status ENUM('Chờ duyệt', 'Đã duyệt', 'Từ chối', 'Đã hủy') DEFAULT 'Chờ duyệt',
    approved_by INT NULL COMMENT 'Người duyệt',
    approved_at DATETIME NULL COMMENT 'Thời gian duyệt',
    notes TEXT NULL COMMENT 'Ghi chú quản lý',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_timeoff (staff_id),
    INDEX idx_date_range (from_date, to_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG ĐÁNH GIÁ ĐỊNH KỲ
-- ================================================
CREATE TABLE IF NOT EXISTS staff_evaluations (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    evaluation_period VARCHAR(50) NOT NULL COMMENT 'Kỳ đánh giá (VD: Q1/2025, Tháng 1/2025)',
    evaluator_name VARCHAR(100) NULL COMMENT 'Người đánh giá',
    professional_skill DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Kỹ năng chuyên môn (0-5)',
    communication_skill DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Kỹ năng giao tiếp (0-5)',
    responsibility DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Tinh thần trách nhiệm (0-5)',
    problem_solving DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Giải quyết vấn đề (0-5)',
    customer_service DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Phục vụ khách hàng (0-5)',
    teamwork DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Làm việc nhóm (0-5)',
    average_score DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Điểm trung bình',
    strengths TEXT NULL COMMENT 'Điểm mạnh',
    weaknesses TEXT NULL COMMENT 'Điểm yếu',
    improvement_plan TEXT NULL COMMENT 'Kế hoạch cải thiện',
    notes TEXT NULL,
    evaluation_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_eval (staff_id),
    INDEX idx_period (evaluation_period),
    INDEX idx_avg_score (average_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG KINH NGHIỆM / DỰ ÁN
-- ================================================
CREATE TABLE IF NOT EXISTS staff_experiences (
    experience_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    company_name VARCHAR(255) NULL COMMENT 'Công ty/Đơn vị từng làm',
    position VARCHAR(100) NULL COMMENT 'Vị trí',
    from_date DATE NULL COMMENT 'Từ tháng/năm',
    to_date DATE NULL COMMENT 'Đến tháng/năm (NULL = hiện tại)',
    description TEXT NULL COMMENT 'Mô tả công việc',
    achievements TEXT NULL COMMENT 'Thành tích đạt được',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_exp (staff_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG THÔNG BÁO / NHẮC NHỞ
-- ================================================
CREATE TABLE IF NOT EXISTS staff_notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NULL COMMENT 'NULL = gửi tất cả nhân viên',
    notification_type ENUM('Phân công tour', 'Nhắc lịch', 'Chứng chỉ sắp hết hạn', 'Đánh giá', 'Khác') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    priority ENUM('Thấp', 'Trung bình', 'Cao', 'Khẩn cấp') DEFAULT 'Trung bình',
    is_read TINYINT(1) DEFAULT 0,
    read_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_notif (staff_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. INSERT DỮ LIỆU MẪU
-- ================================================

-- Dữ liệu mẫu: Chứng chỉ
INSERT INTO staff_certificates (staff_id, certificate_name, certificate_type, certificate_number, issued_by, issued_date, expiry_date, status) VALUES
(1, 'Chứng chỉ Hướng dẫn viên du lịch quốc tế', 'Hướng dẫn viên', 'HDV-2023-001', 'Tổng cục Du lịch Việt Nam', '2023-01-15', '2028-01-15', 'Còn hạn'),
(1, 'TOEIC 850', 'Ngoại ngữ', 'TOEIC-850-2023', 'ETS', '2023-06-20', NULL, 'Còn hạn');

-- Dữ liệu mẫu: Ngôn ngữ
INSERT INTO staff_languages (staff_id, language_name, proficiency_level, certificate_name, certificate_score) VALUES
(1, 'Tiếng Anh', 'Thành thạo', 'TOEIC', '850'),
(1, 'Tiếng Trung', 'Trung cấp', 'HSK', '4'),
(1, 'Tiếng Nhật', 'Cơ bản', 'JLPT', 'N4');

-- Dữ liệu mẫu: Lịch nghỉ
INSERT INTO staff_time_off (staff_id, timeoff_type, from_date, to_date, reason, status) VALUES
(1, 'Nghỉ phép', '2025-12-24', '2025-12-26', 'Nghỉ lễ Giáng sinh', 'Đã duyệt');

-- ================================================
-- 10. VIEWS & STORED PROCEDURES (Tùy chọn)
-- ================================================

-- View: Thống kê hiệu suất nhân viên
CREATE OR REPLACE VIEW v_staff_performance AS
SELECT 
    s.staff_id,
    s.full_name,
    s.staff_type,
    s.staff_category,
    s.total_tours,
    s.performance_rating,
    COUNT(DISTINCT sth.history_id) as completed_tours,
    AVG(sth.customer_rating) as avg_customer_rating,
    AVG(sth.manager_rating) as avg_manager_rating,
    SUM(sth.salary_paid + sth.bonus) as total_earnings
FROM staff s
LEFT JOIN staff_tour_history sth ON s.staff_id = sth.staff_id
WHERE s.status = 1
GROUP BY s.staff_id;

-- View: Nhân viên có lịch trống
CREATE OR REPLACE VIEW v_staff_availability AS
SELECT 
    s.staff_id,
    s.full_name,
    s.staff_type,
    s.phone,
    s.email,
    COUNT(DISTINCT ss.schedule_id) as upcoming_tours
FROM staff s
LEFT JOIN schedule_staff ss ON s.staff_id = ss.staff_id
LEFT JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id 
    AND ts.departure_date >= CURDATE()
    AND ts.status NOT IN ('Cancelled', 'Completed')
WHERE s.status = 1
GROUP BY s.staff_id
HAVING upcoming_tours < 3
ORDER BY upcoming_tours ASC;

-- ================================================
-- 11. KIỂM TRA SAU KHI CHẠY
-- ================================================

-- Kiểm tra cấu trúc bảng staff đã được mở rộng
DESCRIBE staff;

-- Kiểm tra các bảng mới
SHOW TABLES LIKE 'staff_%';

-- Đếm số bản ghi trong các bảng
SELECT 'staff' as table_name, COUNT(*) as records FROM staff
UNION ALL
SELECT 'staff_certificates', COUNT(*) FROM staff_certificates
UNION ALL
SELECT 'staff_languages', COUNT(*) FROM staff_languages
UNION ALL
SELECT 'staff_tour_history', COUNT(*) FROM staff_tour_history
UNION ALL
SELECT 'staff_time_off', COUNT(*) FROM staff_time_off
UNION ALL
SELECT 'staff_evaluations', COUNT(*) FROM staff_evaluations
UNION ALL
SELECT 'staff_experiences', COUNT(*) FROM staff_experiences
UNION ALL
SELECT 'staff_notifications', COUNT(*) FROM staff_notifications;

-- ================================================
-- LƯU Ý QUAN TRỌNG:
-- ================================================
-- 1. BACKUP database trước khi chạy script này
-- 2. Chạy từng phần và kiểm tra kết quả
-- 3. Điều chỉnh staff_id trong INSERT mẫu phù hợp với dữ liệu thực tế
-- 4. Sau khi chạy SQL, cần cập nhật code PHP để sử dụng các trường mới
