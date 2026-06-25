@extends('layouts.default')
@section('title', 'Pricing & Upgrade - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/pricing.css') }}" type="text/css" as="style" />
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
                    <li>Live Option Flow Scanner</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Institutional Data Feed</li>
                    <li>Option Chain Imbalance , Pressure $ GEX analysis</li>
                    <li>Estimated Hedge Shares</li>
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
                    <li>Live Option Flow Scanner</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Institutional Data Feed</li>
                    <li>Option Chain Imbalance , Pressure $ GEX analysis</li>
                    <li>Estimated Hedge Shares</li>
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
                    <li>Live Option Flow Scanner</li>
                    <li>Gamma Levels & Block Alerts</li>
                    <li>Institutional Data Feed</li>
                    <li>Option Chain Imbalance , Pressure $ GEX analysis</li>
                    <li>Estimated Hedge Shares</li>
                </ul>

                @if (Auth::guard('client')->check())
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