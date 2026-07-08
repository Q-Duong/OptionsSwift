@extends('layouts.default')
@section('title', 'About Us - ')

@push('css')
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/terms.css') }}" type="text/css" as="style" />
@endpush

@section('content')
    @include('layouts.section.client.header')

    <div class="terms-container">
        <div class="glass-card">
            <div class="terms-header">
                <h1>About Options Swift | Giới Thiệu Options Swift</h1>
                <p>Professional Real-Time Institutional Money Flow Intelligence</p>
            </div>

            <section class="terms-section">
                <h2>English</h2>
                <p>
                    <strong>Options Swift</strong> is a professional <strong>real-time institutional options flow
                        platform</strong> built for traders who want
                    to understand how capital is moving through the options market.
                </p>
                <p>
                    Unlike traditional indicators that rely on historical price calculations, <strong>Options Swift is not
                        an
                        indicator</strong>. Our
                    platform delivers <strong>real-time institutional money flow</strong>, allowing traders to monitor where
                    significant
                    options capital is entering and exiting the market as activity unfolds.
                </p>
                <p>Through advanced options flow analytics, large premium trade detection, option chain intelligence, and
                    market-wide monitoring, Options Swift transforms complex market data into clear, actionable insights
                    that
                    help traders better understand institutional positioning and market sentiment.
                </p>
                <p>
                    Whether you trade intraday or actively manage options positions, Options Swift provides
                    professional-grade
                    market intelligence designed to support informed, data-driven trading decisions.
                </p>
                <p>
                    <strong>Options Swift — Professional Real-Time Institutional Money Flow Intelligence.</strong>
                </p>

            </section>

            <section class="terms-section">
                <h2>Tiếng Việt</h2>
                <p>
                    <strong>Options Swift</strong> là nền tảng <strong>phân tích dòng tiền của tổ chức theo thời gian thực
                        (Real-Time Institutional
                        Options Flow)</strong>, được xây dựng dành cho các nhà giao dịch muốn hiểu rõ cách dòng vốn đang
                    dịch chuyển
                    trong thị trường.
                </p>
                <p>
                    Khác với các chỉ báo (indicator) truyền thống dựa trên dữ liệu giá trong quá khứ, <strong>Options Swift
                        không
                        phải là một chỉ báo</strong>. Nền tảng của chúng tôi cung cấp <strong>dữ liệu dòng tiền tổ chức theo
                        thời
                        gian thực</strong>, giúp
                    nhà giao dịch theo dõi chính xác nơi dòng vốn lớn đang chảy vào và rút ra khỏi thị trường khi các giao
                    dịch diễn ra.</p>
                <p>Thông qua công nghệ phân tích dòng tiền, phát hiện các lệnh premium lớn, phân tích chuỗi (Option Chain)
                    và giám sát toàn thị trường, Options Swift chuyển đổi dữ liệu phức tạp thành những thông tin rõ ràng và
                    dễ sử dụng, giúp nhà giao dịch hiểu rõ hơn về vị thế của các tổ chức và tâm lý thị trường.</p>
                <p>Dù bạn là nhà giao dịch trong ngày (Day Trader) hay nhà giao dịch chuyên nghiệp, Options Swift cung cấp
                    công cụ phân tích thị trường đạt tiêu chuẩn chuyên nghiệp, hỗ trợ bạn đưa ra các quyết định giao dịch
                    dựa trên dữ liệu thực tế.</p>
                <p><strong>Options Swift - Nền tảng Phân tích Dòng tiền Tổ chức Theo Thời gian Thực dành cho Nhà Giao dịch
                        Chuyên nghiệp.</strong></p>
            </section>
        </div>
    </div>

    @include('layouts.section.client.footer')
@endsection
