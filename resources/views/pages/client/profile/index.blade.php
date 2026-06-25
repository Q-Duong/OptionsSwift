@extends('layouts.default')
@section('title', 'Account & Billing - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/profile.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    @include('layouts.section.client.dashboard_header')
    <div class="profile-container">

        <div class="profile-sidebar">
            <div class="card-box" style="margin-bottom: 30px;">
                <div class="card-title">
                    Account Details
                    @if ($isTrial)
                        <span class="status-badge status-trial">FREE TRIAL</span>
                    @else
                        <span class="status-badge status-pro">PRO PLAN</span>
                    @endif
                </div>

                <div class="info-group">
                    <label>Full Name</label>
                    <div class="info-val">{{ $client->name }}</div>
                </div>

                <div class="info-group">
                    <label>Email Address</label>
                    <div class="info-val">{{ $client->email }}</div>
                </div>

                <div class="info-group">
                    <label>Access Expires</label>
                    <div class="info-val" style="color: {{ $daysLeft <= 3 ? '#ff4d4d' : '#fff' }}">
                        {{ $client->expires_at ? \Carbon\Carbon::parse($client->expires_at)->format('F j, Y') : 'N/A' }}
                        <span style="font-size: 13px; color: #a0aab2;">({{ $daysLeft }} days left)</span>
                    </div>
                </div>

                @if ($isTrial)
                    <div style="margin-top: 30px;">
                        <a href="{{ route('client.pricing') }}"
                            style="display: block; text-align: center; background: #59ea1e; color: #000; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: bold; text-transform: uppercase;">🚀
                            Upgrade To Pro</a>
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
                    <div style="overflow-x: auto;">
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
                                        <td style="font-family: monospace; font-weight: bold;">{{ $order->order_code }}</td>
                                        <td style="text-transform: capitalize;">
                                            {{ str_replace('_', ' ', $order->plan_type) }}</td>
                                        <td style="color: #fff; font-weight: bold;">${{ number_format($order->amount, 2) }}
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if ($order->status == 'paid')
                                                <span class="bill-status bill-paid">Paid</span>
                                            @else
                                                <span class="bill-status bill-pending">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->status == 'pending')
                                                <a href="{{ route('client.invoice', $order->order_code) }}"
                                                    class="btn-action">Pay Now</a>
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