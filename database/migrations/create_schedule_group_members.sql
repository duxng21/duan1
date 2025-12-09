CREATE TABLE IF NOT EXISTS schedule_group_members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    id_number VARCHAR(50) NULL COMMENT 'CMND/CCCD',
    date_of_birth DATE NULL,
    gender ENUM('Nam', 'Nữ', 'Khác') NULL,
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES tour_schedules(schedule_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
