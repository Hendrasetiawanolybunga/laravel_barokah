@extends('layouts.app')

@section('title', 'Edit Produk - Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit text-success me-2"></i>Edit Produk</h2>
                <a href="{{ route('admin.products') }}" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Produk
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>Form Edit Produk
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Terjadi kesalahan:</h6>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype=\"multipart/form-data\">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class=\"col-md-6\">
                                        <div class=\"mb-3\">
                                            <label for=\"nama\" class=\"form-label\">Nama Produk *</label>
                                            <input type=\"text\" class=\"form-control\" id=\"nama\" name=\"nama\" 
                                                   value=\"{{ old('nama', $product->nama) }}\" required>
                                        </div>
                                    </div>
                                    
                                    <div class=\"col-md-6\">
                                        <div class=\"mb-3\">
                                            <label for=\"harga\" class=\"form-label\">Harga *</label>
                                            <div class=\"input-group\">
                                                <span class=\"input-group-text\">Rp</span>
                                                <input type=\"number\" class=\"form-control\" id=\"harga\" name=\"harga\" 
                                                       value=\"{{ old('harga', $product->harga) }}\" min=\"0\" step=\"1000\" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class=\"row\">
                                    <div class=\"col-md-6\">
                                        <div class=\"mb-3\">
                                            <label for=\"stok\" class=\"form-label\">Stok *</label>
                                            <input type=\"number\" class=\"form-control\" id=\"stok\" name=\"stok\" 
                                                   value=\"{{ old('stok', $product->stok) }}\" min=\"0\" required>
                                        </div>
                                    </div>
                                    
                                    <div class=\"col-md-6\">
                                        <div class=\"mb-3\">
                                            <label for=\"foto\" class=\"form-label\">Foto Produk</label>
                                            <input type=\"file\" class=\"form-control\" id=\"foto\" name=\"foto\" 
                                                   accept=\"image/*\">
                                            <small class=\"text-muted\">Biarkan kosong jika tidak ingin mengubah foto</small>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($product->foto)
                                <div class=\"row\">
                                    <div class=\"col-md-6\">
                                        <div class=\"mb-3\">
                                            <label class=\"form-label\">Foto Saat Ini</label>
                                            <div class=\"border rounded p-2\">
                                                <img src=\"{{ Storage::url($product->foto) }}\" 
                                                     alt=\"{{ $product->nama }}\" 
                                                     class=\"img-fluid rounded\" 
                                                     style=\"max-height: 200px;\">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <div class=\"mb-3\">
                                    <label for=\"deskripsi\" class=\"form-label\">Deskripsi Produk *</label>
                                    <textarea class=\"form-control\" id=\"deskripsi\" name=\"deskripsi\" rows=\"4\" required>{{ old('deskripsi', $product->deskripsi) }}</textarea>
                                </div>
                                
                                <div class=\"d-flex justify-content-between\">
                                    <div>
                                        <button type=\"button\" class=\"btn btn-danger\" data-bs-toggle=\"modal\" data-bs-target=\"#deleteModal\">
                                            <i class=\"fas fa-trash me-2\"></i>Hapus Produk
                                        </button>
                                    </div>
                                    <div>
                                        <a href=\"{{ route('admin.products') }}\" class=\"btn btn-secondary me-2\">
                                            <i class=\"fas fa-times me-2\"></i>Batal
                                        </a>
                                        <button type=\"submit\" class=\"btn btn-success\">
                                            <i class=\"fas fa-save me-2\"></i>Update Produk
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class=\"modal fade\" id=\"deleteModal\" tabindex=\"-1\">
    <div class=\"modal-dialog\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <h5 class=\"modal-title\">
                    <i class=\"fas fa-exclamation-triangle text-danger me-2\"></i>
                    Konfirmasi Hapus Produk
                </h5>
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>
            </div>
            <div class=\"modal-body\">
                <p>Apakah Anda yakin ingin menghapus produk <strong>{{ $product->nama }}</strong>?</p>
                <p class=\"text-danger\"><i class=\"fas fa-exclamation-circle me-1\"></i>Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class=\"modal-footer\">
                <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Batal</button>
                <form action=\"{{ route('admin.products.delete', $product->id) }}\" method=\"POST\" class=\"d-inline\">
                    @csrf
                    @method('DELETE')
                    <button type=\"submit\" class=\"btn btn-danger\">
                        <i class=\"fas fa-trash me-2\"></i>Ya, Hapus Produk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Preview uploaded image
    $('#foto').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $('#imagePreview');
                if (preview.length === 0) {
                    $('#foto').parent().append(`
                        <div id=\"imagePreview\" class=\"mt-2\">
                            <img src=\"\" class=\"img-fluid rounded\" style=\"max-height: 150px;\">
                        </div>
                    `);
                }
                $('#imagePreview img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Format harga input
    $('#harga').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(value);
    });
    
    // Show success message if available
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