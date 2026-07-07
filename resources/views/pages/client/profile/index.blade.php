@extends('layouts.default')
@section('title', 'Account & Billing - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/profile.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    @include('layouts.section.client.dashboard_header')

    @php
        // ĐỌC THÔNG TIN TỪ STRIPE (CASHIER)
        $client = Auth::guard('client')->user();
        $subscription = $client->subscription('default');

        // Khởi tạo các trạng thái mặc định
        $statusText = 'NO ACTIVE PLAN';
        $statusClass = '';
        $showUpgradeBtn = true;
        $nextBillingDate = 'None';
        $planName = 'Pro Plan';

        // Các cờ (flags) để điều khiển giao diện
        $isOnTrial = false;
        $isCancelled = false;
        $isOnGracePeriod = false;
        $isActive = false;

        if ($subscription) {
            $isOnTrial = $subscription->onTrial();
            $isCancelled = $subscription->canceled();
            $isOnGracePeriod = $subscription->onGracePeriod();
            $isActive = $subscription->active();

            $annualPrice = (string) env('STRIPE_PRICE_ANNUAL', '');
            $semiPrice = (string) env('STRIPE_PRICE_SEMI_ANNUAL', '');
            $quarterlyPrice = (string) env('STRIPE_PRICE_QUARTERLY', '');

            if ($annualPrice !== '' && $subscription->hasPrice($annualPrice)) {
                $planName = 'Annual Flow';
            } elseif ($semiPrice !== '' && $subscription->hasPrice($semiPrice)) {
                $planName = 'Semi-Annual Flow';
            } elseif ($quarterlyPrice !== '' && $subscription->hasPrice($quarterlyPrice)) {
                $planName = 'Quarterly Flow';
            } else {
                $planName = 'PRO PLAN';
            }

            // Xử lý logic hiển thị Badge (Trạng thái thẻ góc phải)
            if ($isCancelled && $isOnGracePeriod) {
                $statusText = 'CANCELING SOON';
                $statusClass = 'status-grace';
                $showUpgradeBtn = false;
            } elseif ($isOnTrial) {
                $statusText = 'FREE TRIAL';
                $statusClass = 'status-trial';
                $showUpgradeBtn = false; 
            } elseif ($isActive) {
                $statusText = strtoupper($planName);
                $statusClass = 'status-pro';
                $showUpgradeBtn = false;
            }

            // Xử lý logic Ngày tính phí tiếp theo (Next Billing Date)
            if ($isOnTrial) {
                $nextBillingDate = $subscription->trial_ends_at->format('M d, Y');
            } elseif ($isCancelled && !$isOnGracePeriod) {
                $nextBillingDate = 'None';
            } elseif ($isActive) {
                try {
                    $upcomingInvoice = $client->upcomingInvoice();
                    $nextBillingDate = $upcomingInvoice
                        ? \Carbon\Carbon::parse($upcomingInvoice->created)->format('M d, Y')
                        : 'Auto-renews via Stripe';
                } catch (\Exception $e) {
                    $nextBillingDate = 'Auto-renews via Stripe';
                }
            }
        }
    @endphp

    @if (session('success'))
        <div class="alert-success-inline">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (!$client->hasVerifiedEmail())
        <div class="alert-glass-banner">
            <div class="alert-glass-content">
                <div class="alert-glass-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="alert-glass-text">
                    <b>ACTION REQUIRED:</b> Your email (<b>{{ $client->email }}</b>) is unverified.
                    <br>
                    Please authenticate to unlock full features and proceed to checkout.
                </div>
            </div>

            <form action="{{ route('verification.send') }}" method="POST" style="margin: 0;" id="formResendEmail">
                @csrf
                <button type="submit" class="btn-glass-resend" id="btnResendEmail">
                    <span class="btn-spinner" id="spinnerResend"></span>
                    <span id="textResend">Resend</span>
                </button>
            </form>
        </div>
    @endif

    <div class="profile-container">

        <div class="profile-sidebar">
            <div class="card-box" style="margin-bottom: 30px;">
                <div class="card-title">
                    Account Details
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </div>

                <div class="info-group">
                    <label>Full Name</label>
                    <div class="info-val">{{ $client->name }}</div>
                </div>

                <div class="info-group">
                    <label>Email Address</label>
                    <div class="info-val">{{ $client->email }}</div>
                </div>

                <div class="info-group mt-3">
                    <label>Access Status</label>
                    <div class="info-val">
                        @if($isCancelled && $isOnGracePeriod)
                            <div style="color: #ff9800; font-weight: bold; margin-bottom: 4px; font-size: 12px;">CANCELED (GRACE PERIOD)</div>
                            <div style="color: #a0aab2; font-size: 13px; font-style: italic; line-height: 1.4;">
                                You have canceled your subscription.<br>Access remains active until <strong style="color:#fff;">{{ $nextBillingDate }}</strong>.
                            </div>
                        @elseif($isOnTrial)
                            <div style="color: #00ff66; font-weight: bold; margin-bottom: 4px; font-size: 12px;">TRIAL ACTIVE</div>
                            <div style="color: #a0aab2; font-size: 13px; font-style: italic; line-height: 1.4;">
                                Upgrades to <strong style="color:#fff;">{{ $planName }}</strong> after trial ends.<br>You will not be charged until then.
                            </div>
                        @elseif($isActive && !$isCancelled)
                            <div style="color: #00ff66; font-weight: bold;">Auto-renews via Stripe</div>
                        @else
                            <div style="color: #ff4d4d; font-weight: bold;">Expired / No Plan</div>
                        @endif
                    </div>
                </div>

                <div class="info-group mt-3">
                    <label>Next Billing Date</label>
                    <div class="info-val" style="color: #fff; font-weight: bold; font-size: 16px;">
                        {{ $nextBillingDate }}
                    </div>
                </div>

                @if ($showUpgradeBtn)
                    <div style="margin-top: 30px;">
                        <a href="{{ route('client.pricing') }}"
                            style="display: block; text-align: center; background: #59ea1e; color: #000; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: bold; text-transform: uppercase;">🚀 Upgrade To Pro</a>
                    </div>
                @endif

                <div style="margin-top: 30px;">
                    @if ($isActive && !$isCancelled)
                        <form id="formCancelSubscription" method="POST" action="{{ route('client.subscription.cancel') }}">
                            @csrf
                            <button type="button" id="btnTriggerCancel" class="btn-cancel-danger">
                                CANCEL SUBSCRIPTION
                            </button>
                        </form>
                    @elseif ($isCancelled && $isOnGracePeriod)
                        <form id="formResumeSubscription" method="POST" action="{{ route('client.subscription.resume') }}">
                            @csrf
                            <button type="submit" id="btnResumeSubscription" class="btn-resume-subscription">
                                <span id="spinnerResume" class="btn-spinner"></span>
                                <span class="btn-text">Resume Subscription</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="billing-history">
            <div class="card-box">
                <div class="card-title">Billing History</div>

                @if ($orders->isEmpty())
                    <div style="text-align: center; padding: 40px 20px; color: #a0aab2;">
                        <div style="font-size: 40px; margin-bottom: 10px;">🧾</div>
                        <p>You don't have any billing history yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="billing-table">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: bold;">{{ $order->order_code }}
                                        </td>
                                        <td style="text-transform: capitalize;">
                                            {{ $order->plan_type }}
                                        </td>
                                        <td style="color: #fff; font-weight: bold;">${{ number_format($order->amount, 2) }}
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if (in_array($order->status, ['paid', 'completed', 'succeeded']))
                                                <span class="bill-status bill-paid" style="color: #59ea1e;">Paid</span>
                                            @else
                                                <span class="bill-status bill-pending"
                                                    style="color: #ff9800;">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array($order->status, ['paid', 'completed', 'succeeded']))
                                                <span style="color: #a0aab2; font-size: 12px;">-</span>
                                            @else
                                                <a href="{{ url('/pricing') }}" class="btn-action">Buy Plan</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    @include('layouts.section.client.retention_modal')
@endsection

@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush
