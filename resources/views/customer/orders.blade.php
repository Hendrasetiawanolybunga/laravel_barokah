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
                                                                <button class="btn btn-sm btn-outline-success" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#reviewModal{{ $item->id }}">
                                                                    <i class="fas fa-star me-1"></i>Review
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
                                                <div class="modal fade" id="reviewModal{{ $item->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-star text-warning me-2"></i>
                                                                    Review Produk: {{ $item->product->nama }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('customer.orders.review', $item->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="ulasan{{ $item->id }}" class="form-label">Ulasan Produk</label>
                                                                        <textarea class="form-control" id="ulasan{{ $item->id }}" name="ulasan" 
                                                                                  rows="4" placeholder="Berikan ulasan Anda tentang produk ini..." required></textarea>
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
});
</script>
@endpush
@endsection