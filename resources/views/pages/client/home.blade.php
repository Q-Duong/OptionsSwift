@extends('layouts.default')

@push('css')
    {{-- <link rel="stylesheet" href="{{ versionResource('assets/css/main.built.css') }}" type="text/css" as="style" /> --}}
    <style>
        /* CSS DÀNH RIÊNG CHO PHẦN PRICING TRÊN TRANG CHỦ */
        .pricing-section { padding: 80px 20px; display: flex; flex-direction: column; align-items: center; }
        .pricing-header { text-align: center; margin-bottom: 50px; }
        .pricing-header h2 { font-size: 2.5rem; font-style: italic; letter-spacing: 1px; margin-bottom: 10px; color: #fff; text-transform: uppercase; }
        .pricing-header h2 span { color: #59ea1e; }
        .pricing-header p { color: #a0aab2; font-size: 1.1rem; }

        .pricing-container { display: flex; gap: 30px; max-width: 1200px; width: 100%; flex-wrap: wrap; justify-content: center; }

        .pricing-card { background: #0d1317; border: 1px solid #1a242c; border-radius: 12px; padding: 40px 30px; width: 350px; display: flex; flex-direction: column; transition: transform 0.3s ease, border-color 0.3s ease; position: relative; }
        .pricing-card:hover { transform: translateY(-10px); border-color: rgba(89, 234, 30, 0.5); }
        
        .pricing-card.popular { border-color: #59ea1e; box-shadow: 0 10px 30px rgba(89, 234, 30, 0.1); transform: scale(1.05); z-index: 10;}
        .pricing-card.popular:hover { transform: scale(1.05) translateY(-10px); }
        .popular-badge { position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #59ea1e; color: #000; font-size: 12px; font-weight: bold; padding: 5px 15px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }

        .plan-name { font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; color: #a0aab2; margin-bottom: 5px; }
        .pricing-card.popular .plan-name { color: #59ea1e; }
        
        .plan-price { font-size: 3rem; font-weight: bold; margin-bottom: 5px; display: flex; align-items: baseline; color: #fff; }
        .plan-price span { font-size: 1rem; color: #a0aab2; font-weight: normal; margin-left: 5px; }
        
        .price-breakdown { color: #a0aab2; font-size: 0.9rem; margin-bottom: 25px; font-style: italic; }

        .feature-list { list-style: none; margin-bottom: 40px; flex: 1; padding: 0; }
        .feature-list li { margin-bottom: 15px; display: flex; align-items: center; font-size: 0.95rem; color: #e2ebe8; }
        .feature-list li::before { content: '✓'; color: #59ea1e; font-weight: bold; margin-right: 10px; font-size: 1.2rem; }
        .feature-list li.disabled { color: #a0aab2; text-decoration: line-through; }
        .feature-list li.disabled::before { content: '✕'; color: #ff4d4d; }

        .btn-subscribe { display: block; text-align: center; text-decoration: none; background: transparent; border: 2px solid #1a242c; color: #fff; padding: 15px; width: 100%; border-radius: 6px; font-size: 1rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s; }
        .btn-subscribe:hover { background: rgba(255, 255, 255, 0.05); border-color: #fff; }
        
        .pricing-card.popular .btn-subscribe { background: #59ea1e; color: #000; border: none; }
        .pricing-card.popular .btn-subscribe:hover { background: #4cd115; box-shadow: 0 0 15px rgba(89, 234, 30, 0.4); }

        .btn-trial { display: inline-block; background: #59ea1e; color: #000; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 5px 20px rgba(89, 234, 30, 0.3); transition: 0.3s; margin-top: 20px; }
        .btn-trial:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(89, 234, 30, 0.5); }

        @media (max-width: 900px) {
            .pricing-card.popular { transform: scale(1); z-index: 1;}
            .pricing-card.popular:hover { transform: translateY(-10px); }
        }
    </style>
@endpush

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
                    <li>Live Options Flow Scanner</li>
                    <li>Real-time VWAP Tracking</li>
                    <li>Standard Support</li>
                    <li class="disabled">Gamma Levels & Block Alerts</li>
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
                    <li>Live Options Flow Scanner</li>
                    <li>Real-time VWAP Tracking</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Priority Support 24/7</li>
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
                    <li>All Semi-Annual Features</li>
                    <li>Institutional Data Feed</li>
                    <li>API Direct Access (Beta)</li>
                    <li>1-on-1 Trading Setup</li>
                </ul>
                @if(Auth::guard('client')->check())
                    <form action="{{ route('client.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_type" value="12_months">
                        <button type="submit" class="btn-subscribe">Get Annual Plan</button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=12_months" class="btn-subscribe">Get Annual Plan</a>
                @endif
            </div>

        </div>
    </section>
@endsection

@push('js')
    {{-- <script src="{{ versionResource('frontend/js/home.min.js') }}" defer></script> --}}
@endpush