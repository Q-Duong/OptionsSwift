@extends('layouts.default')
@section('title', 'Secure Checkout - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/invoice.css') }}" type="text/css" as="style" />
@endpush
@section('content')
    @include('layouts.section.client.dashboard_header')
    <div class="main-section">
        <div class="checkout-wrapper">
            <div class="invoice-card">
                <div class="invoice-header">
                    <h2>OPTIONS <span>SWIFT</span></h2>
                    <div class="order-id">REF: {{ $order->order_code }}</div>
                </div>

                <div class="order-details">
                    <div class="detail-row">
                        <span>Client Account</span>
                        <span>{{ Auth::guard('client')->user()->email }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Subscription Plan</span>
                        <span style="color: var(--primary-color);">{{ $planName }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Billing Cycle</span>
                        <span>{{ $order->plan_type === 'lifetime' ? 'One-time payment' : 'Non-recurring' }}</span>
                    </div>
                    <div class="total-row">
                        <span style="font-size: 16px; color: #fff;">Total Due</span>
                        <span>${{ number_format($order->amount, 2) }}</span>
                    </div>
                </div>

                <div class="payment-methods">
                    <form action="{{ route('client.invoice.pay', $order->order_code) }}" method="POST"
                        style="width: 100%;">
                        @csrf
                        <button type="submit" class="btn-pay" style="width: 100%; margin-bottom: 12px;">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                <path
                                    d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                            </svg>
                            Simulate Payment (Test)
                        </button>
                    </form>

                    <button class="btn-pay crypto" onclick="alert('Crypto gateway integration pending.')">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                            <path
                                d="M17.06 11.57c.59-.61.94-1.46.94-2.57 0-2.2-1.79-3.5-4.5-3.5H8v14h6c2.83 0 4.88-1.42 4.88-3.83 0-1.5-.75-2.61-1.82-3.2v-.9zm-5.56-4.07h2c1.1 0 1.83.65 1.83 1.5 0 .86-.73 1.5-1.83 1.5h-2v-3zm2.5 8.5h-2.5v-3h2.5c1.22 0 2 .7 2 1.5 0 .81-.78 1.5-2 1.5z" />
                        </svg>
                        Pay with Crypto
                    </button>
                </div>

                <div class="trust-badges">
                    <div class="secure-text">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                        </svg>
                        SSL Encrypted Checkout
                    </div>
                    <span>Guaranteed safe & secure transaction</span>
                </div>

                <a href="{{ route('client.pricing') }}" class="btn-cancel">← Return to Pricing Plans</a>
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
