<!DOCTYPE html>
<html lang="{{ App::getLocale() == 'vn' ? 'vi-VN' : 'en-US' }}" class="enhanced">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')Options Swift
    </title>
    <meta name="description" content="@yield('title') Options Swift">
    <meta name="keywords" content="@yield('title') Options Swift">
    <meta name="author" content="Options Swift">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- #FAVICONS -->
    <link rel='shortcut icon' href="{{ asset('assets/images/logo/options-swift-logo.png') }}" type="image/x-icon">
    <link rel='icon' href="{{ asset('assets/images/logo/options-swift-logo.png') }}" type="image/x-icon">
    <link rel='canonical' href="{{ url()->current() }}">
    <!-- Open Graph Metadata for Social Media -->
    <meta property="og:title" content="@yield('title') Options Swift">
    <meta property="og:description" content="@yield('title') Options Swift">
    <meta property="og:image" content="https://vart.vn/assets/images/logo/options-swift-logo.png">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ App::getLocale() == 'vn' ? 'vi-VN' : 'en-US' }}">
    <meta property="og:site_name" content="Options Swift">
    <meta property="og:type" content="website">
    <link rel='shortcut icon' href="{{ asset('frontend/img/new-logo.jpg') }}" />
    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/bootstrap.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/bootstrap-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ versionResource('assets/client/styles/style.css') }}" type="text/css">
    @stack('css')

    <style>
        .hero {
            padding: 0;
            text-align: center;
            overflow: hidden;
        }

        /* Sửa để sử dụng banner hình ảnh */
        .banner-image {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Các phần dưới banner vẫn giữ màu sắc */
        .extra-features {
            padding: 80px 5%;
            background: radial-gradient(circle at 50% 50%, rgba(89, 234, 30, 0.1) 0%, transparent 60%);
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            font-style: italic;
            text-transform: uppercase;
        }

        .section-title span {
            color: var(--primary-color);
        }

        .feature-grid {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .extra-feature-box {
            flex: 1;
            min-width: 250px;
            max-width: 350px;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #1a242c;
            transition: 0.3s;
        }

        .extra-feature-box:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        }

        .extra-feature-icon {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .extra-feature-box h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .extra-feature-box p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
    </style>
</head>

<body id="section_1">
    <main class="main" role="main">
        @yield('content')
    </main>

    <script src="{{ versionResource('assets/client/js/jquery-3.3.1.js') }}"></script>
    <script src="{{ versionResource('assets/client/js/bootstrap.js') }}"></script>
    {{-- <script src="{{ versionResource('assets/client/js/jquery-ui.min.js') }}" defer></script> --}}
    <script src="https://kit.fontawesome.com/4b68e3663c.js" crossorigin="anonymous" defer></script>

    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            if (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) {
                e.preventDefault();
                return false;
            }
            if (e.ctrlKey && e.keyCode === 85) {
                e.preventDefault();
                return false;
            }
            if (e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                return false;
            }
        });

        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123 || (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) || (e
                    .ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
        });

        setInterval(function() {
            debugger;
        }, 50);
    </script>

    @stack('js')

</body>

</html>
