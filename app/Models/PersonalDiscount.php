<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Scope to get only active discounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
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
     * Check if discount is still valid
     */
    public function isValid(): bool
    {
        return $this->is_active && 
               (is_null($this->expires_at) || $this->expires_at->isFuture());
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
}