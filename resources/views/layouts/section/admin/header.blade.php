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

        <a href="{{ route('admin.clients.pending') }}"
            class="tab-link {{ request()->routeIs('admin.clients.pending') ? 'active' : '' }}">Approvals
            @php
                // Đã cập nhật: Chỉ đếm những khách Pending VÀ Chưa từng có ngày hết hạn
                $pendingCount = \App\Models\Client::where('status', 'pending')
                                                  ->whereNull('expires_at')
                                                  ->count();
            @endphp
            @if ($pendingCount > 0)
                <span style="background: #ff4d4d; color: #fff; padding: 2px 8px; font-size: 11px; border-radius: 12px; margin-left: 5px; font-weight: bold;">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>
    </div>
</header>