-- ================================================
-- MỞ RỘNG HỆ THỐNG QUẢN LÝ NHÂN VIÊN - VERSION AN TOÀN
-- ================================================

-- 1. MỞ RỘNG BẢNG STAFF (Sử dụng PROCEDURE để tránh lỗi nếu cột đã tồn tại)
-- ================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS AddStaffColumns$$
CREATE PROCEDURE AddStaffColumns()
BEGIN
    -- Kiểm tra và thêm từng cột
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='date_of_birth') THEN
        ALTER TABLE staff ADD COLUMN date_of_birth DATE NULL COMMENT 'Ngày sinh';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='gender') THEN
        ALTER TABLE staff ADD COLUMN gender ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam' COMMENT 'Giới tính';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='address') THEN
        ALTER TABLE staff ADD COLUMN address TEXT NULL COMMENT 'Địa chỉ';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='avatar') THEN
        ALTER TABLE staff ADD COLUMN avatar VARCHAR(255) NULL COMMENT 'Ảnh đại diện';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='health_status') THEN
        ALTER TABLE staff ADD COLUMN health_status ENUM('Tốt', 'Khá', 'Trung bình', 'Yếu') DEFAULT 'Tốt' COMMENT 'Tình trạng sức khỏe';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='health_notes') THEN
        ALTER TABLE staff ADD COLUMN health_notes TEXT NULL COMMENT 'Ghi chú sức khỏe';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='staff_category') THEN
        ALTER TABLE staff ADD COLUMN staff_category ENUM('Nội địa', 'Quốc tế', 'Cả hai') DEFAULT 'Nội địa' COMMENT 'Phân loại HDV';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='specialization') THEN
        ALTER TABLE staff ADD COLUMN specialization VARCHAR(255) NULL COMMENT 'Chuyên tuyến';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='group_specialty') THEN
        ALTER TABLE staff ADD COLUMN group_specialty ENUM('Khách lẻ', 'Khách đoàn', 'Cả hai') DEFAULT 'Cả hai' COMMENT 'Chuyên khách';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='performance_rating') THEN
        ALTER TABLE staff ADD COLUMN performance_rating DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Đánh giá hiệu suất';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='total_tours') THEN
        ALTER TABLE staff ADD COLUMN total_tours INT DEFAULT 0 COMMENT 'Tổng số tour';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='emergency_contact') THEN
        ALTER TABLE staff ADD COLUMN emergency_contact VARCHAR(100) NULL COMMENT 'Liên hệ khẩn cấp';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='emergency_phone') THEN
        ALTER TABLE staff ADD COLUMN emergency_phone VARCHAR(20) NULL COMMENT 'SĐT khẩn cấp';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='bank_account') THEN
        ALTER TABLE staff ADD COLUMN bank_account VARCHAR(50) NULL COMMENT 'Số tài khoản';
    END IF;
    
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA='duan1' AND TABLE_NAME='staff' AND COLUMN_NAME='bank_name') THEN
        ALTER TABLE staff ADD COLUMN bank_name VARCHAR(100) NULL COMMENT 'Tên ngân hàng';
    END IF;
END$$

DELIMITER ;

-- Gọi procedure
CALL AddStaffColumns();
DROP PROCEDURE IF EXISTS AddStaffColumns;

-- Tạo index (bỏ qua lỗi nếu đã tồn tại)
CREATE INDEX idx_staff_category ON staff(staff_category);
CREATE INDEX idx_staff_type ON staff(staff_type);
CREATE INDEX idx_staff_status ON staff(status);
CREATE INDEX idx_performance_rating ON staff(performance_rating);

-- 2. BẢNG CHỨNG CHỈ
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

-- 3. BẢNG NGÔN NGỮ
-- ================================================
CREATE TABLE IF NOT EXISTS staff_languages (
    language_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    language_name VARCHAR(50) NOT NULL COMMENT 'Tên ngôn ngữ',
    proficiency_level ENUM('Cơ bản', 'Trung cấp', 'Thành thạo', 'Bản ngữ') NOT NULL COMMENT 'Trình độ',
    certificate_name VARCHAR(255) NULL COMMENT 'Chứng chỉ ngoại ngữ',
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
    role VARCHAR(100) DEFAULT 'Hướng dẫn viên' COMMENT 'Vai trò',
    departure_date DATE NOT NULL,
    return_date DATE NULL,
    number_of_guests INT DEFAULT 0 COMMENT 'Số khách',
    customer_feedback TEXT NULL COMMENT 'Phản hồi khách',
    customer_rating DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Đánh giá khách',
    manager_feedback TEXT NULL COMMENT 'Nhận xét quản lý',
    manager_rating DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Đánh giá quản lý',
    issues TEXT NULL COMMENT 'Vấn đề phát sinh',
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

-- 5. BẢNG LỊCH NGHỈ
-- ================================================
CREATE TABLE IF NOT EXISTS staff_time_off (
    timeoff_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    timeoff_type ENUM('Nghỉ phép', 'Nghỉ ốm', 'Nghỉ không lương', 'Bận việc cá nhân', 'Đi công tác khác') NOT NULL,
    from_date DATE NOT NULL COMMENT 'Từ ngày',
    to_date DATE NOT NULL COMMENT 'Đến ngày',
    reason TEXT NULL COMMENT 'Lý do',
    attachment VARCHAR(255) NULL COMMENT 'File đính kèm',
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
    evaluation_period VARCHAR(50) NOT NULL COMMENT 'Kỳ đánh giá',
    evaluator_name VARCHAR(100) NULL COMMENT 'Người đánh giá',
    professional_skill DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Kỹ năng chuyên môn',
    communication_skill DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Kỹ năng giao tiếp',
    responsibility DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Trách nhiệm',
    problem_solving DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Giải quyết vấn đề',
    customer_service DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Phục vụ khách',
    teamwork DECIMAL(2,1) DEFAULT 0.0 COMMENT 'Làm việc nhóm',
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

-- 7. BẢNG KINH NGHIỆM
-- ================================================
CREATE TABLE IF NOT EXISTS staff_experiences (
    experience_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    company_name VARCHAR(255) NULL COMMENT 'Công ty',
    position VARCHAR(100) NULL COMMENT 'Vị trí',
    from_date DATE NULL COMMENT 'Từ ngày',
    to_date DATE NULL COMMENT 'Đến ngày',
    description TEXT NULL COMMENT 'Mô tả',
    achievements TEXT NULL COMMENT 'Thành tích',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff_exp (staff_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG THÔNG BÁO
-- ================================================
CREATE TABLE IF NOT EXISTS staff_notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NULL COMMENT 'NULL = gửi tất cả',
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

-- 9. VIEWS
-- ================================================
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

-- HOÀN THÀNH!
SELECT 'Đã mở rộng thành công hệ thống quản lý nhân sự!' as message;
