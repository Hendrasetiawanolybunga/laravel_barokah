@extends('layouts.app')

@section('title', 'Checkout - UD. Barokah Jaya Beton')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('customer.cart.show') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 text-primary fw-bold mb-0">
                        <i class="fas fa-credit-card"></i> Checkout
                    </h1>
                    <p class="text-muted mb-0">Selesaikan pembelian Anda</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Process Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="text-success">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h6>1. Keranjang</h6>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-primary">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <h6>2. Pembayaran</h6>
                                <small class="text-primary fw-bold">Sedang Aktif</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">
                                <i class="fas fa-check fa-2x mb-2"></i>
                                <h6>3. Selesai</h6>
                                <small class="text-muted">Menunggu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Ringkasan Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($cart as $productId => $item)
                        <div class="row align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="col-md-2">
                                @if(isset($item['foto']) && $item['foto'])
                                    <img src="{{ Storage::url($item['foto']) }}" 
                                         alt="{{ $item['nama'] ?? 'Produk' }}" 
                                         class="img-thumbnail"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('storage/placeholder-product.svg') }}" 
                                         alt="{{ $item['nama'] ?? 'Produk' }}" 
                                         class="img-thumbnail"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1">{{ $item['nama'] ?? 'Nama Produk Tidak Tersedia' }}</h6>
                                <small class="text-muted">
                                    Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }} x {{ $item['quantity'] ?? 1 }}
                                </small>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-secondary">{{ $item['quantity'] ?? 1 }} pcs</span>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="fw-bold text-success">
                                    Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="card mb-4 payment-instructions-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-university"></i> Instruksi Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0" style="background: rgba(13, 202, 240, 0.1);">
                        <h6 class="alert-heading text-info">
                            <i class="fas fa-info-circle"></i> Transfer Bank Manual
                        </h6>
                        <p class="mb-2">Silakan transfer ke rekening berikut:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="bg-white p-3 rounded border">
                                    <strong class="text-primary">Bank BCA</strong><br>
                                    <code class="bg-light p-1 rounded">1234567890</code><br>
                                    <small class="text-muted">a.n. UD. Barokah Jaya Beton</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white p-3 rounded border">
                                    <strong class="text-primary">Bank Mandiri</strong><br>
                                    <code class="bg-light p-1 rounded">0987654321</code><br>
                                    <small class="text-muted">a.n. UD. Barokah Jaya Beton</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Transfer dalam 24 jam atau pesanan akan dibatalkan otomatis.
                        </small>
                    </div>
                    
                    <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1);">
                        <h6 class="alert-heading text-warning">
                            <i class="fas fa-exclamation-triangle"></i> Penting!
                        </h6>
                        <ul class="mb-0 small">
                            <li>Transfer sesuai jumlah yang tertera (Rp {{ number_format($total, 0, ',', '.') }})</li>
                            <li>Simpan bukti transfer dan upload di form berikut</li>
                            <li>Pesanan akan diproses setelah pembayaran dikonfirmasi</li>
                            <li>Estimasi pengiriman 2-5 hari kerja setelah konfirmasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-upload"></i> Upload Bukti Bayar
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.checkout.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Order Total -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Ongkos Kirim:</span>
                                <span class="text-success">Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold">Total:</h6>
                                <h6 class="fw-bold text-success">Rp {{ number_format($total, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                        
                        <!-- Payment Proof Upload -->
                        <div class="mb-3">
                            <label for="bukti_bayar" class="form-label">
                                <i class="fas fa-camera"></i> Bukti Pembayaran <span class="text-danger">*</span>
                            </label>
                            <input type="file" 
                                   class="form-control @error('bukti_bayar') is-invalid @enderror" 
                                   id="bukti_bayar" 
                                   name="bukti_bayar" 
                                   accept="image/*"
                                   required
                                   onchange="previewPaymentProof(this)">
                            @error('bukti_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="payment-preview" class="mt-3" style="display: none;">
                                <img id="preview-payment" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                            </div>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="mb-3">
                            <h6 class="text-primary">
                                <i class="fas fa-user"></i> Informasi Pemesan
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <div><strong>{{ auth()->user()->name }}</strong></div>
                                <div class="text-muted">{{ auth()->user()->email }}</div>
                                @if(auth()->user()->customer)
                                    <div class="text-muted mt-1">
                                        <i class="fas fa-phone"></i> {{ auth()->user()->customer->no_hp }}
                                    </div>
                                    <div class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> {{ auth()->user()->customer->alamat }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="terms" 
                                       id="terms"
                                       required>
                                <label class="form-check-label small" for="terms">
                                    Saya telah melakukan transfer dan setuju dengan 
                                    <a href="#" class="text-primary">syarat dan ketentuan</a> 
                                    yang berlaku <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Konfirmasi Pesanan
                            </button>
                            <a href="{{ route('customer.cart.show') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Help -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="text-primary">
                        <i class="fas fa-question-circle"></i> Butuh Bantuan?
                    </h6>
                    <small class="text-muted">
                        Hubungi customer service kami:<br>
                        <i class="fas fa-phone"></i> +62 812-3456-7890<br>
                        <i class="fas fa-envelope"></i> support@barokahjayabeton.com
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* PAYMENT INSTRUCTIONS PROTECTION */
.payment-instructions-card {
    position: relative !important;
    z-index: 1 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.payment-instructions-card .card-body {
    position: static !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.payment-instructions-card .alert {
    position: static !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

/* Prevent any JavaScript or CSS from hiding payment instructions */
.payment-instructions-card,
.payment-instructions-card * {
    animation: none !important;
    transition: none !important;
}

/* Enhanced card design */
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: none;
    padding: 1.25rem;
}

.card-body {
    padding: 1.5rem;
}

/* Bank account styling */
.bg-white.p-3.rounded.border {
    transition: all 0.3s ease;
}

.bg-white.p-3.rounded.border:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Button improvements */
.btn {
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
}

/* Checkout steps enhancement */
.card-body .row.text-center .col-md-4 {
    padding: 1rem;
}

.text-success i,
.text-primary i,
.text-muted i {
    transition: transform 0.3s ease;
}

.text-success:hover i,
.text-primary:hover i {
    transform: scale(1.1);
}

/* Responsive design for checkout */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .d-flex.align-items-center .btn {
        margin-bottom: 1rem;
    }
    
    .row.align-items-center {
        text-align: center;
    }
    
    .col-md-2,
    .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .text-end {
        text-align: center !important;
    }
    
    h1.h3 {
        font-size: 1.5rem;
    }
    
    .card-header h5 {
        font-size: 1.1rem;
    }
    
    /* Payment form on mobile */
    .col-lg-4 {
        margin-top: 2rem;
    }
    
    /* Bank account cards responsive */
    .row .col-md-6 {
        margin-bottom: 1rem;
    }
}

@media (max-width: 575.98px) {
    .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    .card {
        border-radius: 10px;
    }
    
    .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
    
    .display-6 {
        font-size: 1.25rem;
    }
    
    /* Stack checkout steps vertically on very small screens */
    .row.text-center .col-md-4 {
        margin-bottom: 1rem;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
    }
    
    .row.text-center .col-md-4:last-child {
        border-bottom: none;
    }
}

/* Additional protection against element disappearing */
.alert-info,
.alert-warning {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
}

/* Ensure payment instructions stay visible */
.payment-instructions-card .alert-info,
.payment-instructions-card .alert-warning {
    margin-bottom: 1rem !important;
}

/* Form enhancements */
.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
}

/* File input styling */
.form-control[type="file"] {
    padding: 0.75rem;
}

/* Preview image enhancement */
#payment-preview {
    border-radius: 10px;
    overflow: hidden;
}

#preview-payment {
    border-radius: 10px;
    transition: transform 0.3s ease;
}

#preview-payment:hover {
    transform: scale(1.02);
}
</style>
@endpush

@push('scripts')
<script>
    function previewPaymentProof(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('preview-payment').src = e.target.result;
                document.getElementById('payment-preview').style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // CRITICAL: Ensure payment instructions never disappear
    document.addEventListener('DOMContentLoaded', function() {
        // Protect payment instructions from being hidden
        const paymentCard = document.querySelector('.payment-instructions-card');
        if (paymentCard) {
            // Remove any potential hiding classes or styles
            paymentCard.style.display = 'block';
            paymentCard.style.visibility = 'visible';
            paymentCard.style.opacity = '1';
            
            // Prevent any scripts from hiding the payment instructions
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && 
                        (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                        const target = mutation.target;
                        if (target.classList.contains('payment-instructions-card') ||
                            target.closest('.payment-instructions-card')) {
                            // Ensure it remains visible
                            target.style.display = 'block';
                            target.style.visibility = 'visible';
                            target.style.opacity = '1';
                        }
                    }
                });
            });
            
            observer.observe(paymentCard, {
                attributes: true,
                childList: true,
                subtree: true,
                attributeFilter: ['style', 'class']
            });
        }
        
        // Enhanced form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const fileInput = document.getElementById('bukti_bayar');
                const termsCheckbox = document.getElementById('terms');
                
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Silakan upload bukti pembayaran terlebih dahulu.');
                    fileInput.focus();
                    return false;
                }
                
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    alert('Silakan centang persetujuan syarat dan ketentuan.');
                    termsCheckbox.focus();
                    return false;
                }
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
@endpush