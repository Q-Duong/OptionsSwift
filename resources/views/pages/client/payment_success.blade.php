@extends('layouts.default')
@section('title', 'Payment Successful - ')
@push('css')
    <style>
        .main-section {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            padding: 20px;
        }

        .success-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(89, 234, 30, 0.1);
            position: relative;
            overflow: hidden;
        }

        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-color);
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: rgba(89, 234, 30, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .check-icon svg {
            width: 40px;
            height: 40px;
            fill: currentColor;
        }

        h2 {
            font-size: 26px;
            font-style: italic;
            margin-bottom: 10px;
            color: #fff;
        }

        p {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 35px;
            line-height: 1.5;
        }

        .btn-dashboard {
            background: var(--primary-color);
            color: #000;
            text-decoration: none;
            padding: 16px 30px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(89, 234, 30, 0.2);
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(89, 234, 30, 0.4);
            background: #4cd115;
        }
    </style>
@endpush
@section('content')
    @include('layouts.section.client.dashboard_header')
    <div class="main-section">
        <div class="success-card">
            <div class="check-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
            </div>
            <h2>Payment Successful!</h2>
            <p>Your transaction has been securely processed. Welcome to the elite ranks of Options Swift. Institutional data
                flow is now unlocked.</p>

            <a href="{{ route('dashboard') }}" class="btn-dashboard">Launch Trading Terminal</a>
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush
