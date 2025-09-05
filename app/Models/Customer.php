<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\IndonesianDateFormat;

class Customer extends Model
{
    use HasFactory, IndonesianDateFormat;

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
     * Check if customer is having birthday today dengan timezone Indonesia
     */
    public function isBirthdayToday(): bool
    {
        if (!$this->tgl_lahir) {
            return false;
        }

        return $this->tgl_lahir->format('m-d') === now()->setTimezone('Asia/Jakarta')->format('m-d');
    }

    /**
     * Check if customer is having birthday this month dengan timezone Indonesia
     */
    public function isBirthdayThisMonth(): bool
    {
        if (!$this->tgl_lahir) {
            return false;
        }

        return $this->tgl_lahir->format('m') === now()->setTimezone('Asia/Jakarta')->format('m');
    }

    /**
     * Get formatted birthday date dalam format Indonesia
     */
    public function getBirthdayFormattedAttribute(): string
    {
        return $this->tgl_lahir ? $this->tgl_lahir->translatedFormat('d M') : '-';
    }
}
