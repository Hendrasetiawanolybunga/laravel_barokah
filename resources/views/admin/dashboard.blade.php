@extends('layouts.app')

@section('title', 'Admin Dashboard - Laravel Barokah')

@section('content')
<div class="container-fluid py-4 admin-dashboard-container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary fw-bold">
                        <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                    </h1>
                    <p class="text-muted mb-0">Selamat datang kembali, {{ auth()->user()->name }}!</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">
                        <i class="fas fa-calendar"></i> {{ now()->format('d F Y, H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Customers -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Total Pelanggan</h6>
                            <h2 class="mb-0">{{ $stats['total_customers'] }}</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25 border-0">
                    <a href="{{ route('admin.customers') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-right"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Total Produk</h6>
                            <h2 class="mb-0">{{ $stats['total_products'] }}</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-box fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25 border-0">
                    <a href="{{ route('admin.products') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-right"></i> Kelola Produk
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Pesanan Pending</h6>
                            <h2 class="mb-0">{{ $stats['pending_orders'] }}</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25 border-0">
                    <a href="{{ route('admin.orders') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-right"></i> Proses Pesanan
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title opacity-75 mb-1">Total Pendapatan</h6>
                            <h2 class="mb-0">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25 border-0">
                    <a href="{{ route('admin.orders') }}" class="text-white text-decoration-none small">
                        <i class="fas fa-arrow-right"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i> Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Tambah Produk Baru
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.customers') }}" class="btn btn-success w-100">
                                <i class="fas fa-users"></i> Kelola Pelanggan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.orders') }}" class="btn btn-warning w-100">
                                <i class="fas fa-shopping-cart"></i> Proses Pesanan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.products') }}" class="btn btn-info w-100">
                                <i class="fas fa-box"></i> Lihat Semua Produk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> Pesanan Terbaru
                    </h5>
                    <a href="{{ route('admin.orders') }}" class="btn btn-outline-primary btn-sm">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#ID</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_orders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $order->tanggal->format('d/m/Y H:i') }}</small>
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
                                                <a href="{{ route('admin.orders.show', $order) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
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
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .stats-card {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }
    
    /* Admin dashboard container styling */
    .admin-dashboard-container {
        padding-left: 3rem;
        padding-right: 3rem;
    }
    
    /* Responsive admin dashboard */
    @media (max-width: 767.98px) {
        .admin-dashboard-container {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .text-end {
            text-align: left !important;
            margin-top: 0.5rem;
        }
        
        h1.h3 {
            font-size: 1.25rem;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .row.g-4 {
            gap: 1rem;
        }
    }
    
    @media (min-width: 768px) and (max-width: 991.98px) {
        .admin-dashboard-container {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }
</style>
@endpush