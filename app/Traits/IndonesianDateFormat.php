<?php

namespace App\Traits;

trait IndonesianDateFormat
{
    /**
     * Format tanggal dalam format Indonesia (WIB)
     * 
     * @param string $attribute - nama atribut tanggal
     * @param string $format - format tanggal ('full', 'short', 'date_only', 'time_only')
     * @return string|null
     */
    public function formatIndonesianDate($attribute, $format = 'full'): ?string
    {
        if (!$this->{$attribute}) {
            return null;
        }

        $date = $this->{$attribute}->setTimezone('Asia/Jakarta');

        return match($format) {
            'full' => $date->translatedFormat('d F Y, H:i') . ' WIB',
            'short' => $date->translatedFormat('d M Y'),
            'date_only' => $date->translatedFormat('d F Y'),
            'time_only' => $date->format('H:i') . ' WIB',
            'datetime' => $date->translatedFormat('d F Y, H:i') . ' WIB',
            default => $date->translatedFormat('d F Y, H:i') . ' WIB'
        };
    }

    /**
     * Format tanggal lahir dalam format Indonesia
     */
    public function getFormattedBirthdayAttribute(): ?string
    {
        if (!$this->tgl_lahir) {
            return null;
        }

        return $this->tgl_lahir->translatedFormat('d F Y');
    }

    /**
     * Format tanggal pesanan dalam format Indonesia
     */
    public function getFormattedOrderDateAttribute(): ?string
    {
        if (!$this->tanggal) {
            return null;
        }

        return $this->tanggal->setTimezone('Asia/Jakarta')
                           ->translatedFormat('d F Y, H:i') . ' WIB';
    }

    /**
     * Format created_at dalam format Indonesia
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->setTimezone('Asia/Jakarta')
                               ->translatedFormat('d F Y, H:i') . ' WIB';
    }

    /**
     * Format updated_at dalam format Indonesia
     */
    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at->setTimezone('Asia/Jakarta')
                               ->translatedFormat('d F Y, H:i') . ' WIB';
    }
}