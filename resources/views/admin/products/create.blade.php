@extends('layouts.app')

@section('title', 'Tambah Produk - Admin Laravel Barokah')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 text-primary fw-bold mb-0">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </h1>
                    <p class="text-muted mb-0">Lengkapi informasi produk yang akan ditambahkan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informasi Produk
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                <i class="fas fa-tag"></i> Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" 
                                   name="nama" 
                                   value="{{ old('nama') }}" 
                                   required 
                                   placeholder="Masukkan nama produk">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Product Description -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                <i class="fas fa-align-left"></i> Deskripsi Produk <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="4" 
                                      required
                                      placeholder="Masukkan deskripsi detail produk">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Price and Stock -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga" class="form-label">
                                        <i class="fas fa-money-bill"></i> Harga <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control @error('harga') is-invalid @enderror" 
                                               id="harga" 
                                               name="harga" 
                                               value="{{ old('harga') }}" 
                                               required 
                                               min="0"
                                               step="1000"
                                               placeholder="0">
                                    </div>
                                    @error('harga')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stok" class="form-label">
                                        <i class="fas fa-cubes"></i> Stok <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('stok') is-invalid @enderror" 
                                           id="stok" 
                                           name="stok" 
                                           value="{{ old('stok') }}" 
                                           required 
                                           min="0"
                                           placeholder="0">
                                    @error('stok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Photo -->
                        <div class="mb-4">
                            <label for="foto" class="form-label">
                                <i class="fas fa-camera"></i> Foto Produk <span class="text-danger">*</span>
                            </label>
                            <input type="file" 
                                   class="form-control @error('foto') is-invalid @enderror" 
                                   id="foto" 
                                   name="foto" 
                                   accept="image/*"
                                   required
                                   onchange="previewImage(this)">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="image-preview" class="mt-3" style="display: none;">
                                <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Produk
                            </button>
                            <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Help Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb"></i> Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="fas fa-tag"></i> Nama Produk
                        </h6>
                        <p class="text-muted small mb-0">
                            Gunakan nama yang jelas dan mudah dicari oleh pelanggan.
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="fas fa-camera"></i> Foto Produk
                        </h6>
                        <p class="text-muted small mb-0">
                            Gunakan foto berkualitas tinggi dengan pencahayaan yang baik untuk menarik pelanggan.
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </h6>
                        <p class="text-muted small mb-0">
                            Berikan informasi detail tentang fitur, manfaat, dan spesifikasi produk.
                        </p>
                    </div>
                    
                    <div class="mb-0">
                        <h6 class="text-primary">
                            <i class="fas fa-money-bill"></i> Harga & Stok
                        </h6>
                        <p class="text-muted small mb-0">
                            Pastikan harga kompetitif dan stok selalu terupdate untuk menghindari kekecewaan pelanggan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Format number input
    document.getElementById('harga').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });
</script>
@endpush