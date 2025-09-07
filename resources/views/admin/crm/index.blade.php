@extends('layouts.app')

@section('title', 'CRM Dashboard - UD. Barokah Jaya Beton')

@section('content')
<div class="container-fluid py-4 admin-dashboard-container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-primary fw-bold">
                <i class="fas fa-heart"></i> Manajemen CRM
            </h1>
            <p class="text-muted mb-0">Kelola hubungan pelanggan dan program loyalitas</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- CRM Header with Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users"></i> Daftar Pelanggan
                        </h5>
                        <form action="{{ route('admin.crm.index') }}" method="GET" class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari pelanggan...">
                                <button class="btn btn-primary" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CRM Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Pelanggan Loyal</h6>
                    <h2>{{ $loyalCustomers->count() }}</h2>
                    <small>Total belanja > Rp 5.000.000</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Ulang Tahun Bulan Ini</h6>
                    <h2>{{ $birthdayCustomers->count() }}</h2>
                    <small>Berulang tahun {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('F') }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Loyal & Ulang Tahun</h6>
                    <h2>{{ $loyalWithBirthday->count() }}</h2>
                    <small>Kombinasi kedua kriteria</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pelanggan Loyal -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-trophy"></i> Pelanggan Loyal</h5>
        </div>
        <div class="card-body">
            @if($loyalCustomers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
<th>Email</th>
<th>Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loyalCustomers as $customer)
                                <tr>
                                    <td><strong>{{ $customer->name }}</strong></td>
                                    <td>{{ $customer->email }}</td>
                                    <td class="text-success">Rp {{ number_format($customer->total_spending, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada pelanggan loyal</p>
            @endif
        </div>
    </div>

    <!-- Pelanggan Ulang Tahun -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-birthday-cake"></i> Ulang Tahun Bulan Ini</h5>
        </div>
        <div class="card-body">
            @if($birthdayCustomers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
<th>Email</th>
<th>Tanggal Lahir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($birthdayCustomers as $customer)
                                <tr class="{{ $customer->is_birthday_today ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $customer->name }}</strong>
                                        @if($customer->is_birthday_today)
                                            <span class="badge bg-warning text-dark">Hari Ini!</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->birthday_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Tidak ada pelanggan berulang tahun bulan ini</p>
            @endif
        </div>
    </div>

    <!-- Pelanggan VIP -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-star"></i> Pelanggan VIP (Loyal & Ulang Tahun hari ini)</h5>
        </div>
        <div class="card-body">
            @if($combinedCustomers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
<th>Email</th>
<th>Total Belanja</th>
<th>Status</th>
<th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($combinedCustomers as $customer)
                                <tr class="table-info">
                                    <td>
                                        <strong>{{ $customer->name }}</strong>
                                        <span class="badge bg-success">VIP</span>
                                    </td>
                                    <td>{{ $customer->email }}</td>
                                    <td class="text-success">Rp {{ number_format($customer->total_spending, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-success">Loyal</span>
                                        <span class="badge bg-warning text-dark">Birthday</span>
                                    </td>
                                    <td>
                                        {{-- <button class="btn btn-primary btn-sm send-message-btn"
                                                data-customer-id="{{ $customer->id }}"
                                                data-customer-name="{{ $customer->name }}">
                                            <i class="fas fa-envelope"></i> Pesan
                                        </button> --}}
                                        <button class="btn btn-success btn-sm set-discount-btn"
                                                data-customer-id="{{ $customer->id }}"
                                                data-customer-name="{{ $customer->name }}">
                                            <i class="fas fa-percent"></i> Diskon
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Tidak ada pelanggan VIP bulan ini</p>
            @endif
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-envelope"></i> Kirim Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendMessageForm">
                @csrf
                <input type="hidden" id="messageCustomerId">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Kepada:</strong> <span id="messageCustomerName"></span>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Isi Pesan</label>
                        <textarea class="form-control" id="messageContent" name="message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Set Discount Modal -->
<div class="modal fade" id="setDiscountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-percent"></i> Tetapkan Diskon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="setDiscountForm">
                @csrf
                <input type="hidden" id="discountCustomerId" name="user_id">
                <div class="modal-body">
                    <div class="alert alert-success">
                        <strong>Untuk:</strong> <span id="discountCustomerName"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productSelect" class="form-label">Pilih Produk <span class="text-danger">*</span></label>
                                <select class="form-select" id="productSelect" name="product_id" required>
                                    <option value="">Pilih produk...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discountPercent" class="form-label">Persentase Diskon (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="discountPercent" name="persen_diskon" min="1" max="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expiresAt" class="form-label">Masa Berlaku Hingga</label>
                                <input type="datetime-local" class="form-control" id="expiresAt" name="expires_at">
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Kosongkan untuk otomatis 2 hari ke depan
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Opsi Pesan</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autoMessage" name="auto_message" value="1">
                                    <label class="form-check-label" for="autoMessage">
                                        <i class="fas fa-magic"></i> Buat pesan otomatis
                                    </label>
                                </div>
                                <button type="button" class="btn btn-outline-info btn-sm mt-2" id="previewMessageBtn" disabled>
                                    <i class="fas fa-eye"></i> Preview Pesan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adminNote" class="form-label">Catatan Admin (Opsional)</label>
                        <textarea class="form-control" id="adminNote" name="admin_note" rows="2" placeholder="Catatan internal tentang diskon ini..."></textarea>
                    </div>
                    
                    <!-- Message Preview Area -->
                    <div id="messagePreviewArea" class="alert alert-info" style="display: none;">
                        <h6><i class="fas fa-envelope"></i> Preview Pesan Otomatis:</h6>
                        <div id="messagePreviewContent" class="border p-2 rounded bg-light"></div>
                        <div class="mt-2">
                            <small class="text-muted">Berlaku hingga: <span id="previewExpiryDate"></span></small>
                        </div>
                    </div>
                    
                    <!-- Custom Message Area -->
                    <div id="customMessageArea" class="mb-3" style="display: none;">
                        <label for="customMessage" class="form-label">
                            <i class="fas fa-edit"></i> Pesan Kustom (Opsional)
                        </label>
                        <textarea class="form-control" id="customMessage" name="custom_message" rows="3" placeholder="Tulis pesan kustom atau biarkan kosong untuk menggunakan pesan otomatis..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Tetapkan Diskon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .admin-dashboard-container {
        padding-left: 3rem;
        padding-right: 3rem;
    }
    
    .card {
        border-radius: 15px;
        transition: transform 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    @media (max-width: 767.98px) {
        .admin-dashboard-container {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let products = [];
    
    // Load products
    fetch('/admin/crm/products')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                products = data.products;
                const select = document.getElementById('productSelect');
                select.innerHTML = '<option value="">Pilih produk...</option>';
                products.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.nama} - Rp ${new Intl.NumberFormat('id-ID').format(product.harga)}`;
                    option.dataset.productName = product.nama;
                    option.dataset.productPrice = product.harga;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            Swal.fire('Error', 'Gagal memuat daftar produk', 'error');
        });
    
    // Auto-message checkbox handler
    const autoMessageCheckbox = document.getElementById('autoMessage');
    const previewMessageBtn = document.getElementById('previewMessageBtn');
    const messagePreviewArea = document.getElementById('messagePreviewArea');
    const customMessageArea = document.getElementById('customMessageArea');
    
    autoMessageCheckbox.addEventListener('change', function() {
        previewMessageBtn.disabled = !this.checked;
        if (!this.checked) {
            messagePreviewArea.style.display = 'none';
            customMessageArea.style.display = 'none';
        } else {
            customMessageArea.style.display = 'block';
        }
    });
    
    // Preview message button handler
    previewMessageBtn.addEventListener('click', function() {
        const customerId = document.getElementById('discountCustomerId').value;
        const productId = document.getElementById('productSelect').value;
        const discountPercent = document.getElementById('discountPercent').value;
        const expiresAt = document.getElementById('expiresAt').value;
        
        if (!productId || !discountPercent) {
            Swal.fire('Peringatan!', 'Silakan pilih produk dan masukkan persentase diskon terlebih dahulu.', 'warning');
            return;
        }
        
        // Show loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
        this.disabled = true;
        
        const formData = new FormData();
        formData.append('user_id', customerId);
        formData.append('product_id', productId);
        formData.append('persen_diskon', discountPercent);
        if (expiresAt) {
            formData.append('expires_at', expiresAt);
        }
        
        fetch('/admin/crm/preview-message', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('messagePreviewContent').innerHTML = data.message;
                document.getElementById('previewExpiryDate').textContent = data.expires_at;
                messagePreviewArea.style.display = 'block';
            } else {
                Swal.fire('Gagal!', data.message || 'Gagal membuat preview pesan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat membuat preview pesan', 'error');
        })
        .finally(() => {
            // Restore button state
            this.innerHTML = originalText;
            this.disabled = false;
        });
    });
    
    // Set default expiration date to 2 days from now dengan timezone Indonesia
    function setDefaultExpirationDate() {
        const expiresAtInput = document.getElementById('expiresAt');
        if (!expiresAtInput.value) {
            // Menggunakan timezone Indonesia (UTC+7)
            const now = new Date();
            const indonesiaTime = new Date(now.getTime() + (7 * 60 * 60 * 1000)); // UTC+7
            indonesiaTime.setDate(indonesiaTime.getDate() + 2);
            
            const year = indonesiaTime.getFullYear();
            const month = String(indonesiaTime.getMonth() + 1).padStart(2, '0');
            const day = String(indonesiaTime.getDate()).padStart(2, '0');
            const hours = String(indonesiaTime.getHours()).padStart(2, '0');
            const minutes = String(indonesiaTime.getMinutes()).padStart(2, '0');
            expiresAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    }
    
    // Send Message Modal
    document.querySelectorAll('.send-message-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('messageCustomerId').value = this.getAttribute('data-customer-id');
            document.getElementById('messageCustomerName').textContent = this.getAttribute('data-customer-name');
            // Reset form
            document.getElementById('sendMessageForm').reset();
            new bootstrap.Modal(document.getElementById('sendMessageModal')).show();
        });
    });
    
    // Set Discount Modal
    document.querySelectorAll('.set-discount-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = document.getElementById('setDiscountModal');
            document.getElementById('discountCustomerId').value = this.getAttribute('data-customer-id');
            document.getElementById('discountCustomerName').textContent = this.getAttribute('data-customer-name');
            
            // Reset form and hide preview areas
            document.getElementById('setDiscountForm').reset();
            messagePreviewArea.style.display = 'none';
            customMessageArea.style.display = 'none';
            autoMessageCheckbox.checked = false;
            previewMessageBtn.disabled = true;
            
            // Set default expiration date
            setDefaultExpirationDate();
            
            new bootstrap.Modal(modal).show();
        });
    });
    
    // Send Message Form
    document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const customerId = document.getElementById('messageCustomerId').value;
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        submitBtn.disabled = true;
        
        fetch(`/admin/crm/${customerId}/message`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('sendMessageModal')).hide();
                    this.reset();
                });
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat mengirim pesan', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Set Discount Form
    document.getElementById('setDiscountForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const customerId = document.getElementById('discountCustomerId').value;
        const productId = document.getElementById('productSelect').value;
        const discountPercent = document.getElementById('discountPercent').value;
        
        console.log('Set Discount Form - Debug Info:', {
            customerId: customerId,
            productId: productId,
            discountPercent: discountPercent,
            formData: new FormData(this)
        });
        
        if (!customerId) {
            Swal.fire('Error!', 'Customer ID tidak ditemukan', 'error');
            return;
        }
        
        if (!productId) {
            Swal.fire('Peringatan!', 'Silakan pilih produk', 'warning');
            return;
        }
        
        if (!discountPercent || discountPercent < 1 || discountPercent > 100) {
            Swal.fire('Peringatan!', 'Persentase diskon harus antara 1-100%', 'warning');
            return;
        }
        
        const formData = new FormData(this);
        const url = `/admin/crm/${customerId}/discount`;
        
        console.log('Sending AJAX request to:', url);
        console.log('Form data entries:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        submitBtn.disabled = true;
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Response is not JSON:', contentType);
                throw new Error('Server did not return JSON response');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('setDiscountModal')).hide();
                    this.reset();
                    messagePreviewArea.style.display = 'none';
                    customMessageArea.style.display = 'none';
                    
                    // Optionally refresh the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                });
            } else {
                // Handle validation errors
                if (data.errors) {
                    let errorMessages = [];
                    for (let field in data.errors) {
                        errorMessages.push(...data.errors[field]);
                    }
                    Swal.fire('Validasi Gagal!', errorMessages.join('\n'), 'error');
                } else {
                    let errorMessage = data.message || 'Terjadi kesalahan saat menetapkan diskon';
                    if (data.debug) {
                        console.error('Server debug info:', data.debug);
                        errorMessage += '\n\nDebug info (check console for details)';
                    }
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Network/Parse Error Details:', {
                error: error,
                message: error.message,
                stack: error.stack,
                url: url,
                timestamp: new Date().toISOString()
            });
            
            let errorMessage = 'Terjadi kesalahan jaringan saat menetapkan diskon';
            
            if (error.message.includes('HTTP')) {
                errorMessage = `Kesalahan server: ${error.message}`;
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Server mengembalikan respons yang tidak valid (bukan JSON)';
            } else {
                errorMessage = `Kesalahan jaringan: ${error.message}`;
            }
            
            Swal.fire({
                title: 'Error!',
                text: errorMessage + '\n\nSilakan periksa console browser untuk detail lebih lanjut.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush