<header>
    <div class="logo-wrapper">
        <a href="{{ route('dashboard') }}" class="logo-container">
            <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo" class="img-logo">
        </a>
    </div>

    @php
        $user = Auth::guard('client')->user();
        
        // Cấu hình mặc định cho thẻ Badge
        $badgeText = 'FREE TIER';
        $badgeStyle = 'background: rgba(150, 150, 150, 0.15); color: #a0aab2; border: 1px solid #a0aab2;';
        $badgeClass = '';

        if ($user) {
            $isVip = $user->is_vip ?? false;
            $subscription = $user->subscription('default');

            // 1. ƯU TIÊN KIỂM TRA TRẠNG THÁI VIP TRƯỚC TIÊN
            if ($isVip) {
                $badgeText = 'LIFETIME VIP';
                $badgeStyle = 'background: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid #ffc107;';
                $badgeClass = 'header-vip-badge'; 
            } 
            // 2. KIỂM TRA CÁC GÓI CƯỚC STRIPE NẾU KHÔNG PHẢI VIP
            elseif ($subscription && $subscription->valid()) {
                $isOnTrial = $subscription->onTrial();
                $isCancelled = $subscription->canceled();
                $isOnGracePeriod = $subscription->onGracePeriod();
                $isActive = $subscription->active();

                $annualPrice = config('services.stripe.price_annual');
                $semiPrice = config('services.stripe.price_semi_annual');
                $quarterlyPrice = config('services.stripe.price_quarterly');

                // Lấy tên gói cước chính xác
                $planName = 'PRO PLAN';
                if ($annualPrice !== '' && $subscription->hasPrice($annualPrice)) {
                    $planName = 'ANNUAL FLOW';
                } elseif ($semiPrice !== '' && $subscription->hasPrice($semiPrice)) {
                    $planName = 'SEMI-ANNUAL FLOW';
                } elseif ($quarterlyPrice !== '' && $subscription->hasPrice($quarterlyPrice)) {
                    $planName = 'QUARTERLY FLOW';
                }

                // Xét ưu tiên hiển thị trạng thái
                if ($isCancelled && $isOnGracePeriod) {
                    $badgeText = 'CANCELING SOON';
                    $badgeStyle = 'background: rgba(255, 152, 0, 0.15); color: #ff9800; border: 1px solid #ff9800;';
                } elseif ($isOnTrial) {
                    $badgeText = 'FREE TRIAL';
                    $badgeStyle = 'background: rgba(77, 184, 255, 0.15); color: #4db8ff; border: 1px solid #4db8ff;';
                } elseif ($isActive) {
                    $badgeText = strtoupper($planName); // In hoa tên gói (VD: QUARTERLY FLOW)
                    $badgeStyle = 'background: rgba(89, 234, 30, 0.15); color: #59ea1e; border: 1px solid #59ea1e;';
                }
            } 
            // 3. NẾU ĐÃ HẾT HẠN HOẶC KHÔNG CÓ GÓI
            elseif (!$isVip && $user->status === 'active' && !$subscription) {
                $badgeText = 'NO ACTIVE PLAN';
                $badgeStyle = 'background: rgba(255, 77, 77, 0.15); color: #ff4d4d; border: 1px solid #ff4d4d;';
            }
        }
    @endphp
    
    <nav class="nav-links">
        <div class="user-dropdown" id="profileDropdown">
            <button class="dropdown-trigger" id="userMenuBtn">
                {{ $user ? $user->name : 'User' }} <span class="arrow">▼</span>
            </button>

            <div class="dropdown-menu" id="userDropdownMenu" style="min-width: 220px;">
                <div class="user-info-header" style="overflow: hidden; text-align: center; padding-bottom: 5px;">

                    <span class="role-badge badge-client {{ $badgeClass }}"
                        style="{{ $badgeStyle }} padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                        {{ $badgeText }}
                    </span>

                    <p class="user-email"
                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; margin-top: 10px; margin-bottom: 5px; color: #a0aab2; font-size: 13px;">
                        {{ $user ? $user->email : '' }}
                    </p>
                </div>

                <div class="dropdown-divider"></div>

                <a href="{{ route('client.profile') }}" class="dropdown-item {{ request()->routeIs('client.profile') ? 'active' : '' }}">
                    My Account & Billing
                </a>

                <a href="{{ route('client.pricing') }}" class="dropdown-item {{ request()->routeIs('client.pricing') ? 'active' : '' }}">
                    Manage Plan
                </a>

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