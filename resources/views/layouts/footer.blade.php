<footer class="bg-primary text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="text-white fw-bold">
                    <i class="fas fa-store"></i> UD. Barokah Jaya Beton
                </h5>
                <p class="text-white opacity-75">
                    Toko online terpercaya dengan produk berkualitas tinggi dan pelayanan terbaik untuk kepuasan pelanggan.
                </p>
            </div>
            
            <div class="col-md-4 mb-4">
                <h6 class="text-white fw-bold">Kontak</h6>
                <ul class="list-unstyled text-white">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-warning"></i>
                        Jl. Contoh No. 123, Jakarta
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-warning"></i>
                        +62 812-3456-7890
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-warning"></i>
                        info@barokahjayabeton.com
                    </li>
                </ul>
            </div>
            
            <div class="col-md-4 mb-4">
                <h6 class="text-white fw-bold">Ikuti Kami</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white hover-effect">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                    <a href="#" class="text-white hover-effect">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                    <a href="#" class="text-white hover-effect">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <hr class="border-light opacity-25">
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-white opacity-75 mb-0">
                    &copy; {{ date('Y') }} UD. Barokah Jaya Beton. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-white opacity-75 mb-0">
                    Built with <i class="fas fa-heart text-danger"></i> using Laravel
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-effect:hover {
        color: #ffffff !important;
        transform: scale(1.1);
        transition: all 0.3s ease;
        opacity: 0.8;
    }
    
    .bg-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
    }
    
    /* Footer responsive design */
    @media (max-width: 768px) {
        footer .col-md-4 {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        footer .text-md-end {
            text-align: center !important;
        }
        
        footer .d-flex {
            justify-content: center;
        }
    }
</style>