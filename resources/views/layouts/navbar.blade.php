@if(Request::route()->getName() === 'landing')
<!-- Landing Page Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand text-primary" href="{{ route('landing') }}">
            <i class="fas fa-store"></i> UD. Barokah Jaya Beton
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#about">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#products">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#faq">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ms-2" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary ms-2" href="{{ route('register') }}">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

@elseif(auth()->check() && auth()->user()->isAdmin())
<!-- Admin Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}" style="color: #ffffff !important; font-weight: bold;">
            <i class="fas fa-shield-alt"></i> UD. BJB Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" 
                       href="{{ route('admin.products') }}">
                        <i class="fas fa-box"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}" 
                       href="{{ route('admin.customers') }}">
                        <i class="fas fa-users"></i> Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" 
                       href="{{ route('admin.orders') }}">
                        <i class="fas fa-shopping-cart"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.crm*') ? 'active' : '' }}" 
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-heart"></i> CRM
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.crm.index') ? 'active' : '' }}" 
                               href="{{ route('admin.crm.index') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard CRM
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.crm.history') ? 'active' : '' }}" 
                               href="{{ route('admin.crm.history') }}">
                                <i class="fas fa-history"></i> Riwayat CRM
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" 
                       href="{{ route('admin.reports') }}">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

@elseif(auth()->check() && auth()->user()->isCustomer())
<!-- Customer Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand text-primary" href="{{ route('customer.home') }}">
            <i class="fas fa-store"></i> UD. Barokah Jaya Beton
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customerNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="customerNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.home') ? 'active' : '' }}" 
                       href="{{ route('customer.home') }}">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}" 
                       href="{{ route('customer.orders') }}">
                        <i class="fas fa-history"></i> Pesanan Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative {{ request()->routeIs('customer.notifications*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                        <i class="fas fa-bell fa-lg"></i>
                        @if(auth()->user())
                            <span class="notification-dot"></span>
                        @endif
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ route('customer.cart.show') }}">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        @if(isset($cartCount) && $cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                            <span class="cart-indicator-dot"></span>
                        @endif
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="dropdown-item" onclick="openProfileModal()">
                                <i class="fas fa-user-edit"></i> Edit Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

@else
<!-- Guest Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand text-primary" href="{{ route('landing') }}">
            <i class="fas fa-store"></i> UD. Barokah Jaya Beton
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="guestNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="btn btn-outline-primary ms-2" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary ms-2" href="{{ route('register') }}">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endif