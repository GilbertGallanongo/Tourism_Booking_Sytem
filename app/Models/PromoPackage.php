<?php

namespace App\Models;

use App\Support\UploadedImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        $today = now()->startOfDay();

        return (bool) $this->is_active
            && $this->start_date?->copy()->startOfDay()->lte($today)
            && $this->end_date?->copy()->endOfDay()->gte($today);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/package-default.svg');
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        $imagePath = UploadedImage::normalize($this->image);

        if ($imagePath === '') {
            return asset('images/package-default.svg');
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return UploadedImage::url($imagePath);
        }

        if (file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }

        if (file_exists(public_path('storage/' . $imagePath))) {
            return asset('storage/' . $imagePath);
        }

        return asset('images/package-default.svg');
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
