document.addEventListener("DOMContentLoaded", function () {
  // Xử lý nút Trước và Sau cho tất cả slider
  document.querySelectorAll(".slider-wrapper").forEach((wrapper) => {
    const slider = wrapper.querySelector(".product-slider");
    const prevBtn = wrapper.querySelector(".slider-prev");
    const nextBtn = wrapper.querySelector(".slider-next");

    prevBtn.addEventListener("click", () => {
      slider.scrollBy({ left: -250, behavior: "smooth" });
    });

    nextBtn.addEventListener("click", () => {
      slider.scrollBy({ left: 250, behavior: "smooth" });
    });
  });
});

document.addEventListener('DOMContentLoaded', function() {
  const dropdownTrigger = document.querySelector('.dropdown-trigger');
  const dropdownContent = document.querySelector('.dropdown-content');
  
  // Toggle dropdown khi click
  if (dropdownTrigger && dropdownContent) {
      dropdownTrigger.addEventListener('click', function(e) {
          e.preventDefault();
          dropdownContent.classList.toggle('show');
      });
      
      // Đóng dropdown khi click ra ngoài
      document.addEventListener('click', function(e) {
          if (!e.target.matches('.dropdown-trigger') && !e.target.matches('.fa-user') && !e.target.matches('.fa-chevron-down')) {
              if (dropdownContent.classList.contains('show')) {
                  dropdownContent.classList.remove('show');
              }
          }
      });
  }
});