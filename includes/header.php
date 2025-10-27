<?php
// Đảm bảo session đã được khởi động
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'cart_functions.php';
$cart_count = getCartItemCount();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Too Beauty</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/chinhsach.css" />
    <link rel="stylesheet" href = "assets/css/danhmucsp.css"/>
    <link rel="stylesheet" href = "assets/css/thanhtoan.css"/>
    <link rel="stylesheet" href = "assets/css/trangchu.css"/>     
    <link rel="stylesheet" href = "assets/css/thongbao.css"/>
    <link rel="stylesheet" href = "assets/css/xacnhanthanhtoan.css"/>
    <link rel="stylesheet" href = "assets/css/auth.css"/>
    <!-- <link rel="stylesheet" href = "assets/css/giohang.css"/> -->
</head>
<body>
    <!-- Header -->
    <header>
    <div class="logo"><a href="index.php"><span>Too</span><span>Beauty</span></a></div>
    <nav>
        <ul>
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="danhmucsp.php">Sản phẩm</a></li>
            <li><a href="chinhsach.php">Giới thiệu</a></li>
            <li><a href="#footer">Liên hệ</a></li>
        </ul>
    </nav>
    <div class="user-actions">
        <!-- Phần dropdown tài khoản đã được cải thiện -->
        <div class="account-dropdown">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="#" class="dropdown-trigger">
                    <i class="fas fa-user-circle"></i> 
                    <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span> 
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="profile.php"><i class="fas fa-user"></i><span> Thông tin cá nhân</span></a>
                    <a href="orders.php"><i class="fas fa-shopping-bag"></i><span>Đơn hàng của tôi</span></a>
                    <a href="orders.php?view=cancelled"><i class="fas fa-ban"></i><span> Đơn hàng đã hủy</span></a>
                    <!-- <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a> -->
                </div>
            <?php else: ?>
                <a href="#" class="dropdown-trigger">
                    <i class="fas fa-user"></i>
                    <span>Tài khoản</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i><span>Đăng nhập</span></a>
                    <a href="register.php"><i class="fas fa-user-plus"></i><span>Đăng ký</span></a>
                </div>
            <?php endif; ?>
        </div>
        <a href="giohang.php" class="cart-icon">
            <div class="icon-container">
                <i class="fas fa-shopping-bag"></i>
                <span class="cart-count" id = "cart-count"><?php echo $cart_count; ?></span>
            </div>
        </a>
    </div>
    </header>