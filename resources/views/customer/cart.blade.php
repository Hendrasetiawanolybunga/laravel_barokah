@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-cart text-success me-2"></i>Keranjang Belanja</h2>
                <a href="{{ route('customer.home') }}" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                </a>
            </div>

            @if(count($cart) > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Item dalam Keranjang
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                @foreach($cart as $productId => $item)
                                    <div class="cart-item border-bottom p-3" data-product-id="{{ $productId }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                @if(isset($item['foto']) && $item['foto'])
                                                    <img src="{{ Storage::url($item['foto']) }}" 
                                                         alt="{{ $item['nama'] ?? 'Produk' }}" 
                                                         class="img-fluid rounded">
                                                @else
                                                    <img src="{{ asset('storage/placeholder-product.svg') }}" 
                                                         alt="{{ $item['nama'] ?? 'Produk' }}" 
                                                         class="img-fluid rounded">
                                                @endif
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <h6 class="mb-1">{{ $item['nama'] ?? 'Nama Produk Tidak Tersedia' }}</h6>
                                                <p class="text-muted mb-0 small">
                                                    {{ isset($item['deskripsi']) ? Str::limit($item['deskripsi'], 60) : 'Deskripsi tidak tersedia' }}
                                                </p>
                                            </div>
                                            
                                            <div class="col-md-2 text-center">
                                                <strong class="text-success">
                                                    Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                                                </strong>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary quantity-btn" 
                                                            type="button" 
                                                            data-action="decrease"
                                                            data-product-id="{{ $productId }}">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="form-control text-center quantity-input" 
                                                           value="{{ $item['quantity'] ?? 1 }}" 
                                                           min="1" 
                                                           max="{{ $item['stok'] ?? 999 }}"
                                                           data-product-id="{{ $productId }}">
                                                    <button class="btn btn-outline-secondary quantity-btn" 
                                                            type="button" 
                                                            data-action="increase"
                                                            data-product-id="{{ $productId }}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">Stok: {{ $item['stok'] ?? 'N/A' }}</small>
                                            </div>
                                            
                                            <div class="col-md-1 text-end">
                                                <button class="btn btn-sm btn-outline-danger remove-from-cart-btn" 
                                                        data-product-id="{{ $productId }}"
                                                        title="Hapus dari keranjang">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-2">
                                            <div class="col-12 text-end">
                                                <strong>
                                                    Subtotal: 
                                                    <span class="text-success item-subtotal">
                                                        Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}
                                                    </span>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>Ringkasan Pesanan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Jumlah Item:</span>
                                    <strong class="total-items">{{ array_sum(array_map(function($item) { return $item['quantity'] ?? 0; }, $cart)) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Total Belanja:</span>
                                    <strong class="text-success cart-total">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                                </div>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('customer.checkout') }}" class="btn btn-success btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Lanjut ke Checkout
                                    </a>
                                    <a href="{{ route('customer.home') }}" class="btn btn-outline-success">
                                        <i class="fas fa-shopping-bag me-2"></i>Lanjut Belanja
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cart Tips -->
                        <div class="card shadow-sm mt-3">
                            <div class="card-body">
                                <h6 class="text-success">
                                    <i class="fas fa-lightbulb me-2"></i>Tips Belanja
                                </h6>
                                <ul class="list-unstyled small text-muted mb-0">
                                    <li><i class="fas fa-check text-success me-2"></i>Pastikan kuantitas sesuai kebutuhan</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Periksa stok produk yang tersedia</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Siapkan bukti pembayaran saat checkout</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="display-1 text-muted mb-3">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4 class="text-muted mb-3">Keranjang Belanja Kosong</h4>
                    <p class="text-muted mb-4">Anda belum menambahkan produk ke keranjang belanja.</p>
                    <a href="{{ route('customer.home') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.cart-item {
    transition: background-color 0.3s ease;
}

.cart-item:hover {
    background-color: rgba(76, 175, 80, 0.05);
}

.quantity-input {
    width: 70px;
}

.quantity-btn {
    padding: 0.25rem 0.5rem;
}

