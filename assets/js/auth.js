document.addEventListener("DOMContentLoaded", function () {
  // Xử lý nút đóng modal
  const closeBtn = document.querySelector(".close-btn");
  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      window.location.href = "index.php";
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Xử lý hiển thị/ẩn mật khẩu

  // Xử lý nút đóng form
  const closeBtn = document.querySelector(".close-btn");
  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      window.location.href = "index.php";
    });
  }

  // Form đăng nhập
  const loginForm = document.querySelector("form.login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const email = this.querySelector('input[name="email"]').value;
      const password = this.querySelector('input[name="password"]').value;
      const errorContainer = document.querySelector(".login-error");

      // Xóa thông báo lỗi cũ
      if (errorContainer) {
        errorContainer.textContent = "";
        errorContainer.style.display = "none";
      }

      // Kiểm tra dữ liệu đầu vào
      if (!email || !password) {
        displayError("Vui lòng nhập đầy đủ email và mật khẩu", errorContainer);
        return;
      }

      // Gửi form
      this.submit();
    });
  }

  // Form đăng ký
  const registerForm = document.querySelector("form.register-form");
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const firstName = this.querySelector('input[name="first_name"]').value;
      const lastName = this.querySelector('input[name="last_name"]').value;
      const email = this.querySelector('input[name="email"]').value;
      const password = this.querySelector('input[name="password"]').value;
      const confirmPassword = this.querySelector(
        'input[name="confirm_password"]'
      ).value;
      const errorContainer = document.querySelector(".register-error");

      // Xóa thông báo lỗi cũ
      if (errorContainer) {
        errorContainer.textContent = "";
        errorContainer.style.display = "none";
      }

      // Kiểm tra dữ liệu đầu vào
      if (!firstName || !lastName || !email || !password || !confirmPassword) {
        displayError("Vui lòng điền đầy đủ thông tin", errorContainer);
        return;
      }

      // Kiểm tra định dạng email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        displayError("Email không hợp lệ", errorContainer);
        return;
      }

      // Kiểm tra mật khẩu
      if (password.length < 6) {
        displayError("Mật khẩu phải có ít nhất 6 ký tự", errorContainer);
        return;
      }

      // Kiểm tra xác nhận mật khẩu
      if (password !== confirmPassword) {
        displayError("Xác nhận mật khẩu không khớp", errorContainer);
        return;
      }

      // Gửi form
      this.submit();
    });
  }

  // Chức năng đăng nhập bằng Google (giả lập)
  const googleBtn = document.querySelector(".google-btn");
  if (googleBtn) {
    googleBtn.addEventListener("click", function () {
      // Trong trường hợp thực tế, bạn sẽ điều hướng đến API OAuth của Google
      alert("Tính năng đang được phát triển.");
      // window.location.href = 'google_auth.php'; // Trang xử lý OAuth
    });
  }

  // Hiển thị lỗi
  function displayError(message, container) {
    if (!container) {
      container = document.createElement("div");
      container.className = "alert alert-danger mt-3";
      const form = document.querySelector("form");
      if (form) {
        form.insertBefore(container, form.firstChild);
      }
    }

    container.textContent = message;
    container.style.display = "block";
    container.style.color = "#721c24";
    container.style.backgroundColor = "#f8d7da";
    container.style.border = "1px solid #f5c6cb";
    container.style.padding = "10px";
    container.style.borderRadius = "4px";
    container.style.marginBottom = "20px";
  }

  // Thêm các classes vào forms nếu chưa có
  const loginFormElement = document.querySelector(".auth-container form");
  const registerFormElement = document.querySelector(
    '.auth-container form[action="register.php"]'
  );

  if (loginFormElement && !loginFormElement.classList.contains("login-form")) {
    loginFormElement.classList.add("login-form");
  }

  if (
    registerFormElement &&
    !registerFormElement.classList.contains("register-form")
  ) {
    registerFormElement.classList.add("register-form");
  }

  // Thêm container hiển thị lỗi nếu chưa có
  if (loginFormElement && !document.querySelector(".login-error")) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "login-error";
    errorDiv.style.display = "none";
    loginFormElement.insertBefore(errorDiv, loginFormElement.firstChild);
  }

  if (registerFormElement && !document.querySelector(".register-error")) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "register-error";
    errorDiv.style.display = "none";
    registerFormElement.insertBefore(errorDiv, registerFormElement.firstChild);
  }
});

// Xử lý dropdown tài khoản trong header
document.addEventListener("DOMContentLoaded", function () {
  const dropdownTrigger = document.querySelector(".dropdown-trigger");
  const dropdownContent = document.querySelector(".dropdown-content");

  // Đảm bảo dropdown hoạt động trên mobile (touch)
  if (dropdownTrigger && dropdownContent) {
    dropdownTrigger.addEventListener("click", function (e) {
      e.preventDefault();

      // Toggle class để hiển thị/ẩn dropdown
      if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
        dropdownTrigger.querySelector(".fa-chevron-down").style.transform =
          "rotate(0)";
      } else {
        dropdownContent.style.display = "block";
        dropdownTrigger.querySelector(".fa-chevron-down").style.transform =
          "rotate(180deg)";
      }
    });

    // Đóng dropdown khi click ra ngoài
    document.addEventListener("click", function (e) {
      if (
        !dropdownTrigger.contains(e.target) &&
        !dropdownContent.contains(e.target)
      ) {
        dropdownContent.style.display = "";
        dropdownTrigger.querySelector(".fa-chevron-down").style.transform = "";
      }
    });
  }

  // Hiệu ứng khi hover vào các mục dropdown
  const dropdownItems = document.querySelectorAll(".dropdown-content a");
  dropdownItems.forEach((item) => {
    item.addEventListener("mouseenter", function () {
      const icon = this.querySelector("i");
      if (icon) {
        icon.classList.add("fa-bounce");
      }
    });

    item.addEventListener("mouseleave", function () {
      const icon = this.querySelector("i");
      if (icon) {
        icon.classList.remove("fa-bounce");
      }
    });
  });

  // Hiệu ứng active cho dropdown trigger
  if (dropdownTrigger) {
    dropdownTrigger.addEventListener("mouseenter", function () {
      this.style.backgroundColor = "#f8f9fa";
    });

    dropdownTrigger.addEventListener("mouseleave", function () {
      if (dropdownContent.style.display !== "block") {
        this.style.backgroundColor = "";
      }
    });
  }
});
