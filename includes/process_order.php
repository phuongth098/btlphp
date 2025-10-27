<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug thông tin session
error_log("Session ID in process_order.php: " . session_id());

require_once 'config.php';
require_once 'cart_functions.php';

function generateOrderNumber() {
    return 'TB' . date('YmdHis') . rand(10, 99);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Đọc dữ liệu JSON từ request
    $json_data = file_get_contents('php://input');
    $order_data = json_decode($json_data, true);
    
    // Debug thông tin nhận được
    error_log("Order data received: " . print_r($order_data, true));
    
    // Kiểm tra dữ liệu đầu vào
    if (
        !isset($order_data['customer']) || 
        !isset($order_data['payment_method'])
    ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }
    
    // Lấy thông tin giỏ hàng hiện tại
    $cart_items = getCart();
    $total_amount = getCartTotal();
    
    // Debug thông tin giỏ hàng
    error_log("Cart items in process_order.php: " . count($cart_items));
    error_log("Cart total: " . $total_amount);
    
    if (empty($cart_items)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
        exit;
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Lưu thông tin khách hàng
        $customer = $order_data['customer'];
        $stmt = $conn->prepare(
            "INSERT INTO customers (first_name, last_name, email, phone, address, city, province) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            "sssssss", 
            $customer['first_name'],
            $customer['last_name'],
            $customer['email'],
            $customer['phone'],
            $customer['address'],
            $customer['city'],
            $customer['province']
        );
        
        $stmt->execute();
        $customer_id = $conn->insert_id;

        $order_number = generateOrderNumber();
        
        
        $stmt = $conn->prepare(
            "INSERT INTO orders (customer_id, order_number, total_amount, payment_method, additional_info) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $additional_info = isset($order_data['additional_info']) ? $order_data['additional_info'] : '';
        
        $stmt->bind_param(
            "isdss", 
            $customer_id,
            $order_number,
            $total_amount,
            $order_data['payment_method'],
            $additional_info
        );
        
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Lưu chi tiết đơn hàng
        $stmt = $conn->prepare(
            "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        foreach ($cart_items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            
            $stmt->bind_param(
                "iiidd", 
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $subtotal
            );
            
            $stmt->execute();
            
            // Cập nhật số lượng sản phẩm trong kho (Giảm số lượng)
            $update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $update_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $update_stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        // Xóa giỏ hàng
        clearCart();
        
        // Trả về thông báo thành công
        echo json_encode(['success' => true, 'order_id' => $order_id, 'order_number' => $order_number]);
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi xử lý đơn hàng: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}