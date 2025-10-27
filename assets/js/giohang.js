document.addEventListener("DOMContentLoaded", function () {
  // Xử lý sự kiện nút tăng số lượng
  document
    .querySelectorAll(".quantity-btn.increase")
    .forEach(function (button) {
      button.addEventListener("click", function () {
        const input = this.parentElement.querySelector(".quantity-input");
        const cartId = input.getAttribute("data-cart-id");
        let value = parseInt(input.value, 10);
        value++;
        input.value = value; // Cập nhật giá trị input trước
        updateCartItem(cartId, value);
      });
    });

  // Xử lý sự kiện nút giảm số lượng
  document
    .querySelectorAll(".quantity-btn.decrease")
    .forEach(function (button) {
      button.addEventListener("click", function () {
        const input = this.parentElement.querySelector(".quantity-input");
        const cartId = input.getAttribute("data-cart-id");
        let value = parseInt(input.value, 10);
        if (value > 1) {
          value--;
          input.value = value; // Cập nhật giá trị input trước
          updateCartItem(cartId, value);
        }
      });
    });

  // Xử lý sự kiện thay đổi số lượng trực tiếp
  document.querySelectorAll(".quantity-input").forEach(function (input) {
    input.addEventListener("change", function () {
      const cartId = this.getAttribute("data-cart-id");
      let value = parseInt(this.value, 10);
      if (isNaN(value) || value < 1) {
        value = 1;
        this.value = 1;
      }
      updateCartItem(cartId, value);
    });
  });

  // Xử lý sự kiện nút xóa
  document.querySelectorAll(".remove-btn").forEach(function (button) {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const cartId = this.getAttribute("data-cart-id");
      removeCartItem(cartId);
    });
  });
});

// Hàm mới để cập nhật giá trị tổng - sẽ thay thế hàm hiện tại
function updateTotalUI(total) {
  try {
    const subtotalEl = document.querySelector(".subtotal-amount");
    const totalEl = document.querySelector(".total-amount");

    if (subtotalEl) {
      subtotalEl.textContent = total + " VNĐ";
    } else {
      console.warn("Không tìm thấy phần tử subtotal-amount");
    }

    if (totalEl) {
      totalEl.textContent = total + " VNĐ";
    } else {
      console.warn("Không tìm thấy phần tử total-amount");
    }
  } catch (error) {
    console.error("Error updating totals:", error);
  }
}

// Hàm cập nhật số lượng sản phẩm - sửa phần xử lý phản hồi
function updateCartItem(cartId, quantity) {
  const formData = new FormData();
  formData.append("action", "update");
  formData.append("cart_id", cartId);
  formData.append("quantity", quantity);

  // Hiển thị loading hoặc disable input khi đang cập nhật
  const input = document.querySelector(
    `.quantity-input[data-cart-id="${cartId}"]`
  );
  const oldValue = input ? input.value : 1;

  if (input) {
    input.disabled = true;
  }

  fetch("includes/cart_actions.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin", // Đảm bảo gửi cookie session
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      console.log("Response data:", data); // Debug log
      if (data.success) {
        // Cách xử lý an toàn hơn - Làm mới trang sau khi cập nhật thành công
        window.location.reload();
      } else {
        console.error("Server error:", data);
        alert(data.message || "Đã xảy ra lỗi khi cập nhật giỏ hàng");
        // Khôi phục giá trị cũ nếu cập nhật thất bại
        if (input) input.value = oldValue;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Đã xảy ra lỗi khi cập nhật giỏ hàng: " + error.message);
      if (input) input.value = oldValue;
    })
    .finally(() => {
      if (input) input.disabled = false;
    });
}

