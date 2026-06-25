@extends('layouts.default')
@section('title', 'Institutional Trading Terminal - ')

@section('content')
    @include('layouts.section.client.dashboard_header')

    @if (session('success'))
        <div id="welcomeToast"
            style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); background: rgba(89, 234, 30, 0.15); border: 1px solid var(--primary-color); color: var(--primary-color); padding: 12px 25px; border-radius: 30px; z-index: 9999; font-size: 14px; font-weight: bold; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 10px; backdrop-filter: blur(5px);">
            <span style="font-size: 18px;">✅</span> {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('welcomeToast');
                if (toast) {
                    toast.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translate(-50%, -20px)';
                    setTimeout(() => toast.remove(), 800);
                }
            }, 5000);
        </script>
    @endif

    @php
        $isTrial = false;
        $daysLeft = 0;

        // Nếu biến $user chưa được Controller truyền ra, thì lấy từ Auth
        $currentUser = $user ?? Auth::guard('client')->user();

        if ($currentUser && $currentUser->expires_at) {
            // 1. Logic kiểm tra Trial (khoảng cách tạo và hết hạn <= 8 ngày)
            if ($currentUser->created_at->diffInDays($currentUser->expires_at) <= 8) {
                $isTrial = true;
            }

            // 2. Tính số ngày còn lại để hiển thị cho nút Upgrade
            // (Chỉ cần tính 1 lần duy nhất ở đây)
            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($currentUser->expires_at), false);
            $daysLeft = $daysLeft < 0 ? 0 : $daysLeft + 1;
        }
    @endphp
    @if (isset($isTrial) && $isTrial)
        <div class="trial-upgrade-banner">
            <div class="banner-content">
                <div class="banner-icon">⚡</div>
                <div class="banner-text">
                    <h4>
                        YOU ARE ON A <span>FREE TRIAL</span> ACCOUNT
                        <span class="days-left">{{ $daysLeft }} {{ Str::plural('Day', $daysLeft) }} Left</span>
                    </h4>
                    <p>Upgrade to a Premium Plan now to unlock permanent Institutional Live Data Flow, Gamma Levels, and
                        Block Alerts.</p>
                </div>
            </div>
            <div class="banner-action">
                <a href="{{ route('client.pricing') }}" class="btn-banner-upgrade">Upgrade To Pro</a>
            </div>
        </div>
    @endif

    <div class="search-center-container">
        <div class="custom-search-dropdown" id="dataFlowDropdown">
            <button class="dropdown-toggle-btn" id="dropdownToggleBtn">
                <span id="dropdownBtnText" style="color: var(--primary-color); font-weight: bold;">🔍 Search Ticker
                    Flow</span>
                <span class="arrow">▼</span>
            </button>

            <div class="dropdown-content-area">
                <input type="text" id="searchInput" class="dropdown-search-input"
                    placeholder="Type Ticker & Press Enter...">

                <div class="dropdown-flow-list" id="flowList">

                    <button class="flow-item" data-ticker="SPY">SPY</button>
                    <button class="flow-item" data-ticker="SPX">SPX</button>
                    <button class="flow-item" data-ticker="QQQ">QQQ</button>
                    <button class="flow-item" data-ticker="AAPL">AAPL</button>
                    <button class="flow-item" data-ticker="MSFT">MSFT</button>
                    <button class="flow-item" data-ticker="NVDA">NVDA</button>
                    <button class="flow-item" data-ticker="TSLA">TSLA</button>
                    <button class="flow-item" data-ticker="AMZN">AMZN</button>
                    <button class="flow-item" data-ticker="META">META</button>

                    @foreach ($widgets as $widget)
                        @php $tickerKey = strtoupper(trim($widget->key)); @endphp

                        @if (strpos(strtolower($tickerKey), 'scanner') === false &&
                                strtolower($tickerKey) !== 'option_chain' &&
                                !in_array($tickerKey, ['SPY', 'SPX', 'QQQ', 'AAPL', 'MSFT', 'NVDA', 'TSLA', 'AMZN', 'META']))
                            <button class="flow-item" style="color: #4db8ff;" data-ticker="{{ $tickerKey }}">
                                {{ $tickerKey }} <span
                                    style="font-size: 9px; color: var(--text-muted); float: right;">(Custom)</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="terminal-container">
        <main class="main-column">
            <div class="main-header">
                <h2>MARKET SCANNER</h2>
                <span class="sub-header">LIVE DATA STREAM <span
                        style="color: var(--primary-color);">●</span></span>
            </div>

            <div class="scanner-container">
                @php
                    $scannerWidget = $widgets->firstWhere('key', 'market_scanner');
                @endphp

                @if ($scannerWidget)
                    <iframe id="mainScannerIframe"
                        src="{{ route('html.secure', 'market_scanner') }}?v={{ $scannerWidget->updated_at->timestamp }}"
                        sandbox="allow-same-origin allow-scripts allow-popups"></iframe>
                @else
                    <div
                        style="padding: 40px; text-align: center; color: #ff4d4d; border: 1px dashed #ff4d4d; margin: 20px; border-radius: 8px;">
                        <strong>SYSTEM ALERT:</strong> <br><br>
                        Vui lòng nhắc Admin tạo một khối HTML Widget với Identifier Key là
                        <strong>market_scanner</strong> để hiển thị màn hình chính.
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection

@push('js')
    <script type="text/javascript" defer>
        const optionChainBaseUrl = "{{ route('html.secure', 'option_chain') }}";
    </script>
    <script src="{{ versionResource('assets/client/js/main.js') }}"></script>
@endpush
