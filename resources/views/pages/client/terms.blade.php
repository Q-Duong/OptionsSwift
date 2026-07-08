@extends('layouts.default')
@section('title', 'Terms & Conditions - Options Swift')
@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/terms.css') }}" type="text/css" as="style" />
@endpush
@section('content')
@include('layouts.section.client.header')
<div class="terms-container">
    <div class="glass-card">
        <div class="terms-header">
            <h1>Terms & Conditions</h1>
            <p>Last updated: June 24, 2026</p>
        </div>

        <section class="terms-section">
            <h2>Disclaimer</h2>
            <p>Options Swift provides market data, analysis, and educational content for informational purposes only. Nothing on this website, dashboard, alerts, or communications should be considered financial, investment, legal, or tax advice, nor a recommendation to buy or sell any security or financial instrument.</p>
            <p>Trading stocks, options, futures, and other financial products involves substantial risk and may result in the loss of some or all of your invested capital. Past performance is not indicative of future results, and no representation is made that any account will achieve profits or losses similar to those discussed or displayed.</p>
        </section>

        <section class="terms-section">
            <h2>Risk Acknowledgement</h2>
            <p>By using Options Swift and its services, you acknowledge and accept that:</p>
            <ul class="styled-list">
                <li><strong>No profits are guaranteed.</strong></li>
                <li>Past performance does not guarantee future results.</li>
                <li>Trading involves substantial risk of loss.</li>
                <li>Options Swift is not a registered investment advisor or broker-dealer.</li>
                <li>You assume full responsibility for all trades and investment decisions.</li>
                <li>Options Swift and its owners, employees, and affiliates shall not be liable for any losses, damages, or expenses arising from the use of this service.</li>
            </ul>
        </section>
    </div>
</div>

@include('layouts.section.client.footer')
@endsection