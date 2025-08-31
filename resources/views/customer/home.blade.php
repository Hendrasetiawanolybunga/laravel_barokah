@extends('layouts.app')

@section('title', 'Beranda - UD. Barokah Jaya Beton')

@section('content')
<div class="container py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-home"></i> Selamat Datang, {{ $user->name }}!
                            </h4>
                            <p class="mb-0 opacity-75">
                                Selamat berbelanja di UD. Barokah Jaya Beton, tempat Anda menemukan produk berkualitas
                            </p>
                        </div>
                        <div class="text-end">
                            @if($user->is_loyal)
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="fas fa-star"></i> Member Loyal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Message Section -->
    @if($user->message)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-bullhorn"></i> Pesan dari Admin
                    </h5>
                    <p class="mb-0">{{ $user->message }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="fas fa-box fa-2x text-success mb-2"></i>
                    <h5 class="card-title">{{ $products->total() }}</h5>
                    <p class="card-text text-muted">Produk Tersedia</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">{{ $cartCount }}</h5>
                    <p class="card-text text-muted">Item di Keranjang</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="fas fa-heart fa-2x text-info mb-2"></i>
                    <h5 class="card-title">Terpercaya</h5>
                    <p class="card-text text-muted">Sejak 2024</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary fw-bold">
                    <i class="fas fa-shopping-bag"></i> Katalog Produk
                </h3>
                <div>
                    <a href="{{ route('customer.cart.show') }}" class="btn btn-outline-primary position-relative">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            @if($product->foto)
                                <img src="{{ asset('storage/' . $product->foto) }}" 
                                     class="card-img-top" 
                                     alt="{{ $product->nama }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Stock Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($product->stok > 0)
                                    <span class="badge bg-success">Stok: {{ $product->stok }}</span>
                                @else
                                    <span class="badge bg-danger">Habis</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold">{{ $product->nama }}</h6>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($product->deskripsi, 80) }}
                            </p>
                            <div class="mt-auto">
                                <h5 class="text-success fw-bold mb-3">
                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                </h5>
                                
                                @if($product->stok > 0)
                                    <div class="d-grid gap-2">
                                        <div class="input-group input-group-sm mb-2">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="decreaseQuantity({{ $product->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   class="form-control text-center" 
                                                   id="quantity-{{ $product->id }}" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $product->stok }}">
                                            <button class="btn btn-outline-secondary" type="button"
                                                    onclick="increaseQuantity({{ $product->id }}, {{ $product->stok }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="addToCart({{ $product->id }})">
                                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    </div>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="fas fa-times"></i> Stok Habis
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Produk sedang tidak tersedia</h5>
                    <p class="text-muted">Silakan kembali lagi nanti untuk melihat produk terbaru</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h6>Berhasil!</h6>
                <p id="success-message" class="text-muted mb-0"></p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <h6>Gagal!</h6>
                <p id="error-message" class="text-muted mb-0"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function increaseQuantity(productId, maxStock) {
        const input = document.getElementById('quantity-' + productId);
        const currentValue = parseInt(input.value);
        if (currentValue < maxStock) {
            input.value = currentValue + 1;
        }
    }
    
    function decreaseQuantity(productId) {
        const input = document.getElementById('quantity-' + productId);
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }
    
    function addToCart(productId) {
        const quantity = document.getElementById('quantity-' + productId).value;
        
        $.ajax({
            url: "{{ route('customer.cart.add') }}",
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update cart count in navbar
                    updateCartCount(response.cartCount);
                    
                    // Show success message
                    showSuccessModal(response.message);
                    
                    // Reset quantity to 1
                    document.getElementById('quantity-' + productId).value = 1;
                } else {
                    showErrorModal(response.message);
                }
            },
            error: function(xhr, status, error) {
                showErrorModal('Terjadi kesalahan saat menambahkan produk ke keranjang.');
            }
        });
    }
    
    function updateCartCount(count) {
        // Update cart badge in navbar
        const cartBadges = document.querySelectorAll('.cart-badge');
        cartBadges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
        
        // Update cart count in stats
        const cartCountElement = document.querySelector('.card-body h5.card-title');
        if (cartCountElement) {
            const cards = document.querySelectorAll('.card-body h5.card-title');
            if (cards.length >= 2) {
                cards[1].textContent = count;
            }
        }
    }
    
    function showSuccessModal(message) {
        document.getElementById('success-message').textContent = message;
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
        
        // Auto hide after 2 seconds
        setTimeout(() => {
            modal.hide();
        }, 2000);
    }
    
    function showErrorModal(message) {
        document.getElementById('error-message').textContent = message;
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        modal.show();
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            modal.hide();
        }, 3000);
    }
</script>
@endpush