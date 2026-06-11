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
        <a href="{{ route('admin.clients.pending') }}"
            class="tab-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">Approvals
            @php
                $pendingCount = \App\Models\Client::where('is_approved', false)->count();
            @endphp
            @if ($pendingCount > 0)
                <span
                    style="background: var(--admin-accent); color: #fff; padding: 1px 6px; font-size: 10px; border-radius: 10px; margin-left: 5px; font-weight: bold;">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>
    </div>
</header>
