<?php

namespace App\Models;

use App\Support\UploadedImage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id',
        'name',
        'description',
        'location',
        'price',
        'duration_days',
        'max_guests',
        'time_start',
        'time_end',
        'image',
        'category',
        'status',
        'rating',
    ];

    private function normalizeImagePath(): string
    {
        return UploadedImage::normalize($this->image);
    }

    private function publicPackageImagePath(): ?string
    {
        $exactName = 'images/' . $this->name;
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            $candidate = $exactName . '.' . $extension;
            if (file_exists(public_path($candidate))) {
                return $candidate;
            }
        }

        $packageSlug = Str::slug($this->name);
        foreach (glob(public_path('images/*')) ?: [] as $file) {
            if (! is_file($file)) {
                continue;
            }

            $filename = pathinfo($file, PATHINFO_FILENAME);
            if (Str::slug($filename) === $packageSlug) {
                return 'images/' . basename($file);
            }
        }

        $fallbacks = [
            'Enchanted Cave & Shell Museum' => 'images/enchanted cave and shell museum.png',
            'Patar Beach Family Picnic' => 'images/patar beach sunrise escape.png',
            'Bolinao Heritage Food Walk' => 'images/Heritage & Culture Museum Tour.jpg',
            'Bolinao Mangrove Eco Cruise' => 'images/Ecotourism & Conservation Areas.png',
        ];

        $fallback = $fallbacks[$this->name] ?? null;
        if ($fallback && file_exists(public_path($fallback))) {
            return $fallback;
        }

        return null;
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            if ($packageImagePath = $this->publicPackageImagePath()) {
                return asset($packageImagePath);
            }

            return asset('images/package-default.svg');
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        $imagePath = $this->normalizeImagePath();

        if ($imagePath === '') {
            return asset('images/package-default.svg');
        }

        // Optimize: Check most likely paths first and cache the result
        $cacheKey = 'package_image_url_v3_' . $this->id . '_' . md5($imagePath . '|' . $this->name);
        
        return cache()->remember($cacheKey, now()->addHours(24), function() use ($imagePath) {
            // Public storage root (storage/app/public) - most common
            if (Storage::disk('public')->exists($imagePath)) {
                return UploadedImage::url($imagePath);
            }

            // Public path directly under public/
            if (file_exists(public_path($imagePath))) {
                return asset($imagePath);
            }

            // Storage public path under storage/app/public/images/
            if (Storage::disk('public')->exists('images/' . $imagePath)) {
                return UploadedImage::url('images/' . $imagePath);
            }

            // Public path under public/images/
            if (file_exists(public_path('images/' . $imagePath))) {
                return asset('images/' . $imagePath);
            }

            // If image is already stored under storage/ path in the DB use it directly
            if (file_exists(public_path('storage/' . $imagePath))) {
                return asset('storage/' . $imagePath);
            }

            if ($packageImagePath = $this->publicPackageImagePath()) {
                return asset($packageImagePath);
            }

            return asset('images/package-default.svg');
        });
    }

    public function getHasImageAttribute(): bool
    {
        if (! $this->image) {
            return $this->publicPackageImagePath() !== null;
        }

        if (str_starts_with($this->image, 'http')) {
            return true;
        }

        $imagePath = $this->normalizeImagePath();

        if ($imagePath === '') {
            return false;
        }

        if (file_exists(public_path($imagePath))) {
            return true;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return true;
        }

        if (file_exists(public_path('images/' . $imagePath))) {
            return true;
        }

        if (Storage::disk('public')->exists('images/' . $imagePath)) {
            return true;
        }

        if (file_exists(public_path('storage/' . $imagePath))) {
            return true;
        }

        return $this->publicPackageImagePath() !== null;
    }

    public static function categoryLabels(): array
    {
        return [
            'natural' => 'Natural Attractions',
            'cultural' => 'Cultural & Historical Sites',
            'recreational' => 'Recreational & Adventure Spots',
            'accommodation' => 'Accommodation & Hospitality',
            'events' => 'Events & Festivals',
            'ecotourism' => 'Ecotourism & Conservation Areas',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? 'Uncategorized';
    }

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'max_guests' => 'integer',
        'time_start' => 'string',
        'time_end' => 'string',
        'rating' => 'decimal:2',
    ];

    public function getTimeStartFormattedAttribute(): string
    {
        if (! $this->time_start) {
            return 'TBD';
        }

        return Carbon::parse($this->time_start)->format('g:i A');
    }

    public function getTimeEndFormattedAttribute(): string
    {
        if (! $this->time_end) {
            return 'TBD';
        }

        return Carbon::parse($this->time_end)->format('g:i A');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes) && $this->attributes['reviews_avg_rating'] !== null) {
            return round($this->attributes['reviews_avg_rating'], 2);
        }

        if ($this->relationLoaded('reviews')) {
            return round($this->reviews->avg('rating') ?? 0, 2);
        }

        return round($this->rating ?? 0, 2);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBolinao(Builder $query)
    {
        return $query->where('location', 'like', '%Bolinao%');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
