@extends('layouts.default')
@section('title', 'Terms & Conditions - Options Swift')
@section('content')
@include('layouts.section.client.header')
<div class="terms-container">
    <div class="glass-card">
        <header class="terms-header">
            <h1>Terms & Conditions</h1>
            <p>Last updated: June 24, 2026</p>
        </header>

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

<style>
    .terms-container {
        max-width: 900px;
        margin: 60px auto;
        padding: 0 20px;
    }
    .glass-card {
        background: linear-gradient(145deg, rgba(10,15,15,0.96), rgba(5,8,8,0.88));
        border: 1px solid rgba(0,255,140,0.15);
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .terms-header h1 {
        font-family: 'IBM Plex Mono', monospace;
        color: #00ff8c;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 5px;
    }
    .terms-header p {
        color: #4a6660;
        font-size: 12px;
        margin-bottom: 40px;
    }
    .terms-section h2 {
        font-family: 'IBM Plex Mono', monospace;
        color: #00ff8c;
        font-size: 18px;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(0,255,140,0.1);
        padding-bottom: 10px;
        margin-top: 40px;
    }
    .terms-section p, .styled-list li {
        color: #e2ebe8;
        line-height: 1.8;
        font-size: 14px;
        margin-bottom: 15px;
    }
    .styled-list {
        padding-left: 20px;
    }
    .styled-list li {
        margin-bottom: 10px;
        list-style-type: square;
        color: #00ff8c;
    }
    .styled-list li span { color: #e2ebe8; }
</style>
@endsection