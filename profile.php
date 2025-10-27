<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Lấy thông tin người dùng
$sql = "SELECT first_name, last_name, email, newsletter FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Xử lý form cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;
        
        if(empty($first_name) || empty($last_name)) {
            $error = "Vui lòng điền đầy đủ họ và tên";
        } else {
            $update_sql = "UPDATE users SET first_name = ?, last_name = ?, newsletter = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssii", $first_name, $last_name, $newsletter, $user_id);
            
            if($update_stmt->execute()) {
                $_SESSION['user_name'] = $first_name;
                $success = "Thông tin cá nhân đã được cập nhật";
                
                // Cập nhật thông tin hiển thị
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['newsletter'] = $newsletter;
            } else {
                $error = "Có lỗi xảy ra khi cập nhật thông tin";
            }
        }
    }
    
    // Xử lý đổi mật khẩu
    if(isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "Vui lòng điền đầy đủ thông tin mật khẩu";
        } elseif($new_password !== $confirm_password) {
            $error = "Mật khẩu mới và xác nhận mật khẩu không khớp";
        } elseif(strlen($new_password) < 6) {
            $error = "Mật khẩu mới phải có ít nhất 6 ký tự";
        } else {
            // Kiểm tra mật khẩu hiện tại
            $check_sql = "SELECT password FROM users WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $current_hash = $check_result->fetch_assoc()['password'];
            
            if(!password_verify($current_password, $current_hash)) {
                $error = "Mật khẩu hiện tại không đúng";
            } else {
                // Cập nhật mật khẩu mới
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_hash, $user_id);
                
                if($update_stmt->execute()) {
                    $success = "Mật khẩu đã được thay đổi thành công";
                } else {
                    $error = "Có lỗi xảy ra khi cập nhật mật khẩu";
                }
            }
        }
    }
}
?>

<?php 
// Thêm header vào đây và CSS cho trang profile
include 'includes/header.php'; 
?>
<!-- Thêm CSS riêng cho trang profile -->
<link rel="stylesheet" href="assets/css/profile.css">

<div class="account-container">
    <div class="container">
        <h2 class="account-title">Tài khoản của tôi</h2>
        
        <div class="account-tabs">
            <a href="profile.php" class="active">Thông tin cá nhân</a>
            <a href="orders.php">Đơn hàng của tôi</a>
            <a href="orders.php?view=cancelled">Đơn hàng đã hủy</a>
            <!-- <a href="logout.php">Đăng xuất</a> -->
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="account-content">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Họ</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Tên</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        <div class="mb-3 form-check" >
                            <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" <?php echo $user['newsletter'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="newsletter" >Đăng ký nhận thông báo về khuyến mãi và sản phẩm mới</label>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary" style = "margin: 10px 0 0 0">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Thay đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <div class="password-field">
                                <input type="password" class="form-control" placeholder="Mật khẩu hiện tại" name="current_password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <div class="password-field">
                                <input type="password" class="form-control" placeholder="Mật khẩu mới" name="new_password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <div class="password-field">
                                <input type="password" class="form-control" placeholder="Xác nhận mật khẩu" name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">Thay đổi mật khẩu</button>
                    </form>
                </div>
            </div>
            <a type="button" class="btn btn-primary" style = "color : white" href="logout.php">Đăng xuất</a>
        </div>
    </div>
</div>

<!-- JavaScript cho trang profile -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý hiển thị/ẩn mật khẩu
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const passwordField = this.parentElement;
            const passwordInput = passwordField.querySelector('input');
            const icon = this.querySelector('i');
            
            // Chuyển đổi giữa text và password
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    });
    
    // Hiển thị thông báo thành công có thể tự động biến mất
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 500);
        }, 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>