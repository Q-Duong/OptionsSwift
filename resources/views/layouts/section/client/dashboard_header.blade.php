<header>
    <div class="logo-wrapper">
        <a href="{{ route('dashboard') }}" class="logo-container">
            <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="img-logo">
        </a>
    </div>

    @yield('search_bar')

    @php
        $user = Auth::guard('client')->user();
        $isTrial = false;
        $daysLeft = 0;

        // Nếu biến $user chưa được Controller truyền ra, thì lấy từ Auth
        $currentUser = $user ?? Auth::guard('client')->user();

        if ($currentUser && $currentUser->expires_at) {
            // 1. Logic kiểm tra Trial (khoảng cách tạo và hết hạn <= 8 ngày)
            if ($currentUser->created_at->diffInDays($currentUser->expires_at) <= 8) {
                $isTrial = true;
            }

            // 2. Tính số ngày còn lại để hiển thị cho nút Upgrade
            // (Chỉ cần tính 1 lần duy nhất ở đây)
            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($currentUser->expires_at), false);
            $daysLeft = $daysLeft < 0 ? 0 : $daysLeft + 1;
        }
    @endphp
    
    <nav class="nav-links">
        <div class="user-dropdown" id="profileDropdown">
            <button class="dropdown-trigger" id="userMenuBtn">
                {{ $user->name }} <span class="arrow">▼</span>
            </button>

            <div class="dropdown-menu" id="userDropdownMenu" style="min-width: 220px;">
                <div class="user-info-header" style="overflow: hidden;">

                    @if ($isTrial)
                        <span class="role-badge badge-client"
                            style="background: rgba(77, 184, 255, 0.15); color: #4db8ff; border: 1px solid #4db8ff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                            ⚡ FREE TRIAL
                        </span>
                    @else
                        <span class="role-badge badge-client"
                            style="background: rgba(89, 234, 30, 0.15); color: #59ea1e; border: 1px solid #59ea1e; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"
                                style="vertical-align: text-top; margin-right: 2px;">
                                <path
                                    d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg> PRO PLAN
                        </span>
                    @endif

                    <p class="user-email"
                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; margin-top: 10px; margin-bottom: 5px;">
                        {{ $user->email }}
                    </p>
                </div>

                <div class="dropdown-divider"></div>

                <!-- CHỈ HIỂN THỊ MENU CLIENT NẾU KHÔNG PHẢI ADMIN -->
                @if (!Auth::guard('web')->check())
                    <a href="{{ route('client.profile') }}" class="dropdown-item"
                        style="display: block; color: #e2ebe8; text-decoration: none; padding: 10px 15px; transition: 0.2s;">
                        👤 My Account & Billing
                    </a>

                    @if ($isTrial)
                        <a href="{{ route('client.pricing') }}" class="dropdown-item"
                            style="display: block; padding: 10px 15px; color: #59ea1e; text-decoration: none; font-weight: bold; background: rgba(89, 234, 30, 0.05); border-left: 3px solid #59ea1e; margin-top: 5px;">
                            🚀 Upgrade To Pro ({{ $daysLeft }} days left)
                        </a>
                    @endif

                    <div class="dropdown-divider"></div>
                @endif

                <!-- LOGOUT FORMS KẾ THỪA CỦA BÁC -->
                @if (Auth::guard('web')->check())
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item btn-logout-action"
                            style="color: #ff4d4d; width: 100%; text-align: left; background: none; border: none; cursor: pointer; padding: 10px 15px;">Logout
                            Admin</button>
                    </form>
                @else
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item btn-logout-action"
                            style="color: #ff4d4d; width: 100%; text-align: left; background: none; border: none; cursor: pointer; padding: 10px 15px;">Logout
                            Account</button>
                    </form>
                @endif
            </div>
        </div>
    </nav>
</header>
