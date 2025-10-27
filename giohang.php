<?php
//Set điều kiện trong giỏ hàng
require_once 'includes/config.php';
require_once 'includes/cart_functions.php';
session_start();
// Lấy danh sách sản phẩm trong giỏ hàng
$cart_items = getCart();
$cart_total = getCartTotal();
require_once 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/giohang.css"/>

<link rel="stylesheet" href="assets/css/style.css" />

<!-- Cart -->
<div class="container">
    <div class="cart-items">
        <table class="cart-table">
            <thead class="cart-header">
                <tr>
                    <th class="product-col">Sản phẩm</th>
                    <th class="price-col">Giá</th>
                    <th class="quantity-col">Số lượng</th>
                    <th class="subtotal-col">Thành tiền</th>
                    <th class="remove-col">Xóa sản phẩm</th>
                </tr>
            </thead>
            <tbody id="cart-items-container">
                <?php if (count($cart_items) > 0): ?>
                  <?php foreach ($cart_items as $item): ?>
                    <tr class="product-row" data-cart-id="<?php echo $item['id']; ?>">
                        <td class="product-col">
                            <div class="product-info">
                                <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'Sp/default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image" onerror="this.src='./Sp/default.jpg'">
                                <span class="product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td class="price-col"><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                        <td class="quantity-col">
                            <div class="quantity-control">
                                <button class="quantity-btn decrease">-</button>
                                <input type="text" class="quantity-input" value="<?php echo $item['quantity']; ?>" data-cart-id="<?php echo $item['id']; ?>">
                                <button class="quantity-btn increase">+</button>
                            </div>
                        </td>
                        <td class="subtotal-col"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VNĐ</td>
                        <td class="remove-col"><button class="remove-btn" data-cart-id="<?php echo $item['id']; ?>">🗑️</button></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">Giỏ hàng trống. <strong style="color: palevioletred;"><a href="danhmucsp.php">Tiếp tục mua sắm</a></strong></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cart-totals">
        <h2>Tổng giỏ hàng</h2>
        <div class="totals-row">
            <span>Tạm tính</span>
            <span class="subtotal-amount"><?php echo number_format($cart_total, 0, ',', '.'); ?> VNĐ</span>
        </div>
        <div class="totals-row">
            <span>Tổng cộng</span>
            <span class="total-amount"><?php echo number_format($cart_total, 0, ',', '.'); ?> VNĐ</span>
        </div>
        <div class="checkout-section">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="thanhtoan.php" class="checkout-btn"> Thanh toán</a>
            <?php else: ?>
                <div class="login-notification">
                    <p>Vui lòng <a href="login.php?checkout_required=1">đăng nhập</a> hoặc <a href="register.php?checkout_required=1">đăng ký</a> để tiến hành thanh toán</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php
require_once 'includes/footer.php';
?>

<!-- Thêm đoạn code này để xử lý lỗi -->
<script>
// Xử lý lỗi cập nhật giỏ hàng
window.addEventListener('error', function(event) {
  console.error("Caught error:", event.error);
  if (event.error && event.error.message && event.error.message.includes("Cannot set properties of null")) {
    console.log("Detected null error, will reload page");
    alert("Có lỗi xảy ra, trang sẽ tải lại để khắc phục");
    window.location.reload();
  }
});

// Xử lý rejection không bắt được
window.addEventListener('unhandledrejection', function(event) {
  console.error("Unhandled Promise Rejection:", event.reason);
  if (event.reason && event.reason.message && event.reason.message.includes("Cannot set properties of null")) {
    console.log("Detected null error in promise, will reload page");
    alert("Có lỗi xảy ra, trang sẽ tải lại để khắc phục");
    window.location.reload();
  }
});
</script>
