@extends('layouts.app')

@section('title', 'Kelola Produk - Admin Laravel Barokah')

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
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </a>
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

    <!-- Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Daftar Produk
                    </h5>
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
                                                    {{ $product->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
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
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada produk</h5>
                            <p class="text-muted">Mulai dengan menambahkan produk pertama Anda</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Produk Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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
</script>
@endpush