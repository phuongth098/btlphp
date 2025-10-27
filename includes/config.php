<?php
$server = 'localhost';
$user = 'root';
$pass = '';
$database = 'toobeauty1';

$conn = new mysqli($server, $user, $pass, $database);
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>