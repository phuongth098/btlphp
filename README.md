# 📌 **README — TooBeauty | Website Quảng Bá & Bán Mỹ Phẩm**

## Giới thiệu dự án

**TooBeauty** là website thương mại điện tử chuyên về mỹ phẩm, được xây dựng nhằm hỗ trợ khách hàng tìm kiếm, xem thông tin và mua sắm các sản phẩm làm đẹp một cách thuận tiện.

---

## 🎯 **Mục tiêu chính**

* Quảng bá thương hiệu mỹ phẩm TooBeauty.
* Cung cấp hệ thống mua sắm trực tuyến đầy đủ chức năng.
* Tạo trải nghiệm thân thiện, hiện đại cho người dùng.
* Xây dựng website có thể mở rộng và dễ bảo trì.

---
## Yêu Cầu Hệ Thống
### Yêu Cầu Kỹ Thuật

- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Máy chủ web (Apache/Nginx)
- PDO PHP Extension
- Trình duyệt web hiện đại có hỗ trợ JavaScript

### Cấu Hình Cơ Sở Dữ Liệu

- Host: localhost
- Tên Database: toobeauty1
- Tên người dùng: root
- Cấu hình mật khẩu mặc định (có thể được sửa đổi trong Config/Database.php)

---
## 👥 **Nhóm phát triển**

* **Sinh viên thực hiện:** Nguyễn Hoàng Thanh Duy, Nguyễn Phương Thảo
* **Lớp:** K59SN1

---

# 📂 **Cấu trúc website**

Website được tổ chức theo **cấu trúc phân cấp**, gồm các trang chính:

### 1. **Trang chủ**

* Banner quảng cáo
* Sản phẩm nổi bật / bán chạy / mới
* Thanh điều hướng
* Footer chứa thông tin liên hệ và chính sách

### 2. **Trang Sản phẩm**

* Danh sách 100 sản phẩm
* Lọc theo danh mục, loại da, giá
* Sắp xếp theo giá, mới nhất, liên quan
* Phân trang (15 sản phẩm/trang)
* Xem chi tiết và thêm vào giỏ hàng

### 3. **Trang Giới thiệu**

* Lịch sử hình thành
* Sứ mệnh, tầm nhìn
* Các chính sách (đổi trả, giao hàng, bảo mật, bảo hành)

### 4. **Trang Liên hệ**

* Email, hotline, mạng xã hội
* Form gửi tin nhắn

### 5. **Giỏ hàng & Thanh toán**

* Thêm/xóa/sửa số lượng sản phẩm
* Tính tổng tiền
* Xác nhận đơn hàng
* Lưu thông tin vào database

### 6. **Trang Tài khoản**

* Thông tin khách hàng
* Lịch sử đơn hàng
* Chi tiết đơn hàng

---

# 🗄️ **Cơ sở dữ liệu (MySQL)**

Hệ thống gồm **8 bảng chính**:

* `brands` – Thương hiệu
* `categories` – Danh mục sản phẩm
* `products` – Sản phẩm
* `product_images` – Hình ảnh sản phẩm
* `customers` – Khách hàng
* `orders` – Đơn hàng
* `order_items` – Chi tiết đơn hàng
* `cart` – Giỏ hàng

> ERD đã được thiết kế đảm bảo ràng buộc khóa ngoại, dễ mở rộng và quản lý.

---

# 📌 **Các chức năng chính**

### ✔️ Quản lý sản phẩm

* Xem danh sách
* Tìm kiếm & lọc
* Xem chi tiết
* Sản phẩm nổi bật / mới

### ✔️ Giỏ hàng

* Thêm/Xóa/Cập nhật số lượng
* Tính tổng tiền theo thời gian thực

### ✔️ Thanh toán

* Nhập thông tin khách hàng
* Tạo đơn hàng
* Lưu xuống database

### ✔️ Quản lý người dùng

* Xem lịch sử đơn hàng
* Trạng thái đơn hàng
* Xác nhận đơn hàng thành công

---

# 🎨 **Giao diện (Figma)**

Gồm các trang:

* Trang chủ
* Sản phẩm
* Giới thiệu
* Liên hệ
* Giỏ hàng
* Thanh toán
* Chi tiết đơn hàng
* Tài khoản

> Giao diện mang phong cách nhẹ nhàng, hiện đại, phù hợp chủ đề mỹ phẩm.

---

# **Kết luận**

Dự án website TooBeauty đã hoàn thiện đầy đủ chức năng cơ bản của một trang thương mại điện tử: hiển thị sản phẩm, giỏ hàng, thanh toán, quản lý đơn hàng và giao diện đẹp mắt.
Dự án giúp rèn luyện kỹ năng lập trình web, thiết kế UI/UX, làm việc với cơ sở dữ liệu và xử lý logic nghiệp vụ.

