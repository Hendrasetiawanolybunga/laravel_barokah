@extends('layouts.app')

@section('title', 'Beranda - UD. Barokah Jaya Beton')

@section('content')
<div class="container py-4">
    <!-- Discount Countdown Timer (will be shown if there are expiring discounts) -->
    @include('components.discount-countdown')
    
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
                            @if($isLoyalCustomer)
                                <span class="badge bg-warning text-dark fs-6 me-2">
                                    <i class="fas fa-star"></i> Member Loyal
                                </span>
                            @endif
                            @if($isBirthday)
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-birthday-cake"></i> Selamat Ulang Tahun!
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

    <!-- Birthday Notification -->
    @if($isBirthday)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-birthday-cake"></i> Selamat Ulang Tahun!
                    </h5>
                    <p class="mb-0">
                        Selamat ulang tahun, {{ $user->name }}! 🎉 
                        Terima kasih telah menjadi bagian dari keluarga UD. Barokah Jaya Beton.
                        @if($activeDiscounts->count() > 0)
                            Kami telah menyiapkan diskon spesial untuk Anda!
                        @endif
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Personal Discounts Notification (Only show if there's a new discount alert in session) -->
    @if(session('new_discount_alert'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-percent"></i> Diskon Baru!
                    </h5>
                    <p class="mb-0">
                        {{ session('new_discount_alert') }}
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Loyalty Progress Notification -->
    @if(!$isLoyalCustomer && $totalSpending > 0)
        @php
            $loyaltyThreshold = 5000000;
            $progressPercent = min(($totalSpending / $loyaltyThreshold) * 100, 100);
            $remainingAmount = max($loyaltyThreshold - $totalSpending, 0);
        @endphp
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-light border-primary" role="alert">
                    <h6 class="alert-heading text-primary">
                        <i class="fas fa-trophy"></i> Status Member Loyal
                    </h6>
                    <div class="mb-2">
                        <small class="text-muted">Total Pembelian: Rp {{ number_format($totalSpending, 0, ',', '.') }}</small>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-primary" 
                             role="progressbar" 
                             style="width: {{ $progressPercent }}%" 
                             aria-valuenow="{{ $progressPercent }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">
                        @if($remainingAmount > 0)
                            <i class="fas fa-info-circle"></i> 
                            Belanja lagi Rp {{ number_format($remainingAmount, 0, ',', '.') }} untuk menjadi Member Loyal 
                            dan dapatkan keuntungan eksklusif!
                        @else
                            <i class="fas fa-check-circle text-success"></i> 
                            Selamat! Anda telah mencapai status Member Loyal!
                        @endif
                    </small>
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
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="fas fa-percent fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">{{ $activeDiscounts->count() }}</h5>
                    <p class="card-text text-muted">Diskon Aktif</p>
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

<!-- Active Discounts Modal -->
<div class="modal fade" id="activeDiscountsModal" tabindex="-1" aria-labelledby="activeDiscountsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="activeDiscountsModalLabel">
                    <i class="fas fa-percent"></i> Diskon Aktif Anda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="discounts-content">
                    <!-- Discounts will be loaded here via AJAX -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2">Memuat diskon aktif...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Active Discounts Modal JavaScript -->
<script>
    // Add click event to the active discounts card
    document.addEventListener('DOMContentLoaded', function() {
        const discountsCard = document.querySelector('.card.text-center.border-warning');
        if (discountsCard) {
            discountsCard.style.cursor = 'pointer';
            discountsCard.addEventListener('click', function() {
                loadActiveDiscounts();
            });
        }
    });
    
    // Function to load active discounts via AJAX
    function loadActiveDiscounts() {
        const modal = new bootstrap.Modal(document.getElementById('activeDiscountsModal'));
        const discountsContent = document.getElementById('discounts-content');
        
        // Show loading state
        discountsContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <p class="mt-2">Memuat diskon aktif...</p>
            </div>
        `;
        
        // Show the modal
        modal.show();
        
        // Fetch discounts data
        fetch('{{ route("customer.active-discounts") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            displayDiscounts(data);
        })
        .catch(error => {
            console.error('Error:', error);
            discountsContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat diskon
                </div>
            `;
        });
    }
    
    // Function to display discounts in the modal
    function displayDiscounts(discounts) {
        const discountsContent = document.getElementById('discounts-content');
        
        if (discounts.length === 0) {
            discountsContent.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-percent fa-3x text-muted mb-3"></i>
                    <h5>Tidak ada diskon aktif</h5>
                    <p class="text-muted">Anda tidak memiliki diskon aktif saat ini.</p>
                </div>
            `;
            return;
        }
        
        let discountsHtml = `
            <div class="row g-4">
        `;
        
        discounts.forEach(discount => {
            // Create countdown timer if discount has expiry date
            let countdownHtml = '';
            if (discount.expires_at) {
                countdownHtml = `
                    <div class="mt-2">
                        <small class="text-muted">Berlaku hingga: ${discount.formatted_expiry}</small>
                        <div class="countdown-timer mt-1" id="countdown-${discount.id}">
                            <!-- Countdown will be populated by JavaScript -->
                        </div>
                    </div>
                `;
            }
            
            discountsHtml += `
                <div class="col-12">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title text-primary">${discount.product_name}</h5>
                                    <p class="card-text text-muted">${discount.product_description}</p>
                                    <div class="badge bg-success fs-6">Diskon ${discount.percentage}%</div>
                                </div>
                                ${countdownHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        discountsHtml += `
            </div>
        `;
        
        discountsContent.innerHTML = discountsHtml;
        
        // Initialize countdown timers for each discount
        discounts.forEach(discount => {
            if (discount.expires_at) {
                initializeCountdown(discount.id, discount.expires_at);
            }
        });
    }
    
    // Function to initialize countdown timer
    function initializeCountdown(discountId, expiryDate) {
        const expiryTime = new Date(expiryDate).getTime();
        const countdownElement = document.getElementById(`countdown-${discountId}`);
        
        // Update the countdown every second
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiryTime - now;
            
            // If the countdown is finished
            if (distance < 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = `
                    <span class="badge bg-danger">Diskon Kadaluarsa</span>
                `;
                return;
            }
            
            // Calculate days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the results
            countdownElement.innerHTML = `
                <div class="d-flex small">
                    <div class="text-center mx-1">
                        <div class="fw-bold">${days}</div>
                        <div>Hari</div>
                    </div>
                    <div class="text-center mx-1">
                        <div class="fw-bold">${hours}</div>
                        <div>Jam</div>
                    </div>
                    <div class="text-center mx-1">
                        <div class="fw-bold">${minutes}</div>
                        <div>Menit</div>
                    </div>
                    <div class="text-center mx-1">
                        <div class="fw-bold">${seconds}</div>
                        <div>Detik</div>
                    </div>
                </div>
            `;
        }, 1000);
    }
</script>
@endpush