<?php
require_once 'includes/config.php';


if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['order_id'];


$stmt = $conn->prepare(
    "SELECT o.*, c.first_name, c.last_name, c.email, c.phone, c.address, c.city, c.province 
     FROM orders o
     JOIN customers c ON o.customer_id = c.id
     WHERE o.id = ?"
);

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$order = $result->fetch_assoc();


$stmt = $conn->prepare(
    "SELECT oi.*, p.name, p.image 
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id = ?"
);

$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$order_items = [];

while ($item = $items_result->fetch_assoc()) {
    $order_items[] = $item;
}
require_once 'includes/header.php';
?>



    <!-- Order Confirmation -->
    <div class="order-confirmation">
        <h1>Đặt hàng thành công!</h1>
        
        <div class="confirmation-message">
            <p>Cảm ơn bạn đã đặt hàng tại Too Beauty. Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
            <p>Mã đơn hàng của bạn là: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong></p>
        </div>
        
        <div class="order-details">
            <h2>Thông tin đơn hàng</h2>
            
            <div class="details-grid">
                <div class="customer-info">
                    <h3>Thông tin khách hàng</h3>
                    <div class="info-group">
                        <div class="info-label">Họ tên:</div>
                        <div><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></div>
                    </div>
                    
                    <div class="info-group">
                    <div class="info-label">Email:</div>
                        <div><?php echo htmlspecialchars($order['email']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Số điện thoại:</div>
                        <div><?php echo htmlspecialchars($order['phone']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Địa chỉ:</div>
                        <div>
                            <?php 
                            echo htmlspecialchars($order['address']) . ', ' . 
                                 htmlspecialchars($order['city']) . ', ' . 
                                 htmlspecialchars($order['province']); 
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="order-info">
                    <h3>Chi tiết đơn hàng</h3>
                    <div class="info-group">
                        <div class="info-label">Mã đơn hàng:</div>
                        <div><?php echo htmlspecialchars($order['order_number']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Ngày đặt hàng:</div>
                        <div><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Phương thức thanh toán:</div>
                        <div>
                            <?php 
                            echo $order['payment_method'] == 'online_payment' 
                                ? 'Thanh toán Online' 
                                : 'Thanh toán khi nhận hàng'; 
                            ?>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Trạng thái:</div>
                        <div>
                            <?php 
                            $status_text = 'Đang xử lý';
                            switch($order['status']) {
                                case 'pending': $status_text = 'Chờ xử lý'; break;
                                case 'processing': $status_text = 'Đang xử lý'; break;
                                case 'completed': $status_text = 'Hoàn thành'; break;
                                case 'cancelled': $status_text = 'Đã hủy'; break;
                            }
                            echo $status_text;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3>Sản phẩm đã đặt</h3>
            <table class="item-list">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['subtotal'], 0, ',', '.') . ' VNĐ'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Tổng cộng:</td>
                        <td style="font-weight: bold; color: #ff6b6b;">
                            <?php echo number_format($order['total_amount'], 0, ',', '.') . ' VNĐ'; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
    </div>

    <!-- Footer -->
<?php
require_once 'includes/footer.php';
?>