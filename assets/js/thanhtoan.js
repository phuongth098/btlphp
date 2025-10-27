document.addEventListener("DOMContentLoaded", function () {
  // Kiểm tra giỏ hàng có trống không khi trang tải xong
  // fetch('includes/get_cart.php')
  //   .then(response => response.json())
  //   .then(data => {
  //     if (!data.success || data.cart_count == 0) {
  //       alert("Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.");
  //       window.location.href = 'giohang.php';
  //       return;
  //     }
  //   })
  //   .catch(error => {
  //     console.error("Error checking cart:", error);
  //   });

  // Thêm kiểm tra real-time cho các trường input
  const requiredFields = [
    { id: 'first-name', name: 'Họ' },
    { id: 'last-name', name: 'Tên' },
    { id: 'email', name: 'Email' },
    { id: 'phone', name: 'Số điện thoại' },
    { id: 'street-address', name: 'Địa chỉ' },
    { id: 'town-city', name: 'Thành phố' },
    { id: 'province', name: 'Tỉnh/Thành phố' }
  ];
  
  // Thêm validation styles
  const styleElement = document.createElement('style');
  styleElement.textContent = `
    .form-group.error input {
      border-color: #ff3860 !important;
    }
    .error-message {
      color: #ff3860;
      font-size: 12px;
      height: 20px;
      margin-top: 2px;
    }
    .form-group input:focus {
      border-color: #4db6ac;
    }
    .form-group.success input {
      border-color: #23d160 !important;
    }
  `;
  document.head.appendChild(styleElement);
  
  // Thêm error message div dưới mỗi input
  requiredFields.forEach(field => {
    const input = document.getElementById(field.id);
    if (input) {
      const errorDiv = document.createElement('div');
      errorDiv.className = 'error-message';
      errorDiv.id = `${field.id}-error`;
      input.parentNode.insertBefore(errorDiv, input.nextSibling);
      
      // Thêm event listeners cho validation
      input.addEventListener('blur', function() {
        validateField(field.id, field.name);
      });
      
      input.addEventListener('input', function() {
        clearError(field.id);
      });
    }
  });

  // Thêm validation cho email
  const emailInput = document.getElementById('email');
  if (emailInput) {
    emailInput.addEventListener('blur', function() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (this.value.trim() !== '' && !emailRegex.test(this.value)) {
        setError('email', 'Email không đúng định dạng');
      } else if (this.value.trim() !== '') {
        setSuccess('email');
      }
    });
  }

  // Thêm validation cho số điện thoại
  const phoneInput = document.getElementById('phone');
  if (phoneInput) {
    phoneInput.addEventListener('blur', function() {
      const phoneRegex = /^[0-9]{10,11}$/;
      if (this.value.trim() !== '' && !phoneRegex.test(this.value)) {
        setError('phone', 'Số điện thoại phải có 10-11 chữ số');
      } else if (this.value.trim() !== '') {
        setSuccess('phone');
      }
    });
  }

  // Xử lý nút đặt hàng
  document
    .getElementById("place-order-btn")
    .addEventListener("click", function (e) {
      e.preventDefault();
      
      // Kiểm tra tất cả các trường trước khi đặt hàng
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!validateField(field.id, field.name)) {
          isValid = false;
        }
      });
      
      // Kiểm tra phương thức thanh toán
      const paymentMethod = document.querySelector('input[name="payment-method"]:checked');
      if (!paymentMethod) {
        alert("Vui lòng chọn phương thức thanh toán");
        isValid = false;
      }
      
      if (isValid) {
        placeOrder();
      } else {
        // Cuộn đến trường lỗi đầu tiên
        const firstErrorField = document.querySelector('.form-group.error');
        if (firstErrorField) {
          firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
    
  // Hàm validate field
  function validateField(fieldId, fieldName) {
    const field = document.getElementById(fieldId);
    const value = field.value.trim();
    
    if (value === '') {
      setError(fieldId, `Vui lòng nhập ${fieldName}`);
      return false;
    } else {
      setSuccess(fieldId);
      return true;
    }
  }
  
  // Hàm set error
  function setError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    field.parentElement.classList.add('error');
    field.parentElement.classList.remove('success');
    if (errorDiv) {
      errorDiv.textContent = message;
    }
  }
  
  // Hàm set success
  function setSuccess(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    field.parentElement.classList.remove('error');
    field.parentElement.classList.add('success');
    if (errorDiv) {
      errorDiv.textContent = '';
    }
  }
  
  // Hàm clear error
  function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    field.parentElement.classList.remove('error');
    if (errorDiv) {
      errorDiv.textContent = '';
    }
  }
});

// Hàm xử lý việc đặt hàng
function placeOrder() {
  // Hiển thị trạng thái đang xử lý
  const orderBtn = document.getElementById("place-order-btn");
  orderBtn.textContent = "Đang xử lý...";
  orderBtn.disabled = true;

  // Kiểm tra lại giỏ hàng trước khi đặt
  fetch('includes/get_cart.php')
    .then(response => response.json())
    .then(data => {
      // if (!data.success || data.cart_count == 0) {
      //   alert("Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.");
      //   window.location.href = 'giohang.php';
      //   return;
      // }
      // Tiếp tục với quá trình đặt hàng nếu giỏ hàng có sản phẩm
      processOrder();
    })
    .catch(error => {
      console.error("Error checking cart:", error);
      resetButton();
    });

  function processOrder() {
    // Lấy thông tin khách hàng từ form
    const firstName = document.getElementById("first-name").value.trim();
    const lastName = document.getElementById("last-name").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("street-address").value.trim();
    const city = document.getElementById("town-city").value.trim();
    const province = document.getElementById("province").value.trim();
    const additionalInfo = document.getElementById("additional-info").value.trim();
    
    // Lấy phương thức thanh toán
    const paymentMethod = document.querySelector(
      'input[name="payment-method"]:checked'
    );

    // Tạo đối tượng đơn hàng
    const order = {
      customer: {
        first_name: firstName,
        last_name: lastName,
        email: email,
        phone: phone,
        address: address,
        city: city,
        province: province,
      },
      payment_method: paymentMethod.value,
      additional_info: additionalInfo,
    };

    fetch("includes/process_order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(order),
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then(data => {
            throw new Error(data.message || "Lỗi khi xử lý đơn hàng");
          });
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Chuyển hướng đến trang xác nhận
          window.location.href = "order_confirmation.php?order_id=" + data.order_id;
        } else {
          throw new Error(data.message || "Có lỗi xảy ra khi xử lý đơn hàng =((");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert(error.message);
        resetButton();
      });
  }

  // Hàm để reset nút đặt hàng
  function resetButton() {
    orderBtn.textContent = "Đặt Hàng";
    orderBtn.disabled = false;
  }
}
