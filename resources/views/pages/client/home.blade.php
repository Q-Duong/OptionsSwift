@extends('layouts.default')

@section('content')
    @include('layouts.section.client.header')

    <section class="hero">
        <img src="{{ asset('assets/images/banner/9866ebb2-c028-4440-84f4-bc72c7cef40e.jpeg') }}"
            alt="Trading Confidence Banner" class="banner-image">
    </section>

    <section class="extra-features">
        <h2 class="section-title"><span>CORE</span> FEATURES</h2>
        <div class="feature-grid">
            <div class="extra-feature-box">
                <div class="extra-feature-icon">🚀</div>
                <h3>PRECISION TRADING</h3>
                <p>Our platform provides highly accurate data to help you make smarter, data-driven decisions.</p>
            </div>
            <div class="extra-feature-box">
                <div class="extra-feature-icon">🛡️</div>
                <h3>MAXIMUM SECURITY</h3>
                <p>Your account and trading data are protected by state-of-the-art security layers and encryption.</p>
            </div>
            <div class="extra-feature-box">
                <div class="extra-feature-icon">👥</div>
                <h3>PRO COMMUNITY</h3>
                <p>Join our exclusive community to share insights and learn from experienced institutional traders.</p>
            </div>
        </div>
    </section>

    <section class="pricing-section">
        <div class="pricing-header">
            <h2>CHOOSE YOUR <span>PLAN</span></h2>
            <p>Select a premium plan to secure your access, or start your free trial below.</p>
            
            @guest('client')
                <a href="{{ route('register', ['trial' => 'true']) }}" class="btn-trial">
                    Start 7-Day Free Trial
                </a>
            @else
                @if (empty($currentPlanId))
                    <form action="{{ route('client.checkout') }}" method="GET" class="form-pricing" style="display:inline;">
                        <input type="hidden" name="plan_type" value=""> 
                        <button type="submit" class="btn-trial" style="border:none; cursor:pointer;">
                            <span class="auth-spinner"></span>
                            <span class="btn-text">Start 7-Day Free Trial</span>
                        </button>
                    </form>
                @endif
            @endguest
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
                            style="background: #222; color: #555; cursor: not-allowed; opacity: 0.7; width: 100%; padding: 16px; border-radius: 15px; border: none; font-weight: bold;">
                            CURRENT PLAN
                        </button>
                    @else
                        @if (Auth::guard('client')->check())
                            <form action="{{ route('client.checkout') }}" method="GET" class="form-pricing">
                                <input type="hidden" name="plan_type" value="{{ $plan['id'] }}">
                                <button type="submit" class="btn-pricing-neon">
                                    <span class="auth-spinner"></span>
                                    <span class="btn-text">{{ isset($currentPlanId) ? 'SWITCH TO THIS PLAN' : 'SELECT PLAN' }}</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('register', ['plan_type' => $plan['id']]) }}" class="btn-pricing-neon" style="text-decoration: none;">
                                <span class="btn-text">SELECT PLAN</span>
                            </a>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

    </section>
@endsection

@push('js')
    {{-- <script src="{{ versionResource('frontend/js/home.min.js') }}" defer></script> --}}
@endpush
