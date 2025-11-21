-- ================================================
-- KIỂM TRA VÀ SỬA CẤU TRÚC BẢNG BOOKINGS
-- ================================================

-- Xem cấu trúc hiện tại của bảng bookings
DESCRIBE bookings;

-- Kiểm tra xem cột customer_id có cho phép NULL không
SHOW COLUMNS FROM bookings LIKE 'customer_id';

-- Nếu customer_id không cho phép NULL, sửa lại để cho phép NULL
-- (vì booking đoàn không cần customer_id)
ALTER TABLE bookings MODIFY COLUMN customer_id INT NULL;

-- Kiểm tra lại sau khi sửa
SHOW COLUMNS FROM bookings LIKE 'customer_id';

-- ================================================
-- CẤU TRÚC ĐÚNG CỦA BẢNG BOOKINGS
-- ================================================
/*
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    tour_date DATE NULL COMMENT 'Ngày khởi hành mong muốn',
    customer_id INT NULL COMMENT 'NULL nếu là booking đoàn',
    booking_type ENUM('Cá nhân', 'Đoàn') DEFAULT 'Cá nhân',
    
    -- Thông tin đoàn (chỉ dùng khi booking_type = 'Đoàn')
    organization_name VARCHAR(255) NULL,
    contact_name VARCHAR(100) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_email VARCHAR(100) NULL,
    
    -- Số lượng khách
    num_adults INT DEFAULT 1,
    num_children INT DEFAULT 0,
    num_infants INT DEFAULT 0,
    
    special_requests TEXT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('Chờ xác nhận', 'Đã đặt cọc', 'Hoàn tất', 'Hủy') DEFAULT 'Chờ xác nhận',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    
    INDEX idx_tour_date (tour_id, tour_date),
    INDEX idx_status (status),
    INDEX idx_booking_date (booking_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/

-- Kiểm tra dữ liệu có vấn đề (customer_id = 0 hoặc empty)
SELECT booking_id, tour_id, customer_id, booking_type, organization_name 
FROM bookings 
WHERE customer_id = 0 OR customer_id = '';

-- Sửa dữ liệu lỗi: chuyển customer_id = 0 thành NULL cho booking đoàn
UPDATE bookings 
SET customer_id = NULL 
WHERE booking_type = 'Đoàn' AND (customer_id = 0 OR customer_id IS NOT NULL);

-- ================================================
-- KIỂM TRA BẢNG BOOKING_DETAILS
-- ================================================
DESCRIBE booking_details;

/*
CREATE TABLE IF NOT EXISTS booking_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    service_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/

-- Kiểm tra constraint và triggers
SHOW CREATE TABLE bookings;
SHOW TRIGGERS WHERE `Table` = 'bookings';
