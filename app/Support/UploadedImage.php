<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class UploadedImage
{
    public static function normalize(?string $path): string
    {
        $normalized = ltrim((string) $path, '/');
        $normalized = preg_replace('#^(public/storage/|public/|storage/)#i', '', $normalized);

        return ltrim($normalized, '/');
    }

    public static function url(?string $path): string
    {
        $normalized = self::normalize($path);

        if ($normalized === '') {
            return asset('images/package-default.svg');
        }

        if (str_starts_with((string) $path, 'http')) {
            return (string) $path;
        }

        if (file_exists(public_path($normalized))) {
            return asset($normalized);
        }

        if (Storage::disk('public')->exists($normalized)) {
            return route('uploads.show', ['path' => $normalized]);
        }

        if (file_exists(public_path('storage/' . $normalized))) {
            return asset('storage/' . $normalized);
        }

        return asset('images/package-default.svg');
    }
}
