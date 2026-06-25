@extends('layouts.default')
@section('title', 'Institutional Trading Terminal - ')
@section('search_bar')
    @include('layouts.section.client.search_bar')
@endsection

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

        <style>
            /* ========================================================= */
            /* CSS CHUNG (CHO MÀN HÌNH MÁY TÍNH & IPAD LỚN)             */
            /* ========================================================= */
            .trial-upgrade-banner {
                position: relative;
                z-index: 50;
                background: linear-gradient(90deg, rgba(13, 19, 23, 0.9) 0%, rgba(89, 234, 30, 0.05) 100%);
                border: 1px solid rgba(89, 234, 30, 0.2);
                border-left: 4px solid var(--primary-color, #59ea1e);
                padding: 18px 25px;
                border-radius: 8px;
                margin: 15px 20px 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            }

            @media (max-width: 768px) {
                .trial-upgrade-banner {
                    margin: 15px;
                }
            }

            .banner-content {
                display: flex;
                align-items: center;
                gap: 15px;
                flex: 1;
            }

            .banner-icon {
                background: rgba(89, 234, 30, 0.1);
                min-width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--primary-color, #59ea1e);
                font-size: 20px;
            }

            .banner-text h4 {
                color: #fff;
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 4px;
                letter-spacing: 0.5px;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
            }

            .banner-text h4>span:first-child {
                color: var(--primary-color, #59ea1e);
            }

            .banner-text .days-left {
                background: rgba(77, 184, 255, 0.2);
                color: #4db8ff;
                font-size: 11px;
                padding: 3px 10px;
                border-radius: 12px;
                font-weight: normal;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .banner-text p {
                color: var(--text-muted, #a0aab2);
                font-size: 13.5px;
                margin: 0;
                line-height: 1.4;
            }

            .btn-banner-upgrade {
                display: inline-block;
                background: var(--primary-color, #59ea1e);
                color: #000;
                padding: 12px 25px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: bold;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 1px;
                box-shadow: 0 0 15px rgba(89, 234, 30, 0.3);
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                text-align: center;
                white-space: nowrap;
            }

            .btn-banner-upgrade:hover {
                background: #4cd115;
                box-shadow: 0 0 20px rgba(89, 234, 30, 0.5);
                transform: translateY(-2px);
            }

            /* ========================================================= */
            /* CSS RIÊNG CHO ĐIỆN THOẠI (MOBILE RESPONSIVE)             */
            /* ========================================================= */
            @media (max-width: 768px) {
                .trial-upgrade-banner {
                    flex-direction: column;
                    /* Xếp dọc các phần tử */
                    align-items: stretch;
                    /* Kéo giãn full chiều ngang */
                    padding: 15px;
                    border-left: none;
                    border-top: 4px solid var(--primary-color, #59ea1e);
                    /* Dời viền màu lên trên */
                    gap: 15px;
                }

                .banner-content {
                    align-items: flex-start;
                    /* Ép chữ căn trái */
                }

                .banner-text h4 {
                    font-size: 14px;
                }

                .banner-text p {
                    font-size: 12.5px;
                }

                .banner-action {
                    width: 100%;
                }

                .btn-banner-upgrade {
                    display: block;
                    /* Nút to full viền trên Mobile */
                    width: 100%;
                    padding: 14px 20px;
                    font-size: 14px;
                }
            }

            /* Ẩn Icon sấm sét ở điện thoại quá nhỏ để tiết kiệm diện tích chữ */
            @media (max-width: 480px) {
                .banner-icon {
                    display: none;
                }
            }
        </style>
    @endif

    <div class="terminal-container">
        <main class="main-column">
            <div class="main-header">
                <h2>MARKET SCANNER</h2>
                <span style="color: var(--text-muted); font-size: 13px; font-weight: bold;">LIVE DATA STREAM <span
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

    <div class="flow-modal-overlay" id="flowModal">
        <div class="flow-modal-window">
            <div class="modal-header">
                <h3 id="modalFlowTitle">DATA FLOW: <span>TICKER</span></h3>
                <button class="close-modal-btn" id="closeModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="loader-wrapper" id="iframeLoader" style="display: none;">
                    <div class="spinner"></div>
                    <span id="loadingText">Connecting to data node...</span>
                </div>
                <div id="popupContent"
                    style="width: 100%; height: 100%; overflow-y: auto; padding: 15px; background: var(--bg-color);">
                </div>
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
