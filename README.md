# 📌 **README — TooBeauty | Website Quảng Bá & Bán Mỹ Phẩm**

## Giới thiệu dự án

**TooBeauty** là website thương mại điện tử chuyên về mỹ phẩm, được xây dựng nhằm hỗ trợ khách hàng tìm kiếm, xem thông tin và mua sắm các sản phẩm làm đẹp một cách thuận tiện.
Dự án được phát triển cho học phần **Thiết kế và Triển khai Ứng Dụng Web**.

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

## 👥 **Nhóm phát triển**

* **Sinh viên thực hiện:** Nguyễn Hoàng Thanh Duy, Nguyễn Phương Thảo
* **Lớp:** K59SN1
* **Giảng viên hướng dẫn:** 

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

