@extends('layouts.default')

@section('content')
    @include('layouts.section.client.header')

    <section class="hero">
        <img src="{{ asset('assets/images/banner/9866ebb2-c028-4440-84f4-bc72c7cef40e.jpeg') }}" alt="Trading Confidence Banner" class="banner-image">
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

    <!-- ===================================== -->
    <!-- PHẦN BẢNG GIÁ (PRICING SECTION) -->
    <!-- ===================================== -->
    <section class="pricing-section">
        <div class="pricing-header">
            <h2>CHOOSE YOUR <span>PLAN</span></h2>
            <p>Select a premium plan to secure your access, or start your free trial below.</p>
            @if(!Auth::guard('client')->check())
                <a href="{{ route('register') }}" class="btn-trial">🚀 Start 7-Day Free Trial</a>
            @endif
        </div>

        <div class="pricing-container">
            
            <!-- GÓI 3 THÁNG -->
            <div class="pricing-card">
                <h3 class="plan-name">Quarterly Flow</h3>
                <div class="plan-price">$120<span>/3 months</span></div>
                <div class="price-breakdown">Just $40 per month</div>
                <ul class="feature-list">
                    <li>Live Option Flow Scanner</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Institutional Data Feed</li>
                    <li>Option Chain Imbalance , Pressure $ GEX analysis</li>
                    <li>Estimated Hedge Shares</li>
                </ul>
                @if(Auth::guard('client')->check())
                    <form action="{{ route('client.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_type" value="3_months">
                        <button type="submit" class="btn-subscribe">Select Plan</button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=3_months" class="btn-subscribe">Select Plan</a>
                @endif
            </div>

            <!-- GÓI 6 THÁNG (MOST POPULAR) -->
            <div class="pricing-card popular">
                <div class="popular-badge">Most Popular</div>
                <h3 class="plan-name">Semi-Annual Flow</h3>
                <div class="plan-price">$210<span>/6 months</span></div>
                <div class="price-breakdown">Save 12% - Just $35 per month</div>
                <ul class="feature-list">
                    <li>Live Option Flow Scanner</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Institutional Data Feed</li>
                    <li>Option Chain Imbalance , Pressure $ GEX analysis</li>
                    <li>Estimated Hedge Shares</li>
                </ul>
                @if(Auth::guard('client')->check())
                    <form action="{{ route('client.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_type" value="6_months">
                        <button type="submit" class="btn-subscribe">Subscribe Now</button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=6_months" class="btn-subscribe">Subscribe Now</a>
                @endif
            </div>

            <!-- GÓI 12 THÁNG -->
            <div class="pricing-card">
                <h3 class="plan-name">Annual Flow</h3>
                <div class="plan-price">$360<span>/12 months</span></div>
                <div class="price-breakdown">Save 25% - Just $30 per month</div>
                <ul class="feature-list">
                    <li>Live Option Flow Scanner</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Institutional Data Feed</li>
                    <li>Option Chain Imbalance , Pressure $ GEX analysis</li>
                    <li>Estimated Hedge Shares</li>
                </ul>
                @if(Auth::guard('client')->check())
                    <form action="{{ route('client.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_type" value="12_months">
                        <button type="submit" class="btn-subscribe">Select Plan</button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=12_months" class="btn-subscribe">Select Plan</a>
                @endif
            </div>

        </div>
    </section>
@endsection

@push('js')
    {{-- <script src="{{ versionResource('frontend/js/home.min.js') }}" defer></script> --}}
@endpush