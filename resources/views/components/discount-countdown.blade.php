<!-- Discount Countdown Timer Component -->
<div class="discount-countdown-container mb-3" id="discountCountdownContainer" style="display: none;">
    <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-clock fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1">
                    <i class="fas fa-bolt"></i> Penawaran Berakhir!
                </h6>
                <p class="mb-2">
                    Diskon pribadi Anda akan berakhir dalam:
                </p>
                <div class="countdown-timer d-flex justify-content-center">
                    <div class="countdown-item text-center mx-2">
                        <div class="countdown-value fw-bold fs-5" id="days">00</div>
                        <div class="countdown-label small">Hari</div>
                    </div>
                    <div class="countdown-item text-center mx-2">
                        <div class="countdown-value fw-bold fs-5" id="hours">00</div>
                        <div class="countdown-label small">Jam</div>
                    </div>
                    <div class="countdown-item text-center mx-2">
                        <div class="countdown-value fw-bold fs-5" id="minutes">00</div>
                        <div class="countdown-label small">Menit</div>
                    </div>
                    <div class="countdown-item text-center mx-2">
                        <div class="countdown-value fw-bold fs-5" id="seconds">00</div>
                        <div class="countdown-label small">Detik</div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have discount expiration data
    const discountExpiryData = @json($activeDiscounts->filter(function($discount) { 
        return $discount->expires_at && $discount->isValid(); 
    })->sortBy('expires_at')->first()?->expires_at?->toISOString() ?? null);
    
    if (discountExpiryData) {
        initializeCountdown(discountExpiryData);
    }
    
    function initializeCountdown(expiryDate) {
        const expiryTime = new Date(expiryDate).getTime();
        const countdownContainer = document.getElementById('discountCountdownContainer');
        
        // Show the countdown container
        countdownContainer.style.display = 'block';
        
        // Update the countdown every second
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiryTime - now;
            
            // Calculate days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the results
            document.getElementById('days').innerHTML = days.toString().padStart(2, '0');
            document.getElementById('hours').innerHTML = hours.toString().padStart(2, '0');
            document.getElementById('minutes').innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').innerHTML = seconds.toString().padStart(2, '0');
            
            // If the countdown is finished
            if (distance < 0) {
                clearInterval(countdownInterval);
                countdownContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1">Penawaran Telah Berakhir!</h6>
                                <p class="mb-0">Diskon pribadi Anda telah kedaluwarsa.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                
                // Reload page after 5 seconds to refresh discount status
                setTimeout(function() {
                    location.reload();
                }, 5000);
            }
        }, 1000);
    }
});
</script>