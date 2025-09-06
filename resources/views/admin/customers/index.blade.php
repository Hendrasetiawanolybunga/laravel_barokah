@extends('layouts.app')

@section('title', 'Kelola Pelanggan - Admin UD. Barokah Jaya Beton')

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
                    <button class="btn btn-success me-2" onclick="openAddCustomerModal()">
                        <i class="fas fa-plus"></i> Tambah Pelanggan
                    </button>
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
                                        <th width="150">Aksi</th>
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
                                                @if($customer->customer && $customer->customer->tgl_lahir)
                                                    <small>{{ $customer->customer->tgl_lahir->translatedFormat('d F Y') }}</small>
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
                                                    {{ $customer->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Edit Customer -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary"
                                                            onclick="openEditCustomerModal({{ $customer->id }})"
                                                            title="Edit Data Pelanggan">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <!-- Delete Customer -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')"
                                                            title="Hapus Pelanggan">
                                                        <i class="fas fa-trash"></i>
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
                            <nav aria-label="Navigasi halaman pelanggan">
                                {{ $customers->links('vendor.pagination.bootstrap-5') }}
                            </nav>
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

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Tambah Pelanggan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCustomerForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="add_email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="add_password" name="password" required minlength="6">
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_no_hp" name="no_hp" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="add_tgl_lahir" name="tgl_lahir" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_pekerjaan" name="pekerjaan" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="add_alamat" name="alamat" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Edit Data Pelanggan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_customer_id" name="customer_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_no_hp" name="no_hp" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_tgl_lahir" name="tgl_lahir" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_pekerjaan" name="pekerjaan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status Bergabung</label>
                                <input type="text" class="form-control" id="edit_created_at" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Data
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pelanggan <strong id="deleteCustomerName"></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data pelanggan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Current customer ID for delete operation
    let currentCustomerId = null;
    
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
    
    function openAddCustomerModal() {
        // Reset form
        document.getElementById('addCustomerForm').reset();
        
        const modal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
        modal.show();
    }
    
    function openEditCustomerModal(userId) {
        showLoading();
        
        // Get customer data
        $.ajax({
            url: `/admin/api/customers`,
            method: 'GET',
            success: function(response) {
                hideLoading();
                
                const customer = response.customers.find(c => c.id === userId);
                if (customer) {
                    // Set basic user info
                    document.getElementById('edit_customer_id').value = customer.id;
                    document.getElementById('edit_name').value = customer.name;
                    document.getElementById('edit_email').value = customer.email;
                    
                    // Set customer profile info
                    if (customer.customer) {
                        document.getElementById('edit_no_hp').value = customer.customer.no_hp || '';
                        document.getElementById('edit_tgl_lahir').value = customer.customer.tgl_lahir || '';
                        document.getElementById('edit_pekerjaan').value = customer.customer.pekerjaan || '';
                        document.getElementById('edit_alamat').value = customer.customer.alamat || '';
                    }
                    
                    // Set created at info
                    if (customer.created_at) {
                        const createdAt = new Date(customer.created_at);
                        document.getElementById('edit_created_at').value = createdAt.toLocaleDateString('id-ID');
                    }
                    
                    // Set form action
                    document.getElementById('editCustomerForm').action = `/admin/customers/${userId}`;
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
                    modal.show();
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                alert('Terjadi kesalahan saat mengambil data customer.');
            }
        });
    }
    
    function deleteCustomer(customerId, customerName) {
        currentCustomerId = customerId;
        document.getElementById('deleteCustomerName').textContent = customerName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteCustomerModal'));
        modal.show();
    }
    
    // Confirm delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!currentCustomerId) return;
        
        showLoading();
        
        $.ajax({
            url: `/admin/customers/${currentCustomerId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function(response) {
                hideLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCustomerModal'));
                modal.hide();
                
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Terjadi kesalahan saat menghapus customer.');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCustomerModal'));
                modal.hide();
                alert('Terjadi kesalahan saat menghapus customer.');
            }
        });
    });
    
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
    
    // Handle add customer form submission
    document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/admin/customers',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                modal.hide();
                
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Terjadi kesalahan saat menambahkan customer.');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMsg = 'Terjadi kesalahan:\n';
                    Object.values(xhr.responseJSON.errors).forEach(function(errors) {
                        errors.forEach(function(error) {
                            errorMsg += '- ' + error + '\n';
                        });
                    });
                    alert(errorMsg);
                } else {
                    alert('Terjadi kesalahan saat menambahkan customer.');
                }
            }
        });
    });
    
    // Handle edit customer form submission
    document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: this.action,
            method: 'PUT',
            data: {
                name: formData.get('name'),
                email: formData.get('email'),
                no_hp: formData.get('no_hp'),
                tgl_lahir: formData.get('tgl_lahir'),
                pekerjaan: formData.get('pekerjaan'),
                alamat: formData.get('alamat'),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('editCustomerModal'));
                modal.hide();
                
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Terjadi kesalahan saat memperbarui data customer.');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMsg = 'Terjadi kesalahan:\n';
                    Object.values(xhr.responseJSON.errors).forEach(function(errors) {
                        errors.forEach(function(error) {
                            errorMsg += '- ' + error + '\n';
                        });
                    });
                    alert(errorMsg);
                } else {
                    alert('Terjadi kesalahan saat memperbarui data customer.');
                }
            }
        });
    });
</script>
@endpush