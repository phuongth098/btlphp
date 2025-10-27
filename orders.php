<?php
require_once 'includes/config.php';

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Thêm dòng này để định nghĩa biến $view_cancelled
$view_cancelled = isset($_GET['view']) && $_GET['view'] == 'cancelled';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Xử lý hủy đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    
    // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
    $check_query = "
        SELECT o.id FROM orders o
        INNER JOIN customers c ON o.customer_id = c.id
        WHERE o.id = ? AND c.email = (SELECT email FROM users WHERE id = ?)
    ";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $order_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 1) {
        // Chỉ cho phép hủy đơn hàng đang ở trạng thái 'pending' hoặc 'processing'
        $update_query = "
            UPDATE orders SET 
            status = 'cancelled',
            updated_at = NOW()
            WHERE id = ? AND (status = 'pending' OR status = 'processing')
        ";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $order_id);
        
        if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
            $success = "Đơn hàng #" . $order_id . " đã được hủy thành công.";
            // Chuyển hướng để tránh gửi lại form khi refresh
            header('Location: orders.php?cancel_success=1');
            exit;
        } else {
            $error = "Không thể hủy đơn hàng. Đơn hàng có thể đã được xử lý hoặc giao hàng.";
        }
    } else {
        $error = "Đơn hàng không tồn tại hoặc bạn không có quyền hủy đơn hàng này.";
    }
}

// Lấy danh sách đơn hàng của người dùng
$orders = [];
$active_orders = []; // Đơn hàng đang hoạt động
$cancelled_orders = []; // Đơn hàng đã hủy

$order_query = "
    SELECT o.*, c.first_name, c.last_name, c.email, c.phone, c.address, c.city, c.province
    FROM orders o
    INNER JOIN customers c ON o.customer_id = c.id
    WHERE c.email = (SELECT email FROM users WHERE id = ?)
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Lấy chi tiết sản phẩm trong đơn hàng
        $items_query = "
            SELECT oi.*, p.name, pi.image_url
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id
            WHERE oi.order_id = ? AND (pi.sort_order = 1 OR pi.sort_order IS NULL OR pi.id IS NULL)
            GROUP BY oi.id
        ";
        $items_stmt = $conn->prepare($items_query);
        $items_stmt->bind_param("i", $row['id']);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        
        $items = [];
        while ($item = $items_result->fetch_assoc()) {
            $items[] = $item;
        }
        
        $row['items'] = $items;
        
        // Phân loại đơn hàng
        if ($row['status'] == 'cancelled') {
            $cancelled_orders[] = $row;
        } else {
            $active_orders[] = $row;
        }
    }
}

require_once 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/orders.css">
<link rel="stylesheet" href="assets/css/profile.css">

