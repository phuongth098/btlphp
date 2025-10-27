<?php
// Kết nối cơ sở dữ liệu
$server = 'localhost';
$user = 'root';
$pass = '';
$database = 'toobeauty1';

$conn = new mysqli($server, $user, $pass, $database);

if ($conn) {
    mysqli_query($conn, "SET NAMES 'utf8'");
} else {
    echo 'Kết nối thất bại';
    exit();
}
require_once 'includes/header.php';
?>

    <main>
        <div class="sidebar">
            <h2>Làm sạch kép</h2>
            <ul>
                <li><a href="#cleansing-balms">Dầu tẩy trang</a></li>
                <li><a href="#oil-cleansers">Nước tẩy rửa</a></li>
                <li><a href="#water-cleansers">Bông tẩy trang</a></li>
            </ul>

            <div class="filter-container">
                <div class="filter-section">
                    <div class="filter-section-header">LOẠI DA</div>
                    <div class="filter-section-content">
                        <div class="checkbox-item">
                            <input type="checkbox" id="all" />
                            <label for="all">Tất cả</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="mixed" />
                            <label for="mixed">Da hỗn hợp/ Da dầu</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="dry" />
                            <label for="dry">Da khô</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="normal" />
                            <label for="normal">Da bình thường</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="sensitive" />
                            <label for="sensitive">Da nhạy cảm</label>
                        </div>
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-section-header">Khoảng giá</div>
                    <div class="filter-section-content">
                        <div class="radio-item">
                            <input type="radio" name="price" id="under25" />
                            <label for="under25">Dưới 200.000 VNĐ</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" name="price" id="25to50" />
                            <label for="25to50">200.000 VNĐ - 500.000 VNĐ</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" name="price" id="50to100" />
                            <label for="50to100">500.000 VNĐ - 800.000 VNĐ</label>
                        </div>
                        <div class="radio-item">
                            <span class="selected-circle"></span>
                            <label>Giá</label>
                        </div>

                        <div class="price-inputs">
                            <div class="price-input">
                                <input type="text" placeholder="Tối Thiểu" />
                            </div>
                            <div class="price-input">
                                <input type="text" placeholder="Tối Đa" />
                            </div>
                        </div>
                    </div>
                </div>

                <button class="apply-button">Áp Dụng</button>
            </div>
        </div>

        <div class="product-container">
    <div class="product-header">
        <?php
        // Đếm tổng số sản phẩm
        $count_sql = "SELECT COUNT(*) as total FROM products";
        $count_result = $conn->query($count_sql);
        $count_row = $count_result->fetch_assoc();
        $total_products = $count_row['total'];

        // Giả định $sort_by được lấy từ query string hoặc mặc định
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'relevance';
        ?>
        <div class="product-count" id="product-count"><?php echo $total_products; ?> SẢN PHẨM</div>
        <div class="product-sort">
            <label for="sort">SẮP XẾP THEO:</label>
            <select id="sort" class="sort-dropdown">
                <option value="relevance" <?php echo $sort_by === 'relevance' ? 'selected' : ''; ?>>LIÊN QUAN</option>
                <option value="price_asc" <?php echo $sort_by === 'price_asc' ? 'selected' : ''; ?>>GIÁ: Tăng dần</option>
                <option value="price_desc" <?php echo $sort_by === 'price_desc' ? 'selected' : ''; ?>>GIÁ: Giảm dần</option>
                <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>MỚI NHẤT</option>
            </select>
        </div>
    </div>
    <div class="product-grid">
        <?php
        // Truy vấn lấy sản phẩm và hình ảnh đầu tiên (sort_order = 1)
        $products_sql = "SELECT DISTINCT p.*, pi.image_url 
                         FROM products p 
                         LEFT JOIN product_images pi ON p.id = pi.product_id 
                         WHERE pi.sort_order = 1 OR pi.sort_order IS NULL
                         ";
        $products_result = $conn->query($products_sql);

        // Xác định loại da cho từng sản phẩm
        $skin_types = [
            'dry' => ['dưỡng ẩm', 'da khô', 'cấp ẩm'],
            'mixed' => ['da dầu', 'da hỗn hợp', 'kiềm dầu'],
            'sensitive' => ['da nhạy cảm', 'làm dịu', 'nhạy cảm'],
            'normal' => ['mọi loại da', 'da bình thường']
        ];

        if ($products_result->num_rows > 0) {
            $index = 0;
            $items_per_page = 15;

            while ($row = $products_result->fetch_assoc()) {
                $index++;
                $page_number = ceil($index / $items_per_page);

                $price = number_format($row['price'], 0, ',', '.') . ' VNĐ';
                $description = htmlspecialchars($row['short_description']);
                $name = htmlspecialchars($row['name']);
                $image_url = htmlspecialchars($row['image_url'] ?? 'Sp/default.jpg');

                // Xác định loại da
                $skin_type = 'normal';
                $desc_lower = strtolower($row['description'] . ' ' . $row['short_description']);
                foreach ($skin_types as $type => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (strpos($desc_lower, $keyword) !== false) {
                            $skin_type = $type;
                            break 2;
                        }
                    }
                }

                // Xác định danh mục (category) dựa trên mô tả
                $category = 'other';
                if (strpos($desc_lower, 'dầu tẩy trang') !== false) {
                    $category = 'dầu tẩy trang';
                } elseif (strpos($desc_lower, 'nước tẩy rửa') !== false) {
                    $category = 'nước tẩy rửa';
                } elseif (strpos($desc_lower, 'bông tẩy trang') !== false) {
                    $category = 'bông tẩy trang';
                }
                $category_map = [
                    'dầu tẩy trang' => 'cleansing-balms',
                    'nước tẩy rửa' => 'oil-cleansers',
                    'bông tẩy trang' => 'water-cleansers'
                ];
                $data_category = $category_map[$category] ?? 'other';

                echo '
                <div class="product-card" data-id="' . $row['id'] . '" data-skin="' . $skin_type . '" data-price="' . $row['price'] . '" data-page="' . $page_number . '" data-category="' . $data_category . '">
                    <div class="product-image">
                        <img src="' . $image_url . '" alt="' . $name . '" onerror="this.src=\'./Sp/default.jpg\'" />
                        ' . ($row['is_top'] ? '<div class="product-tag">Bán chạy</div>' : '') . '
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">' . $name . '</h3>
                        <p class="product-description">' . $description . '</p>
                        <div class="product-price">' . $price . '</div>
                        <a href="#" class="btn product-btn" data-product-id="' . $row['id'] . '">Thêm vào giỏ</a>
                    </div>
                </div>';
            }
        } else {
            echo '<p>Không có sản phẩm nào để hiển thị.</p>';
        }
        ?>
    </div>
    <div class="pagination-new">
        <div class="pagination-items">
            <a href="#" class="arrow"><i class="fas fa-chevron-left"></i></a>
            <a href="#" class="arrow"><i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
</div>
    </main>

    <!-- Footer -->
<?php
require_once 'includes/footer.php';
?>

<?php
$conn->close();
?>