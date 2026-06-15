<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tourism Booking System') }}</title>
    {{-- Use built Vite assets when available, fallback to legacy public/css and public/js paths --}}
    @php
        $viteCss = public_path('build/assets/app.css');
        $viteJs = public_path('build/assets/app.js');
    @endphp
    @if (file_exists($viteCss))
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    @endif
    {{-- Provide runtime API URL for non-built frontend (useful on free Render plan without Vite build) --}}
    <script>
        window.__API_URL__ = "{{ env('VITE_API_URL', env('APP_URL')) }}";
    </script>
    @if (file_exists($viteJs))
        <script src="{{ asset('build/assets/app.js') }}" defer></script>
    @else
        <script src="{{ asset('js/app.js') }}" defer></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global button enhancement for immediate response
        document.addEventListener('DOMContentLoaded', function() {

            // Enhance all buttons with immediate feedback
            const buttons = document.querySelectorAll('button, .btn, .btn-primary, .btn-secondary, .navbtn, .bolinao-button');

            buttons.forEach(button => {
                // Skip password toggles
                if (button.classList.contains('password-toggle')) return;
                
                // Skip if already has onclick handler or is a link with valid href
                if (button.getAttribute('onclick')) return;
                if (button.tagName === 'A' && button.getAttribute('href') && button.getAttribute('href') !== '#' && !button.getAttribute('href').startsWith('javascript')) return;

                button.addEventListener('click', function(e) {
                    const originalText = this.textContent;
                    const isLink = this.tagName === 'A';
                    const href = isLink ? this.getAttribute('href') : null;

                    // Show immediate feedback
                    this.style.opacity = '0.7';
                    this.style.transform = 'scale(0.98)';

                    // If it's a link, navigate immediately
                    if (isLink && href && href !== '#' && !href.startsWith('javascript')) {
                        e.preventDefault();
                        setTimeout(() => {
                            window.location.href = href;
                        }, 50);
                    }

                    // Reset visual state after short delay
                    setTimeout(() => {
                        this.style.opacity = '1';
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Enhance form submissions
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.textContent;
                        submitBtn.textContent = 'Processing...';
                        submitBtn.style.opacity = '0.7';
                    }
                });
            });
        });
    </script>
</head>
@php
    $webUser = auth()->user();
    $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?: ($webUser?->isAdmin() ? $webUser : null);
    $touristUser = $webUser?->isTourist() ? $webUser : null;
    $isAuthPage = request()->routeIs(['admin.login']);
    $isPackagesPage = request()->routeIs('packages.*');
@endphp
<body class="shell {{ $isPackagesPage ? 'packages-shell ' : '' }}{{ request()->routeIs('home') ? 'home-shell' : ($isAuthPage ? 'auth-shell' : 'page-shell') }}">

    <div class="topbar {{ request()->routeIs('home') ? 'home-topbar' : ($isAuthPage ? 'auth-topbar' : 'page-topbar') }}">
        <div class="frame">
            <nav class="bolinao-nav" aria-label="Main navigation">
                <span class="bolinao-brand" aria-current="page">Bolinao</span>
                <div class="bolinao-navlinks">
                    @if($adminUser)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                        <x-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.index')">Bookings</x-nav-link>
                        <x-nav-link :href="route('admin.packages-stats')" :active="request()->routeIs('admin.packages-stats')">Packages</x-nav-link>
                        <x-nav-link :href="route('admin.promo-packages.index')" :active="request()->routeIs('admin.promo-packages.*')">Promo Packages</x-nav-link>
                        <x-nav-link :href="route('admin.famous-tourist-spots.index')" :active="request()->routeIs('admin.famous-tourist-spots.index')">Tourist Spots</x-nav-link>
                        <x-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.index')">Payments</x-nav-link>
                        <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">Reports</x-nav-link>
                        <form method="POST" action="{{ route('logout') }}" class="nav-form">
                            @csrf
                            <button class="navbtn">Logout</button>
                        </form>
                    @elseif($touristUser)
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-nav-link>
                        <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.index')">Packages</x-nav-link>
                        <x-nav-link :href="route('promo-packages.index')" :active="request()->routeIs('promo-packages.*')">Promo Packages</x-nav-link>
                        <x-nav-link :href="route('famous-tourist-spots.index')" :active="request()->routeIs('famous-tourist-spots.index')">Tourist Spots</x-nav-link>
                        @if(!$touristUser->isGuest())
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                            <a href="{{ route('dashboard') }}" class="profile-link" title="Go to profile">
                                <span class="profile-icon" aria-hidden="true">{{ strtoupper(substr($touristUser->name, 0, 1)) }}</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="nav-form">
                            @csrf
                            <button class="navbtn">Logout</button>
                        </form>
                    @else
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-nav-link>
                        <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.index')">Packages</x-nav-link>
                        <x-nav-link :href="route('promo-packages.index')" :active="request()->routeIs('promo-packages.*')">Promo Packages</x-nav-link>
                        <x-nav-link :href="route('famous-tourist-spots.index')" :active="request()->routeIs('famous-tourist-spots.index')">Tourist Spots</x-nav-link>
                        <a href="#" class="bolinao-button bolinao-button-light" data-auth-open>Login / Register</a>
                    @endif
                </div>
            </nav>
        </div>
    </div>

    <main class="content">
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        {{ $slot }}
    </main>

    <div class="auth-modal" data-auth-modal hidden>
        <div class="auth-modal-backdrop" data-auth-close></div>

        <section class="auth-modal-panel" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
            <button type="button" class="auth-modal-close" data-auth-close aria-label="Close authentication form">&times;</button>

            <div class="auth-modal-heading">
                <p>Bolinao Account</p>
                <h2 id="auth-modal-title">Log in to your account</h2>
            </div>

            <div class="auth-pane active" data-auth-pane="signin">
                @if ($errors->any() && ! old('name') && ! old('password_confirmation') && (isset($errors) && $errors->has(['email', 'password'])))
                    <div class="alert alert-error">
                        <strong>Login Failed</strong>
                        <ul>
                            @foreach ($errors->only(['email', 'password']) as $field => $messages)
                                @foreach ($messages as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                    @csrf

                    <div class="auth-group">
                        <label for="modal-login-email">Email Address</label>
                        <input id="modal-login-email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-login-password">Password</label>
                        <input id="modal-login-password" name="password" type="password" placeholder="Password" class="auth-input" required />
                    </div>

                    <label class="remember-row">
                        <input name="remember" type="checkbox" value="1">
                        <span>Remember me</span>
                    </label>

                    <button type="submit" class="btn-primary">Sign In</button>
                </form>

                <div class="auth-helper">
                    <p>Need admin access? <a href="{{ route('admin.login') }}">Sign in here</a>.</p>
                    <p class="muted">Don't have an account? <a href="#" data-auth-open data-auth-mode="register">Create one now</a>.</p>
                </div>
                <form method="POST" action="{{ route('guest.login') }}" class="auth-form" style="margin-top:1rem">
                    @csrf
                    <button type="submit" class="btn-secondary">Continue as Guest</button>
                    <p class="muted small mt-2">Guest access is view-only. Register or sign in with your account to make bookings.</p>
                </form>
            </div>

            <div class="auth-pane" data-auth-pane="register">
                @if ($errors->any() && (old('name') || old('password_confirmation')) && (isset($errors) && $errors->has(['name', 'email', 'password'])))
                    <div class="alert alert-error">
                        <strong>Registration Error</strong>
                        <ul>
                            @foreach ($errors->only(['name', 'email', 'password']) as $field => $messages)
                                @foreach ($messages as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="auth-group">
                        <label for="modal-register-name">Full name</label>
                        <input id="modal-register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-register-email">Email Address</label>
                        <input id="modal-register-email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-register-password">Password</label>
                        <input id="modal-register-password" name="password" type="password" placeholder="Password" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-register-password-confirm">Confirm Password</label>
                        <input id="modal-register-password-confirm" name="password_confirmation" type="password" placeholder="Confirm Password" class="auth-input" required />
                    </div>

                    <button type="submit" class="btn-primary">Create Account</button>
                </form>
                <div class="auth-helper">
                    <p class="muted">Already have an account? <a href="#" data-auth-open data-auth-mode="signin">Sign in instead</a>.</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
