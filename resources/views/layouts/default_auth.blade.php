<!DOCTYPE html>

<head>
    <title>Medicen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel='shortcut icon' href="{{ asset('frontend/img/new-logo.jpg') }}" />
    <script type="application/x-javascript">
    addEventListener("load", function() {
        setTimeout(hideURLbar, 0);
    }, false);

    function hideURLbar() {
        window.scrollTo(0, 1);
    }
    </script>
    <!-- bootstrap-css -->
    <link rel="stylesheet" href="{{ versionResource('assets/admin/css/bootstrap.min.css') }} " as="style">
    <!-- //bootstrap-css -->
    <!-- Custom CSS -->
    <link href="{{ versionResource('assets/admin/css/style.css') }}" rel='stylesheet' type='text/css' as="style" />
    <link href="{{ versionResource('assets/admin/css/style-responsive.css') }}" rel="stylesheet" as="style" />
    <link href="{{ versionResource('assets/admin/css/jquery.dataTables.min.css') }}" rel="stylesheet" as="style" />
    <link href="{{ versionResource('assets/admin/css/responsive-jqueryui.min.css') }}" rel="stylesheet"
        as="style" />
    <link href="{{ versionResource('assets/admin/css/themes-base-jquery-ui.css') }}" rel="stylesheet" as="style" />
    <link href="{{ versionResource('assets/admin/css/overview.built.css') }}" rel='stylesheet' type='text/css'
        as="style" />
    <link rel="stylesheet" href="{{ versionResource('assets/admin/css/built/unified.css') }}" type="text/css"
        as="style">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" as="style" />
    <!-- //select2 -->
    <link href="{{ versionResource('assets/admin/css/select2.min.css') }}" rel="stylesheet" as="style" />
    @stack('css')
</head>

<body>
    <section id="container">
        @include('layouts.section.admin.header')
        @include('layouts.section.admin.side_bar')
        <section id="main-content">
            <section class="wrapper">
                <div class="container-fluid">
                    @yield('admin_content')
                </div>
            </section>
            @include('layouts.section.admin.footer')
        </section>

    </section>

    <script src="{{ versionResource('assets/admin/js/jquery2.0.3.min.js') }}"></script>
    <script src="{{ versionResource('assets/admin/js/bootstrap.js') }}"></script>
    <!-- Ux Ui -->
    <script src="{{ versionResource('assets/admin/js/ux-ui/jquery.dcjqaccordion.2.7.min.js') }}" defer></script>
    <script src="{{ versionResource('assets/admin/js/ux-ui/jquery.slimscroll.min.js') }}" defer></script>
    <script src="{{ versionResource('assets/admin/js/ux-ui/jquery.nicescroll.min.js') }}" defer></script>
    <script src="{{ versionResource('assets/admin/js/ux-ui/left-side.min.js') }}" defer></script>
    <script src="https://kit.fontawesome.com/4b68e3663c.js" crossorigin="anonymous" defer></script>
    <script src="{{ versionResource('assets/admin/js/ux-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ versionResource('assets/admin/js/tool/select2.min.js') }}"></script>
    <script src="{{ versionResource('assets/admin/js/tool/main.min.js') }}"></script>
    @stack('js')

    
</body>

</html>
