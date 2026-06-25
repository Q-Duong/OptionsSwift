@extends('layouts.default')
@section('title', 'Account & Billing - ')
@push('css')
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }

        .card-box {
            background: #0d1317;
            border: 1px solid #1a242c;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #1a242c;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Status Badge */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-trial {
            background: rgba(77, 184, 255, 0.1);
            color: #4db8ff;
            border: 1px solid rgba(77, 184, 255, 0.3);
        }

        .status-pro {
            background: rgba(89, 234, 30, 0.1);
            color: #59ea1e;
            border: 1px solid rgba(89, 234, 30, 0.3);
        }

        /* Info List */
        .info-group {
            margin-bottom: 20px;
        }

        .info-group label {
            display: block;
            color: #a0aab2;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-group .info-val {
            color: #fff;
            font-size: 16px;
            font-weight: 500;
        }

        /* Table Billing */
        .billing-table {
            width: 100%;
            border-collapse: collapse;
        }

        .billing-table th {
            text-align: left;
            padding: 12px 15px;
            color: #a0aab2;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #1a242c;
        }

        .billing-table td {
            padding: 15px;
            border-bottom: 1px solid #1a242c;
            color: #e2ebe8;
            font-size: 14.5px;
        }

        .billing-table tr:last-child td {
            border-bottom: none;
        }

        .bill-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .bill-paid {
            background: rgba(89, 234, 30, 0.1);
            color: #59ea1e;
        }

        .bill-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .btn-action {
            display: inline-block;
            background: transparent;
            border: 1px solid #59ea1e;
            color: #59ea1e;
            padding: 6px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-action:hover {
            background: #59ea1e;
            color: #000;
        }
    </style>
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