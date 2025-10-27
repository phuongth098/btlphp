<?php
session_start();

// Xóa tất cả biến session
$_SESSION = array();

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ với thông báo đăng xuất thành công
header('Location: index.php?logout_success=1');
exit;
?>