@extends('layouts.default')
@section('title', 'Account & Billing - ')

@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/profile.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    @include('layouts.section.client.dashboard_header')

    @php
        // ĐỌC THÔNG TIN TỪ CASHIER & DATABASE
        $client = Auth::guard('client')->user();
        $subscription = $client->subscription('default');

        // Trích xuất cờ VIP
        $isVip = $client->is_vip ?? false;

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

            $annualPrice = config('services.stripe.price_annual');
            $semiPrice = config('services.stripe.price_semi_annual');
            $quarterlyPrice = config('services.stripe.price_quarterly');

            if ($annualPrice !== '' && $subscription->hasPrice($annualPrice)) {
                $planName = 'Annual Flow';
            } elseif ($semiPrice !== '' && $subscription->hasPrice($semiPrice)) {
                $planName = 'Semi-Annual Flow';
            } elseif ($quarterlyPrice !== '' && $subscription->hasPrice($quarterlyPrice)) {
                $planName = 'Quarterly Flow';
            } else {
                $planName = 'PRO PLAN';
            }
        }

        // XỬ LÝ LOGIC HIỂN THỊ BADGE (ƯU TIÊN VIP LÊN ĐẦU)
        if ($isVip) {
            $statusText = 'LIFETIME VIP';
            $statusClass = 'status-vip';
            $showUpgradeBtn = false;
        } elseif ($isCancelled && $isOnGracePeriod) {
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

        // XỬ LÝ LOGIC NGÀY TÍNH PHÍ (ƯU TIÊN VIP)
        if ($isVip) {
            $nextBillingDate = 'Never Expires';
        } elseif ($isOnTrial) {
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

        // TÍNH TOÁN SỐ DƯ TÍN DỤNG (CREDIT) TỪ STRIPE
        $creditBalance = 0;
        if ($client->hasStripeId()) {
            try {
                $customer = $client->asStripeCustomer();
                if ($customer->balance < 0) {
                    $creditBalance = abs($customer->balance / 100);
                }
            } catch (\Exception $e) {
                // Bỏ qua nếu lỗi API
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
                        @if ($isVip)
                            <div class="vip-text-glow">VIP SPONSOR</div>
                            <div style="color: #a0aab2; font-size: 13px; font-style: italic; line-height: 1.4;">
                                Complimentary lifetime access.<br>No billing required.
                            </div>
                        @elseif ($isCancelled && $isOnGracePeriod)
                            <div style="color: #ff9800; font-weight: bold; margin-bottom: 4px; font-size: 12px;">CANCELED
                                (GRACE PERIOD)</div>
                            <div style="color: #a0aab2; font-size: 13px; font-style: italic; line-height: 1.4;">
                                You have canceled your subscription.<br>Access remains active until
                                <strong style="color:#fff;">
                                    {{ $client->subscription('default')->ends_at->format('M d, Y') }}
                                </strong>.
                            </div>
                        @elseif($isOnTrial)
                            <div style="color: #00ff66; font-weight: bold; margin-bottom: 4px; font-size: 12px;">TRIAL
                                ACTIVE</div>
                            <div style="color: #a0aab2; font-size: 13px; font-style: italic; line-height: 1.4;">
                                Upgrades to <strong style="color:#fff;">{{ $planName }}</strong> after trial
                                ends.<br>You will not be charged until then.
                            </div>
                        @elseif($isActive && !$isCancelled)
                            <div style="color: #00ff66; font-weight: bold;">Auto-renews via Stripe</div>
                        @else
                            <div style="color: #ff4d4d; font-weight: bold;">Expired / No Plan</div>
                        @endif
                    </div>
                </div>

                <div class="info-group mt-3">
                    @if ($isVip)
                        <label>Expiration Date</label>
                        <div class="info-val" style="color: #ffc107; font-weight: bold; font-size: 16px;">
                            {{ $nextBillingDate }}
                        </div>
                    @elseif ($isCancelled && $isOnGracePeriod)
                        <label>Expires On</label>
                        <div class="info-val" style="color: #ff9800; font-weight: bold; font-size: 16px;">
                            {{ $client->subscription('default')->ends_at->format('M d, Y') }}
                        </div>
                    @else
                        <label>Next Billing Date</label>
                        <div class="info-val" style="color: #fff; font-weight: bold; font-size: 16px;">
                            {{ $nextBillingDate }}
                        </div>
                    @endif
                </div>

                @if ($creditBalance > 0 && !$isVip)
                    <div
                        style="background-color: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.15); border-left: 4px solid #22c55e; padding: 16px; border-radius: 15px; margin-top: 24px; margin-bottom: 24px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 20px; height: 20px; color: #22c55e;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                                </path>
                            </svg>
                            <h4
                                style="color: #22c55e; font-size: 0.95rem; font-weight: 600; margin: 0; letter-spacing: 0.5px;">
                                GRAND OPENING PROMO APPLIED
                            </h4>
                        </div>
                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; line-height: 1.6;">
                            You have a <strong
                                style="color: #f8fafc; font-weight: 700; font-size: 0.9rem;">${{ number_format($creditBalance, 2) }}</strong>
                            credit balance. Your next renewal will be automatically covered by this credit. <span
                                style="color: #22c55e; font-weight: 500;">No charge will be made to your card.</span>
                        </p>
                    </div>
                @endif

                @if ($showUpgradeBtn)
                    <div style="margin-top: 30px;">
                        <a href="{{ route('client.pricing') }}"
                            style="display: block; text-align: center; background: #59ea1e; color: #000; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: bold; text-transform: uppercase;">
                            🚀 Upgrade To Pro
                        </a>
                    </div>
                @endif

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
                                        <td style="font-family: monospace;">{{ $order->order_code }}</td>
                                        <td style="text-transform: capitalize; font-weight: bold;">{{ $order->plan_type }}
                                        </td>
                                        <td style="color: #fff; font-weight: bold;">
                                            @if ($order->amount < 0)
                                                <span
                                                    style="color: #22c55e;">-${{ number_format(abs($order->amount), 2) }}</span>
                                            @else
                                                ${{ number_format($order->amount, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if ($order->amount <= 0)
                                                <span class="bill-status bill-credited">Credited</span>
                                            @elseif (in_array(strtolower($order->status), ['paid', 'completed', 'succeeded']))
                                                <span class="bill-status bill-paid">Paid</span>
                                            @else
                                                <span class="bill-status bill-pending">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->amount <= 0 || in_array(strtolower($order->status), ['paid', 'completed', 'succeeded']))
                                                <span style="color: #a0aab2; font-size: 12px;">-</span>
                                            @else
                                                <a href="{{ url('/pricing') }}" class="btn-action"
                                                    style="border: 1px solid #ff9800; color: #ff9800; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; transition: 0.3s;">Buy
                                                    Plan</a>
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
@endsection

@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush
