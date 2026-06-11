@extends('layouts.default')
@push('css')
    {{-- <link rel="stylesheet" href="{{ versionResource('assets/css/main.built.css') }}" type="text/css" as="style" /> --}}
@endpush
@section('content')
    <header>
        <a href="{{ route('home.index') }}" class="logo-container">
            <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
        </a>
        <nav class="nav-links">
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'text-primary' : '' }}">
                Login
            </a>
            <a href="{{ route('register') }}" class="btn-outline {{ request()->routeIs('register') ? 'active' : '' }}">
                Register
            </a>
        </nav>
    </header>

    <section class="hero">
        <img src="{{ asset('assets/images/banner/9866ebb2-c028-4440-84f4-bc72c7cef40e.jpeg') }}" alt="Trading Confidence Banner" class="banner-image">
    </section>

    <section class="extra-features">
        <h2 class="section-title"><span>CORE</span> FEATURES</h2>
        <div class="feature-grid">
            <div class="extra-feature-box">
                <div class="extra-feature-icon">🚀</div>
                <h3>PRECISION TRADING</h3>
                <p>Our platform provides highly accurate data to help you make smarter, data-driven decisions.</p>
            </div>
            <div class="extra-feature-box">
                <div class="extra-feature-icon">🛡️</div>
                <h3>MAXIMUM SECURITY</h3>
                <p>Your account and trading data are protected by state-of-the-art security layers and encryption.</p>
            </div>
            <div class="extra-feature-box">
                <div class="extra-feature-icon">👥</div>
                <h3>PRO COMMUNITY</h3>
                <p>Join our exclusive community to share insights and learn from experienced institutional traders.</p>
            </div>
        </div>
    </section>
@endsection
@push('js')
    {{-- <script src="{{ versionResource('frontend/js/home.min.js') }}" defer></script> --}}
@endpush
