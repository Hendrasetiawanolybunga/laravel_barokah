@extends('layouts.app')

@section('title', 'Kelola Pesanan - Admin Laravel Barokah')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary fw-bold">
                        <i class="fas fa-shopping-cart"></i> Kelola Pesanan
                    </h1>
                    <p class="text-muted mb-0">Proses dan kelola semua pesanan pelanggan</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">
                        Total: {{ $orders->total() }} Pesanan
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Order Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h5>{{ $orders->where('status', 'pending')->count() }}</h5>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check fa-2x mb-2"></i>
                    <h5>{{ $orders->where('status', 'paid')->count() }}</h5>
                    <small>Dibayar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-shipping-fast fa-2x mb-2"></i>
                    <h5>{{ $orders->where('status', 'shipped')->count() }}</h5>
                    <small>Dikirim</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-times fa-2x mb-2"></i>
                    <h5>{{ $orders->where('status', 'canceled')->count() }}</h5>
                    <small>Dibatalkan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Daftar Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">#ID</th>
                                        <th>Pelanggan</th>
                                        <th width="120">Tanggal</th>
                                        <th width="120">Total</th>
                                        <th width="100">Status</th>
                                        <th width="120">Bukti Bayar</th>
                                        <th width="200">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                    <br>
                                                    <small class="text-info">
                                                        <i class="fas fa-box"></i> {{ $order->orderItems->count() }} item
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $order->tanggal->format('d/m/Y') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $order->tanggal->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($order->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($order->status === 'paid')
                                                    <span class="badge bg-success">Dibayar</span>
                                                @elseif($order->status === 'shipped')
                                                    <span class="badge bg-info">Dikirim</span>
                                                @else
                                                    <span class="badge bg-danger">Dibatalkan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->bukti_bayar)
                                                    <button class="btn btn-outline-primary btn-sm" 
                                                            onclick="viewPaymentProof('{{ asset('storage/' . $order->bukti_bayar) }}')"
                                                            title="Lihat Bukti Bayar">
                                                        <i class="fas fa-image"></i> Lihat
                                                    </button>
                                                @else
                                                    <small class="text-muted">Belum ada</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical w-100" role="group">
                                                    <!-- View Detail -->
                                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                                       class="btn btn-outline-info btn-sm mb-1">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    
                                                    <!-- Status Update -->
                                                    @if($order->status !== 'canceled')
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-primary btn-sm dropdown-toggle w-100" 
                                                                    type="button" data-bs-toggle="dropdown">
                                                                <i class="fas fa-edit"></i> Update Status
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if($order->status === 'pending')
                                                                    <li>
                                                                        <a class="dropdown-item" 
                                                                           onclick="updateStatus({{ $order->id }}, 'paid')">
                                                                            <i class="fas fa-check text-success"></i> Konfirmasi Pembayaran
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" 
                                                                           onclick="updateStatus({{ $order->id }}, 'canceled')">
                                                                            <i class="fas fa-times text-danger"></i> Batalkan
                                                                        </a>
                                                                    </li>
                                                                @elseif($order->status === 'paid')
                                                                    <li>
                                                                        <a class="dropdown-item" 
                                                                           onclick="updateStatus({{ $order->id }}, 'shipped')">
                                                                            <i class="fas fa-shipping-fast text-info"></i> Kirim Pesanan
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pesanan</h5>
                            <p class="text-muted">Pesanan baru akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentProofModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-image"></i> Bukti Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="paymentProofImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 mb-0">Memproses...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewPaymentProof(imageUrl) {
        document.getElementById('paymentProofImage').src = imageUrl;
        const modal = new bootstrap.Modal(document.getElementById('paymentProofModal'));
        modal.show();
    }
    
    function updateStatus(orderId, newStatus) {
        let confirmMessage = '';
        let successMessage = '';
        
        switch(newStatus) {
            case 'paid':
                confirmMessage = 'Konfirmasi pembayaran untuk pesanan ini?';
                successMessage = 'Pembayaran berhasil dikonfirmasi!';
                break;
            case 'shipped':
                confirmMessage = 'Tandai pesanan ini sebagai sudah dikirim?';
                successMessage = 'Pesanan berhasil ditandai sebagai dikirim!';
                break;
            case 'canceled':
                confirmMessage = 'Batalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.';
                successMessage = 'Pesanan berhasil dibatalkan!';
                break;
        }
        
        if (!confirm(confirmMessage)) {
            return;
        }
        
        showLoading();
        
        $.ajax({
            url: `/admin/orders/${orderId}/status`,
            method: 'PUT',
            data: {
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    // Show success message with SweetAlert2 if available, otherwise use alert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: successMessage,
                            icon: 'success',
                            confirmButtonColor: '#4CAF50'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert(successMessage);
                        location.reload();
                    }
                } else {
                    alert(response.message || 'Terjadi kesalahan saat memperbarui status pesanan.');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                let errorMessage = 'Terjadi kesalahan saat memperbarui status pesanan.';
                
                // Try to parse JSON error response
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Show error message with SweetAlert2 if available, otherwise use alert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    alert(errorMessage);
                }
            }
        });
    }
    
    function showLoading() {
        const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
        modal.show();
    }
    
    function hideLoading() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
        if (modal) {
            modal.hide();
        }
    }
</script>
@endpush