<header>
    <a href="{{ route('home.index') }}" class="logo-container">
        <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
    </a>
    <nav class="nav-links">
        @if (Auth::guard('client')->check())
            <a href="{{ route('dashboard') }}" class="btn-badge">Dashboard</a>
        @else
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
            <a href="{{ route('register') }}"
                class="btn-badge {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
        @endif
    </nav>
</header>
