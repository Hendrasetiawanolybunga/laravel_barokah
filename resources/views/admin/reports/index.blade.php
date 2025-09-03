@extends('layouts.app')

@section('title', 'Laporan - UD. Barokah Jaya Beton')

@section('content')
<div class="container-fluid py-4 admin-dashboard-container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 text-primary fw-bold">
                    <i class="fas fa-chart-bar"></i> Laporan Penjualan
                </h1>
                <p class="text-muted mb-0">Analisis data penjualan dan performa produk</p>
            </div>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Penjualan Bulan Ini</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['total_sales_this_month'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pesanan Bulan Ini</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_orders_this_month']) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Revenue</h6>
                            <h3 class="mb-0">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Pesanan</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Sales Chart -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-area text-primary"></i> Penjualan 12 Bulan Terakhir
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Penjualan</th>
                                    <th>Grafik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxSale = collect($monthlySales)->max('total') ?: 1;
                                @endphp
                                @foreach($monthlySales as $sale)
                                    <tr>
                                        <td><strong>{{ $sale['month'] }}</strong></td>
                                        <td class="text-success">
                                            Rp {{ number_format($sale['total'], 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                @php
                                                    $percentage = $maxSale > 0 ? ($sale['total'] / $maxSale) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%"
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy text-warning"></i> Produk Terlaris
                    </h5>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topProducts->take(5) as $index => $product)
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-rank me-2 
                                            @if($index == 0) bg-warning
                                            @elseif($index == 1) bg-secondary  
                                            @elseif($index == 2) bg-dark
                                            @else bg-light text-dark
                                            @endif">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <h6 class="mb-0">{{ Str::limit($product->nama, 20) }}</h6>
                                            <small class="text-muted">
                                                Rp {{ number_format($product->harga, 0, ',', '.') }}
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success">
                                        {{ number_format($product->order_items_sum_jumlah_item ?? 0) }} terjual
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">Belum ada data produk terjual</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-table text-info"></i> Detail Laporan Produk
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Total Terjual</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $product->nama }}</strong>
                                        </td>
                                        <td class="text-success">
                                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->stok > 10 ? 'bg-success' : ($product->stok > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $product->stok }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($product->order_items_sum_jumlah_item ?? 0) }}</strong>
                                        </td>
                                        <td class="text-primary">
                                            <strong>Rp {{ number_format(($product->order_items_sum_jumlah_item ?? 0) * $product->harga, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            Belum ada data penjualan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Print styles */
@media print {
    .btn, .alert, .navbar, .sidebar {
        display: none !important;
    }
    
    .admin-dashboard-container {
        padding: 0 !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    .bg-primary, .bg-success, .bg-info, .bg-warning {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
    
    .text-white {
        color: #000 !important;
    }
    
    .progress-bar {
        background-color: #6c757d !important;
    }
}

.badge-rank {
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.75rem;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.list-group-item {
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(0,0,0,0.05);
}
</style>
@endpush