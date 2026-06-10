<?php

namespace App\Models;

use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tour_package_id',
        'rating',
        'comment',
    ];

    protected static function booted()
    {
        static::saved(function (Review $review) {
            $review->syncTourPackageRating();
        });

        static::deleted(function (Review $review) {
            $review->syncTourPackageRating();
        });
    }

    protected function syncTourPackageRating(): void
    {
        $tourPackage = TourPackage::find($this->tour_package_id);

        if (! $tourPackage) {
            return;
        }

        $averageRating = $tourPackage->reviews()->avg('rating');

        $tourPackage->update([
            'rating' => $averageRating !== null ? round($averageRating, 2) : null,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }
}
