@extends('layouts.app')

@section('title', 'Riwayat CRM - UD. Barokah Jaya Beton')

@section('content')
<div class="container-fluid py-4 admin-dashboard-container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-primary fw-bold">
                <i class="fas fa-history"></i> Riwayat CRM
            </h1>
            <p class="text-muted mb-0">Riwayat pesan dan diskon yang telah diberikan</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Riwayat Pesan & Diskon</h5>
            <a href="{{ route('admin.crm.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke CRM
            </a>
        </div>
        <div class="card-body">
            @if($paginatedHistory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Customer</th>
                                <th>Konten</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginatedHistory as $item)
                                <tr>
                                    <td>{{ $item['created_at'] }}</td>
                                    <td>
                                        @if($item['type'] === 'message')
                                            <span class="badge bg-info">Pesan</span>
                                        @else
                                            <span class="badge bg-success">Diskon</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item['customer_name'] }}</strong><br>
                                        <small class="text-muted">{{ $item['customer_email'] }}</small>
                                    </td>
                                    <td>
                                        @if($item['type'] === 'message')
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $item['content'] }}">
                                                {{ $item['content'] }}
                                            </div>
                                        @else
                                            <div>{{ $item['content'] }}</div>
                                            @if(isset($item['admin_note']) && $item['admin_note'])
                                                <small class="text-muted d-block">Catatan: {{ $item['admin_note'] }}</small>
                                            @endif
                                            @if(isset($item['expires_at']))
                                                <small class="text-muted d-block">Berlaku hingga: {{ $item['expires_at'] }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['type'] === 'message')
                                            <span class="badge bg-secondary">Aktif</span>
                                        @else
                                            @if($item['is_active'])
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Kadaluarsa</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['type'] === 'message')
                                            <button class="btn btn-primary btn-sm edit-history-btn"
                                                    data-id="{{ $item['id'] }}"
                                                    data-type="{{ $item['type'] }}"
                                                    data-content="{{ $item['content'] }}"
                                                    title="Edit Pesan">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-danger btn-sm delete-history-btn"
                                                data-id="{{ $item['id'] }}"
                                                data-type="{{ $item['type'] }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada riwayat CRM</h5>
                    <p class="text-muted">Riwayat pesan dan diskon akan muncul di sini setelah Anda mengirimkannya.</p>
                </div>
            @endif
            
            <!-- Pagination -->
            @if($paginatedHistory->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Navigasi halaman riwayat CRM">
                        {{ $paginatedHistory->links('vendor.pagination.bootstrap-5') }}
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Message Modal -->
<div class="modal fade" id="editMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMessageForm">
                @csrf
                <input type="hidden" id="editMessageId" name="id">
                <input type="hidden" name="type" value="message">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editMessageContent" class="form-label">Isi Pesan</label>
                        <textarea class="form-control" id="editMessageContent" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    // Delete history item
    document.querySelectorAll('.delete-history-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus ${type === 'message' ? 'pesan' : 'diskon'} ini dari riwayat?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('type', type);
                    
                    fetch('/admin/crm/history/delete', {
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
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus riwayat.', 'error');
                    });
                }
            });
        });
    });
    
    // Edit message
    document.querySelectorAll('.edit-history-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            const content = this.getAttribute('data-content');
            
            if (type === 'message') {
                document.getElementById('editMessageId').value = id;
                document.getElementById('editMessageContent').value = content;
                new bootstrap.Modal(document.getElementById('editMessageModal')).show();
            }
        });
    });
    
    // Update message form
    document.getElementById('editMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/admin/crm/history/update', {
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
                    window.location.reload();
                });
            } else {
                if (data.errors) {
                    let errorMessages = [];
                    for (let field in data.errors) {
                        errorMessages.push(...data.errors[field]);
                    }
                    Swal.fire('Validasi Gagal!', errorMessages.join('\n'), 'error');
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat memperbarui pesan.', 'error');
        });
    });
});
</script>
@endpush