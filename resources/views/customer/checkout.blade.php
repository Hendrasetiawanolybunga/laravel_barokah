@extends('layouts.app')

@section('title', 'Checkout - Laravel Barokah')

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
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-university"></i> Instruksi Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Transfer Bank Manual
                        </h6>
                        <p class="mb-2">Silakan transfer ke rekening berikut:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Bank BCA</strong><br>
                                <code>1234567890</code><br>
                                <small>a.n. Laravel Barokah</small>
                            </div>
                            <div class="col-md-6">
                                <strong>Bank Mandiri</strong><br>
                                <code>0987654321</code><br>
                                <small>a.n. Laravel Barokah</small>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Transfer dalam 24 jam atau pesanan akan dibatalkan otomatis.
                        </small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
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
                        <i class="fas fa-envelope"></i> support@laravelbarokah.com
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
</script>
@endpush