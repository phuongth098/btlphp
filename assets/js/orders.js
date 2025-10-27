// JavaScript cho trang đơn hàng

document.addEventListener('DOMContentLoaded', function() {
    // Theo dõi việc mở/đóng accordion
    const accordionButtons = document.querySelectorAll('.accordion-button');
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Thêm hiệu ứng animation khi mở accordion nếu muốn
        });
    });
    
    // Xác nhận lại khi hủy đơn hàng
    const cancelForms = document.querySelectorAll('form[name="cancel_order"]');
    cancelForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
                event.preventDefault();
            }
        });
    });
    
    // Auto-hide thông báo sau 3 giây
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                // Fade out
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                
                // Xóa khỏi DOM sau khi fade out
                setTimeout(() => {
                    alert.remove();
                }, 500);
            });
        }, 3000);
    }
    
    // Scroll to specific order if URL contains order_id parameter
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    if (orderId) {
        // Kiểm tra xem đơn hàng có phải là đơn hủy không
        const cancelledOrderElement = document.getElementById('collapseCancelled' + orderId);
        const activeOrderElement = document.getElementById('collapseActive' + orderId);
        
        if (cancelledOrderElement) {
            // Mở accordion của đơn hàng đó
            const orderButton = document.querySelector(`button[data-bs-target="#collapseCancelled${orderId}"]`);
            if (orderButton && orderButton.classList.contains('collapsed')) {
                orderButton.click();
            }
            
            // Scroll đến đơn hàng đó
            setTimeout(() => {
                cancelledOrderElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        } 
        else if (activeOrderElement) {
            // Mở accordion của đơn hàng đó
            const orderButton = document.querySelector(`button[data-bs-target="#collapseActive${orderId}"]`);
            if (orderButton && orderButton.classList.contains('collapsed')) {
                orderButton.click();
            }
            
            // Scroll đến đơn hàng đó
            setTimeout(() => {
                activeOrderElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    }
});