@extends('layouts.default')
@section('content')
@section('title', 'Register - Options Swift')

<header>
    <a href="{{ route('home.index') }}" class="logo-container">
        <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
    </a>
</header>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Create an Account</h2>
        
        <form action="{{ route('register.submit') }}" method="POST">
            @csrf <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g., John Doe" required>
                @error('name')
                    <span style="color: #ff4d4d; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                @error('email')
                    <span style="color: #ff4d4d; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
                @error('password')
                    <span style="color: #ff4d4d; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm your password" required>
            </div>
            
            <button type="submit" class="btn-primary">Register</button>
        </form>
        
        <div class="auth-links">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
</div>

</body>
@endsection
