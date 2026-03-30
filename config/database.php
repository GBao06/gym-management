<?php
// Cấu hình kết nối Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gym_management');

// Tạo kết nối
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3307);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// Đặt charset
$conn->set_charset("utf8");

?>
