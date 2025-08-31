@extends('layouts.app')

@section('title', 'UD. Barokah Jaya Beton - Toko Online Terpercaya')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center position-relative">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-text">
                <h1 class="display-4 fw-bold mb-4 hero-title">
                    Selamat Datang di UD. Barokah Jaya Beton
                </h1>
                <p class="lead mb-4 hero-description">
                    Temukan produk berkualitas tinggi dengan harga terjangkau. 
                    Belanja mudah, aman, dan terpercaya hanya di UD. Barokah Jaya Beton.
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
            <div class="col-lg-6 hero-image-container">
                <img src="{{ asset('pelanggan/img/hero-image.jpeg') }}" 
                     alt="Hero Image" 
                     class="img-fluid rounded shadow-lg hero-image"
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
                <h2 class="display-5 fw-bold text-primary mb-4">Tentang UD. Barokah Jaya Beton</h2>
                <p class="lead text-muted mb-4">
                    UD. Barokah Jaya Beton adalah platform e-commerce yang berkomitmen memberikan pengalaman 
                    berbelanja online terbaik dengan produk berkualitas dan layanan prima.
                </p>
            </div>
        </div>
        
        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm why-choose-card">
                    <div class="card-body text-center p-4">
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
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm why-choose-card">
                    <div class="card-body text-center p-4">
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
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm why-choose-card">
                    <div class="card-body text-center p-4">
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
    </div>
</section>

<!-- Featured Products Section -->
<section id="products" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold text-primary">Produk Unggulan</h2>
                <p class="lead text-muted">
                    Temukan produk-produk terbaik dan terpopuler dari UD. Barokah Jaya Beton
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <div class="position-relative overflow-hidden">
                        <img src="{{ asset('pelanggan/img/galeri/produk1.jpeg') }}" 
                             class="card-img-top product-image" 
                             alt="Produk 1"
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease; cursor: pointer;"
                             onclick="showProductImage('{{ asset('pelanggan/img/galeri/produk1.jpeg') }}', 'Produk Unggulan 1')"
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
                             class="card-img-top product-image" 
                             alt="Produk 2"
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease; cursor: pointer;"
                             onclick="showProductImage('{{ asset('pelanggan/img/galeri/produk2.jpeg') }}', 'Produk Unggulan 2')"
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
                             class="card-img-top product-image" 
                             alt="Produk 3"
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease; cursor: pointer;"
                             onclick="showProductImage('{{ asset('pelanggan/img/galeri/produk3.jpeg') }}', 'Produk Unggulan 3')"
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
                    Temukan jawaban untuk pertanyaan umum tentang UD. Barokah Jaya Beton
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana cara berbelanja di UD. Barokah Jaya Beton?
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
                    Bergabung dengan ribuan pelanggan yang sudah merasakan kemudahan berbelanja di UD. Barokah Jaya Beton
                </p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-rocket"></i> Mulai Sekarang
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

<!-- Product Image Modal -->
<div class="modal fade" id="productImageModal" tabindex="-1" aria-labelledby="productImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 bg-transparent">
                <h5 class="modal-title text-white" id="productImageModalLabel">Produk Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="modalProductImage" src="" alt="" class="img-fluid rounded shadow-lg" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* RESPONSIVE HERO SECTION */
.hero-section {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 100px 0;
    min-height: 500px;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

/* Desktop & Tablet Layout (default) */
.hero-text {
    z-index: 2;
    position: relative;
}

.hero-image-container {
    z-index: 2;
    position: relative;
}

.hero-image {
    transition: transform 0.3s ease;
}

.hero-image:hover {
    transform: scale(1.05);
}

/* Why Choose Us Cards with Shadow */
.why-choose-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
}

.why-choose-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
}

/* Product Cards Enhancement */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.product-image {
    border-radius: 15px 15px 0 0;
}

/* MOBILE RESPONSIVE DESIGN */
@media (max-width: 768px) {
    /* Hero Section Mobile Layout */
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        position: relative;
        padding: 80px 0;
        min-height: 600px;
    }
    
    /* Add background image with blur on mobile */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('{{ asset('pelanggan/img/hero-image.jpeg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: scroll;
        opacity: 0.5;
        filter: blur(3px);
        z-index: 1;
    }
    
    /* Hide desktop image on mobile */
    .hero-image-container {
        display: none !important;
    }
    
    /* Ensure text appears above background */
    .hero-text {
        z-index: 2;
        position: relative;
        text-align: center;
        width: 100%;
    }
    
    .hero-title {
        font-size: 2rem !important;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        margin-bottom: 1.5rem;
    }
    
    .hero-description {
        font-size: 1.1rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        margin-bottom: 2rem;
    }
    
    /* Button improvements for mobile */
    .hero-section .btn {
        font-size: 1rem;
        padding: 12px 24px;
        margin: 0.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    /* General mobile improvements */
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .display-5 {
        font-size: 1.75rem;
    }
    
    .display-6 {
        font-size: 1.5rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    /* Card spacing for mobile */
    .why-choose-card {
        margin-bottom: 1.5rem;
    }
    
    .product-card {
        margin-bottom: 1.5rem;
    }
    
    /* Accordion improvements */
    .accordion-button {
        font-size: 0.95rem;
        padding: 1rem;
    }
    
    .accordion-body {
        font-size: 0.9rem;
    }
}

/* Extra small devices */
@media (max-width: 575.98px) {
    .hero-section {
        padding: 60px 0;
        min-height: 500px;
    }
    
    .hero-title {
        font-size: 1.75rem !important;
    }
    
    .hero-description {
        font-size: 1rem;
    }
    
    .hero-section .btn {
        font-size: 0.9rem;
        padding: 10px 20px;
        display: block;
        width: 100%;
        max-width: 250px;
        margin: 0.5rem auto;
    }
    
    .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}

/* Tablet specific */
@media (min-width: 769px) and (max-width: 991.98px) {
    .hero-section {
        padding: 80px 0;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
}

/* Large desktop */
@media (min-width: 1200px) {
    .hero-section {
        padding: 120px 0;
    }
    
    .container {
        max-width: 1140px;
    }
}

/* Modal enhancements */
.modal-content.bg-transparent {
    background: rgba(0,0,0,0.9) !important;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Animation for product images */
@keyframes imagePopIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal.show #modalProductImage {
    animation: imagePopIn 0.3s ease;
}
</style>
@endpush

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
    
    // Product image modal function
    function showProductImage(imageSrc, productName) {
        const modal = new bootstrap.Modal(document.getElementById('productImageModal'));
        const modalImage = document.getElementById('modalProductImage');
        const modalTitle = document.getElementById('productImageModalLabel');
        
        modalImage.src = imageSrc;
        modalImage.alt = productName;
        modalTitle.textContent = productName;
        
        modal.show();
    }
    
    // Enhanced mobile background image handling
    function setMobileHeroBackground() {
        if (window.innerWidth <= 768) {
            const heroSection = document.querySelector('.hero-section');
            if (heroSection) {
                // Background is already set in CSS, this function can be used for dynamic adjustments
                console.log('Mobile hero background applied');
            }
        }
    }
    
    // Call on load and resize
    window.addEventListener('load', setMobileHeroBackground);
    window.addEventListener('resize', setMobileHeroBackground);
</script>
@endpush