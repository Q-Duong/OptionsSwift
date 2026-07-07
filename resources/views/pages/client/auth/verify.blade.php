@extends('layouts.default')
@section('title', 'Verify Your Email - ')

@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/verify.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    <div class="verify-page-wrapper">
        <div class="verify-card">

            {{-- Icon SVG Ngầu + Hiệu ứng Pulse --}}
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>

            <h2 class="verify-title">Verify Your Access</h2>

            @if (session('success'))
                <div class="alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <p class="verify-text">
                Welcome to Options Swift! We've dispatched a secure link to <br>
                <b>{{ Auth::guard('client')->user()->email }}</b>. <br><br>
                Please authenticate your email to unlock your account and proceed to the dashboard.
            </p>

            <form method="POST" action="{{ route('verification.send') }}" id="formResendVerify">
                @csrf
                <button type="submit" class="btn-resend" id="btnResendVerify">
                    <span class="verify-spinner"></span>
                    <span id="textResendVerify">Resend Authentication Link</span>
                </button>
            </form>

            <div class="logout-wrapper">
                <p style="color: #a0aab2; font-size: 14px; margin-bottom: 10px;">Used the wrong email?</p>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout-minimal">Log out and register again</button>
                </form>
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