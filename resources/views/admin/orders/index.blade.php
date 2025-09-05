@extends('layouts.app')

@section('title', 'Kelola Pesanan - Admin UD. Barokah Jaya Beton')

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
                    <button class="btn btn-success me-2" onclick="openCreateOrderModal()">
                        <i class="fas fa-plus"></i> Buat Pesanan Baru
                    </button>
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
                                                <small>{{ $order->tanggal->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}</small>
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

<!-- Create Order Modal -->
<div class="modal fade" id="createOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Buat Pesanan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createOrderForm">
                @csrf
                <div class="modal-body">
                    <!-- Customer Selection -->
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Pilih Pelanggan <span class="text-danger">*</span></label>
                        <select class="form-select" id="customer_id" name="user_id" required>
                            <option value="">-- Pilih Pelanggan --</option>
                        </select>
                        <div class="form-text">
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="openCreateCustomerFromOrder()">
                                <i class="fas fa-user-plus"></i> Buat Pelanggan Baru
                            </button>
                        </div>
                    </div>

                    <!-- Order Status -->
                    <div class="mb-3">
                        <label for="order_status" class="form-label">Status Pesanan <span class="text-danger">*</span></label>
                        <select class="form-select" id="order_status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="paid" selected>Paid (Dibayar)</option>
                            <option value="shipped">Shipped (Dikirim)</option>
                            <option value="delivered">Delivered (Selesai)</option>
                        </select>
                    </div>

                    <!-- Products Section -->
                    <div class="mb-3">
                        <label class="form-label">Produk <span class="text-danger">*</span></label>
                        <div id="products-container">
                            <div class="product-item border rounded p-3 mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select product-select" name="items[0][product_id]" required>
                                            <option value="">-- Pilih Produk --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control quantity-input" 
                                               name="items[0][quantity]" placeholder="Jumlah" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="product-price text-success fw-bold">Rp 0</div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-product" 
                                                onclick="removeProduct(this)" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addProduct()">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </button>
                    </div>

                    <!-- Total -->
                    <div class="row">
                        <div class="col-md-6 ms-auto">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Pesanan:</strong>
                                        <strong class="text-success" id="order-total">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Buat Pesanan
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
    let customerData = [];
    let productData = [];
    let productIndex = 0;

    function viewPaymentProof(imageUrl) {
        document.getElementById('paymentProofImage').src = imageUrl;
        const modal = new bootstrap.Modal(document.getElementById('paymentProofModal'));
        modal.show();
    }

    function openCreateOrderModal() {
        // Load customers and products
        loadCustomers();
        loadProducts();
        
        // Reset form
        document.getElementById('createOrderForm').reset();
        resetProductsContainer();
        
        const modal = new bootstrap.Modal(document.getElementById('createOrderModal'));
        modal.show();
    }

    function loadCustomers() {
        $.ajax({
            url: '/admin/api/customers',
            method: 'GET',
            success: function(response) {
                customerData = response.customers;
                const select = document.getElementById('customer_id');
                select.innerHTML = '<option value="">-- Pilih Pelanggan --</option>';
                
                response.customers.forEach(function(customer) {
                    const option = document.createElement('option');
                    option.value = customer.id;
                    option.textContent = `${customer.name} (${customer.email})`;
                    select.appendChild(option);
                });
            },
            error: function() {
                alert('Gagal memuat data pelanggan');
            }
        });
    }

    function loadProducts() {
        $.ajax({
            url: '/admin/api/products',
            method: 'GET',
            success: function(response) {
                productData = response.products;
                updateProductSelects();
            },
            error: function() {
                alert('Gagal memuat data produk');
            }
        });
    }

    function updateProductSelects() {
        document.querySelectorAll('.product-select').forEach(function(select) {
            const currentValue = select.value;
            select.innerHTML = '<option value="">-- Pilih Produk --</option>';
            
            productData.forEach(function(product) {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.nama} - Rp ${new Intl.NumberFormat('id-ID').format(product.harga)} (Stok: ${product.stok})`;
                option.dataset.price = product.harga;
                option.dataset.stock = product.stok;
                select.appendChild(option);
            });
            
            if (currentValue) {
                select.value = currentValue;
            }
        });
    }

    function addProduct() {
        productIndex++;
        const container = document.getElementById('products-container');
        const newItem = document.createElement('div');
        newItem.className = 'product-item border rounded p-3 mb-2';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <select class="form-select product-select" name="items[${productIndex}][product_id]" required>
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control quantity-input" 
                           name="items[${productIndex}][quantity]" placeholder="Jumlah" min="1" required>
                </div>
                <div class="col-md-2">
                    <div class="product-price text-success fw-bold">Rp 0</div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-product" 
                            onclick="removeProduct(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        
        // Update product selects and add event listeners
        updateProductSelects();
        addProductEventListeners(newItem);
        
        // Show remove buttons if more than one product
        updateRemoveButtons();
    }

    function removeProduct(button) {
        button.closest('.product-item').remove();
        updateRemoveButtons();
        calculateTotal();
    }

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.product-item');
        items.forEach(function(item, index) {
            const removeBtn = item.querySelector('.remove-product');
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    function resetProductsContainer() {
        productIndex = 0;
        const container = document.getElementById('products-container');
        container.innerHTML = `
            <div class="product-item border rounded p-3 mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-select product-select" name="items[0][product_id]" required>
                            <option value="">-- Pilih Produk --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control quantity-input" 
                               name="items[0][quantity]" placeholder="Jumlah" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <div class="product-price text-success fw-bold">Rp 0</div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-product" 
                                onclick="removeProduct(this)" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add event listeners to the initial product item
        addProductEventListeners(container.querySelector('.product-item'));
    }

    function addProductEventListeners(productItem) {
        const productSelect = productItem.querySelector('.product-select');
        const quantityInput = productItem.querySelector('.quantity-input');
        const priceDiv = productItem.querySelector('.product-price');

        productSelect.addEventListener('change', function() {
            updateProductPrice(this, priceDiv, quantityInput);
        });

        quantityInput.addEventListener('input', function() {
            updateProductPrice(productSelect, priceDiv, this);
        });
    }

    function updateProductPrice(productSelect, priceDiv, quantityInput) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = selectedOption.dataset.price || 0;
        const quantity = quantityInput.value || 0;
        const total = price * quantity;
        
        priceDiv.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-item').forEach(function(item) {
            const productSelect = item.querySelector('.product-select');
            const quantityInput = item.querySelector('.quantity-input');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption.dataset.price || 0;
            const quantity = quantityInput.value || 0;
            total += price * quantity;
        });
        
        document.getElementById('order-total').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
    }

    function openCreateCustomerFromOrder() {
        // This would open the customer creation modal
        // For now, just alert the user
        alert('Fitur tambah pelanggan akan segera tersedia. Silakan gunakan menu Pelanggan untuk menambah pelanggan baru.');
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

    // Handle create order form submission
    document.getElementById('createOrderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/admin/orders',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('createOrderModal'));
                modal.hide();
                
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Pesanan berhasil dibuat!',
                            icon: 'success',
                            confirmButtonColor: '#4CAF50'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert('Pesanan berhasil dibuat!');
                        location.reload();
                    }
                } else {
                    alert(response.message || 'Terjadi kesalahan saat membuat pesanan.');
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
                    alert('Terjadi kesalahan saat membuat pesanan.');
                }
            }
        });
    });

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to initial product item if it exists
        const initialProductItem = document.querySelector('.product-item');
        if (initialProductItem) {
            addProductEventListeners(initialProductItem);
        }
    });
</script>
@endpush