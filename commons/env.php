<?php

// Biến môi trường, dùng chung toàn hệ thống
// Khai báo dưới dạng HẰNG SỐ để không phải dùng $GLOBALS

define('BASE_URL', 'http://localhost:82/duan1/admin/'); // Đường dẫn gốc khu vực admin
define('ROOT_URL', 'http://localhost:82/duan1/'); // Đường dẫn gốc ứng dụng (frontend/login)

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'duan1');  // Tên database

define('PATH_ROOT', __DIR__ . '/../');
