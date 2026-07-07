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
</head>

<body id="section_1">
    <main class="main" role="main">
        @yield('content')
    </main>

    <script src="{{ versionResource('assets/client/js/jquery-3.3.1.js') }}"></script>
    <script src="{{ versionResource('assets/client/js/bootstrap.js') }}"></script>
    {{-- <script src="{{ versionResource('assets/client/js/jquery-ui.min.js') }}" defer></script> --}}
    <script src="https://kit.fontawesome.com/4b68e3663c.js" crossorigin="anonymous" defer></script>
    <script src="{{ versionResource('assets/client/js/home.js') }}"></script>

    @stack('js')

</body>

</html>
