<?php 
session_start();
require_once 'includes/config.php';

// Nếu đã đăng nhập rồi, chuyển đến trang chủ
if(isset($_SESSION['user_id'])) {
    header('Location: index.php?register_success=1');
    exit;
}

$error = '';
$success = '';

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Kiểm tra các trường
    if(empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';
    } elseif(strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif($password !== $confirm_password) {
        $error = 'Xác nhận mật khẩu không khớp';
    } else {
        // Kiểm tra email đã tồn tại chưa
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $error = 'Email này đã được sử dụng';
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Thêm người dùng mới
            $insert_query = "INSERT INTO users (first_name, last_name, email, password, newsletter) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('ssssi', $first_name, $last_name, $email, $hashed_password, $newsletter);
            
            if($stmt->execute()) {
                // Đăng nhập người dùng
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $first_name;
                
                // Chuyển hướng về trang chủ
                header('Location: index.php');
                exit;
            } else {
                $error = 'Đăng ký không thành công, vui lòng thử lại sau';
            }
        }
    }
}

require_once 'includes/header.php'; 
?>

<div class="auth-container">
    <button class="close-btn">&times;</button>
    <h2>ĐĂNG KÝ</h2>
    <p class="subtitle">Vui lòng điền các thông tin dưới đây:</p>
    
    <?php if(!empty($error)): ?>
        <div class="register-error" style="display: block; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="register-form">
        <div class="input-label">Họ</div>
        <input type="text" class="form-control" placeholder="Họ" name="last_name" required>

        <div class="input-label">Tên</div>
        <input type="text" class="form-control" placeholder="Tên" name="first_name" required>
        
        <div class="input-label">Email</div>
        <input type="email" class="form-control" placeholder="Email" name="email" required>
        
        <div class="input-label">Mật khẩu</div>
        <div class="password-field">
            <input type="password" class="form-control" placeholder="Mật khẩu" name="password" required>
            
        </div>
        
        <div class="input-label">Xác nhận mật khẩu</div>
        <div class="password-field">
            <input type="password" class="form-control" placeholder="Xác nhận mật khẩu" name="confirm_password" required>
        </div>
        
        <div class="newsletter-checkbox">
            <input type="checkbox" id="newsletter" name="newsletter">
            <label for="newsletter">Tôi muốn đăng kí nhận bản tin ngay bây giờ</label>
        </div>
        
        <button type="submit" class="auth-btn">Đăng ký</button>
    </form>
    
    <div class="divider">Hoặc</div>
    
    <button class="google-btn">
        <img src="assets/images/gg.png" alt="Google" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/24px-Google_%22G%22_Logo.svg.png'; this.onerror=null;">
        Tiếp tục với Google
    </button>
    
    <div class="auth-links">
        <p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>