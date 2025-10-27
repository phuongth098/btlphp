<?php
// Thêm dòng này vào đầu file để debug
error_log('Cart action request: ' . print_r($_POST, true));

//Cấu hình hành động của giỏ hàng
require_once 'config.php';
require_once 'cart_functions.php';

// Bắt đầu phiên nếu chưa được khởi tạo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if (empty($action)) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin hành động']);
        exit;
    }
    
    // Log action
    error_log("Cart action: $action");

    switch ($action) {
        case 'add':
            if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                exit;
            }
            
            $product_id = (int)$_POST['product_id'];
            $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if (addToCart($product_id, $quantity)) {
                $cart_items = getCart();
                $cart_total = getCartTotal();
                
                // Lấy thông tin sản phẩm vừa thêm
                $product_info = null;
                foreach ($cart_items as $item) {
                    if ($item['product_id'] == $product_id) {
                        $product_info = $item;
                        break;
                    }
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                    'cart_items' => $cart_items,
                    'cart_count' => getCartItemCount(),
                    'cart_total' => number_format($cart_total, 0, ',', '.'),
                    'product_info' => $product_info
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thêm sản phẩm vào giỏ hàng']);
            }
            break;

        case 'update':
            error_log("Update cart request: cart_id=" . $_POST['cart_id'] . ", quantity=" . $_POST['quantity']);
            
            if (!isset($_POST['cart_id']) || !is_numeric($_POST['cart_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID giỏ hàng không hợp lệ']);
                exit;
            }
            
            if (!isset($_POST['quantity']) || !is_numeric($_POST['quantity'])) {
                echo json_encode(['success' => false, 'message' => 'Số lượng không hợp lệ']);
                exit;
            }
            
            $cart_id = (int)$_POST['cart_id'];
            $quantity = (int)$_POST['quantity'];
            
            try {
                if ($quantity <= 0) {
                    // Nếu số lượng <= 0, xóa sản phẩm khỏi giỏ hàng
                    if (removeCartItem($cart_id)) {
                        // Trả về thông báo thành công và làm mới trang
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                            'refresh' => true
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm khỏi giỏ hàng']);
                    }
                } else {
                    // Cập nhật giỏ hàng
                    if (updateCartItem($cart_id, $quantity)) {
                        // Trả về thông báo thành công và làm mới trang
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Đã cập nhật giỏ hàng',
                            'refresh' => true
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật giỏ hàng']);
                    }
                }
            } catch (Exception $e) {
                error_log("Cart update error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
            break;
            
        case 'remove':
            if (!isset($_POST['cart_id']) || !is_numeric($_POST['cart_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID giỏ hàng không hợp lệ']);
                exit;
            }
            
            $cart_id = (int)$_POST['cart_id'];
            
            if (removeCartItem($cart_id)) {
                $cart_total = getCartTotal();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                    'cart_count' => getCartItemCount(),
                    'cart_total' => number_format($cart_total, 0, ',', '.')
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm khỏi giỏ hàng']);
            }
            break;
            
        case 'clear':
            if (clearCart()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng',
                    'cart_count' => 0,
                    'cart_total' => '0'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa giỏ hàng']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Hành động không được hỗ trợ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?>