<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Options Swift</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/client/styles/style.css') }}" type="text/css">
    <style>
        .dashboard-wrapper {
            padding: 50px 5%;
            min-height: 80vh;
        }

        .welcome-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 8px;
            border-top: 3px solid var(--primary-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .welcome-card h1 {
            font-style: italic;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .welcome-card h1 span {
            color: var(--primary-color);
        }

        /* --- Tinh chỉnh CSS để form và nút Logout hiển thị cùng dòng, giống trang chủ --- */
        .nav-links form {
            display: inline-block;
            margin-left: 30px;
        }

        .nav-links .btn-logout {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            font-family: inherit;
        }

        .nav-links .btn-logout:hover {
            background: var(--primary-color);
            color: #000;
        }
    </style>
</head>

<body>

    <header>
        <a href="{{ route('dashboard') }}" class="logo-container">
            <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo"
                class="logo-image">
        </a>

        <nav class="nav-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('dashboard') }}">Dashboard</a>

            @if (Auth::guard('web')->check())
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" style="border-color: #ff4d4d; color: #ff4d4d;">Logout
                        Admin</button>
                </form>
            @else
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            @endif
        </nav>
    </header>

    <div class="dashboard-wrapper">
        <p style="color: var(--text-muted); text-align: center; margin-top: 20px;">
            Viewing as: <strong style="color: var(--text-main);">{{ $user->name }}</strong>
        </p>

        <div
            style="background: var(--card-bg); padding: 10px; border-radius: 8px; border-top: 3px solid var(--primary-color);">
            @if ($htmlSetting && \Illuminate\Support\Facades\Storage::disk('public')->exists("html/{$htmlSetting->key}.html"))
                <iframe
                    src="{{ route('html.secure', $htmlSetting->key) }}?v={{ $htmlSetting->updated_at->timestamp }}"
                    style="width: 100%; height: 100%; border: none; border-radius: 4px; display: block; position: absolute; top: 100px; left: 0;"
                    sandbox="allow-same-origin allow-scripts"></iframe>
            @else
                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <h3 style="color: #fff; margin-bottom: 10px;">DEFAULT DASHBOARD</h3>
                    <p>Welcome to Options Swift. Institutional data components are currently being configured.</p>
                </div>
            @endif
        </div>
    </div>

</body>

<script>
    // 1. Chặn click chuột phải
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // 2. Chặn các phím tắt mở DevTools
    document.addEventListener('keydown', function(e) {
        // Chặn F12
        if (e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        // Chặn Ctrl+Shift+I (Inspect) và Ctrl+Shift+J (Console)
        if (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) {
            e.preventDefault();
            return false;
        }
        // Chặn Ctrl+U (View Source)
        if (e.ctrlKey && e.keyCode === 85) {
            e.preventDefault();
            return false;
        }
        // Chặn Ctrl+S (Save trang web)
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

    // Kích hoạt bẫy treo máy ở trang mẹ
    setInterval(function() {
        debugger;
    }, 50);
</script>

</html>
