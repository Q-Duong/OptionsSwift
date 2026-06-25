<header>
    <a href="{{ route('home.index') }}" class="logo-container">
        <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="logo-image">
    </a>
    <nav class="nav-links">
        <!-- NẾU KHÁCH ĐÃ ĐĂNG NHẬP THÌ HIỆN NÚT VÀO DASHBOARD -->
        @if(Auth::guard('client')->check())
            <a href="{{ route('dashboard') }}" class="btn-outline">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'text-primary' : '' }}">Login</a>
            <a href="{{ route('register') }}" class="btn-outline {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
        @endif
    </nav>
</header>