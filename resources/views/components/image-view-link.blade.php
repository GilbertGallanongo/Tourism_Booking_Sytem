@props([
    'src',
    'title' => 'Image preview',
    'back' => request()->fullUrl(),
])

@php
    $linkStyle = trim('display:block;text-decoration:none;color:inherit; ' . ($attributes->get('style') ?? ''));
@endphp

<a {{ $attributes->except('style')->merge([
    'href' => route('images.view', ['src' => $src, 'title' => $title, 'back' => $back]),
    'aria-label' => 'View image: ' . $title,
    'style' => $linkStyle,
]) }}>
    {{ $slot }}
</a>
