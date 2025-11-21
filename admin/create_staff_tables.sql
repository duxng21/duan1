-- TẠO CÁC BẢNG PHỤ CHO QUẢN LÝ NHÂN SỰ
-- ================================================

-- 1. BẢNG CHỨNG CHỈ
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
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG NGÔN NGỮ
CREATE TABLE IF NOT EXISTS staff_languages (
    language_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    language_name VARCHAR(50) NOT NULL COMMENT 'Tên ngôn ngữ',
    proficiency_level ENUM('Cơ bản', 'Trung cấp', 'Thành thạo', 'Bản ngữ') NOT NULL COMMENT 'Trình độ',
    certificate_name VARCHAR(255) NULL COMMENT 'Chứng chỉ ngoại ngữ',
    certificate_score VARCHAR(50) NULL COMMENT 'Điểm số',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG LỊCH SỬ DẪN TOUR
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
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG LỊCH NGHỈ
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
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG ĐÁNH GIÁ ĐỊNH KỲ
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
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG KINH NGHIỆM
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
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG THÔNG BÁO
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
    
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Tạo bảng thành công!' as message;
