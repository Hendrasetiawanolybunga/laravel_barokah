/**
 * Barokah Store - Pelanggan JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Product quantity input
    const quantityInputs = document.querySelectorAll('.quantity-input');
    if (quantityInputs) {
        quantityInputs.forEach(input => {
            const minusBtn = input.parentElement.querySelector('.quantity-minus');
            const plusBtn = input.parentElement.querySelector('.quantity-plus');
            
            if (minusBtn) {
                minusBtn.addEventListener('click', function() {
                    let value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                        triggerChangeEvent(input);
                    }
                });
            }
            
            if (plusBtn) {
                plusBtn.addEventListener('click', function() {
                    let value = parseInt(input.value);
                    let max = input.getAttribute('max') ? parseInt(input.getAttribute('max')) : 100;
                    if (value < max) {
                        input.value = value + 1;
                        triggerChangeEvent(input);
                    }
                });
            }
        });
    }

    // Trigger change event for quantity inputs
    function triggerChangeEvent(input) {
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }

    // Cart quantity change
    const cartQuantityInputs = document.querySelectorAll('.cart-quantity-input');
    if (cartQuantityInputs) {
        cartQuantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    }

    // Product search
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });
    }

    // Product filter
    const filterForm = document.getElementById('productFilterForm');
    const filterInputs = document.querySelectorAll('.product-filter');
    if (filterForm && filterInputs) {
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }

    // Confirm delete
    const confirmDeleteBtns = document.querySelectorAll('.confirm-delete');
    if (confirmDeleteBtns) {
        confirmDeleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                    e.preventDefault();
                }
            });
        });
    }

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Update cart total
    function updateCartTotal() {
        const cartItems = document.querySelectorAll('.cart-item');
        let total = 0;
        
        cartItems.forEach(item => {
            const price = parseFloat(item.querySelector('.cart-item-price').getAttribute('data-price'));
            const quantity = parseInt(item.querySelector('.cart-quantity-input').value);
            total += price * quantity;
        });
        
        const totalElement = document.getElementById('cartTotal');
        if (totalElement) {
            totalElement.textContent = formatCurrency(total);
        }
    }

    // Initialize cart total calculation
    const cartContainer = document.querySelector('.cart-container');
    if (cartContainer) {
        updateCartTotal();
        
        // Listen for changes in cart quantities
        const quantityInputs = cartContainer.querySelectorAll('.cart-quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', updateCartTotal);
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    if (alerts) {
        alerts.forEach(alert => {
            setTimeout(() => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                }
            }, 5000);
        });
    }
});