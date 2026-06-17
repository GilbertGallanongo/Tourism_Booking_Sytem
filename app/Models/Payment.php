<?php

namespace App\Models;

use App\Support\UploadedImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'reference_number',
        'proof',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getProofUrlAttribute(): ?string
    {
        if (! $this->proof) {
            return null;
        }

        if (str_starts_with($this->proof, 'http')) {
            return $this->proof;
        }

        $proofPath = UploadedImage::normalize($this->proof);

        if ($proofPath === '') {
            return null;
        }

        if (Storage::disk('public')->exists($proofPath)) {
            return UploadedImage::url($proofPath);
        }

        if (file_exists(public_path($proofPath))) {
            return asset($proofPath);
        }

        if (file_exists(public_path('storage/' . $proofPath))) {
            return asset('storage/' . $proofPath);
        }

        return null;
    }

    public function getProofIsImageAttribute(): bool
    {
        if (! $this->proof) {
            return false;
        }

        $extension = strtolower(pathinfo(parse_url($this->proof, PHP_URL_PATH) ?: $this->proof, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true);
    }
}