.item-subtotal {
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    transition: box-shadow 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity controls
    $('.quantity-btn').on('click', function() {
        const action = $(this).data('action');
        const productId = $(this).data('product-id');
        const quantityInput = $(`.quantity-input[data-product-id="${productId}"]`);
        let currentQuantity = parseInt(quantityInput.val());
        const maxStock = parseInt(quantityInput.attr('max'));
        
        if (action === 'increase' && currentQuantity < maxStock) {
            quantityInput.val(currentQuantity + 1);
        } else if (action === 'decrease' && currentQuantity > 1) {
            quantityInput.val(currentQuantity - 1);
        }
        
        updateCartQuantity(quantityInput[0]);
    });
    
    // Direct quantity input change
    $('.quantity-input').on('change', function() {
        const maxStock = parseInt($(this).attr('max'));
        let value = parseInt($(this).val());
        
        if (value > maxStock) {
            $(this).val(maxStock);
            showNotification('Kuantitas tidak boleh melebihi stok yang tersedia', 'warning');
        } else if (value < 1) {
            $(this).val(1);
        }
        
        updateCartQuantity(this);
    });
    
    // Remove from cart
    $('.remove-from-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        
        Swal.fire({
            title: 'Hapus dari Keranjang?',
            text: 'Apakah Anda yakin ingin menghapus item ini dari keranjang?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                removeFromCart($(this)[0]);
            }
        });
    });
    
    // Auto-save cart changes (already defined in main app.js)
    function updateCartQuantity(input) {
        const productId = $(input).data('product-id');
        const quantity = parseInt($(input).val());
        const price = getItemPrice(productId);
        
        // Update subtotal display
        const subtotal = price * quantity;
        $(`.cart-item[data-product-id="${productId}"] .item-subtotal`).text('Rp ' + formatNumber(subtotal));
        
        // Update cart totals
        updateCartTotals();
        
        // Send AJAX request to update server-side cart
        clearTimeout(window.cartUpdateTimeout);
        window.cartUpdateTimeout = setTimeout(() => {
            $.ajax({
                url: '{{ route("customer.cart.update") }}',
                method: 'PUT',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        updateCartCount(response.cartCount);
                    }
                },
                error: function() {
                    showNotification('Terjadi kesalahan saat memperbarui keranjang', 'error');
                }
            });
        }, 500);
    }
    
    function removeFromCart(element) {
        const productId = $(element).data('product-id');
        
        $.ajax({
            url: '{{ route("customer.cart.remove") }}',
            method: 'DELETE',
            data: {
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    $(`.cart-item[data-product-id="${productId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        updateCartTotals();
                        updateCartCount(response.cartCount);
                        
                        // Check if cart is empty
                        if ($('.cart-item').length === 0) {
                            location.reload();
                        }
                    });
                    showNotification(response.message, 'success');
                }
            },
            error: function() {
                showNotification('Terjadi kesalahan saat menghapus item', 'error');
            }
        });
    }
    
    function getItemPrice(productId) {
        const cartItem = $(`.cart-item[data-product-id="${productId}"]`);
        const priceText = cartItem.find('.text-success').first().text();
        return parseInt(priceText.replace(/[^0-9]/g, ''));
    }
    
    function updateCartTotals() {
        let totalItems = 0;
        let totalPrice = 0;
        
        $('.cart-item').each(function() {
            const quantity = parseInt($(this).find('.quantity-input').val());
            const productId = $(this).data('product-id');
            const price = getItemPrice(productId);
            
            totalItems += quantity;
            totalPrice += price * quantity;
        });
        
        $('.total-items').text(totalItems);
        $('.cart-total').text('Rp ' + formatNumber(totalPrice));
    }
    
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function showNotification(message, type) {
        const alertClass = type === 'error' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-success';
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }
    
    function updateCartCount(count) {
        $('.cart-count').text(count);
        if (count > 0) {
            $('.cart-count').show();
        } else {
            $('.cart-count').hide();
        }
    }
});
</script>
@endpush
@endsection