@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id . ' - Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-receipt text-success me-2"></i>Detail Pesanan #{{ $order->id }}</h2>
                <a href="{{ route('admin.orders') }}" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Pesanan
                </a>
            </div>

            <!-- Order Summary -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <i class="fas fa-shopping-bag text-success me-2"></i>
                                        Informasi Pesanan
                                    </h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge 
                                        @if($order->status === 'pending') bg-warning
                                        @elseif($order->status === 'paid') bg-success
                                        @elseif($order->status === 'shipped') bg-info
                                        @elseif($order->status === 'canceled') bg-danger
                                        @endif
                                        fs-6 px-3 py-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-success mb-2">Informasi Pelanggan:</h6>
                                    <p class="mb-1"><strong>Nama:</strong> {{ $order->user->name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                                    @if($order->user->is_loyal)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-crown me-1"></i>Pelanggan Loyal
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success mb-2">Detail Pesanan:</h6>
                                    <p class="mb-1"><strong>Tanggal Pesanan:</strong> {{ $order->tanggal->format('d M Y, H:i') }}</p>
                                    <p class="mb-1"><strong>Total Pembayaran:</strong> 
                                        <span class="text-success fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </p>
                                    <p class="mb-1"><strong>Jumlah Item:</strong> {{ $order->orderItems->count() }} produk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs text-success me-2"></i>Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($order->status !== 'canceled')
                                <div class="d-grid gap-2">
                                    @if($order->status === 'pending')
                                        <button class="btn btn-success" onclick="updateStatus('paid')">
                                            <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                                        </button>
                                        <button class="btn btn-danger" onclick="updateStatus('canceled')">
                                            <i class="fas fa-times me-2"></i>Batalkan Pesanan
                                        </button>
                                    @elseif($order->status === 'paid')
                                        <button class="btn btn-info" onclick="updateStatus('shipped')">
                                            <i class="fas fa-shipping-fast me-2"></i>Kirim Pesanan
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-danger text-center">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Pesanan telah dibatalkan
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    @if($order->bukti_bayar)
                        <div class="card shadow-sm mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-image text-success me-2"></i>Bukti Pembayaran
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ Storage::url($order->bukti_bayar) }}" 
                                     alt="Bukti Pembayaran" 
                                     class="img-fluid rounded mb-2"
                                     style="max-height: 200px; cursor: pointer;"
                                     onclick="viewFullImage('{{ Storage::url($order->bukti_bayar) }}')">
                                <br>
                                <small class="text-muted">Klik untuk memperbesar</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list text-success me-2"></i>Detail Produk Pesanan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Foto</th>
                                            <th width="150">Produk</th>
                                            <th width="100">Harga</th>
                                            <th width="80">Qty</th>
                                            <th width="120">Subtotal</th>
                                            @if($order->status === 'shipped' && $order->orderItems->where('ulasan', '!=', null)->count() > 0)
                                                <th>Ulasan</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    @if($item->product->foto)
                                                        <img src="{{ Storage::url($item->product->foto) }}" 
                                                             alt="{{ $item->product->nama }}" 
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <h6 class="mb-1">{{ $item->product->nama }}</h6>
                                                   
                                                </td>
                                                <td>
                                                    <span class="text-success">
                                                        Rp {{ number_format($item->sub_total / $item->jumlah_item, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $item->jumlah_item }}x</span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        Rp {{ number_format($item->sub_total, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                @if($order->status === 'shipped' && $order->orderItems->where('ulasan', '!=', null)->count() > 0)
                                                    <td>
                                                        @if($item->ulasan)
                                                            <div class="text-success mb-1">
                                                                <i class="fas fa-comment-alt me-1"></i>
                                                                <small class="fw-bold">Ada Ulasan</small>
                                                            </div>
                                                            <small class="text-muted">{{ Str::limit($item->ulasan, 200) }}</small>
                                                        @else
                                                            <small class="text-muted">Belum ada ulasan</small>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="{{ $order->status === 'shipped' && $order->orderItems->where('ulasan', '!=', null)->count() > 0 ? 5 : 4 }}" 
                                                class="text-end"><strong>Total Pesanan:</strong></td>
                                            <td>
                                                <strong class="text-success fs-5">
                                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                                </strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fullImage" src="" alt="Bukti Pembayaran" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(newStatus) {
    let confirmMessage = '';
    let actionText = '';
    
    switch(newStatus) {
        case 'paid':
            confirmMessage = 'Konfirmasi pembayaran untuk pesanan ini?';
            actionText = 'mengkonfirmasi pembayaran';
            break;
        case 'shipped':
            confirmMessage = 'Tandai pesanan ini sebagai sudah dikirim?';
            actionText = 'mengirim pesanan';
            break;
        case 'canceled':
            confirmMessage = 'Batalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.';
            actionText = 'membatalkan pesanan';
            break;
    }
    
    Swal.fire({
        title: 'Konfirmasi',
        text: confirmMessage,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4CAF50',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: `Sedang ${actionText}`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '{{ route("admin.orders.status", $order) }}',
                method: 'PUT',
                data: {
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status pesanan berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat memperbarui status pesanan.',
                        confirmButtonColor: '#4CAF50'
                    });
                }
            });
        }
    });
}

function viewFullImage(imageSrc) {
    $('#fullImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}

// Auto-refresh status every 30 seconds
setInterval(function() {
    // Optional: Add auto-refresh logic here if needed
}, 30000);
</script>
@endpush
@endsection