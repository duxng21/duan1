<?php
// File kiểm tra mật khẩu
$hash_from_db = '$2y$10$7GPRX1QlT2UID7.6K2NEE.2lQURiNUzFpD8P0gmuibF9t31kqW9xW';

$passwords_to_test = [
    '123456',
    'Admin@123',
    'admin123',
    'admin@123', 
    'password',
    '123',
    'abc123',
    'admin',
    'duan1',
    'laragon'
];

echo "Hash từ DB: " . $hash_from_db . "\n\n";

foreach ($passwords_to_test as $pwd) {
    $verify = password_verify($pwd, $hash_from_db);
    echo "Password: '$pwd' => " . ($verify ? "✓ ĐÚNG" : "✗ Sai") . "\n";
}

// Tạo hash mới cho mật khẩu '123456' để so sánh
echo "\n--- Tạo hash mới ---\n";
$new_hash = password_hash('123456', PASSWORD_DEFAULT);
echo "Hash mới cho '123456': " . $new_hash . "\n";
echo "Verify hash mới: " . (password_verify('123456', $new_hash) ? "✓ ĐÚNG" : "✗ Sai") . "\n";
?>