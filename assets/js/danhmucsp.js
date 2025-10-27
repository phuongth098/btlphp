document.addEventListener("DOMContentLoaded", function () {
  // Khai báo biến toàn cục
  const itemsPerPage = 15;
  const products = Array.from(document.querySelectorAll(".product-card"));
  const paginationContainer = document.querySelector(".pagination-items");
  let currentPage = 1;
  let totalPages = 0;
  let totalFilteredProducts = products.length; // Khởi tạo với tổng số sản phẩm ban đầu
  let filteredProducts = products; // Lưu danh sách sản phẩm đã lọc

  // Lưu trạng thái bộ lọc
  let currentCategory = "";
  let selectedSkins = [];
  let selectedPriceRange = null;
  let minPrice = null;
  let maxPrice = null;

  const prevArrow = paginationContainer.querySelector(".arrow:first-child");
  const nextArrow = paginationContainer.querySelector(".arrow:last-child");
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');
  const submitButton = document.querySelector(".apply-button");
  const radio = document.querySelectorAll('input[type="radio"]');
  const minPriceInput = document.querySelector(
    '.price-inputs .price-input input[placeholder="Tối Thiểu"]'
  );
  const maxPriceInput = document.querySelector(
    '.price-inputs .price-input input[placeholder="Tối Đa"]'
  );
  const sortDropdown = document.querySelector("#sort.sort-dropdown");
  const productGrid = document.querySelector(".product-grid");
  const categoryLinks = document.querySelectorAll(".sidebar ul li a");

  console.log("Sort Dropdown:", sortDropdown);
  console.log("Category Links:", categoryLinks);
  console.log("Products:", products);

  // Hàm cập nhật tổng sản phẩm
  function updateProductCount() {
    const productCountElement = document.querySelector("#product-count");
    if (productCountElement) {
      productCountElement.textContent = `${totalFilteredProducts} SẢN PHẨM`;
    } else {
      console.error("Product count element not found");
    }
  }

  // Hàm áp dụng tất cả bộ lọc (danh mục, loại da, giá)
  function applyAllFilters() {
    // Lấy giá trị bộ lọc
    selectedSkins = Array.from(
      document.querySelectorAll('input[type="checkbox"]:checked')
    ).map((cb) => cb.id);
    selectedPriceRange =
      document.querySelector('input[type="radio"]:checked')?.id || null;
    minPrice = minPriceInput.value ? parseInt(minPriceInput.value) : null;
    maxPrice = maxPriceInput.value ? parseInt(maxPriceInput.value) : null;

    // Áp dụng bộ lọc
    filteredProducts = products.filter((product) => {
      let skin = product.getAttribute("data-skin");
      let price = parseInt(product.getAttribute("data-price"));
      let category = product.getAttribute("data-category");

      // Bộ lọc loại da
      let showBySkin =
        selectedSkins.includes("all") ||
        selectedSkins.length === 0 ||
        selectedSkins.includes(skin);

      // Bộ lọc giá
      let showByPrice = true;
      if (selectedPriceRange === "under25") {
        showByPrice = price <= 200000;
      } else if (selectedPriceRange === "25to50") {
        showByPrice = price > 200000 && price <= 500000;
      } else if (selectedPriceRange === "50to100") {
        showByPrice = price > 500000 && price <= 800000;
      }
      if (minPrice !== null && price < minPrice) showByPrice = false;
      if (maxPrice !== null && price > maxPrice) showByPrice = false;

      // Bộ lọc danh mục
      let showByCategory =
        currentCategory === "" || category === currentCategory;

      return showBySkin && showByPrice && showByCategory;
    });

    // Cập nhật tổng số sản phẩm và số trang
    totalFilteredProducts = filteredProducts.length;
    totalPages = Math.ceil(totalFilteredProducts / itemsPerPage);

    // Sắp xếp lại sản phẩm nếu cần
    if (sortDropdown) {
      sortProducts(sortDropdown.value);
    } else {
      filteredProducts.forEach((product) => {
        productGrid.appendChild(product);
      });
    }

    // Cập nhật giao diện
    updateProductCount();
    showPage(1);
  }

  // Hàm hiển thị trang
  function showPage(page) {
    currentPage = page;

    // Ẩn tất cả sản phẩm trước
    products.forEach((product) => {
      product.style.display = "none";
    });

    // Tính chỉ số bắt đầu và kết thúc của sản phẩm trên trang hiện tại
    let startIndex = (currentPage - 1) * itemsPerPage;
    let endIndex = startIndex + itemsPerPage;

    // Hiển thị các sản phẩm trong khoảng startIndex đến endIndex
    filteredProducts.slice(startIndex, endIndex).forEach((product) => {
      product.style.display = "block";
    });

    // Cập nhật phân trang
    updatePagination();
  }

  // Hàm cập nhật phân trang
  function updatePagination() {
    paginationContainer
      .querySelectorAll("a.page-link, span.dots")
      .forEach((el) => el.remove());

    const maxVisiblePages = 3;
    let start = Math.max(1, currentPage - 1);
    let end = Math.min(totalPages, start + maxVisiblePages - 1);

    if (end - start < maxVisiblePages - 1) {
      start = Math.max(1, end - maxVisiblePages + 1);
    }

    if (start > 1) {
      insertPageButton(1);
      if (start > 2) insertDots();
    }

    for (let i = start; i <= end; i++) {
      insertPageButton(i);
    }

    if (end < totalPages) {
      if (end < totalPages - 1) insertDots();
      insertPageButton(totalPages);
    }

    prevArrow.style.pointerEvents = currentPage === 1 ? "none" : "auto";
    nextArrow.style.pointerEvents =
      currentPage === totalPages ? "none" : "auto";
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

  // Sự kiện áp dụng bộ lọc
  if (submitButton) {
    submitButton.addEventListener("click", applyAllFilters);
  } else {
    console.error("Submit button not found");
  }

  // Hàm sắp xếp sản phẩm
  function sortProducts(sortType) {
    if (!filteredProducts.length) {
      console.log("No products to sort");
      return;
    }

    try {
      switch (sortType) {
        case "relevance":
          filteredProducts.sort((a, b) => {
            return (
              parseInt(a.getAttribute("data-id")) -
              parseInt(b.getAttribute("data-id"))
            );
          });
          break;
        case "newest":
          filteredProducts.sort((a, b) => {
            return (
              parseInt(b.getAttribute("data-id")) -
              parseInt(a.getAttribute("data-id"))
            );
          });
          break;
        case "price_asc":
          filteredProducts.sort((a, b) => {
            const priceA = parseInt(a.getAttribute("data-price"));
            const priceB = parseInt(b.getAttribute("data-price"));
            return priceA - priceB;
          });
          break;
        case "price_desc":
          filteredProducts.sort((a, b) => {
            const priceA = parseInt(a.getAttribute("data-price"));
            const priceB = parseInt(b.getAttribute("data-price"));
            return priceB - priceA;
          });
          break;
        default:
          console.log("Unknown sort type:", sortType);
      }

      filteredProducts.forEach((product) => {
        productGrid.appendChild(product);
      });
    } catch (error) {
      console.error("Error in sortProducts:", error);
    }
  }

  // Sự kiện sắp xếp
  if (sortDropdown) {
    sortDropdown.addEventListener("change", function () {
      console.log("Sort dropdown changed to:", this.value);
      sortProducts(this.value);
      showPage(1);
    });
  } else {
    console.error("Sort dropdown not found");
  }

  // Sự kiện lọc danh mục
  if (categoryLinks.length > 0) {
    categoryLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        currentCategory = this.getAttribute("href").substring(1);
        console.log("Category clicked:", currentCategory);
        applyAllFilters();
      });
    });
  } else {
    console.error("Category links not found");
  }

  // Khởi tạo: Áp dụng bộ lọc mặc định
  totalPages = Math.ceil(totalFilteredProducts / itemsPerPage);
  updateProductCount();
  applyAllFilters();
});