<div class="account-container">
    <div class="container">
        <h2 class="account-title">Tài khoản của tôi</h2>
        
        <div class="account-tabs">
            <a href="profile.php">Thông tin cá nhân</a>
            <a href="orders.php" <?php echo !isset($_GET['view']) ? 'class="active"' : ''; ?>>Đơn hàng của tôi</a>
            <a href="orders.php?view=cancelled" <?php echo isset($_GET['view']) && $_GET['view'] == 'cancelled' ? 'class="active"' : ''; ?>>Đơn hàng đã hủy</a>
            <!-- <a href="logout.php">Đăng xuất</a> -->
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success) || isset($_GET['cancel_success'])): ?>
            <div class="alert alert-success">
                <?php echo !empty($success) ? $success : "Đơn hàng đã được hủy thành công."; ?>
            </div>
        <?php endif; ?>
        
        <div class="account-content">
            <div class="order-sections">
                <?php if(!$view_cancelled): ?>
                <!-- Hiển thị đơn hàng đang xử lý -->
                <div class="orders-section active-orders">
                    <h3>Đơn hàng đang xử lý</h3>
                    <?php if(count($active_orders) > 0): ?>
                        <div class="accordion orders-accordion" id="activeOrdersAccordion">
                            <?php foreach($active_orders as $index => $order): ?>
                                <div class="accordion-item order-item">
                                    <h2 class="accordion-header" id="headingActive<?php echo $order['id']; ?>">
                                        <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseActive<?php echo $order['id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapseActive<?php echo $order['id']; ?>">
                                            <div class="order-summary">
                                                <div class="order-id">Đơn hàng #<?php echo $order['order_number']; ?></div>
                                                <div class="order-date">Ngày đặt: <?php echo date('d/m/Y', strtotime($order['created_at'])); ?></div>
                                                <div class="order-status <?php echo $order['status']; ?>"><?php echo getStatusText($order['status']); ?></div>
                                                <div class="order-total"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseActive<?php echo $order['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="headingActive<?php echo $order['id']; ?>" data-bs-parent="#activeOrdersAccordion">
                                        <div class="accordion-body">
                                            <!-- Chi tiết đơn hàng -->
                                            <div class="order-details">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Thông tin đơn hàng</h6>
                                                        <table class="info-table">
                                                            <tr>
                                                                <td>Mã đơn hàng:</td>
                                                                <td><?php echo $order['order_number']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Ngày đặt:</td>
                                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Trạng thái:</td>
                                                                <td class="status-text <?php echo $order['status']; ?>">
                                                                    <?php echo getStatusText($order['status']); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phương thức thanh toán:</td>
                                                                <td>
                                                                    <?php echo $order['payment_method'] == 'online_payment' ? 'Thanh toán Online' : 'Thanh toán khi nhận hàng'; ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Thông tin giao hàng</h6>
                                                        <table class="info-table">
                                                            <tr>
                                                                <td>Họ tên:</td>
                                                                <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Địa chỉ:</td>
                                                                <td><?php echo $order['address']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Thành phố:</td>
                                                                <td><?php echo $order['city']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Tỉnh/Thành phố:</td>
                                                                <td><?php echo $order['province']; ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="order-products">
                                                    <h6>Sản phẩm đã mua</h6>
                                                    <table class="products-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Sản phẩm</th>
                                                                <th>Giá</th>
                                                                <th>Số lượng</th>
                                                                <th>Thành tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($order['items'] as $item): ?>
                                                                <tr>
                                                                    <td class="product-info">
                                                                        <?php if(!empty($item['image_url'])): ?>
                                                                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" class="product-thumbnail">
                                                                        <?php endif; ?>
                                                                        <span><?php echo $item['name']; ?></span>
                                                                    </td>
                                                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                                                    <td><?php echo $item['quantity']; ?></td>
                                                                    <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VNĐ</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                                                                <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    

                                                    <?php if($order['status'] == 'pending' || $order['status'] == 'processing'): ?>
                                                        <div class="order-actions">
                                                            <form method="POST" action="">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <button type="submit" name="cancel_order" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">Hủy đơn hàng</button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-orders">
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag"></i>
                                <p>Bạn chưa có đơn hàng nào đang xử lý</p>
                                <a href="danhmucsp.php" class="btn btn-primary">Mua sắm ngay</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <!-- Hiển thị đơn hàng đã hủy -->
                <div class="orders-section cancelled-orders">
                    <h3>Đơn hàng đã hủy</h3>
                    <?php if(count($cancelled_orders) > 0): ?>
                        <div class="accordion orders-accordion" id="cancelledOrdersAccordion">
                            <?php foreach($cancelled_orders as $index => $order): ?>
                                <div class="accordion-item order-item cancelled">
                                    <h2 class="accordion-header" id="headingCancelled<?php echo $order['id']; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCancelled<?php echo $order['id']; ?>" aria-expanded="false" aria-controls="collapseCancelled<?php echo $order['id']; ?>">
                                            <div class="order-summary">
                                                <div class="order-id">Đơn hàng #<?php echo $order['order_number']; ?></div>
                                                <div class="order-date">Ngày đặt: <?php echo date('d/m/Y', strtotime($order['created_at'])); ?></div>
                                                <div class="order-status cancelled">Đã hủy</div>
                                                <div class="order-total"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseCancelled<?php echo $order['id']; ?>" class="accordion-collapse collapse" aria-labelledby="headingCancelled<?php echo $order['id']; ?>" data-bs-parent="#cancelledOrdersAccordion">
                                        <div class="accordion-body">
                                            <div class="order-details">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Thông tin đơn hàng</h6>
                                                        <table class="info-table">
                                                            <tr>
                                                                <td>Mã đơn hàng:</td>
                                                                <td><?php echo $order['order_number']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Ngày đặt:</td>
                                                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Trạng thái:</td>
                                                                <td class="status-text cancelled">Đã hủy</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Ngày hủy:</td>
                                                                <td><?php echo date('d/m/Y H:i', strtotime($order['updated_at'])); ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Thông tin giao hàng</h6>
                                                        <table class="info-table">
                                                            <tr>
                                                                <td>Họ tên:</td>
                                                                <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Địa chỉ:</td>
                                                                <td><?php echo $order['address']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Thành phố:</td>
                                                                <td><?php echo $order['city']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Tỉnh/Thành phố:</td>
                                                                <td><?php echo $order['province']; ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="order-products">
                                                    <h6>Sản phẩm đã đặt</h6>
                                                    <table class="products-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Sản phẩm</th>
                                                                <th>Giá</th>
                                                                <th>Số lượng</th>
                                                                <th>Thành tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($order['items'] as $item): ?>
                                                                <tr>
                                                                    <td class="product-info">
                                                                        <?php if(!empty($item['image_url'])): ?>
                                                                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" class="product-thumbnail">
                                                                        <?php endif; ?>
                                                                        <span><?php echo $item['name']; ?></span>
                                                                    </td>
                                                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                                                    <td><?php echo $item['quantity']; ?></td>
                                                                    <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VNĐ</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                                                                <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    
                                                    <div class="reorder-section">
                                                        <a href="danhmucsp.php" class="btn btn-primary">Mua sắm thêm sản phẩm</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-orders">
                            <div class="empty-state">
                                <i class="fas fa-ban"></i>
                                <p>Bạn chưa có đơn hàng nào đã hủy</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if(count($active_orders) == 0 && count($cancelled_orders) == 0): ?>
                <div class="no-orders">
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <p>Bạn chưa có đơn hàng nào</p>
                        <a href="danhmucsp.php" class="btn btn-primary">Mua sắm ngay</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Hàm chuyển đổi trạng thái thành text
function getStatusText($status) {
    switch($status) {
        case 'pending': return 'Chờ xử lý';
        case 'processing': return 'Đang xử lý';
        case 'shipped': return 'Đã giao cho đơn vị vận chuyển';
        case 'delivered': return 'Đã giao hàng';
        case 'cancelled': return 'Đã hủy';
        default: return 'Không xác định';
    }
}

require_once 'includes/footer.php';
?>