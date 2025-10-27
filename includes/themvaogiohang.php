<?php
session_start();
require_once 'config.php';
require_once 'cart_functions.php';

// Kiểm tra xem có tham số product_id được truyền không
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];
    $quantity = isset($_GET['quantity']) && is_numeric($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    
    // Thêm debug
    error_log("Thêm vào giỏ hàng: product_id=$product_id, quantity=$quantity");
    
    // Thêm sản phẩm vào giỏ hàng
    if (addToCart($product_id, $quantity)) {
        // Thêm thông báo để debugging
        error_log("Thêm vào giỏ hàng thành công");
        
        // Kiểm tra xem request có phải là ajax không
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Đã thêm sản phẩm vào giỏ hàng', 'cart_count' => getCartItemCount()]);
            exit;
        }
        
        // Nếu không phải ajax, chuyển hướng về trang giỏ hàng
        header('Location: ../giohang.php');
        exit;
    } else {
        // Thêm thông báo để debugging
        error_log("Thêm vào giỏ hàng thất bại");
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.']);
            exit;
        }
        
        echo "Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.";
    }
} else {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
        exit;
    }
    
    // Nếu không có product_id hoặc không hợp lệ, chuyển hướng về trang sản phẩm
    header('Location: ../danhmucsp.php');
    exit;
}