// Hàm xóa sản phẩm khỏi giỏ hàng
function removeCartItem(cartId) {
  // Thêm confirm
  if (!confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?")) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "remove");
  formData.append("cart_id", cartId);

  // Hiển thị loading hoặc disable nút khi đang xóa
  const removeBtn = document.querySelector(
    `.remove-btn[data-cart-id="${cartId}"]`
  );
  if (removeBtn) removeBtn.disabled = true;

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
      if (data.success) {
        // Xóa dòng sản phẩm khỏi bảng
        const row = document.querySelector(
          `.product-row[data-cart-id="${cartId}"]`
        );
        if (row) {
          row.remove();
        }

        // Cập nhật tổng giỏ hàng
        const subtotalEl = document.querySelector(".subtotal-amount");
        const totalEl = document.querySelector(".total-amount");
        const cartCountEl = document.getElementById("cart-count");

        if (subtotalEl) subtotalEl.textContent = data.cart_total + " VNĐ";
        if (totalEl) totalEl.textContent = data.cart_total + " VNĐ";
        if (cartCountEl) cartCountEl.textContent = data.cart_count;

        // Nếu giỏ hàng trống, hiển thị thông báo
        if (data.cart_count === 0 || data.cart_count === "0") {
          const tbody = document.getElementById("cart-items-container");
          if (tbody) {
            tbody.innerHTML =
              '<tr><td colspan="5" style="text-align: center; padding: 20px;">Giỏ hàng trống. <a href="danhmucsp.php">Tiếp tục mua sắm</a></td></tr>';

            // Vô hiệu hóa nút thanh toán
            const checkoutBtn = document.querySelector(".checkout-btn");
            if (checkoutBtn) {
              checkoutBtn.disabled = true;
              checkoutBtn.style.backgroundColor = "#ccc";
            }
          }
        }
      } else {
        alert(data.message || "Không thể xóa sản phẩm");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Đã xảy ra lỗi khi xóa sản phẩm khỏi giỏ hàng: " + error.message);
    })
    .finally(() => {
      if (removeBtn) removeBtn.disabled = false;
    });
}

// Hàm cập nhật giao diện giỏ hàng
function updateCartUI(data) {
  try {
    // Cập nhật số lượng cho tất cả các sản phẩm
    const cartCountEl = document.getElementById("cart-count");
    if (cartCountEl) {
      cartCountEl.textContent = data.cart_count;
    }

    // Cập nhật tổng tiền
    const subtotalEl = document.querySelector(".subtotal-amount");
    const totalEl = document.querySelector(".total-amount");

    if (subtotalEl) subtotalEl.textContent = data.cart_total + " VNĐ";
    if (totalEl) totalEl.textContent = data.cart_total + " VNĐ";

    // Cập nhật thành tiền cho từng sản phẩm
    if (data.cart_items && Array.isArray(data.cart_items)) {
      data.cart_items.forEach((item) => {
        const row = document.querySelector(
          `.product-row[data-cart-id="${item.id}"]`
        );
        if (row) {
          const subtotalElement = row.querySelector(".subtotal-col");
          if (subtotalElement) {
            subtotalElement.textContent =
              new Intl.NumberFormat("vi-VN").format(item.subtotal) + " VNĐ";
          }

          const quantityInput = row.querySelector(".quantity-input");
          if (quantityInput) {
            quantityInput.value = item.quantity;
            quantityInput.defaultValue = item.quantity;
          }
        }
      });
    }

    // Kiểm tra nếu giỏ hàng trống
    if (data.cart_count === 0 || data.cart_count === "0") {
      const tbody = document.getElementById("cart-items-container");
      if (tbody) {
        tbody.innerHTML =
          '<tr><td colspan="5" style="text-align: center; padding: 20px;">Giỏ hàng trống. <a href="danhmucsp.php">Tiếp tục mua sắm</a></td></tr>';

        // Vô hiệu hóa nút thanh toán
        const checkoutBtn = document.querySelector(".checkout-btn");
        if (checkoutBtn) {
          checkoutBtn.disabled = true;
          checkoutBtn.style.backgroundColor = "#ccc";
        }
      }
    }
  } catch (error) {
    console.error("Error updating cart UI:", error);
  }
}
