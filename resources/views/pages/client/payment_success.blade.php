@extends('layouts.default')
@section('title', 'Payment Successful - ')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/payment.css') }}" type="text/css" as="style" />
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
