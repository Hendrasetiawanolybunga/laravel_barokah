@extends('layouts.app')

@section('title', 'Laravel Barokah - Toko Online Terpercaya')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Selamat Datang di Laravel Barokah
                </h1>
                <p class="lead mb-4">
                    Temukan produk berkualitas tinggi dengan harga terjangkau. 
                    Belanja mudah, aman, dan terpercaya hanya di Laravel Barokah.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 me-md-2">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('pelanggan/img/hero-image.jpeg') }}" 
                     alt="Hero Image" 
                     class="img-fluid rounded shadow-lg"
                     style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold text-primary mb-4">Tentang Laravel Barokah</h2>
                <p class="lead text-muted mb-4">
                    Laravel Barokah adalah platform e-commerce yang berkomitmen memberikan pengalaman 
                    berbelanja online terbaik dengan produk berkualitas dan layanan prima.
                </p>
            </div>
        </div>
        
        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x text-white"></i>
                    </div>
                    <h4>Terpercaya</h4>
                    <p class="text-muted">
                        Kami berkomitmen memberikan produk asli dan layanan terbaik untuk kepuasan pelanggan.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-shipping-fast fa-2x text-white"></i>
                    </div>
                    <h4>Pengiriman Cepat</h4>
                    <p class="text-muted">
                        Sistem pengiriman yang efisien memastikan produk sampai dengan cepat dan aman.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x text-white"></i>
                    </div>
                    <h4>Customer Service 24/7</h4>
                    <p class="text-muted">
                        Tim customer service kami siap membantu Anda kapan saja untuk kebutuhan belanja Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="products" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold text-primary">Produk Unggulan</h2>
                <p class="lead text-muted">
                    Temukan produk-produk terbaik dan terpopuler dari Laravel Barokah
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative overflow-hidden">
                        <img src="{{ asset('pelanggan/img/galeri/produk1.jpeg') }}" 
                             class="card-img-top" 
                             alt="Produk 1"
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">Produk Unggulan 1</h5>
                        <p class="card-text text-muted">
                            Produk berkualitas tinggi dengan desain modern dan fungsi optimal.
                        </p>
                        <h5 class="text-success fw-bold">Rp 150.000</h5>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative overflow-hidden">
                        <img src="{{ asset('pelanggan/img/galeri/produk2.jpeg') }}" 
                             class="card-img-top" 
                             alt="Produk 2"
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">Produk Unggulan 2</h5>
                        <p class="card-text text-muted">
                            Inovasi terbaru dengan teknologi canggih untuk kebutuhan harian.
                        </p>
                        <h5 class="text-success fw-bold">Rp 200.000</h5>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative overflow-hidden">
                        <img src="{{ asset('pelanggan/img/galeri/produk3.jpeg') }}" 
                             class="card-img-top" 
                             alt="Produk 3"
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">Produk Unggulan 3</h5>
                        <p class="card-text text-muted">
                            Desain elegan dengan performa tinggi dan kualitas premium.
                        </p>
                        <h5 class="text-success fw-bold">Rp 175.000</h5>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="lead text-muted mb-3">Ingin melihat semua produk kami?</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Daftar dan Mulai Belanja
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold text-primary">Pertanyaan yang Sering Diajukan</h2>
                <p class="lead text-muted">
                    Temukan jawaban untuk pertanyaan umum tentang Laravel Barokah
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana cara berbelanja di Laravel Barokah?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Untuk berbelanja, Anda perlu mendaftar terlebih dahulu, kemudian login dan pilih produk yang diinginkan. 
                                Tambahkan ke keranjang, lakukan checkout, dan upload bukti pembayaran.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Metode pembayaran apa yang tersedia?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Saat ini kami menerima pembayaran melalui transfer bank. Setelah melakukan transfer, 
                                silakan upload bukti pembayaran untuk proses verifikasi.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Berapa lama proses pengiriman?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Setelah pembayaran dikonfirmasi, pesanan akan diproses dalam 1-2 hari kerja dan 
                                dikirim ke alamat tujuan dengan estimasi 2-5 hari kerja tergantung lokasi.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Apakah saya bisa memberikan review produk?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, Anda dapat memberikan review setelah pesanan berstatus "Selesai". 
                                Review Anda sangat membantu customer lain dalam memilih produk.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-6 fw-bold mb-4">Siap Memulai Berbelanja?</h2>
                <p class="lead mb-4">
                    Bergabung dengan ribuan pelanggan yang sudah merasakan kemudahan berbelanja di Laravel Barokah
                </p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-rocket"></i> Mulai Sekarang
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
@endpush