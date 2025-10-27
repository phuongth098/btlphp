<?php
require_once 'includes/config.php';
require_once 'includes/cart_functions.php';
$cart_count = getCartItemCount();
// Lấy sản phẩm mới
$new_products_sql = "SELECT p.*, pi.image_url 
                     FROM products p 
                     LEFT JOIN product_images pi ON p.id = pi.product_id 
                     WHERE p.is_new = 1 AND (pi.sort_order = 1 OR pi.sort_order IS NULL)
                     LIMIT 8";
$new_result = $conn->query($new_products_sql);

// Lấy sản phẩm bán chạy
$top_products_sql = "SELECT p.*, pi.image_url 
                     FROM products p 
                     LEFT JOIN product_images pi ON p.id = pi.product_id 
                     WHERE p.is_top = 1 AND (pi.sort_order = 1 OR pi.sort_order IS NULL)
                     LIMIT 8";
$top_result = $conn->query($top_products_sql);

// Đếm số sản phẩm trong giỏ hàng

require_once 'includes/header.php';
?>


    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <div class="hero-content">
                <h2>KHÁM PHÁ VẺ ĐẸP CỦA BẠN VỚI</h2>
                <h1>TOO BEAUTY</h1>
                <p>Chăm sóc bản thân, làn tóa yêu thương - bắt đầu từ những điều nhỏ bé</p>
                <a href="danhmucsp.php" class="btn primary-btn">MUA NGAY</a>
            </div>
            <div class="hero-image">
                <img src="./Trangchu/first-screen.png" alt="Too Beauty Products" />
            </div>
        </div>
    </section>
    <!-- New Products -->
    <section class="new-products">
        <div class="container">
            <div class="section-header">
                <h2>SẢN PHẨM MỚI</h2>
                <span><a href="danhmucsp.php">Xem tất cả <i class="fas fa-arrow-right"></i></a></span>
            </div>
            <div class="slider-wrapper">
                <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
                <div class="product-slider">
                    <?php
                    if ($new_result->num_rows > 0) {
                        while ($row = $new_result->fetch_assoc()) {
                            $price = number_format($row['price'], 0, ',', '.');
                            $image_url = $row['image_url'] ?? 'Sp/default.jpg';
                            echo '
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="' . $image_url . '" alt="' . htmlspecialchars($row['name']) . '" onerror="this.src=\'./Sp/default.jpg\'" />
                                </div>
                                <div class="product-info">
                                    <h3>' . htmlspecialchars($row['name']) . '</h3>
                                    <p class="product-description">' . htmlspecialchars($row['short_description']) . '</p>
                                    <div class="product-price">
                                        <span class="current-price">' . $price . ' VNĐ</span>
                                    </div>
                                     <a href="#" class="btn product-btn" data-product-id="' . $row['id'] . '">Thêm vào giỏ</a>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p>Không có sản phẩm mới.</p>';
                    }
                    ?>
                </div>
                <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>


    <!-- Best Selling Products -->
    <section class="best-selling">
        <div class="container">
            <div class="section-header">
                <h2>SẢN PHẨM BÁN CHẠY</h2>
                <span><a href="danhmucsp.php">Xem tất cả <i class="fas fa-arrow-right"></i></a></span>
            </div>
            <div class="slider-wrapper">
                <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
                <div class="product-slider">
                    <?php
                    if ($top_result->num_rows > 0) {
                        while ($row = $top_result->fetch_assoc()) {
                            $price = number_format($row['price'], 0, ',', '.');
                            $image_url = $row['image_url'] ?? 'Sp/default.jpg';
                            echo '
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="' . $image_url . '" alt="' . htmlspecialchars($row['name']) . '" onerror="this.src=\'./Sp/default.jpg\'" />
                                </div>
                                <div class="product-info">
                                    <h3>' . htmlspecialchars($row['name']) . '</h3>
                                    <p class="product-description">' . htmlspecialchars($row['short_description']) . '</p>
                                    <div class="product-price">
                                        <span class="current-price">' . $price . ' VNĐ</span>
                                    </div>
                                  <a href="#" class="btn product-btn" data-product-id="' . $row['id'] . '">Thêm vào giỏ</a>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p>Không có sản phẩm bán chạy.</p>';
                    }
                    ?>
                </div>
                <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        </section>
        <section class="about-us">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Too Beauty</h2>
                    <p>
                        Chúng tôi là cửa hàng mỹ phẩm chuyên cung cấp các sản phẩm chăm sóc da chính hãng. Chúng tôi tin rằng vẻ đẹp không chỉ đến từ bên ngoài mà còn từ sự tự tin và thói quen làm đẹp hàng ngày.
                    </p>
                    <a href="chinhsach.php" class="btn secondary-btn">Về Chúng Tôi</a>
                </div>
                <div class="about-image">
                    <img src="./Trangchu/8.jpg" alt="Too Beauty Products" />
                </div>
            </div>
        </div>
    </section>

    <section class="skin-care-section">
        <div class="container">
            <div class="skin-care-content">
                <div class="skin-care-image">
                    <img src="./Trangchu/10.jpg" alt="Skin Care Products" />
                </div>
                <div class="skin-care-text">
                    <h2>Nâng niu làn da</h2>
                    <p>
                        Sở hữu làn da khỏe mạnh, rạng rỡ với các sản phẩm dưỡng da Hàn Quốc chính hãng từ những thương hiệu nổi tiếng.
                    </p>
                    <a href="danhmucsp.php?category=skin" class="btn accent-btn">Sản Phẩm</a>
                </div>
            </div>
        </div>
    </section>

    <section class="beauty-tips">
        <div class="container">
            <div class="section-header">
                <h2>TIPS LÀM ĐẸP</h2>
            </div>
            <div class="tips-container">
                <div class="tip-card">
                    <div class="tip-image">
                        <img src="./Trangchu/11.jpg" alt="Công dụng dầu dừa" />
                    </div>
                    <div class="tip-content">
                        <h3>Công dụng dầu dừa</h3>
                        <p>Dầu dừa được ca ngợi vì những lợi ích của nó trong việc dưỡng ẩm, chăm sóc cơ thể và tóc.</p>
                        <a href="https://hellobacsi.com/da-lieu/cham-soc-da/dau-dua-than-duoc-cho-ve-dep/" class="btn tip-btn" target="_blank">Xem Thêm</a>
                    </div>
                </div>
                <div class="tip-card">
                    <div class="tip-image">
                        <img src="./Trangchu/12.jpg" alt="Món quà sống còn của sắc đẹp năm 2025" />
                    </div>
                    <div class="tip-content">
                        <h3>Món quà sống còn của sắc đẹp năm 2025</h3>
                        <p>Danh sách các xu hướng làm đẹp phải có trong năm 2025</p>
                        <a href="https://thegioitiepthi.danviet.vn/top-7-xu-huong-lam-dep-len-ngoi-nam-2025-d137753.html" class="btn tip-btn" target="_blank">Xem Thêm</a>
                    </div>
                </div>
                <div class="tip-card">
                    <div class="tip-image">
                        <img src="./Trangchu/13.jpg" alt="7 thói quen chăm sóc da cần loại bỏ ngay" />
                    </div>
                    <div class="tip-content">
                        <h3>7 thói quen chăm sóc da cần loại bỏ ngay</h3>
                        <p>Thói quen thái quá có thể hấp dẫn nhưng thường gây hại nhiều hơn lợi.</p>
                        <a href="https://suckhoedoisong.vn/7-thoi-quen-lam-dep-tai-hai-ban-can-tu-bo-ngay-169155254.htm" class="btn tip-btn" target="_blank">Xem Thêm</a>
                    </div>
                </div>
            </div>
            <div class="slider-nav">
                <span class="active"></span>
                <span></span>
            </div>
        </div>
    </section>

    <section class="instagram-feed">
        <div class="container">
            <div class="section-header">
                <h2>CHIA SẺ CÁCH LÀM ĐẸP VỚI #TOOBEAUTY</h2>
                <span>Xem tất cả</span>
            </div>
            <div class="instagram-grid">
                <div class="insta-item">
                    <img src="./Trangchu/14.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/15.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/16.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/17.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/18.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/19.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/20.jpg" alt="Instagram Post" />
                </div>
                <div class="insta-item">
                    <img src="./Trangchu/21.jpg" alt="Instagram Post" />
                </div>
            </div>
            <div class="text-center">
                <a href="#" class="btn accent-btn">Tham gia ngay</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    
<?php
require_once 'includes/footer.php';
?>

<?php
$conn->close();
?>