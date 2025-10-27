<?php 
session_start();
require_once 'includes/config.php';

// Nếu đã đăng nhập rồi, chuyển đến trang chủ
if(isset($_SESSION['user_id'])) {
    header('Location: index.php?login_success=1');
    exit;
}

$error = '';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ email và mật khẩu';
    } else {
        // Kiểm tra đăng nhập
        $query = "SELECT id, first_name, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])) {
                // Đăng nhập thành công
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                
                // Chuyển hướng về trang chủ
                header('Location: index.php');
                exit;
            } else {
                $error = 'Email hoặc mật khẩu không đúng';
            }
        } else {
            $error = 'Email hoặc mật khẩu không đúng';
        }
    }
}

require_once 'includes/header.php'; 
?>

<div class="auth-container">
    <button class="close-btn">&times;</button>
    <h2 style = "margin: 0px 0px 20px 0">ĐĂNG NHẬP</h2>
    
    <?php if(!empty($error)): ?>
        <div class="login-error" style="display: block; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="login-form">
        <input type="email" class="form-control" placeholder="Email" name="email" required>
        
        <div class="password-field">
            <input type="password" class="form-control" placeholder="Password" name="password" required>
        </div>
        <button type="submit" class="auth-btn">Đăng nhập</button>
    </form>
    
    <div class="divider">hoặc</div>
    
    <button class="google-btn">
        <img src="assets/images/gg.png" alt="Google" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/24px-Google_%22G%22_Logo.svg.png'; this.onerror=null;">
        Tiếp tục với Google
    </button>
    
    <div class="auth-links">
        <p>Bạn chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>