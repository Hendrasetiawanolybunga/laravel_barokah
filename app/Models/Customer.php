<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tgl_lahir',
        'alamat',
        'pekerjaan',
        'no_hp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    /**
     * Get the user that owns the customer profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if customer is having birthday today
     */
    public function isBirthdayToday(): bool
    {
        if (!$this->tgl_lahir) {
            return false;
        }

        return $this->tgl_lahir->format('m-d') === now()->format('m-d');
    }

    /**
     * Check if customer is having birthday this month
     */
    public function isBirthdayThisMonth(): bool
    {
        if (!$this->tgl_lahir) {
            return false;
        }

        return $this->tgl_lahir->format('m') === now()->format('m');
    }

    /**
     * Get formatted birthday date
     */
    
    public function getBirthdayFormattedAttribute(): string
    {
        return $this->tgl_lahir ? $this->tgl_lahir->format('d M') : '-';
    }
}
