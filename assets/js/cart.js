function updateCartCount(count) {
  // Nếu không có tham số count, lấy dữ liệu từ server
  if (count === undefined) {
    fetch("includes/get_cart.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const cartCountElement = document.getElementById("cart-count");
          if (cartCountElement) {
            cartCountElement.textContent = data.cart_count;
            cartCountElement.style.display =
              data.cart_count > 0 ? "flex" : "none";
          }
        }
      })
      .catch((error) => {
        console.error("Error fetching cart data:", error);
      });
  } else {
    // Cập nhật số lượng trực tiếp
    const cartCountElement = document.getElementById("cart-count");
    if (cartCountElement) {
      cartCountElement.textContent = count;
      cartCountElement.style.display = count > 0 ? "flex" : "none";
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // Xử lý nút Thêm vào giỏ ở trang sản phẩm và trang chủ
  document
    .querySelectorAll(".product-btn, .btn-them-vao-gio")
    .forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        const productId = this.getAttribute("data-product-id");
        if (!productId) {
          console.error("Không tìm thấy product_id");
          return;
        }

        console.log(`Adding product ${productId} to cart`);

        // Gửi request AJAX để thêm sản phẩm vào giỏ hàng
        const formData = new FormData();
        formData.append("action", "add");
        formData.append("product_id", productId);
        formData.append("quantity", 1);

        // Disable button and show loading state
        this.disabled = true;
        const originalText = this.textContent;
        this.textContent = "Đang xử lý...";

        fetch("includes/cart_actions.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            console.log("Server response:", data);
            if (data.success) {
              // Cập nhật số lượng sản phẩm trong giỏ hàng
              updateCartCount(data.cart_count);

              // Thu thập thông tin sản phẩm
              const productElement =
                button.closest(".product-item") ||
                button.closest(".product-card");
              let productInfo = {};

              if (productElement) {
                const nameElement =
                  productElement.querySelector(".product-name") ||
                  productElement.querySelector(".ten");
                const priceElement =
                  productElement.querySelector(".product-price") ||
                  productElement.querySelector(".gia");
                const imageElement = productElement.querySelector("img");

                productInfo = {
                  name: nameElement
                    ? nameElement.textContent.trim()
                    : "Sản phẩm",
                  price: priceElement ? priceElement.textContent.trim() : "",
                  image: imageElement ? imageElement.src : "",
                  quantity: 1,
                };
              }

              // Hiển thị thông báo thêm vào giỏ thành công
              if (typeof notifications !== "undefined") {
                notifications.addToCart(productInfo);
              } else {
                alert("Đã thêm sản phẩm vào giỏ hàng!");
              }
            } else {
              console.error("Error adding to cart:", data.message);
              if (typeof notifications !== "undefined") {
                notifications.show(
                  "error",
                  "Lỗi",
                  data.message || "Không thể thêm sản phẩm vào giỏ hàng"
                );
              } else {
                alert(
                  data.message ||
                    "Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng."
                );
              }
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.");
          })
          .finally(() => {
            // Re-enable button and restore text
            this.disabled = false;
            this.textContent = originalText;
          });
      });
    });
});
