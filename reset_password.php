<?php
session_start();
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if(empty($email)) {
        $error = "Vui lòng nhập email";
    } else {
        // Kiểm tra email có tồn tại không
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            // Tạo token khôi phục mật khẩu
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $token, $expires);
            
            if($stmt->execute()) {
                // Trong môi trường thực tế, bạn sẽ gửi email với link khôi phục
                // Ở đây chỉ hiển thị thông báo thành công
                $reset_link = "http://{$_SERVER['HTTP_HOST']}/haha/new_password.php?token=$token";
                $success = "Đường dẫn khôi phục mật khẩu đã được gửi đến email của bạn";
                
                // Trong thực tế sẽ gửi email bằng PHPMailer hoặc các thư viện gửi mail khác
                // mail($email, "Khôi phục mật khẩu", "Click vào đường dẫn sau để đặt lại mật khẩu: $reset_link");
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại";
            }
        } else {
            // Không tìm thấy email, nhưng vẫn hiển thị thông báo thành công để bảo mật
            $success = "Nếu email tồn tại, đường dẫn khôi phục mật khẩu đã được gửi đến email của bạn";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Recover Password</h3>
                    <p class="text-muted">Please enter your email address and we'll send you a link to reset your password.</p>
                </div>
                <div class="card-body">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if(!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-block" style="background-color: #f5a9b8; border: none;">Send Reset Link</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p><a href="login.php">Back to Log In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>