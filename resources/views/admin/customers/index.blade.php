@extends('layouts.app')

@section('title', 'Kelola Pelanggan - Admin Laravel Barokah')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary fw-bold">
                        <i class="fas fa-users"></i> Kelola Pelanggan (CRM)
                    </h1>
                    <p class="text-muted mb-0">Manajemen pelanggan dan program loyalitas</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">
                        Total: {{ $customers->total() }} Pelanggan
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

    <!-- Customer Management Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Daftar Pelanggan
                    </h5>
                </div>
                <div class="card-body">
                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">#ID</th>
                                        <th>Informasi Pelanggan</th>
                                        <th width="120">Tgl Lahir</th>
                                        <th width="150">Kontak</th>
                                        <th width="100">Status</th>
                                        <th width="120">Bergabung</th>
                                        <th width="200">Aksi CRM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $customer->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $customer->name }}</div>
                                                    <small class="text-muted">{{ $customer->email }}</small>
                                                    @if($customer->customer)
                                                        <br>
                                                        <small class="text-info">
                                                            <i class="fas fa-briefcase"></i> {{ $customer->customer->pekerjaan }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-map-marker-alt"></i> 
                                                            {{ Str::limit($customer->customer->alamat, 30) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($customer->customer)
                                                    <small>{{ $customer->customer->tgl_lahir->format('d/m/Y') }}</small>
                                                    <br>
                                                    <small class="text-muted">
                                                        ({{ $customer->customer->tgl_lahir->age }} tahun)
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer->customer)
                                                    <small>
                                                        <i class="fas fa-phone"></i> {{ $customer->customer->no_hp }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer->is_loyal)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star"></i> Loyal
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Regular</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $customer->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical w-100" role="group">
                                                    <!-- Loyalty Toggle -->
                                                    <button type="button" 
                                                            class="btn btn-sm {{ $customer->is_loyal ? 'btn-warning' : 'btn-outline-warning' }} mb-1"
                                                            onclick="toggleLoyalty({{ $customer->id }}, {{ $customer->is_loyal ? 'false' : 'true' }})"
                                                            title="{{ $customer->is_loyal ? 'Hapus Status Loyal' : 'Jadikan Member Loyal' }}">
                                                        <i class="fas fa-star"></i> 
                                                        {{ $customer->is_loyal ? 'Hapus Loyal' : 'Set Loyal' }}
                                                    </button>
                                                    
                                                    <!-- Message Management -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary"
                                                            onclick="openMessageModal({{ $customer->id }}, '{{ $customer->name }}', '{{ addslashes($customer->message ?? '') }}')"
                                                            title="Kirim/Edit Pesan">
                                                        <i class="fas fa-envelope"></i> Pesan
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $customers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pelanggan terdaftar</h5>
                            <p class="text-muted">Pelanggan baru akan muncul di sini setelah registrasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-envelope"></i> Kelola Pesan Pelanggan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="messageForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pelanggan:</label>
                        <p id="customerName" class="text-muted mb-3"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">
                            <i class="fas fa-comment"></i> Pesan Khusus
                        </label>
                        <textarea class="form-control" 
                                  id="message" 
                                  name="message" 
                                  rows="4" 
                                  placeholder="Masukkan pesan khusus untuk pelanggan (misal: ucapan ulang tahun, diskon khusus, dll)"></textarea>
                        <div class="form-text">
                            Kosongkan untuk menghapus pesan. Pesan akan ditampilkan di beranda pelanggan.
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb"></i> Tips Pesan Efektif:
                        </h6>
                        <ul class="mb-0 small">
                            <li>Gunakan nama pelanggan untuk personalisasi</li>
                            <li>Berikan informasi promo atau diskon khusus</li>
                            <li>Ucapan selamat ulang tahun atau hari spesial</li>
                            <li>Notifikasi produk baru yang sesuai minat</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pesan
                    </button>
                </div>
            </form>
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
    function toggleLoyalty(userId, newStatus) {
        showLoading();
        
        $.ajax({
            url: `/admin/customers/${userId}/loyalty`,
            method: 'PUT',
            data: {
                is_loyal: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
                location.reload();
            },
            error: function(xhr, status, error) {
                hideLoading();
                alert('Terjadi kesalahan saat memperbarui status loyalty.');
            }
        });
    }
    
    function openMessageModal(userId, customerName, currentMessage) {
        document.getElementById('customerName').textContent = customerName;
        document.getElementById('message').value = currentMessage;
        document.getElementById('messageForm').action = `/admin/customers/${userId}/message`;
        
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();
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
    
    // Handle message form submission
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: this.action,
            method: 'PUT',
            data: {
                message: formData.get('message'),
                _token: formData.get('_token')
            },
            success: function(response) {
                hideLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('messageModal'));
                modal.hide();
                location.reload();
            },
            error: function(xhr, status, error) {
                hideLoading();
                alert('Terjadi kesalahan saat menyimpan pesan.');
            }
        });
    });
</script>
@endpush