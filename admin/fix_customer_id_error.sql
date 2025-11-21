-- ================================================
-- FIX LỖI CUSTOMER_ID TRONG BẢNG TOUR_SCHEDULES
-- ================================================

-- Kiểm tra xem bảng tour_schedules có cột customer_id không
-- Nếu có, cột này có thể đã được thêm nhầm

-- Xem cấu trúc bảng tour_schedules
DESCRIBE tour_schedules;

-- Nếu cột customer_id tồn tại trong tour_schedules, xóa nó
-- (tour_schedules không cần customer_id vì đây là lịch khởi hành,
-- customer_id thuộc về bảng bookings hoặc tour_bookings)

-- CẢNH BÁO: Backup database trước khi chạy lệnh này!
-- ALTER TABLE tour_schedules DROP COLUMN customer_id;

-- Kiểm tra cấu trúc đúng của bảng tour_schedules
-- Cấu trúc đúng:
/*
CREATE TABLE IF NOT EXISTS tour_schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE,
    meeting_point VARCHAR(255),
    meeting_time TIME,
    max_participants INT DEFAULT 0,
    current_participants INT DEFAULT 0,
    price_adult DECIMAL(10,2) DEFAULT 0,
    price_child DECIMAL(10,2) DEFAULT 0,
    status ENUM('Open', 'Confirmed', 'Full', 'Completed', 'Cancelled') DEFAULT 'Open',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/

-- Kiểm tra các triggers trên bảng tour_schedules
SHOW TRIGGERS WHERE `Table` = 'tour_schedules';

-- Nếu có trigger nào liên quan đến customer_id, xem xét xóa hoặc sửa
-- SHOW CREATE TRIGGER trigger_name;
-- DROP TRIGGER IF EXISTS trigger_name;

-- ================================================
-- HƯỚNG DẪN SỬ DỤNG:
-- ================================================
-- 1. Chạy lệnh DESCRIBE tour_schedules; để xem cấu trúc bảng
-- 2. Nếu thấy cột customer_id, uncomment lệnh ALTER TABLE để xóa
-- 3. Chạy lệnh SHOW TRIGGERS để kiểm tra triggers
-- 4. Sau khi fix, test lại chức năng cập nhật lịch khởi hành
