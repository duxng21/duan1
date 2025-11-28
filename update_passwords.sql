-- Script SQL để cập nhật mật khẩu cho các user
-- Mật khẩu mới sẽ là: 123456

-- Tạo hash cho mật khẩu 123456
-- Hash này được tạo bằng PHP: password_hash('123456', PASSWORD_DEFAULT)

UPDATE users SET 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    login_attempts = 0,
    status = 'Active'
WHERE username IN ('admin', 'admin2', 'HDV');

-- Hiển thị thông tin user sau khi cập nhật
SELECT user_id, username, full_name, email, status, login_attempts 
FROM users 
WHERE username IN ('admin', 'admin2', 'HDV');