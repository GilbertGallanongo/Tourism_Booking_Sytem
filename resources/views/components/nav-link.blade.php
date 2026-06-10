@props(['active' => false, 'href'])

<a href="{{ $href }}" class="navlink {{ $active ? 'active' : '' }}">
    {{ $slot }}
</a>
