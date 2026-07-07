@extends('layouts.default')
@section('title', 'Pricing & Upgrade - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/pricing.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    @include('layouts.section.client.dashboard_header')

    <div class="main-section">
        @if (session('error'))
            <div class="alert-box"><strong>System Alert:</strong> {{ session('error') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert-box"
                style="background: rgba(255, 193, 7, 0.1); border-color: rgba(255, 193, 7, 0.3); color: #ffc107;">
                <strong>Notice:</strong> {{ session('warning') }}
            </div>
        @endif

        <div class="header-text">
            @if (Auth::guard('client')->check())
                <h1>OPTIONS <span>SWIFT</span></h1>
            @else
                <h1>WELCOME TO <span>OPTIONS SWIFT</span></h1>
                <p>Select a premium plan to secure your access, or start your free trial below.</p>
                <div style="margin-top: 25px;">
                    <a href="{{ route('register') }}"
                        style="display: inline-block; background: var(--primary-color); color: #000; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 5px 20px rgba(89, 234, 30, 0.3); transition: 0.3s;">🚀
                        Start 7-Day Free Trial</a>
                </div>
            @endif
        </div>

        <div class="pricing-container">
            @foreach ($plans as $plan)
                <div class="pricing-card {{ isset($plan['popular']) ? 'popular' : '' }}">
                    @if (isset($plan['popular']))
                        <div class="popular-badge">Most Popular</div>
                    @endif

                    <h3 class="plan-name">{{ $plan['name'] }}</h3>
                    <div class="plan-price">
                        {{ $plan['price'] }}<span>/{{ str_replace(' ', '', $plan['name'] == 'Quarterly Flow' ? '3 months' : ($plan['name'] == 'Semi-Annual Flow' ? '6 months' : '12 months')) }}</span>
                    </div>
                    <div class="price-breakdown">{{ $plan['breakdown'] }}</div>

                    <ul class="feature-list">
                        @foreach ($features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>

                    @if (isset($currentPlanId) && $plan['id'] === $currentPlanId)
                        <button class="btn-subscribe" disabled
                            style="background: #222; color: #555; border: 1px solid #333; cursor: not-allowed; opacity: 0.7;">
                            CURRENT PLAN
                        </button>
                    @else
                        <form action="{{ route('client.checkout') }}" method="GET" class="form-pricing">
                            <input type="hidden" name="plan_type" value="{{ $plan['id'] }}">
                            <button type="submit" class="btn-pricing-neon">
                                <span class="auth-spinner"></span>
                                <span class="btn-text">
                                    @if (Auth::guard('client')->check())
                                        {{ $currentPlanId ? 'SWITCH TO THIS PLAN' : 'SELECT PLAN' }}
                                    @else
                                        SUBSCRIBE NOW
                                    @endif
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush