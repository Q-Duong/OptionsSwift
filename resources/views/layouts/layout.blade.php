    <!DOCTYPE html>
    <html lang="{{ App::getLocale() == 'vn' ? 'vi-VN' : 'en-US' }}" class="enhanced">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            @yield('title') DG Gonstead
        </title>
        <meta name="description" content="@yield('title') DG Gonstead">
        <meta name="keywords" content="@yield('title') DG Gonstead">
        <meta name="author" content="DG Gonstead">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- #FAVICONS -->
        <link rel='shortcut icon' href="{{ asset('assets/images/logo/vart-logo.png') }}" type="image/x-icon">
        <link rel='icon' href="{{ asset('assets/images/logo/vart-logo.png') }}" type="image/x-icon">
        <link rel='canonical' href="{{ url()->current() }}">
        <!-- Open Graph Metadata for Social Media -->
        <meta property="og:title" content="@yield('title') DG Gonstead">
        <meta property="og:description" content="@yield('title') DG Gonstead">
        <meta property="og:image" content="https://vart.vn/assets/images/logo/vart-logo.png">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:locale" content="{{ App::getLocale() == 'vn' ? 'vi-VN' : 'en-US' }}">
        <meta property="og:site_name" content="DG Gonstead">
        <meta property="og:type" content="website">
        <link rel='shortcut icon' href="{{ asset('frontend/img/new-logo.jpg') }}" />
        <!-- Css Styles -->
        <link rel="stylesheet" href="{{ asset('assets/client/styles/bootstrap.min.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('assets/client/styles/font-awesome.min.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('assets/client/styles/style.css') }}" type="text/css">
    </head>

    <body>
        @include('layouts.section.header')
        @yield('content')
        @include('layouts.section.footer')

        <!-- Js Plugins -->
        <script src="{{ asset('frontend/js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>

        <script src="{{ asset('frontend/js/jquery.slicknav.js') }}"></script>

        {{-- <script src="{{ asset('frontend/js/main.js') }}"></script> --}}
       
        <script src="{{ asset('frontend/js/jquery.scrollUp.min.js') }}"></script>
       
        <script src="{{ asset('frontend/js/jquery-ui.js') }}"></script>
        <script src="js/jquery.sticky.js"></script>
        <script src="js/click-scroll.js"></script>
        <script src="js/counter.js"></script>
        <script src="js/custom.js"></script>
        <script type="text/javascript"></script>

    </body>

    </html>
