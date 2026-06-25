@extends('layouts.default')
@section('content')
@section('title', 'Register - ')

<header>
    <a href="{{ route('home.index') }}" class="logo-container">
        <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
    </a>
</header>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Create an Account</h2>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="selected_plan" value="{{ request('plan') }}">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g., John Doe" required>
                @error('name')
                    <span
                        style="color: #ff4d4d; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email"
                    required>
                @error('email')
                    <span
                        style="color: #ff4d4d; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
                @error('password')
                    <span
                        style="color: #ff4d4d; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm your password" required>
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

            <button type="submit" class="btn-primary">Register</button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>

        <div style="margin-top: 15px; font-size: 12px; color: #64748b; text-align: center;">
            By logging in, you agree to our 
            <a href="{{ route('terms') }}" target="_blank" style="color: #38bdf8; text-decoration: underline;">Terms & Conditions</a>.
        </div>
    </div>
</div>

</body>
@endsection
