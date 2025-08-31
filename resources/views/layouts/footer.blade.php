<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="text-success">
                    <i class="fas fa-store"></i> Laravel Barokah
                </h5>
                <p class="text-light">
                    Toko online terpercaya dengan produk berkualitas tinggi dan pelayanan terbaik untuk kepuasan pelanggan.
                </p>
            </div>
            
            <div class="col-md-4 mb-4">
                <h6>Kontak</h6>
                <ul class="list-unstyled text-light">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-success"></i>
                        Jl. Contoh No. 123, Jakarta
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-success"></i>
                        +62 812-3456-7890
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-success"></i>
                        info@laravelbarokah.com
                    </li>
                </ul>
            </div>
            
            <div class="col-md-4 mb-4">
                <h6>Ikuti Kami</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <hr class="border-secondary">
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-light mb-0">
                    &copy; {{ date('Y') }} Laravel Barokah. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-light mb-0">
                    Built with <i class="fas fa-heart text-danger"></i> using Laravel
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .text-light a:hover {
        color: #4CAF50 !important;
        transition: color 0.3s ease;
    }
</style>