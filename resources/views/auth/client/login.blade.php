@extends('layouts.default')
@section('content')
@section('title', 'Login - Options Swift')

<header>
    <a href="{{ route('home.index') }}" class="logo-container">
        <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
    </a>
</header>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Login</h2>
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
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
            <button type="submit" class="btn-primary">Login</button>
        </form>
        <div class="auth-links">
            Don't have an account? <a href="{{ route('register') }}">Sign up now</a>
        </div>
    </div>
</div>

</body>
@endsection
