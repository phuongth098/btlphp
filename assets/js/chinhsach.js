document.addEventListener('DOMContentLoaded', function () {
    // 1. Xử lý chuyển đổi giữa các phần nội dung chính sách
    const menuItems = document.querySelectorAll('.sidebar-menu li');
    const sections = document.querySelectorAll('.policy-section');

    menuItems.forEach(item => {
        item.addEventListener('click', function () {
            // Xóa class active khỏi tất cả các menu items
            menuItems.forEach(item => item.classList.remove('active'));

            // Thêm class active cho item được click
            this.classList.add('active');

            // Lấy target section từ data-target
            const target = this.getAttribute('data-target');

            // Ẩn tất cả các sections
            sections.forEach(section => {
                section.classList.remove('active');

                // Thêm hiệu ứng fade out
                section.style.opacity = '0';
            });

            // Hiển thị section tương ứng
            const activeSection = document.getElementById(target);
            if (activeSection) {
                setTimeout(() => {
                    activeSection.classList.add('active');
                    setTimeout(() => {
                        activeSection.style.opacity = '1';
                    }, 10);
                }, 300); // Đợi animation fade out hoàn thành
            }
        });
    });
});