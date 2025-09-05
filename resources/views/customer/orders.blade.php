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
                                            <small class="text-muted">{{ $order->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</small>
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

<!-- TOP-LEVEL MODALS FOR PROPER Z-INDEX POSITIONING -->
@if($orders->count() > 0)
    @foreach($orders as $order)
        @foreach($order->orderItems as $item)
            @if($order->status === 'shipped' && !$item->ulasan)
            <!-- Review Modal - Positioned at top level -->
            <div class="modal fade top-level-modal" id="reviewModal{{ $item->id }}" tabindex="-1" 
                 data-bs-backdrop="static" data-bs-keyboard="false" 
                 aria-labelledby="reviewModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
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
    @endforeach
@endif

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

/* COMPLETE MODAL POSITIONING AND Z-INDEX FIX */
/* Ensure modals are always on top level with maximum z-index */
.modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 10000 !important;
    display: none !important;
    pointer-events: auto !important;
    background: rgba(0, 0, 0, 0.5) !important;
}

.modal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.top-level-modal {
    position: fixed !important;
    z-index: 10001 !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
}

.modal-dialog {
    position: relative !important;
    z-index: 10002 !important;
    pointer-events: auto !important;
    margin: auto !important;
    max-width: 500px !important;
    width: 90% !important;
}

.modal-content {
    position: relative !important;
    z-index: 10003 !important;
    pointer-events: auto !important;
    background: white !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
}

.modal-backdrop {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 9999 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    pointer-events: auto !important;
}

/* CRITICAL: Prevent parent containers from affecting modal positioning */
.container,
.container-fluid,
.row,
.col-12,
.card {
    overflow: visible !important;
    position: static !important;
}

/* Ensure modal appears above all content regardless of container nesting */
body.modal-open {
    overflow: hidden !important;
    pointer-events: auto !important;
}

body.modal-open .modal {
    overflow-x: hidden !important;
    overflow-y: auto !important;
}

/* Remove transforms that could interfere with positioning */
.modal * {
    transform: none !important;
}

/* Stable review button */
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

/* CRITICAL: Prevent body events from interfering with modals */
body.modal-open {
    pointer-events: auto !important;
    overflow: hidden !important;
}

body.modal-open * {
    pointer-events: auto !important;
}

/* Disable mouse events that could trigger modal conflicts */
body:hover,
body:focus,
.container:hover,
.row:hover,
.col-12:hover {
    pointer-events: auto !important;
}

/* COMPREHENSIVE RESPONSIVE DESIGN */
/* Mobile First Approach - Extra Small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
    .container {
        padding: 0.5rem;
    }
    
    h1, .h1 {
        font-size: 1.25rem;
    }
    
    h2, .h2 {
        font-size: 1.15rem;
    }
    
    h3, .h3, h4, .h4, h5, .h5, h6, .h6 {
        font-size: 1rem;
    }
    
    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .card {
        margin-bottom: 0.75rem;
        border-radius: 0.5rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .card-header {
        padding: 0.75rem;
    }
    
    .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
    
    .alert {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
    
    .modal-dialog {
        margin: 0.25rem;
        max-width: calc(100% - 0.5rem);
    }
    
    .modal-content {
        border-radius: 0.5rem;
    }
    
    .modal-header {
        padding: 0.75rem;
    }
    
    .modal-header h5 {
        font-size: 0.95rem;
    }
    
    .modal-body {
        padding: 0.75rem;
        font-size: 0.85rem;
    }
    
    .modal-footer {
        padding: 0.5rem 0.75rem;
    }
    
    .table {
        font-size: 0.75rem;
    }
    
    .display-1 {
        font-size: 3rem;
    }
    
    .border {
        padding: 0.5rem !important;
    }
    
    .row .col-md-2,
    .row .col-md-4,
    .row .col-md-6,
    .row .col-md-8 {
        margin-bottom: 0.5rem;
    }
    
    img {
        max-width: 100%;
        height: auto;
    }
    
    .text-md-end {
        text-align: left !important;
    }
}

/* Small devices (landscape phones, 576px and up) */
@media (min-width: 576px) and (max-width: 767.98px) {
    .container {
        padding: 0.75rem;
    }
    
    h1, .h1 {
        font-size: 1.4rem;
    }
    
    h2, .h2 {
        font-size: 1.25rem;
    }
    
    .btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body {
        padding: 0.875rem;
        font-size: 0.9rem;
    }
}

/* Medium devices (tablets, 768px and up) */
@media (min-width: 768px) and (max-width: 991.98px) {
    .container {
        padding: 1rem;
    }
    
    .modal-dialog {
        margin: 1rem;
    }
}

/* Large devices (desktops, 992px and up) - default styles */
@media (min-width: 992px) {
    .card {
        transition: all 0.3s ease;
    }
}

/* Ensure images are responsive across all screen sizes */
.img-fluid {
    max-width: 100%;
    height: auto;
}

/* Responsive text alignment */
@media (max-width: 767.98px) {
    .text-md-end {
        text-align: center !important;
        margin-top: 0.5rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .d-flex.justify-content-between .btn {
        margin-top: 0.5rem;
        width: 100%;
    }
}

/* Enhanced responsive grid */
@media (max-width: 767.98px) {
    .row.align-items-center > .col-md-2,
    .row.align-items-center > .col-md-4,
    .row.align-items-center > .col-md-6,
    .row.align-items-center > .col-md-8 {
        text-align: center;
        margin-bottom: 0.5rem;
    }
    
    .row.align-items-center > .col-md-2:last-child {
        margin-bottom: 0;
    }
}

/* Responsive product image sizing */
@media (max-width: 575.98px) {
    .col-md-2 img,
    .col-auto img {
        width: 40px !important;
        height: 40px !important;
        max-height: 40px !important;
    }
    
    .bg-secondary.rounded {
        width: 40px !important;
        height: 40px !important;
    }
}

@media (min-width: 576px) and (max-width: 767.98px) {
    .col-md-2 img,
    .col-auto img {
        width: 50px !important;
        height: 50px !important;
        max-height: 50px !important;
    }
    
    .bg-secondary.rounded {
        width: 50px !important;
        height: 50px !important;
    }
}

/* Responsive spacing */
@media (max-width: 767.98px) {
    .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    .py-5 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }
    
    .mb-4 {
        margin-bottom: 1rem !important;
    }
    
    .mb-3 {
        margin-bottom: 0.75rem !important;
    }
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
    
    // COMPREHENSIVE MODAL POSITIONING SYSTEM
    let modalProcessing = false;
    let currentModal = null;
    let bodyEventsDisabled = false;
    
    // CRITICAL: Force all containers to allow modal overflow
    function ensureModalOverflow() {
        // Prevent any parent containers from clipping modals
        $('.container, .container-fluid, .row, .col-12, .card, .card-body').css({
            'overflow': 'visible !important',
            'position': 'static !important'
        });
        
        // Ensure body allows modal positioning
        $('body').css({
            'position': 'relative',
            'z-index': 'auto'
        });
    }
    
    // CRITICAL: Completely disable all body events that could interfere with modals
    function disableAllBodyEvents() {
        if (bodyEventsDisabled) return;
        
        // Remove all existing event handlers that could cause conflicts
        $('body, html, document, window').off('mousemove.modalFlicker mouseenter.modalFlicker mouseleave.modalFlicker mouseover.modalFlicker mouseout.modalFlicker hover.modalFlicker');
        $(document).off('mousemove.modalFlicker mouseenter.modalFlicker mouseleave.modalFlicker');
        $('body').off('mousemove mouseenter mouseleave mouseover mouseout hover');
        
        // Prevent any new hover or mouse events
        $('body').css('pointer-events', 'auto');
        bodyEventsDisabled = true;
    }
    
    // Initialize complete body event protection immediately
    disableAllBodyEvents();
    
    // Initialize container overflow prevention
    ensureModalOverflow();
    
    // REMOVE ALL BOOTSTRAP DEFAULT MODAL TRIGGERS
    $('.review-btn').each(function() {
        $(this).removeAttr('data-bs-toggle');
        $(this).removeAttr('data-bs-target');
        $(this).removeData('bs-toggle');
        $(this).removeData('bs-target');
    });
    
    // COMPLETE CUSTOM MODAL CONTROL SYSTEM
    $('.review-btn').on('click', function(event) {
        // Prevent any default behavior or propagation
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        
        // Block multiple rapid clicks
        if (modalProcessing) {
            return false;
        }
        modalProcessing = true;
        
        const button = $(this);
        const itemId = button.data('item-id');
        const modalId = '#reviewModal' + itemId;
        const productName = button.data('product-name');
        
        // Force close ANY existing modals immediately
        $('.modal').each(function() {
            const existingModal = bootstrap.Modal.getInstance(this);
            if (existingModal) {
                existingModal.dispose();
            }
            $(this).removeClass('show').hide();
        });
        
        // Remove any existing backdrops
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        
        // Ensure body events stay disabled
        disableAllBodyEvents();
        
        // Ensure containers don't clip the modal
        ensureModalOverflow();
        
        // Wait for complete cleanup before showing new modal
        setTimeout(function() {
            try {
                const modalElement = document.querySelector(modalId);
                if (!modalElement) {
                    console.error('Modal element not found:', modalId);
                    modalProcessing = false;
                    return;
                }
                
                // Update textarea placeholder with product info
                const textarea = modalElement.querySelector('textarea');
                if (textarea && productName) {
                    textarea.setAttribute('placeholder', 
                        `Ceritakan pengalaman Anda menggunakan ${productName}. Apakah produk sesuai ekspektasi? Bagaimana kualitasnya?`);
                }
                
                // Create new modal instance with strict configuration
                currentModal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false,
                    focus: true
                });
                
                // Add comprehensive event handlers for this modal
                modalElement.addEventListener('shown.bs.modal', function() {
                    modalProcessing = false;
                    disableAllBodyEvents();
                    
                    // CRITICAL: Ensure modal is positioned at top level with maximum z-index
                    $(modalElement).css({
                        'position': 'fixed',
                        'top': '0',
                        'left': '0',
                        'width': '100vw',
                        'height': '100vh',
                        'z-index': '10000',
                        'display': 'flex',
                        'align-items': 'center',
                        'justify-content': 'center',
                        'pointer-events': 'auto',
                        'background': 'rgba(0, 0, 0, 0.5)'
                    });
                    
                    // Ensure modal dialog is properly positioned
                    $(modalElement).find('.modal-dialog').css({
                        'position': 'relative',
                        'z-index': '10002',
                        'margin': 'auto',
                        'max-width': '500px',
                        'width': '90%'
                    });
                    
                    // Ensure modal content has proper styling
                    $(modalElement).find('.modal-content').css({
                        'position': 'relative',
                        'z-index': '10003',
                        'background': 'white',
                        'border-radius': '0.5rem',
                        'box-shadow': '0 10px 25px rgba(0, 0, 0, 0.5)'
                    });
                }, { once: true });
                
                modalElement.addEventListener('hidden.bs.modal', function() {
                    modalProcessing = false;
                    currentModal = null;
                    disableAllBodyEvents();
                    
                    // Clean up any remaining modal artifacts
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }, { once: true });
                
                modalElement.addEventListener('hide.bs.modal', function() {
                    if (currentModal) {
                        currentModal = null;
                    }
                });
                
                // Show the modal
                currentModal.show();
                
                // Safety timeout to reset processing flag
                setTimeout(function() {
                    if (modalProcessing) {
                        modalProcessing = false;
                    }
                }, 2000);
                
            } catch (error) {
                console.error('Error showing modal:', error);
                modalProcessing = false;
                currentModal = null;
                disableAllBodyEvents();
            }
        }, 100);
        
        return false;
    });
    
    // PREVENT ALL MOUSE EVENTS FROM AFFECTING MODALS
    $(document).on('mousemove mouseenter mouseleave mouseover mouseout hover', function(event) {
        if ($('.modal.show').length > 0 || currentModal) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    });
    
    // SECURE MODAL CLOSING HANDLERS
    $(document).on('click', '.modal', function(event) {
        if (event.target === this) {
            event.stopPropagation();
            const modal = bootstrap.Modal.getInstance(this);
            if (modal) {
                modal.hide();
            }
        }
    });
    
    // Prevent closing when clicking inside modal content
    $(document).on('click', '.modal-dialog', function(event) {
        event.stopPropagation();
    });
    
    // Secure close button handling
    $(document).on('click', '.modal .btn-close, .modal [data-bs-dismiss="modal"]', function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const modalEl = $(this).closest('.modal')[0];
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    });
    
    // GLOBAL PROTECTION SYSTEM
    // Disable any body events that could interfere with modals
    $('body').on('mousemove mouseenter mouseleave mouseover mouseout hover', function(event) {
        if ($('.modal.show').length > 0 || currentModal) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    });
    
    // Prevent any hover effects on containers when modal is open
    $('.container, .row, .col-12, .card').on('mouseenter mouseleave mouseover mouseout hover', function(event) {
        if ($('.modal.show').length > 0 || currentModal) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    });
    
    // Monitor for unexpected modal state changes and positioning issues
    setInterval(function() {
        // If a modal is supposed to be open but isn't visible, prevent flickering
        if (currentModal && $('.modal.show').length === 0) {
            disableAllBodyEvents();
        }
        
        // Clean up orphaned backdrops
        if ($('.modal.show').length === 0 && $('.modal-backdrop').length > 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        }
        
        // Continuously ensure modal positioning for any open modals
        $('.modal.show').each(function() {
            $(this).css({
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'width': '100vw',
                'height': '100vh',
                'z-index': '10000',
                'display': 'flex'
            });
        });
        
        // Ensure containers don't interfere
        ensureModalOverflow();
    }, 100);
    
    // Final protection: Disable events immediately on page load
    disableAllBodyEvents();
});
</script>
@endpush
@endsection