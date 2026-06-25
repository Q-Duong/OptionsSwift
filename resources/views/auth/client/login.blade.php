@extends('layouts.default')
@section('content')
@section('title', 'Login - ')
<style>
    .alert-status {
        padding: 12px 15px;
        background: rgba(89, 234, 30, 0.1);
        /* Nền xanh neon trong suốt */
        color: var(--primary-color);
        /* Chữ xanh neon */
        border: 1px solid rgba(89, 234, 30, 0.3);
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        box-shadow: 0 0 15px rgba(89, 234, 30, 0.1);
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<header>
    <a href="{{ route('home.index') }}" class="logo-container">
        <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
    </a>
</header>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Login</h2>
        @if (session('verified_status'))
            <div class="alert-status">
                {{ session('verified_status') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="max-width: 400px; width: 100%; margin: 0 auto 20px auto; background: rgba(255, 77, 77, 0.1); border: 1px solid rgba(255, 77, 77, 0.3); padding: 15px 20px; border-radius: 8px; color: #ff4d4d; text-align: center; font-weight: bold;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your Email" value="{{ old('email') }}"
                    required>
                @error('email')
                    <span style="color: red; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group" style="text-align: right; margin-top: -10px;">
                <a href="#" style="font-size: 13px; color: var(--text-muted);">Forgot password?</a>
            </div>

            <div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; text-align: left;">
                <input type="checkbox" name="disclaimer" id="disclaimer" required
                    style="margin-top: 3px; cursor: pointer; min-width: 16px; min-height: 16px; accent-color: #59ea1e;">
                <label for="disclaimer"
                    style="color: #94a3b8; font-size: 13px; line-height: 1.5; cursor: pointer; margin: 0;">
                    I understand that Options Swift provides educational and informational content only and does not
                    provide financial advice. I acknowledge that trading involves risk and that I am solely responsible
                    for my trading decisions and any resulting gains or losses.
                </label>
            </div>
            <!-- KẾT THÚC: DISCLAIMER CHECKBOX -->
            <button type="submit" class="btn-primary">Login</button>
        </form>
        <div class="auth-links">
            Don't have an account? <a href="{{ route('register') }}">Sign up now</a>
        </div>
        <div style="margin-top: 15px; font-size: 12px; color: #64748b; text-align: center;">
            By logging in, you agree to our 
            <a href="{{ route('terms') }}" target="_blank" style="color: #38bdf8; text-decoration: underline;">Terms & Conditions</a>.
        </div>
    </div>
</div>

</body>
@endsection
