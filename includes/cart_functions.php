<?php
require_once 'config.php';
//THỰC HIỆN TÍNH TOÁN CÁC THAO TÁC CÓ THỂ SẢY RA TRÊN GIỎ HÀNG
// Hàm để lấy giỏ hàng hiện tại
function getCart() {
    global $conn;
    $session_id = session_id();
    $cart_items = [];
    
    // Sử dụng giá gốc thay vì giá khuyến mãi
    $sql = "SELECT c.id, c.product_id, c.quantity, p.name, p.price, 
           p.price as display_price, p.price * c.quantity as subtotal,
           pi.image_url 
           FROM cart c
           JOIN products p ON c.product_id = p.id
           LEFT JOIN product_images pi ON p.id = pi.product_id
           WHERE c.session_id = ? AND (pi.sort_order = 1 OR pi.sort_order IS NULL)
           ORDER BY c.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
    
    return $cart_items;
}

// Hàm để thêm sản phẩm vào giỏ hàng
function addToCart($product_id, $quantity = 1) {
    global $conn;
    
    // Thêm debug logging
    error_log("addToCart called with product_id=$product_id, quantity=$quantity");
    
    // Kiểm tra sản phẩm có tồn tại không
    $check_sql = "SELECT id FROM products WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows == 0) {
        error_log("Product with ID $product_id does not exist");
        return false;
    }
    
    $session_id = session_id();
    
    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $sql = "SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $session_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $cart_id = $row['id'];
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $cart_id);
        $success = $stmt->execute();
        error_log("Update existing cart item: success=$success");
        return $success;
    } else {
        // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
        $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $session_id, $product_id, $quantity);
        $success = $stmt->execute();
        error_log("Insert new cart item: success=$success");
        return $success;
    }
}

// Thêm try-catch để bắt lỗi cụ thể
function updateCartItem($cart_id, $quantity) {
    global $conn;
    $session_id = session_id();
    
    try {
        if ($quantity <= 0) {
            // Nếu số lượng <= 0, xóa sản phẩm khỏi giỏ hàng
            return removeCartItem($cart_id);
        }
        
        // Kiểm tra xem cart_id có tồn tại không
        $check_stmt = $conn->prepare("SELECT id FROM cart WHERE id = ? AND session_id = ?");
        $check_stmt->bind_param("is", $cart_id, $session_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Không tìm thấy cart_id
            error_log("Cart item not found: cart_id=$cart_id, session_id=$session_id");
            return false;
        }
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ? AND session_id = ?");
        $stmt->bind_param("iis", $quantity, $cart_id, $session_id);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error updating cart: " . $e->getMessage());
        throw $e;
    }
}

// Hàm để xóa sản phẩm khỏi giỏ hàng
function removeCartItem($cart_id) {
    global $conn;
    $session_id = session_id();
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
    $stmt->bind_param("is", $cart_id, $session_id);
    return $stmt->execute();
}

// Hàm để làm trống giỏ hàng
function clearCart() {
    global $conn;
    $session_id = session_id();
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    return $stmt->execute();
}

// Hàm để tính tổng giỏ hàng
function getCartTotal() {
    $cart_items = getCart();
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

// Hàm để đếm số lượng sản phẩm trong giỏ hàng
function getCartItemCount() {
    $cart_items = getCart();
    $count = 0;
    
    foreach ($cart_items as $item) {
        $count += $item['quantity'];
    }
    
    return $count;
}
?>