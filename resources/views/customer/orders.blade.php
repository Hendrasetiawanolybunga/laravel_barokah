@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-history text-success me-2"></i>Riwayat Pesanan</h2>
                <a href="{{ route('customer.home') }}" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                </a>
            </div>

            @if($orders->count() > 0)
                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-0">
                                                <i class="fas fa-shopping-bag text-success me-2"></i>
                                                Pesanan #{{ $order->id }}
                                            </h5>
                                            <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <span class="badge 
                                                @if($order->status === 'pending') bg-warning
                                                @elseif($order->status === 'confirmed') bg-info
                                                @elseif($order->status === 'processing') bg-primary
                                                @elseif($order->status === 'shipped') bg-secondary
                                                @elseif($order->status === 'delivered') bg-success
                                                @elseif($order->status === 'cancelled') bg-danger
                                                @endif
                                                fs-6 px-3 py-2">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="text-success mb-3">Detail Produk:</h6>
                                            @foreach($order->orderItems as $item)
                                                <div class="border rounded p-3 mb-3 bg-light">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-2">
                                                            @if($item->product->foto)
                                                                <img src="{{ Storage::url($item->product->foto) }}" 
                                                                     alt="{{ $item->product->nama }}" 
                                                                     class="img-fluid rounded" style="max-height: 60px;">
                                                            @else
                                                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                                                     style="height: 60px; width: 60px;">
                                                                    <i class="fas fa-image text-white"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h6 class="mb-1">{{ $item->product->nama }}</h6>
                                                            <small class="text-muted">{{ Str::limit($item->product->deskripsi, 50) }}</small>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <span class="badge bg-success">{{ $item->jumlah_item }}x</span>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <strong>Rp {{ number_format($item->sub_total / $item->jumlah_item, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div class="col-md-2 text-end">
                                                            @if($order->status === 'shipped' && !$item->ulasan)
                                                                <button type="button" class="btn btn-sm btn-outline-success review-btn" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#reviewModal{{ $item->id }}"
                                                                        data-item-id="{{ $item->id }}"
                                                                        data-product-name="{{ $item->product->nama }}"
                                                                        data-product-qty="{{ $item->jumlah_item }}"
                                                                        data-product-price="{{ number_format($item->sub_total / $item->jumlah_item, 0, ',', '.') }}">
                                                                    <i class="fas fa-comment me-1"></i>Review
                                                                </button>
                                                            @elseif($item->ulasan)
                                                                <span class="text-success">
                                                                    <i class="fas fa-check-circle me-1"></i>Reviewed
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Review Modal -->
                                                @if($order->status === 'shipped' && !$item->ulasan)
                                                <div class="modal fade" id="reviewModal{{ $item->id }}" tabindex="-1" 
                                                     data-bs-backdrop="static" data-bs-keyboard="false" 
                                                     aria-labelledby="reviewModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="reviewModalLabel{{ $item->id }}">
                                                                    <i class="fas fa-comment text-primary me-2"></i>
                                                                    Tulis Ulasan Produk
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('customer.orders.review', $item->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <!-- Product Information -->
                                                                    <div class="alert alert-light border mb-3">
                                                                        <div class="row align-items-center">
                                                                            <div class="col-auto">
                                                                                @if($item->product->foto)
                                                                                    <img src="{{ Storage::url($item->product->foto) }}" 
                                                                                         alt="{{ $item->product->nama }}" 
                                                                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                                                @else
                                                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                                                                         style="width: 60px; height: 60px;">
                                                                                        <i class="fas fa-image text-white"></i>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col">
                                                                                <h6 class="mb-1 text-dark">{{ $item->product->nama }}</h6>
                                                                                <small class="text-muted">Jumlah: {{ $item->jumlah_item }}x | Harga: Rp {{ number_format($item->sub_total / $item->jumlah_item, 0, ',', '.') }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Review Form -->
                                                                    <div class="mb-3">
                                                                        <label for="ulasan{{ $item->id }}" class="form-label">
                                                                            <i class="fas fa-edit me-1"></i>Bagikan pengalaman Anda dengan produk ini
                                                                        </label>
                                                                        <textarea class="form-control" id="ulasan{{ $item->id }}" name="ulasan" 
                                                                                  rows="4" 
                                                                                  placeholder="Ceritakan pengalaman Anda menggunakan {{ $item->product->nama }}. Apakah produk sesuai ekspektasi? Bagaimana kualitasnya?" 
                                                                                  required></textarea>
                                                                        <div class="form-text">
                                                                            <i class="fas fa-info-circle me-1"></i>Ulasan Anda akan membantu pembeli lain untuk membuat keputusan yang tepat.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="fas fa-paper-plane me-2"></i>Kirim Review
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded">
                                                <h6 class="text-success mb-3">Ringkasan Pesanan:</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Item:</span>
                                                    <strong>{{ $order->orderItems->sum('jumlah_item') }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Total Harga:</span>
                                                    <strong class="text-success">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                                </div>
                                                <hr>
                                                @if($order->bukti_bayar)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Bukti Pembayaran:</small>
                                                        <br>
                                                        <a href="{{ Storage::url($order->bukti_bayar) }}" 
                                                           target="_blank" class="btn btn-sm btn-outline-info mt-1">
                                                            <i class="fas fa-eye me-1"></i>Lihat Bukti
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="display-1 text-muted mb-3">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Pesanan</h4>
                    <p class="text-muted mb-4">Anda belum memiliki riwayat pesanan. Mulai berbelanja sekarang!</p>
                    <a href="{{ route('customer.home') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mulai Berbelanja
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    border: none;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    border-radius: 20px;
}

.bg-light {
    background-color: rgba(76, 175, 80, 0.05) !important;
}

/* Enhanced modal stability styles */
.modal {
    pointer-events: auto !important;
}

.modal-dialog {
    pointer-events: auto !important;
}

.modal-content {
    pointer-events: auto !important;
}

/* Prevent any hover effects on modal elements that could cause flickering */
.modal .btn:hover,
.modal .btn:focus,
.modal .btn:active {
    pointer-events: auto !important;
}

/* Ensure review button is stable */
.review-btn {
    transition: all 0.2s ease;
    pointer-events: auto !important;
}

.review-btn:hover {
    transform: none !important;
}

/* Product info in modal */
.modal .alert {
    background-color: rgba(76, 175, 80, 0.1) !important;
    border-color: rgba(76, 175, 80, 0.2) !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Show success message after review submission
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    // Enhanced modal control to prevent flickering completely
    let modalProcessing = false;
    
    // Remove default Bootstrap modal triggers to prevent conflicts
    $('.review-btn').removeAttr('data-bs-toggle').removeAttr('data-bs-target');
    
    // Custom modal handling with enhanced stability
    $('.review-btn').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        
        // Prevent multiple clicks
        if (modalProcessing) return false;
        modalProcessing = true;
        
        const button = $(this);
        const modalId = '#reviewModal' + button.data('item-id');
        const productName = button.data('product-name');
        const productQty = button.data('product-qty');
        const productPrice = button.data('product-price');
        
        // Close any existing modals first
        $('.modal').each(function() {
            const existingModal = bootstrap.Modal.getInstance(this);
            if (existingModal) {
                existingModal.hide();
            }
        });
        
        // Wait for any existing modals to fully close
        setTimeout(function() {
            try {
                const modalElement = document.querySelector(modalId);
                if (modalElement) {
                    // Update modal content dynamically
                    const placeholder = modalElement.querySelector('textarea').getAttribute('placeholder');
                    if (placeholder.includes('{{')) {
                        modalElement.querySelector('textarea').setAttribute('placeholder', 
                            `Ceritakan pengalaman Anda menggunakan ${productName}. Apakah produk sesuai ekspektasi? Bagaimana kualitasnya?`);
                    }
                    
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false,
                        focus: true
                    });
                    
                    modal.show();
                    
                    // Reset processing flag when modal is shown
                    modalElement.addEventListener('shown.bs.modal', function() {
                        modalProcessing = false;
                    }, { once: true });
                    
                    // Reset processing flag if modal fails to show
                    setTimeout(function() {
                        modalProcessing = false;
                    }, 1000);
                }
            } catch (error) {
                console.error('Error showing modal:', error);
                modalProcessing = false;
            }
        }, 150);
        
        return false;
    });
    
    // Prevent modal from closing on hover or mouse events
    $('.modal').on('mouseenter mouseleave mouseover mouseout', function(event) {
        event.stopPropagation();
    });
    
    // Handle modal backdrop clicks properly
    $('.modal').on('click', function(event) {
        if (event.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            if (modal) {
                modal.hide();
            }
        }
    });
    
    // Prevent modal from closing when clicking inside modal content
    $('.modal-dialog').on('click', function(event) {
        event.stopPropagation();
    });
    
    // Ensure modal closes properly with close button
    $('.modal .btn-close, .modal [data-bs-dismiss="modal"]').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const modal = bootstrap.Modal.getInstance($(this).closest('.modal')[0]);
        if (modal) {
            modal.hide();
        }
    });
    
    // Reset processing flag when any modal is hidden
    $('.modal').on('hidden.bs.modal', function() {
        modalProcessing = false;
    });
});
</script>
@endpush
@endsection