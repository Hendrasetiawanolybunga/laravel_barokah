<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_loyal',
        'message',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_loyal' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get the customer profile associated with the user.
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the personal discounts for the user.
     */
    public function personalDiscounts()
    {
        return $this->hasMany(PersonalDiscount::class);
    }

    /**
     * Get active personal discounts for the user.
     */
    public function activeDiscounts()
    {
        return $this->personalDiscounts()->active();
    }

    /**
     * Scope to get loyal customers (total spending > 5,000,000)
     */
    public function scopeLoyalCustomers($query)
    {
        return $query->where('role', 'customer')
                    ->whereHas('orders', function ($orderQuery) {
                        $orderQuery->where('status', 'delivered')
                                  ->selectRaw('user_id, SUM(total) as total_spending')
                                  ->groupBy('user_id')
                                  ->havingRaw('SUM(total) > 5000000');
                    });
    }

    /**
     * Scope to get customers having birthday today
     */
    public function scopeBirthdayToday($query)
    {
        $today = now();
        return $query->where('role', 'customer')
                    ->whereHas('customer', function ($customerQuery) use ($today) {
                        $customerQuery->whereNotNull('tgl_lahir')
                                     ->whereRaw("strftime('%m-%d', tgl_lahir) = ?", [$today->format('m-d')]);
                    });
    }

    /**
     * Scope to get customers having birthday this month
     */
    public function scopeBirthdayThisMonth($query)
    {
        return $query->where('role', 'customer')
                    ->whereHas('customer', function ($customerQuery) {
                        $customerQuery->whereNotNull('tgl_lahir')
                                     ->whereRaw("strftime('%m', tgl_lahir) = ?", [now()->format('m')]);
                    });
    }

    /**
     * Scope to get loyal customers with birthday this month
     */
    public function scopeLoyalWithBirthday($query)
    {
        return $query->loyalCustomers()->birthdayThisMonth();
    }

    /**
     * Get total spending for this customer
     */
    public function getTotalSpending()
    {
        return $this->orders()
                   ->where('status', 'delivered')
                   ->sum('total');
    }

    /**
     * Check if customer is having birthday today
     */
    public function isBirthdayToday(): bool
    {
        if (!$this->customer || !$this->customer->tgl_lahir) {
            return false;
        }

        return $this->customer->tgl_lahir->format('m-d') === now()->format('m-d');
    }

    /**
     * Check if customer is having birthday this month
     */
    public function isBirthdayThisMonth(): bool
    {
        if (!$this->customer || !$this->customer->tgl_lahir) {
            return false;
        }

        return $this->customer->tgl_lahir->format('m') === now()->format('m');
    }

    /**
     * Get discount for a specific product
     */
    public function getDiscountForProduct($productId)
    {
        return $this->activeDiscounts()
                   ->where('product_id', $productId)
                   ->first();
    }
}
