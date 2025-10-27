<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ghi log thông tin session
error_log("Current session ID: " . session_id());

// Trả về ID phiên hiện tại để JavaScript có thể sử dụng
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'session_id' => session_id(),
    'timestamp' => time()
]);