<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FamousTouristSpot extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    private function normalizeImagePath(): string
    {
        $imagePath = ltrim((string) $this->image, '/');

        if ($imagePath === '') {
            return '';
        }

        $imagePath = preg_replace('#^(public/|storage/)#i', '', $imagePath);
        $imagePath = preg_replace('#^(public/storage/)#i', '', $imagePath);

        return ltrim($imagePath, '/');
    }

    private function publicFallbackImagePath(): ?string
    {
        $fallbacks = [
            'Bolinao Falls' => 'images/Bolinao Waterfall Trekking.png',
            'Patar White Beach' => 'images/patar beach sunrise escape.png',
            'Enchanted Cave' => 'images/enchanted cave and shell museum.png',
            'Cape Bolinao Lighthouse' => 'images/Bolinao Lighthouse Adventure.webp',
            'Balingasay River' => 'images/Ecotourism & Conservation Areas.png',
            'Tara Falls' => 'images/Bolinao Waterfall Trekking.png',
        ];

        $path = $fallbacks[$this->name] ?? null;

        if ($path && file_exists(public_path($path))) {
            return asset($path);
        }

        return null;
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return $this->publicFallbackImagePath() ?? asset('images/package-default.svg');
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        $imagePath = $this->normalizeImagePath();

        if ($imagePath === '') {
            return $this->publicFallbackImagePath() ?? asset('images/package-default.svg');
        }

        $disk = config('filesystems.default') ?? env('FILESYSTEM_DISK', 'public');

        // If the file exists on the configured disk (S3 or local), return its URL
        try {
            if (Storage::disk($disk)->exists($imagePath)) {
                return Storage::disk($disk)->url($imagePath);
            }
        } catch (\Exception $e) {
            // ignore and fall back to local public checks
        }

        if (file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }

        if (file_exists(public_path('storage/' . $imagePath))) {
            return asset('storage/' . $imagePath);
        }

        return $this->publicFallbackImagePath() ?? asset('images/package-default.svg');
    }
}
