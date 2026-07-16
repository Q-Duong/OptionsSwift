@extends('layouts.default')
@section('title', 'Manage Plan & Pricing - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/pricing.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    @include('layouts.section.client.dashboard_header')

    @if (auth('client')->check() && auth('client')->user()->is_vip)
        <!-- GIAO DIỆN DÀNH RIÊNG CHO VIP -->
        <div
            style="text-align: center; padding: 60px 20px; background: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 16px; max-width: 600px; margin: 40px auto;">
            <div style="margin-bottom: 25px; display: flex; justify-content: center;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffe55c" />
                            <stop offset="50%" stop-color="#ffc107" />
                            <stop offset="100%" stop-color="#cc9900" />
                        </linearGradient>
                        <filter id="glowCrown" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="3" result="blur" />
                            <feComposite in="SourceGraphic" in2="blur" operator="over" />
                        </filter>
                    </defs>

                    <!-- Hình dáng vương miện -->
                    <path filter="url(#glowCrown)"
                        d="M4.5 17L3 6l5.5 4.5L12 4l3.5 6.5L21 6l-1.5 11h-15zM20 19c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1v-1h16v1z"
                        fill="url(#goldGradient)" />
                </svg>
            </div>
            <h2
                style="color: #ffc107; margin-bottom: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px;">
                LIFETIME VIP ACCESS
            </h2>
            <p style="color: #a0aab2; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                You have been granted complimentary, unrestricted access to our platform.
                There is no need to purchase any additional plans. Enjoy your lifetime privileges!
            </p>
            <a href="{{ route('dashboard') }}"
                style="background: #ffc107; color: #000; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; text-transform: uppercase;">
                Return to Dashboard
            </a>
        </div>
    @else
        <div class="manage-plan-container">
            @if (session('error'))
                <div class="alert-box"><strong>System Alert:</strong> {{ session('error') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert-box alert-warning">
                    <strong>Notice:</strong> {{ session('warning') }}
                </div>
            @endif

            <div class="header-text">
                <h1>MANAGE YOUR <span>PLAN</span></h1>
            </div>

            @php
                $user = Auth::guard('client')->check() ? Auth::guard('client')->user() : null;
                $hasSubscription = $user && $user->subscribed('default');
                $onGracePeriod = $hasSubscription && $user->subscription('default')->onGracePeriod();

                $safeCurrentPlanId = $currentPlanId ?? null;

                // 1. Lấy ra gói đang dùng
                $currentPlan = collect($plans)->firstWhere('id', $safeCurrentPlanId);
                $currentPriceValue = 0;
                if ($currentPlan) {
                    $currentPriceValue = floatval(preg_replace('/[^0-9.]/', '', $currentPlan['price']));
                }

                // 2. Tách mảng: Gói cao hơn (Higher Plans) và Gói thấp hơn (Lower Plans)
                $higherPlans = [];
                $lowerPlans = [];

                if ($hasSubscription) {
                    // Lọc gói đắt hơn và sắp xếp (Ưu tiên Popular -> Giá cao xuống thấp)
                    $higherPlans = collect($plans)
                        ->where('id', '!=', $safeCurrentPlanId)
                        ->filter(function ($plan) use ($currentPriceValue) {
                            return floatval(preg_replace('/[^0-9.]/', '', $plan['price'])) > $currentPriceValue;
                        })
                        ->sortByDesc(function ($plan) {
                            $price = floatval(preg_replace('/[^0-9.]/', '', $plan['price']));
                            return (isset($plan['popular']) ? 10000 : 0) + $price;
                        })
                        ->all();

                    // Lọc gói rẻ hơn (Sắp xếp giá từ cao xuống thấp)
                    $lowerPlans = collect($plans)
                        ->where('id', '!=', $safeCurrentPlanId)
                        ->filter(function ($plan) use ($currentPriceValue) {
                            return floatval(preg_replace('/[^0-9.]/', '', $plan['price'])) < $currentPriceValue;
                        })
                        ->sortByDesc(function ($plan) {
                            return floatval(preg_replace('/[^0-9.]/', '', $plan['price']));
                        })
                        ->all();
                } else {
                    // Khách chưa mua gói nào -> Gom hết vào 1 mảng chung
                    $higherPlans = collect($plans)
                        ->sortByDesc(function ($plan) {
                            $price = floatval(preg_replace('/[^0-9.]/', '', $plan['price']));
                            return (isset($plan['popular']) ? 10000 : 0) + $price;
                        })
                        ->all();
                }
            @endphp

            @if ($hasSubscription && $currentPlan)
                <div class="plan-section">
                    <span class="section-title">Current Plan</span>
                    <div class="plan-card-active">
                        <div class="plan-info">
                            <h3>{{ $currentPlan['name'] }}</h3>
                            <p>{{ $currentPlan['breakdown'] }}</p>

                            @if ($onGracePeriod)
                                <span class="badge-cancel">Cancels on
                                    {{ $user->subscription('default')->ends_at->format('M d, Y') }}</span>
                            @else
                                <span class="badge-active">Active</span>
                            @endif

                            <details class="feature-dropdown">
                                <summary>View all benefits</summary>
                                <ul class="feature-list-small">
                                    @foreach ($features as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        </div>

                        <div class="plan-price-block">
                            <div class="price">
                                {{ $currentPlan['price'] }}
                                <span
                                    class="duration">/{{ str_replace(' ', '', $currentPlan['name'] == 'Quarterly Flow' ? '3 months' : ($currentPlan['name'] == 'Semi-Annual Flow' ? '6 months' : '12 months')) }}</span>
                            </div>

                            @if ($onGracePeriod)
                                <form method="POST" action="{{ route('client.subscription.resume') }}"
                                    id="formResumeSubscription" style="margin-top: 15px;">
                                    @csrf
                                    <button type="submit" id="btnResumeSubscription" class="btn-text-danger"
                                        style="color: #22c55e; display: inline-flex; align-items: center; justify-content: center; border: none; background: transparent; padding: 0; cursor: pointer;">
                                        <svg id="spinnerResume"
                                            style="display: none; animation: spin 1s linear infinite; margin-right: 6px; height: 16px; width: 16px;"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" style="opacity: 0.25;"></circle>
                                            <path fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                style="opacity: 0.75;"></path>
                                        </svg>
                                        <span class="btn-text" style="font-weight: inherit;">Resume Subscription</span>
                                    </button>
                                </form>
                            @else
                                <div style="margin-top: 15px;">
                                    <button type="button" id="btnTriggerCancel" class="btn-text-danger"
                                        style="border: none; background: transparent; padding: 0; cursor: pointer;">
                                        Cancel Subscription
                                    </button>

                                    <form method="POST" action="{{ route('client.subscription.cancel') }}"
                                        id="formCancelSubscription" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (count($higherPlans) > 0)
                <div class="plan-section" style="{{ $hasSubscription ? 'margin-top: 50px;' : '' }}">
                    <span class="section-title">{{ $hasSubscription ? 'HIGHER PLANS' : 'AVAILABLE PLANS' }}</span>

                    <div class="plans-list">
                        @foreach ($higherPlans as $plan)
                            <div class="plan-card-option {{ isset($plan['popular']) ? 'is-popular' : '' }}">
                                <div class="plan-info">
                                    <h3>
                                        {{ $plan['name'] }}
                                        @if (isset($plan['popular']))
                                            <span class="badge-tag badge-recommended">Recommended</span>
                                        @endif
                                        @if ($hasSubscription)
                                            <span class="badge-tag badge-upgrade">Upgrade</span>
                                        @endif
                                    </h3>
                                    <p>{{ $plan['breakdown'] }}</p>

                                    <details class="feature-dropdown">
                                        <summary>View all benefits</summary>
                                        <ul class="feature-list-small">
                                            @foreach ($features as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </div>

                                <div class="plan-price-block">
                                    <div class="price">
                                        {{ $plan['price'] }}
                                        <span
                                            class="duration">/{{ str_replace(' ', '', $plan['name'] == 'Quarterly Flow' ? '3 months' : ($plan['name'] == 'Semi-Annual Flow' ? '6 months' : '12 months')) }}</span>
                                    </div>

                                    <form action="{{ route('client.checkout') }}" method="GET" class="form-pricing"
                                        style="margin-top: 15px;">
                                        <input type="hidden" name="plan_type" value="{{ $plan['id'] }}">
                                        <button type="submit" class="btn-pricing-neon">
                                            <span class="auth-spinner"></span>
                                            <span class="btn-text">
                                                {{ $hasSubscription ? 'UPGRADE PLAN' : 'SUBSCRIBE NOW' }}
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (count($lowerPlans) > 0)
                <div class="plan-section" style="margin-top: 50px;">
                    <span class="section-title">LOWER PLANS</span>

                    <div class="plans-list">
                        @foreach ($lowerPlans as $plan)
                            <div class="plan-card-option">
                                <div class="plan-info">
                                    <h3>
                                        {{ $plan['name'] }}
                                        <span class="badge-tag badge-downgrade">Downgrade</span>
                                    </h3>
                                    <p>{{ $plan['breakdown'] }}</p>

                                    <details class="feature-dropdown">
                                        <summary>View all benefits</summary>
                                        <ul class="feature-list-small">
                                            @foreach ($features as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </div>

                                <div class="plan-price-block">
                                    <div class="price">
                                        {{ $plan['price'] }}
                                        <span
                                            class="duration">/{{ str_replace(' ', '', $plan['name'] == 'Quarterly Flow' ? '3 months' : ($plan['name'] == 'Semi-Annual Flow' ? '6 months' : '12 months')) }}</span>
                                    </div>

                                    <form action="{{ route('client.checkout') }}" method="GET" class="form-pricing"
                                        style="margin-top: 15px;">
                                        <input type="hidden" name="plan_type" value="{{ $plan['id'] }}">
                                        <button type="submit" class="btn-pricing-outline">
                                            <span class="auth-spinner"></span>
                                            <span class="btn-text">DOWNGRADE PLAN</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
    @include('layouts.section.client.retention_modal')
@endsection

@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush
