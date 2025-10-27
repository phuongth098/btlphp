<?php
require_once 'config.php';
require_once 'cart_functions.php';
header('Content-Type: application/json');

// Lấy thông tin giỏ hàng
$cart_items = getCart();
$cart_total = getCartTotal();
$cart_count = getCartItemCount();

// Trả về dữ liệu
echo json_encode([
    'success' => true,
    'cart_items' => $cart_items,
    'cart_count' => $cart_count,
    'cart_total' => number_format($cart_total, 0, ',', '.'),
    'cart_total_raw' => $cart_total
]);
?>