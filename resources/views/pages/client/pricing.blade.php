@extends('layouts.default')
@section('title', 'Pricing & Upgrade - ')
@push('css')
    <style>
        .main-section {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .header-text {
            text-align: center;
            margin-bottom: 50px;
        }

        .header-text h1 {
            font-size: 2.5rem;
            font-style: italic;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .header-text h1 span {
            color: var(--primary-color);
        }

        .header-text p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .pricing-container {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            width: 100%;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pricing-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px 30px;
            width: 350px;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, border-color 0.3s ease;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            border-color: rgba(89, 234, 30, 0.5);
        }

        .pricing-card.popular {
            border-color: var(--primary-color);
            box-shadow: 0 10px 30px rgba(89, 234, 30, 0.1);
            transform: scale(1.05);
            z-index: 10;
        }

        .pricing-card.popular:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary-color);
            color: #000;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .plan-name {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .pricing-card.popular .plan-name {
            color: var(--primary-color);
        }

        .plan-price {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            align-items: baseline;
        }

        .plan-price span {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: normal;
            margin-left: 5px;
        }

        .price-breakdown {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 25px;
            font-style: italic;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 40px;
            flex: 1;
        }

        .feature-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            color: #e2ebe8;
        }

        .feature-list li::before {
            content: '✓';
            color: var(--primary-color);
            font-weight: bold;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .feature-list li.disabled {
            color: var(--text-muted);
            text-decoration: line-through;
        }

        .feature-list li.disabled::before {
            content: '✕';
            color: #ff4d4d;
        }

        .btn-subscribe {
            display: block;
            text-align: center;
            text-decoration: none;
            background: transparent;
            border: 2px solid var(--border-color);
            color: #fff;
            padding: 15px;
            width: 100%;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-subscribe:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: #fff;
        }

        .pricing-card.popular .btn-subscribe {
            background: var(--primary-color);
            color: #000;
            border: none;
        }

        .pricing-card.popular .btn-subscribe:hover {
            background: #4cd115;
            box-shadow: 0 0 15px rgba(89, 234, 30, 0.4);
        }

        .alert-box {
            max-width: 1200px;
            width: 100%;
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.3);
            padding: 15px 20px;
            border-radius: 8px;
            color: #ff4d4d;
            margin-bottom: 30px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .pricing-card.popular {
                transform: scale(1);
                z-index: 1;
            }

            .pricing-card.popular:hover {
                transform: translateY(-10px);
            }
        }
    </style>
@endpush
@section('content')
    @include('layouts.section.client.dashboard_header')

    <div class="main-section">
        @if (session('error'))
            <div class="alert-box">
                <strong>System Alert:</strong> {{ session('error') }}
            </div>
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
                <p>Your access has expired. Choose a plan to keep the data flowing.</p>
            @else
                <h1>WELCOME TO <span>OPTIONS SWIFT</span></h1>
                <p>Select a premium plan to secure your access, or start your free trial below.</p>
                <div style="margin-top: 25px;">
                    <a href="{{ route('register') }}"
                        style="display: inline-block; background: var(--primary-color); color: #000; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 5px 20px rgba(89, 234, 30, 0.3); transition: 0.3s;">
                        🚀 Start 7-Day Free Trial
                    </a>
                </div>
            @endif
        </div>

        <div class="pricing-container">

            <!-- ===================================== -->
            <!-- GÓI 3 THÁNG -->
            <!-- ===================================== -->
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

                @if (Auth::guard('client')->check())
                    <form action="{{ route('client.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_type" value="3_months">
                        <button type="submit" class="btn-subscribe">Select Plan</button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=3_months" class="btn-subscribe">Select Plan</a>
                @endif
            </div>

            <!-- ===================================== -->
            <!-- GÓI 6 THÁNG (MOST POPULAR) -->
            <!-- ===================================== -->
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

                @if (Auth::guard('client')->check())
                    <form action="{{ route('client.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_type" value="6_months">
                        <button type="submit" class="btn-subscribe">Subscribe Now</button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=6_months" class="btn-subscribe">Subscribe Now</a>
                @endif
            </div>

            <!-- ===================================== -->
            <!-- GÓI 12 THÁNG -->
            <!-- ===================================== -->
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

                @if (Auth::guard('client')->check())
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

        @if (Auth::guard('client')->check())
            <div style="margin-top: 40px;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        style="background: none; border: none; color: var(--text-muted); text-decoration: underline; cursor: pointer; font-size: 14px;">
                        Log out and switch account
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush