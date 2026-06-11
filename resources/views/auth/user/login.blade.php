<!DOCTYPE html>

<head>
    <title>Trang quản lý Admin Web</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords"
        content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <script type="application/x-javascript">
    addEventListener("load", function() {
        setTimeout(hideURLbar, 0);
    }, false);

    function hideURLbar() {
        window.scrollTo(0, 1);
    }
    </script>
    <!-- bootstrap-css -->
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
    <!-- //bootstrap-css -->
    <!-- Custom CSS -->
    <link href="{{ asset('backend/css/login.css') }}" rel='stylesheet' type='text/css' />
    <!-- font CSS -->
    <link
        href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic'
        rel='stylesheet' type='text/css'>
    <!-- font-awesome icons -->
    <link rel="stylesheet" href="{{ asset('backendcss/font.css') }}" type="text/css" />
    <link href="{{ asset('backend/css/font-awesome.css') }}" rel="stylesheet">
    <!-- //font-awesome icons -->
    <script src="{{ asset('backend/js/jquery2.0.3.min.js') }}"></script>
</head>

<body>
    <div class="w3layouts-main">
        <h2>Đăng nhập</h2>
        @if (session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="post">
            @csrf
            <div class="form-group">
                <input type="text" class="input-control" name="email" placeholder="Điền E-Mail"
                    required="">
            </div>
            <div class="form-group">
                <input type="password" class="input-control" name="password" placeholder="Điền Password"
                    required="" id="password_login">
            </div>
            <div class="row ">
                <div class="col-lg-12">
                    <i class="fa-styling fa fa-eye-slash " id="toggleEye"></i>
                </div>
            </div>
            <span><input type="checkbox" />Nhớ lần đăng nhập tới</span>
            <h6><a href="#">Quên mật khẩu</a></h6>
            <button type="submit" class="primary-btn-submit">Đăng nhập</button>
        </form>
        <!-- <p>Don't Have an Account ?<a href="registration.html">Create an account</a></p> -->
    </div>
    <script src="{{ asset('backend/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/js/jquery.dcjqaccordion.2.7.js') }}"></script>
    <script src="{{ asset('backend/js/scripts.js') }}"></script>
    <script src="{{ asset('backend/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('backend/js/jquery.nicescroll.js') }}"></script>
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
    <script src="{{ asset('backend/js/jquery.scrollTo.js') }}"></script>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-success').fadeOut('fast');
        }, 3000);
    });

    const toggleEye = document.querySelector("#toggleEye");
    const password_login = document.querySelector("#password_login");

    toggleEye.addEventListener("click", function() {
        const type = password_login.getAttribute("type") === "password" ? "text" : "password";
        password_login.setAttribute("type", type);
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });
</script>

</html>
