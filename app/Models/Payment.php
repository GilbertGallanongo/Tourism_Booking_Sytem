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

    public const SAMPLE_PROOF_PATH = 'images/sample-proof-of-payment.png';

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
        $proof = $this->effectiveProofPath();

        if (! $proof) {
            return null;
        }

        if (str_starts_with($proof, 'http')) {
            return $proof;
        }

        $proofPath = UploadedImage::normalize($proof);

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
        $proof = $this->effectiveProofPath();

        if (! $proof) {
            return false;
        }

        $extension = strtolower(pathinfo(parse_url($proof, PHP_URL_PATH) ?: $proof, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true);
    }

    public function getProofDisplayNameAttribute(): string
    {
        return $this->effectiveProofPath() ?: 'No proof of payment attached';
    }

    public function getHasUploadedProofAttribute(): bool
    {
        return (bool) $this->proof;
    }

    private function effectiveProofPath(): ?string
    {
        if ($this->proof) {
            return $this->proof;
        }

        return $this->status === 'paid' ? self::SAMPLE_PROOF_PATH : null;
    }
}
