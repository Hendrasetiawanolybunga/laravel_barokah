@extends('layouts.app')

@section('title', 'Kelola Produk - Admin UD. Barokah Jaya Beton')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary fw-bold">
                        <i class="fas fa-box"></i> Kelola Produk
                    </h1>
                    <p class="text-muted mb-0">Kelola semua produk dalam toko Anda</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </button>
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

    <!-- Products Table Header with Search -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Daftar Produk
                        </h5>
                        <form action="{{ route('admin.products') }}" method="GET" class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari produk...">
                                <button class="btn btn-primary" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">#ID</th>
                                        <th width="100">Foto</th>
                                        <th>Nama Produk</th>
                                        <th width="120">Harga</th>
                                        <th width="80">Stok</th>
                                        <th width="120">Dibuat</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $product->id }}</span>
                                            </td>
                                            <td>
                                                @if($product->foto)
                                                    <img src="{{ asset('storage/' . $product->foto) }}" 
                                                         alt="{{ $product->nama }}" 
                                                         class="img-thumbnail"
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light border d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $product->nama }}</div>
                                                    <small class="text-muted">
                                                        {{ Str::limit($product->deskripsi, 50) }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($product->stok > 0)
                                                    <span class="badge bg-success">{{ $product->stok }}</span>
                                                @else
                                                    <span class="badge bg-danger">Habis</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $product->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm"
                                                            onclick="openEditProductModal({{ $product->id }})"
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="confirmDelete({{ $product->id }})"
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Hidden Delete Form -->
                                                <form id="delete-form-{{ $product->id }}" 
                                                      action="{{ route('admin.products.delete', $product) }}" 
                                                      method="POST" 
                                                      style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Navigasi halaman produk">
                                {{ $products->links('vendor.pagination.bootstrap-5') }}
                            </nav>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada produk</h5>
                            <p class="text-muted">Mulai dengan menambahkan produk pertama Anda</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                                <i class="fas fa-plus"></i> Tambah Produk Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProductModalLabel">
                    <i class="fas fa-plus text-primary"></i> Tambah Produk Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="create_nama" class="form-label">
                                    <i class="fas fa-tag"></i> Nama Produk <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="create_nama" name="nama" required 
                                       placeholder="Masukkan nama produk">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_harga" class="form-label">
                                    <i class="fas fa-money-bill"></i> Harga <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="create_harga" name="harga" required 
                                           min="0" step="1000" placeholder="0">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_stok" class="form-label">
                                    <i class="fas fa-cubes"></i> Stok <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="create_stok" name="stok" required 
                                       min="0" placeholder="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="create_deskripsi" class="form-label">
                            <i class="fas fa-align-left"></i> Deskripsi Produk <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="create_deskripsi" name="deskripsi" rows="3" required
                                  placeholder="Masukkan deskripsi detail produk"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="create_foto" class="form-label">
                            <i class="fas fa-camera"></i> Foto Produk <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="create_foto" name="foto" accept="image/*" required
                               onchange="previewCreateImage(this)">
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                        </div>
                        <div class="invalid-feedback"></div>
                        
                        <!-- Image Preview -->
                        <div id="create-image-preview" class="mt-3" style="display: none;">
                            <img id="create-preview-img" src="" alt="Preview" class="img-thumbnail" 
                                 style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">
                    <i class="fas fa-edit text-warning"></i> Edit Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_product_id" name="product_id">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_nama" class="form-label">
                                    <i class="fas fa-tag"></i> Nama Produk <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required 
                                       placeholder="Masukkan nama produk">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_harga" class="form-label">
                                    <i class="fas fa-money-bill"></i> Harga <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="edit_harga" name="harga" required 
                                           min="0" step="1000" placeholder="0">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_stok" class="form-label">
                                    <i class="fas fa-cubes"></i> Stok <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="edit_stok" name="stok" required 
                                       min="0" placeholder="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">
                            <i class="fas fa-align-left"></i> Deskripsi Produk <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3" required
                                  placeholder="Masukkan deskripsi detail produk"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_foto" class="form-label">
                                    <i class="fas fa-camera"></i> Foto Produk
                                </label>
                                <input type="file" class="form-control" id="edit_foto" name="foto" accept="image/*"
                                       onchange="previewEditImage(this)">
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Biarkan kosong jika tidak ingin mengubah foto.
                                </div>
                                <div class="invalid-feedback"></div>
                                
                                <!-- Image Preview -->
                                <div id="edit-image-preview" class="mt-3" style="display: none;">
                                    <img id="edit-preview-img" src="" alt="Preview" class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Foto Saat Ini</label>
                                <div id="current-product-image">
                                    <!-- Current image will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('delete-form-' + productId).submit();
        }
    }
    
    // Preview image for create form
    function previewCreateImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('create-preview-img').src = e.target.result;
                document.getElementById('create-image-preview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Preview image for edit form
    function previewEditImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit-preview-img').src = e.target.result;
                document.getElementById('edit-image-preview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Open edit product modal
    function openEditProductModal(productId) {
        // Clear previous form data
        document.getElementById('editProductForm').reset();
        document.getElementById('edit-image-preview').style.display = 'none';
        
        // Clear previous validation errors
        document.querySelectorAll('#editProductModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Fetch product data
        fetch(`/admin/api/products/${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.product;
                    
                    // Populate form fields
                    document.getElementById('edit_product_id').value = product.id;
                    document.getElementById('edit_nama').value = product.nama;
                    document.getElementById('edit_harga').value = product.harga;
                    document.getElementById('edit_stok').value = product.stok;
                    document.getElementById('edit_deskripsi').value = product.deskripsi;
                    
                    // Display current image
                    const currentImageDiv = document.getElementById('current-product-image');
                    if (product.foto) {
                        currentImageDiv.innerHTML = `
                            <img src="/storage/${product.foto}" alt="${product.nama}" 
                                 class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        `;
                    } else {
                        currentImageDiv.innerHTML = `
                            <div class="bg-light border d-flex align-items-center justify-content-center" 
                                 style="width: 200px; height: 200px;">
                                <i class="fas fa-image text-muted fa-2x"></i>
                            </div>
                        `;
                    }
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data produk');
            });
    }
    
    // Handle create product form submission
    document.getElementById('createProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        
        // Clear previous validation errors
        document.querySelectorAll('#createProductModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            el.nextElementSibling.textContent = '';
        });
        
        fetch('{{ route("admin.products.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and reload page
                const modal = bootstrap.Modal.getInstance(document.getElementById('createProductModal'));
                modal.hide();
                
                // Show success message and reload
                alert('Produk berhasil ditambahkan!');
                window.location.reload();
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(`create_${field}`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.errors[field][0];
                            }
                        }
                    });
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan produk');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // Handle edit product form submission
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const productId = document.getElementById('edit_product_id').value;
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupdate...';
        
        // Clear previous validation errors
        document.querySelectorAll('#editProductModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            el.nextElementSibling.textContent = '';
        });
        
        fetch(`/admin/products/${productId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and reload page
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                modal.hide();
                
                // Show success message and reload
                alert('Produk berhasil diupdate!');
                window.location.reload();
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(`edit_${field}`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.errors[field][0];
                            }
                        }
                    });
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate produk');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // Clear form when modals are hidden
    document.getElementById('createProductModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('createProductForm').reset();
        document.getElementById('create-image-preview').style.display = 'none';
        document.querySelectorAll('#createProductModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    });
    
    document.getElementById('editProductModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('editProductForm').reset();
        document.getElementById('edit-image-preview').style.display = 'none';
        document.querySelectorAll('#editProductModal .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    });
</script>
@endpush