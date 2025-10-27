document.addEventListener("DOMContentLoaded", function () {
    const itemsPerPage = 15;
    const products = document.querySelectorAll(".product-card");
    const paginationContainer = document.querySelector(".pagination-items");
    const totalPages = Math.ceil(products.length / itemsPerPage);
    let currentPage = 1;

    const prevArrow = paginationContainer.querySelector(".arrow:first-child");
    const nextArrow = paginationContainer.querySelector(".arrow:last-child");

    function showPage(page) {
        currentPage = page;

        products.forEach((product, index) => {
            const pageIndex = Math.floor(index / itemsPerPage) + 1;
            product.style.display = (pageIndex === page) ? "block" : "none";
        });

        updatePagination();
    }

    function updatePagination() {
        // Xóa các nút trang cũ (chừa lại mũi tên)
        paginationContainer.querySelectorAll("a.page-link, span.dots").forEach(el => el.remove());

        const maxVisiblePages = 3;
        let start = Math.max(1, currentPage - 1);
        let end = Math.min(totalPages, start + maxVisiblePages - 1);

        if (end - start < maxVisiblePages - 1) {
            start = Math.max(1, end - maxVisiblePages + 1);
        }

        // Nút đầu tiên
        if (start > 1) {
            insertPageButton(1);
            if (start > 2) insertDots();
        }

        // Các nút giữa
        for (let i = start; i <= end; i++) {
            insertPageButton(i);
        }

        // Nút cuối
        if (end < totalPages) {
            if (end < totalPages - 1) insertDots();
            insertPageButton(totalPages);
        }

        // Cập nhật trạng thái mũi tên
        prevArrow.style.pointerEvents = currentPage === 1 ? "none" : "auto";
        nextArrow.style.pointerEvents = currentPage === totalPages ? "none" : "auto";
        prevArrow.style.opacity = currentPage === 1 ? "0.5" : "1";
        nextArrow.style.opacity = currentPage === totalPages ? "0.5" : "1";
    }

    function insertPageButton(i) {
        const link = document.createElement("a");
        link.href = "#";
        link.className = "page-link";
        link.textContent = i;
        link.dataset.page = i;
        if (i === currentPage) link.classList.add("active");

        link.addEventListener("click", (e) => {
            e.preventDefault();
            showPage(i);
        });

        paginationContainer.insertBefore(link, nextArrow);
    }

    function insertDots() {
        const span = document.createElement("span");
        span.textContent = "...";
        span.className = "dots";
        paginationContainer.insertBefore(span, nextArrow);
    }

    prevArrow.addEventListener("click", function (e) {
        e.preventDefault();
        if (currentPage > 1) showPage(currentPage - 1);
    });

    nextArrow.addEventListener("click", function (e) {
        e.preventDefault();
        if (currentPage < totalPages) showPage(currentPage + 1);
    });

    // Khởi chạy trang đầu
    showPage(1);
});
