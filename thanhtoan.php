<?php
//Điều kiện trước khi vào thanh toán
require_once 'includes/config.php';
require_once 'includes/cart_functions.php';

// Bắt đầu phiên làm việc nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Debug thông tin session
error_log("Session ID in thanhtoan.php: " . session_id());

// Lấy danh sách sản phẩm trong giỏ hàng
$cart_items = getCart();
$cart_total = getCartTotal();

// Debug thông tin giỏ hàng
error_log("Cart items in thanhtoan.php: " . count($cart_items));
error_log("Cart total: " . $cart_total);

// Kiểm tra giỏ hàng có trống không và điều hướng nếu trống
if (empty($cart_items)) {
  error_log("Cart is empty, redirecting to giohang.php");
  header('Location: giohang.php');
  exit;
}

require_once 'includes/header.php';
?>


<!-- Main Content -->
<div class="container checkout-container">
  <!-- Billing Details -->
  <section class="billing-details">
    <h2>Thông tin thanh toán</h2>
    <form id="billing-form">
      <div class="form-row">
        <div class="form-group">
          <label for="first-name" class="required">Họ</label>
          <input type="text" id="first-name" name="first-name" required />
        </div>
        <div class="form-group">
          <label for="last-name">Tên</label>
          <input type="text" id="last-name" name="last-name" required />
        </div>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="form-group">
        <label for="phone">Số điện thoại</label>
        <input type="tel" id="phone" name="phone" required />
      </div>
      <div class="form-group">
        <label for="street-address">Địa chỉ</label>
        <input type="text" id="street-address" name="street-address" required />
      </div>

      <div class="form-group">
        <label for="province">Tỉnh/Thành phố</label>
        <select id="province" name="province" required>
          <option value="">-- Chọn Tỉnh/Thành phố --</option>
          <option value="Hà Nội">Hà Nội</option>
          <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
          <option value="Đà Nẵng">Đà Nẵng</option>
          <option value="Hải Phòng">Hải Phòng</option>
          <option value="Cần Thơ">Cần Thơ</option>
          <option value="An Giang">An Giang</option>
          <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
          <option value="Bắc Giang">Bắc Giang</option>
          <option value="Bắc Kạn">Bắc Kạn</option>
          <option value="Bạc Liêu">Bạc Liêu</option>
          <option value="Bắc Ninh">Bắc Ninh</option>
          <option value="Bến Tre">Bến Tre</option>
          <option value="Bình Định">Bình Định</option>
          <option value="Bình Dương">Bình Dương</option>
          <option value="Bình Phước">Bình Phước</option>
          <option value="Bình Thuận">Bình Thuận</option>
          <option value="Cà Mau">Cà Mau</option>
          <option value="Cao Bằng">Cao Bằng</option>
          <option value="Đắk Lắk">Đắk Lắk</option>
          <option value="Đắk Nông">Đắk Nông</option>
          <option value="Điện Biên">Điện Biên</option>
          <option value="Đồng Nai">Đồng Nai</option>
          <option value="Đồng Tháp">Đồng Tháp</option>
          <option value="Gia Lai">Gia Lai</option>
          <option value="Hà Giang">Hà Giang</option>
          <option value="Hà Nam">Hà Nam</option>
          <option value="Hà Tĩnh">Hà Tĩnh</option>
          <option value="Hải Dương">Hải Dương</option>
          <option value="Hậu Giang">Hậu Giang</option>
          <option value="Hòa Bình">Hòa Bình</option>
          <option value="Hưng Yên">Hưng Yên</option>
          <option value="Khánh Hòa">Khánh Hòa</option>
          <option value="Kiên Giang">Kiên Giang</option>
          <option value="Kon Tum">Kon Tum</option>
          <option value="Lai Châu">Lai Châu</option>
          <option value="Lâm Đồng">Lâm Đồng</option>
          <option value="Lạng Sơn">Lạng Sơn</option>
          <option value="Lào Cai">Lào Cai</option>
          <option value="Long An">Long An</option>
          <option value="Nam Định">Nam Định</option>
          <option value="Nghệ An">Nghệ An</option>
          <option value="Ninh Bình">Ninh Bình</option>
          <option value="Ninh Thuận">Ninh Thuận</option>
          <option value="Phú Thọ">Phú Thọ</option>
          <option value="Phú Yên">Phú Yên</option>
          <option value="Quảng Bình">Quảng Bình</option>
          <option value="Quảng Nam">Quảng Nam</option>
          <option value="Quảng Ngãi">Quảng Ngãi</option>
          <option value="Quảng Ninh">Quảng Ninh</option>
          <option value="Quảng Trị">Quảng Trị</option>
          <option value="Sóc Trăng">Sóc Trăng</option>
          <option value="Sơn La">Sơn La</option>
          <option value="Tây Ninh">Tây Ninh</option>
          <option value="Thái Bình">Thái Bình</option>
          <option value="Thái Nguyên">Thái Nguyên</option>
          <option value="Thanh Hóa">Thanh Hóa</option>
          <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
          <option value="Tiền Giang">Tiền Giang</option>
          <option value="Trà Vinh">Trà Vinh</option>
          <option value="Tuyên Quang">Tuyên Quang</option>
          <option value="Vĩnh Long">Vĩnh Long</option>
          <option value="Vĩnh Phúc">Vĩnh Phúc</option>
          <option value="Yên Bái">Yên Bái</option>
        </select>
      </div>
      <div class="form-group">
        <label for="town-city">Quận/Huyện</label>
        <input type="text" id="town-city" name="town-city" required />
      </div>
      <div class="form-group">
        <label for="additional-info">Ghi chú đơn hàng (tùy chọn)</label>
        <textarea id="additional-info" name="additional-info" rows="4" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay địa điểm giao hàng chi tiết hơn."></textarea>
      </div>
    </form>
  </section>

  <!-- Order Summary -->
  <section class="order-summary">
    <div class="summary-header">
      <h3>Sản phẩm</h3>
      <h3>Thành tiền</h3>
    </div>
    <div id="cart-items">
      <?php foreach ($cart_items as $item): ?>
        <div class="summary-item">
          <p><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></p>
          <p><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VNĐ</p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="summary-total">
      <p><strong>Tạm tính</strong></p>
      <p><strong id="subtotal"><?php echo number_format($cart_total, 0, ',', '.'); ?> VNĐ</strong></p>
    </div>
    <div class="summary-total">
      <p><strong>Tổng cộng</strong></p>
      <p>
        <strong id="total" class="highlight-total"><?php echo number_format($cart_total, 0, ',', '.'); ?> VNĐ</strong>
      </p>
    </div>
    <div class="payment-methods">
      <h3>Lựa chọn phương thức thanh toán</h3>
      <p class="payment-description">
        Vui lòng chọn phương thức thanh toán phù hợp với bạn.
      </p>
      <label class="payment-option">
        <input type="radio" name="payment-method" value="online_payment" />
        Thanh toán Online
        <span class="payment-description">
          Thanh toán trực tiếp vào tài khoản ngân hàng của chúng tôi. Lưu ý rằng đơn hàng của bạn sẽ không được xử lý và vận chuyển cho đến khi bạn hoàn tất thanh toán.
        </span>
      </label>
      <label class="payment-option">
        <input type="radio" name="payment-method" value="direct_check" />
        Thanh toán khi nhận hàng (COD)
        <span class="payment-description">
          Đơn hàng sẽ được giao đến địa chỉ của bạn và bạn sẽ thanh toán khi nhận được hàng.
        </span>
      </label>
    </div>
    <button class="place-order-btn" id="place-order-btn">Đặt Hàng</button>
  </section>
</div>

<!-- Footer -->
<?php
require_once 'includes/footer.php';
?>

<script>
  // Đảm bảo giữ phiên làm việc
  function syncSession() {
    fetch('includes/sync_session.php')
      .then(response => response.json())
      .then(data => {
        console.log("Session synchronized: ", data.session_id);
      })
      .catch(error => {
        console.error("Error syncing session:", error);
      });
  }

  // Sync session khi trang tải xong
  document.addEventListener('DOMContentLoaded', function() {
    syncSession();
  });

  // Sync session mỗi 30 giây để tránh session timeout
  setInterval(syncSession, 30000);
</script>