---

# **Liên hệ**

* 📧 Email: [toobeauty@gmail.com](mailto:toobeauty@gmail.com)
* 🌐 Website: TooBeauty.vn (demo)
* 📱 Facebook / Instagram: @TooBeauty

---

### 🗄️ Cấu Hình Cơ Sở Dữ Liệu

Database gồm **8 bảng chính**:

* `categories`
* `brands`
* `products`
* `product_images`
* `customers`
* `orders`
* `order_items`
* `cart`

Dữ liệu mẫu:

* 100+ sản phẩm (50 dầu tẩy trang, 30 nước tẩy rửa, 20 bông tẩy trang).

---

## 👤 Vai Trò Người Dùng và Quyền Truy Cập

### 🛍️ Khách Hàng

* Xem danh sách toàn bộ sản phẩm
* Lọc theo danh mục, loại da, khoảng giá
* Sắp xếp sản phẩm (mới nhất / giá / liên quan)
* Xem chi tiết sản phẩm
* Thêm sản phẩm vào giỏ hàng
* Đặt hàng và thanh toán
* Xem thông tin giới thiệu, chính sách, liên hệ

---

## 🧩 Use Cases (Trường Hợp Sử Dụng)

### 🔐 Use Cases Xác Thực

**Đăng nhập khách hàng**

* Nhập email và mật khẩu
* Hệ thống xác thực và tạo session
* Chuyển hướng đến trang chủ

**Đăng ký khách hàng**

* Nhập thông tin cá nhân
* Lưu vào bảng `customers`
* Đăng nhập và tiếp tục mua sắm

**Đăng xuất**

* Hệ thống hủy session

---

### 🛒 Use Cases Sản Phẩm & Giỏ Hàng

#### **Xem danh sách sản phẩm**

* Người dùng truy cập trang “Sản phẩm”
* Hệ thống hiển thị danh sách 15 sp/trang
* Lọc & sắp xếp theo điều kiện

#### **Xem chi tiết sản phẩm**

* Người dùng chọn 1 sản phẩm
* Website hiển thị hình ảnh, mô tả, giá, thông tin liên quan

#### **Thêm vào giỏ hàng**

* Chọn số lượng và thêm vào giỏ
* Hệ thống lưu vào bảng `cart`

#### **Xem giỏ hàng**

* Hiển thị sản phẩm đang có
* Tính tổng tiền
* Cho phép sửa/xóa số lượng

#### **Đặt hàng – Thanh toán**

* Nhập thông tin người mua
* Lưu vào bảng `orders` & `order_items`
* Xác nhận đơn hàng

---

### 📞 Use Cases Giới thiệu – Chính sách – Liên hệ

#### **Xem trang Giới thiệu**

* Hiển thị:

  * Lịch sử hình thành
  * Sứ mệnh – Tầm nhìn
  * Cam kết chất lượng

#### **Xem trang Chính sách**

* Chính sách bảo hành
* Chính sách đổi trả
* Chính sách bảo mật
* Chính sách thanh toán

#### **Liên hệ**

* Form gửi yêu cầu
* Thông tin email, số điện thoại, địa chỉ

---

## 🌟 Tính Năng Chính

### 🎨 Frontend

* Giao diện thiết kế bằng **Figma**, triển khai bằng HTML/CSS/JS
* Responsive trên PC – Tablet – Mobile
* Màu sắc nhẹ nhàng, phù hợp ngành làm đẹp

### 📦 Danh mục & Sản phẩm

* Lọc theo danh mục (Dầu tẩy trang, Nước tẩy rửa, …)
* Bộ lọc giá, loại da
* Sắp xếp theo nhiều tiêu chí
* Phân trang 15 sản phẩm/trang

### 🛒 Giỏ hàng & Thanh toán

* Thêm sản phẩm
* Cập nhật số lượng
* Tính tổng tiền tự động
* Thanh toán (COD, Chuyển khoản, E-wallet)
* Lưu đơn hàng vào database

### 📑 Trang nội dung

* Trang Giới Thiệu
* Chính sách bảo hành
* Chính sách đổi trả
* Chính sách vận chuyển
* Chính sách bảo mật

### 🔧 Backend (PHP – MySQL)

* Kết nối CSDL MySQL
* Các chức năng CRUD sản phẩm (nếu cấp độ admin)
* Xử lý giỏ hàng, đơn hàng
* API nhỏ bằng PHP (get_cart.php, cart_functions.php)

---

