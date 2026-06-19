<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImageViewerController extends Controller
{
    public function show(Request $request): View
    {
        $src = trim((string) $request->query('src', ''));

        abort_if($src === '' || Str::startsWith($src, 'data:'), 404);

        $title = trim((string) $request->query('title', 'Image preview')) ?: 'Image preview';
        $backUrl = $this->safeBackUrl($request, (string) $request->query('back', ''));

        return view('image-viewer', [
            'src' => $src,
            'title' => $title,
            'backUrl' => $backUrl,
        ]);
    }

    private function safeBackUrl(Request $request, string $backUrl): string
    {
        $backUrl = trim($backUrl);

        if ($backUrl === '') {
            return url()->previous() ?: route('home');
        }

        if (Str::startsWith($backUrl, ['/'])) {
            return $backUrl;
        }

        $host = parse_url($backUrl, PHP_URL_HOST);

        if ($host && strcasecmp($host, $request->getHost()) === 0) {
            return $backUrl;
        }

        return route('home');
    }
}
