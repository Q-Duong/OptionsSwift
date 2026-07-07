<header>
    <div class="logo-wrapper">
        <a href="{{ route('dashboard') }}" class="logo-container">
            <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="img-logo">
        </a>
    </div>

    @php
        $user = Auth::guard('client')->user();
        
        $isTrial = false;
        $isPro = false;
        $daysLeft = 0;

        // Logic check trạng thái gói chuẩn Stripe
        if ($user) {
            $subscription = $user->subscription('default');
            
            // Nếu có gói và gói đó đang hợp lệ (active, trialing, hoặc on grace period)
            if ($subscription && $subscription->valid()) {
                if ($subscription->onTrial()) {
                    $isTrial = true;
                    $diff = now()->diffInDays($subscription->trial_ends_at, false);
                    $daysLeft = $diff < 0 ? 0 : $diff + 1; 
                } else {
                    $isPro = true; // Gói trả phí đang hoạt động
                }
            }
        }
    @endphp
    
    <nav class="nav-links">
        <div class="user-dropdown" id="profileDropdown">
            <button class="dropdown-trigger" id="userMenuBtn">
                {{ $user ? $user->name : 'User' }} <span class="arrow">▼</span>
            </button>

            <div class="dropdown-menu" id="userDropdownMenu" style="min-width: 220px;">
                <div class="user-info-header" style="overflow: hidden;">

                    {{-- HIỂN THỊ THẺ TAG DỰA TRÊN 3 TRẠNG THÁI --}}
                    @if ($isTrial)
                        <span class="role-badge badge-client"
                            style="background: rgba(77, 184, 255, 0.15); color: #4db8ff; border: 1px solid #4db8ff; padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                            FREE TRIAL
                        </span>
                    @elseif ($isPro)
                        <span class="role-badge badge-client"
                            style="background: rgba(89, 234, 30, 0.15); color: #59ea1e; border: 1px solid #59ea1e; padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                            PRO PLAN
                        </span>
                    @else
                        <span class="role-badge badge-client"
                            style="background: rgba(150, 150, 150, 0.15); color: #a0aab2; border: 1px solid #a0aab2; padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                            FREE TIER
                        </span>
                    @endif

                    <p class="user-email"
                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; margin-top: 10px; margin-bottom: 5px;">
                        {{ $user ? $user->email : '' }}
                    </p>
                </div>

                <div class="dropdown-divider"></div>

                <a href="{{ route('client.profile') }}" class="dropdown-item"
                    style="display: block; color: #e2ebe8; text-decoration: none; padding: 10px 15px; transition: 0.2s;">
                    My Account & Billing
                </a>

                {{-- NÚT UPGRADE HIỆN CHO CẢ NGƯỜI CHƯA CÓ GÓI VÀ NGƯỜI ĐANG DÙNG THỬ --}}
                @if (!$isPro)
                    <a href="{{ route('client.pricing') }}" class="dropdown-item"
                        style="display: block; padding: 10px 15px; color: #59ea1e; text-decoration: none; font-weight: bold; background: rgba(89, 234, 30, 0.05); border-left: 3px solid #59ea1e; margin-top: 5px;">
                         Upgrade To Pro @if($isTrial) ({{ $daysLeft }} days left) @endif
                    </a>
                @endif

                <div class="dropdown-divider"></div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="dropdown-item btn-logout-action"
                        style="color: #ff4d4d; width: 100%; text-align: left; background: none; border: none; cursor: pointer; padding: 10px 15px;">Logout</button>
                </form>
            </div>
        </div>
    </nav>
</header>