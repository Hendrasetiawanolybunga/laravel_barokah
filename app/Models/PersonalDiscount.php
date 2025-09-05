<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PersonalDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'persen_diskon',
        'admin_note',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'persen_diskon' => 'decimal:2',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the discount
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that the discount applies to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get only active discounts with explicit timezone handling
     */
    public function scopeActive($query)
    {
        $currentTime = now()->setTimezone('Asia/Jakarta');
        
        return $query->where('is_active', true)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', $currentTime);
                    });
    }

    /**
     * Scope to get discounts for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get discounts for a specific product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if discount is still valid with explicit timezone handling
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if (is_null($this->expires_at)) {
            return true;
        }
        
        // Pastikan perbandingan waktu menggunakan timezone Indonesia
        $currentTime = now()->setTimezone('Asia/Jakarta');
        $expiryTime = $this->expires_at->setTimezone('Asia/Jakarta');
        
        return $expiryTime->isFuture();
    }

    /**
     * Calculate discounted price for a given price
     */
    public function applyDiscount($originalPrice): float
    {
        if (!$this->isValid()) {
            return $originalPrice;
        }

        $discountAmount = ($originalPrice * $this->persen_diskon) / 100;
        return max(0, $originalPrice - $discountAmount);
    }

    /**
     * Get formatted expiry date in Indonesian format
     */
    public function getFormattedExpiryAttribute(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return $this->expires_at->setTimezone('Asia/Jakarta')
                                ->translatedFormat('d F Y, H:i') . ' WIB';
    }

    /**
     * Get formatted expiry date in short Indonesian format
     */
    public function getFormattedExpiryShortAttribute(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return $this->expires_at->setTimezone('Asia/Jakarta')
                                ->translatedFormat('d M Y');
    }
}