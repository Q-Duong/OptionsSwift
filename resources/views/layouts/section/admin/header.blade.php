<header>
    <div class="header-top">
        <div class="logo-panel">ADMIN <span>PANEL</span></div>
        <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>

    <div class="nav-tabs">
        <a href="{{ route('admin.html.index') }}"
            class="tab-link {{ request()->routeIs('admin.html.*') ? 'active' : '' }}">HTML Widgets</a>
            
        <a href="{{ route('admin.clients.index') }}"
            class="tab-link {{ request()->routeIs('admin.clients.index') ? 'active' : '' }}">All Clients</a>
    </div>
</header>