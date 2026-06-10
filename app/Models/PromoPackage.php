<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount_percentage',
        'start_date',
        'end_date',
        'is_active',
        'image',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function isActive(): bool
    {
        return $this->is_active 
            && $this->start_date <= now() 
            && $this->end_date >= now();
    }

    public function minGuestCapacity(): ?int
    {
        if (! $this->description) {
            return null;
        }

        if (preg_match('/(\d+)\s*or\s*more/i', $this->description, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function minStartDays(): int
    {
        if (preg_match('/\bearly\s*bird\b/i', $this->name)) {
            return 30;
        }

        if (preg_match('/(\d+)\s*days?\s*(?:in\s*advance|from\s*now|from\s*today)/i', $this->description, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    public function discountedPrice(float $price): float
    {
        return round($price * (1 - ($this->discount_percentage / 100)), 2);
    }
}
