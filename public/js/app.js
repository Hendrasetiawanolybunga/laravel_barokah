/**
 * Laravel Barokah E-commerce - Custom JavaScript
 * Enhanced functionality for better user experience
 */

// Global variables
let cartUpdateTimeout;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize application
 */
function initializeApp() {
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize form validations
    initializeFormValidations();
    
    // Initialize cart functionality
    initializeCartFunctionality();
    
    // Initialize image loading
    initializeImageLoading();
    
    // Initialize smooth animations
    initializeSmoothAnimations();
    
    // Initialize search functionality
    initializeSearchFunctionality();
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize form validations
 */
function initializeFormValidations() {
    // Add custom validation styles
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Real-time validation for specific fields
    const emailFields = document.querySelectorAll('input[type="email"]');
    emailFields.forEach(function(field) {
        field.addEventListener('blur', function() {
            validateEmail(this);
        });
    });
    
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(function(field) {
        field.addEventListener('input', function() {
            validatePasswordStrength(this);
        });
    });
}

/**
 * Initialize cart functionality
 */
function initializeCartFunctionality() {
    // Cart quantity controls
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            updateCartQuantity(this);
        });
    });
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            addToCart(this);
        });
    });
    
    // Remove from cart buttons
    const removeFromCartButtons = document.querySelectorAll('.remove-from-cart-btn');
    removeFromCartButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            removeFromCart(this);
        });
    });
}

/**
 * Initialize image loading with lazy loading
 */
function initializeImageLoading() {
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
    
    // Handle image load errors
    const allImages = document.querySelectorAll('img');
    allImages.forEach(function(img) {
        img.addEventListener('error', function() {
            this.src = '/storage/placeholder-product.svg';
            this.alt = 'Gambar tidak tersedia';
        });
    });
}

/**
 * Initialize smooth animations
 */
function initializeSmoothAnimations() {
    // Fade in animation for cards
    const cards = document.querySelectorAll('.card');
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    });

    cards.forEach(card => cardObserver.observe(card));
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Initialize search functionality
 */
function initializeSearchFunctionality() {
    const searchInput = document.querySelector('#product-search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterProducts(this.value);
            }, 300);
        });
    }
}

/**
 * Add product to cart
 */
function addToCart(button) {
    const productId = button.dataset.productId;
    const quantity = button.dataset.quantity || 1;
    
    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="loading-spinner"></span> Menambahkan...';
    button.disabled = true;
    
    // Simulate AJAX request (replace with actual AJAX call)
    fetch('/customer/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
            updateCartCount(data.cartCount);
        } else {
            showNotification(data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menambah ke keranjang', 'error');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

/**
 * Update cart quantity
 */
function updateCartQuantity(input) {
    const productId = input.dataset.productId;
    const quantity = parseInt(input.value);
    
    if (quantity <= 0) {
        removeFromCart(input);
        return;
    }
    
    clearTimeout(cartUpdateTimeout);
    cartUpdateTimeout = setTimeout(() => {
        fetch('/customer/cart/update', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartTotal(data.total);
                updateCartCount(data.cartCount);
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
        });
    }, 500);
}

/**
 * Remove item from cart
 */
function removeFromCart(element) {
    const productId = element.dataset.productId;
    
    if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
        fetch('/customer/cart/remove', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the cart item element
                const cartItem = element.closest('.cart-item');
                if (cartItem) {
                    cartItem.remove();
                }
                updateCartCount(data.cartCount);
                showNotification('Item berhasil dihapus dari keranjang', 'success');
            }
        })
        .catch(error => {
            console.error('Error removing from cart:', error);
        });
    }
}

/**
 * Filter products based on search query
 */
function filterProducts(query) {
    const products = document.querySelectorAll('.product-card');
    const searchQuery = query.toLowerCase();
    
    products.forEach(function(product) {
        const productName = product.querySelector('.product-name').textContent.toLowerCase();
        const productDescription = product.querySelector('.product-description').textContent.toLowerCase();
        
        if (productName.includes(searchQuery) || productDescription.includes(searchQuery)) {
            product.style.display = 'block';
            product.classList.add('fade-in');
        } else {
            product.style.display = 'none';
        }
    });
}

/**
 * Update cart count in header
 */
function updateCartCount(count) {
    const cartBadges = document.querySelectorAll('.cart-count');
    cartBadges.forEach(function(badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

/**
 * Update cart total
 */
function updateCartTotal(total) {
    const totalElements = document.querySelectorAll('.cart-total');
    totalElements.forEach(function(element) {
        element.textContent = 'Rp ' + formatNumber(total);
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Format number with thousands separator
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Validate email format
 */
function validateEmail(input) {
    const email = input.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        input.setCustomValidity('Format email tidak valid');
        input.classList.add('is-invalid');
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
}

/**
 * Validate password strength
 */
function validatePasswordStrength(input) {
    const password = input.value;
    const strengthMeter = input.parentNode.querySelector('.password-strength');
    
    if (!strengthMeter) return;
    
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength++;
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength++;
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength++;
    
    // Number check
    if (/\d/.test(password)) strength++;
    
    // Special character check
    if (/[^A-Za-z\d]/.test(password)) strength++;
    
    // Update strength meter
    const strengthLabels = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
    const strengthColors = ['#f44336', '#ff9800', '#ffeb3b', '#4caf50', '#2196f3'];
    
    strengthMeter.textContent = strengthLabels[strength - 1] || '';
    strengthMeter.style.color = strengthColors[strength - 1] || '#666';
}

/**
 * Utility function to debounce function calls
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format currency for Indonesian Rupiah
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Export functions for use in other scripts
window.LaravelBarokah = {
    addToCart,
    removeFromCart,
    updateCartQuantity,
    showNotification,
    formatNumber,
    formatCurrency,
    debounce
};