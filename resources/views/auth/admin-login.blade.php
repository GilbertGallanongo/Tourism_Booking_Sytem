<x-layout>
    @php
        $submittedAuthForm = old('auth_form');
        $showAdminErrors = $errors->any() && $submittedAuthForm !== 'token';
        $showTokenErrors = $errors->any() && $submittedAuthForm === 'token';
    @endphp

    <div class="auth-panel admin">
        <div class="auth-header">
            <div class="admin-login-brand">
                <span class="admin-login-logo">BOLINAO</span>
                <span class="admin-login-label">Admin Portal</span>
            </div>
            <h1 class="auth-title">Sign in as admin</h1>
            <p class="auth-lead">Manage bookings, packages, payments, reports, and tourist content.</p>
        </div>

        @if ($showAdminErrors)
            <div class="alert alert-error">
                <strong>Admin login failed.</strong>
                <div>Please verify your credentials and try again.</div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
            @csrf
            <input type="hidden" name="auth_form" value="admin">

            <div class="auth-group">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" class="auth-input" required autocomplete="email" />
                @error('email')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="auth-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Enter your password" class="auth-input" required autocomplete="current-password" />
                @error('password')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="auth-group">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn-primary">Sign In</button>
        </form>

        <div class="divider">
            <div></div>
            <span>or use token</span>
            <div></div>
        </div>

        @if ($showTokenErrors)
            <div class="alert alert-error">
                <strong>Token login failed.</strong>
                <div>Please paste a valid admin personal access token.</div>
                @error('token')<p class="error-text">{{ $message }}</p>@enderror
            </div>
        @endif

        <form method="POST" action="{{ route('token.login') }}" class="auth-form">
            @csrf
            <input type="hidden" name="auth_form" value="token">

            <div class="auth-group">
                <label for="token">Personal Access Token</label>
                <input id="token" name="token" type="password" placeholder="Paste your admin token" class="auth-input" required autocomplete="off" />
            </div>

            <div class="auth-group">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="token_remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="token_remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn-secondary">Continue with Token</button>
        </form>

        <div class="divider">
            <div></div>
            <span>Not an admin?</span>
            <div></div>
        </div>

        <div class="auth-helper">
            <p>Use the tourist sign-in page, or continue browsing without an account.</p>
        </div>

        <div class="auth-links">
            <a href="{{ route('login') }}" class="btn-secondary">Tourist Sign In</a>
            <a href="{{ route('home') }}" class="btn-secondary">Continue as Guest</a>
        </div>
    </div>
</x-layout>
