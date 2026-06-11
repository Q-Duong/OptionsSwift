<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Options Swift</title>
    <style>
        :root {
            --bg-color: #05080a; --card-bg: #0d1317; --primary-neon: #59ea1e;
            --admin-accent: #ff4d4d; --text-main: #ffffff; --text-muted: #a0aab2; --border-color: #1a242c;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg-color); color: var(--text-main); line-height: 1.6; }
        
        header { display: flex; flex-direction: column; gap: 15px; padding: 15px 5%; background: rgba(13, 19, 23, 0.98); border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 100; }
        .header-top { display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .logo-panel { font-size: 20px; font-weight: 900; font-style: italic; }
        .logo-panel span { color: var(--admin-accent); }
        
        .nav-tabs { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none; }
        .nav-tabs::-webkit-scrollbar { display: none; }
        .tab-link { background: transparent; border: none; color: var(--text-muted); padding: 8px 16px; font-weight: 600; text-transform: uppercase; font-size: 12px; border-radius: 4px; text-decoration: none; }
        .tab-link.active { color: var(--admin-accent); background: rgba(255, 77, 77, 0.1); border: 1px solid rgba(255, 77, 77, 0.2); }
        
        .btn-logout { background: transparent; border: 1px solid var(--admin-accent); color: var(--admin-accent); padding: 6px 16px; border-radius: 4px; font-weight: 600; text-transform: uppercase; font-size: 12px; cursor: pointer; }
        .btn-logout:hover { background: var(--admin-accent); color: #fff; }
        
        .admin-wrapper { padding: 25px 5%; max-width: 1300px; margin: 0 auto; }
        .alert-success { padding: 12px; background: rgba(89, 234, 30, 0.15); color: var(--primary-neon); border: 1px solid var(--primary-neon); border-radius: 4px; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .panel-card { background: var(--card-bg); padding: 20px; border-radius: 8px; border-top: 3px solid var(--admin-accent); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .panel-header { display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 20px; }
        .panel-header h2 { font-style: italic; text-transform: uppercase; font-size: 1.3rem; }
        
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; text-align: left; min-width: 550px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 12px; }
        tr:hover { background: rgba(255,255,255,0.01); }
        
        .action-group { display: flex; gap: 8px; justify-content: flex-end; }
        .action-btn { padding: 6px 12px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; text-transform: uppercase; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-add { background: var(--admin-accent); color: #fff; padding: 10px 20px; font-size: 12px; }
        .btn-edit { background: var(--primary-neon); color: #000; }
        .btn-delete { background: transparent; border: 1px solid var(--admin-accent); color: var(--admin-accent); }
        .btn-delete:hover { background: var(--admin-accent); color: #fff; }
        .btn-approve { background: var(--primary-neon); color: #000; padding: 8px 16px; }

        @media (min-width: 768px) {
            header { flex-direction: row; justify-content: space-between; align-items: center; padding: 15px 5%; }
            .header-top { width: auto; gap: 30px; }
            .nav-tabs { padding-bottom: 0; }
            .admin-wrapper { padding: 40px 5%; }
            .panel-card { padding: 30px; }
            .panel-header h2 { font-size: 1.5rem; }
        }
    </style>
    @stack('styles')
</head>

<body>
    <section id="container">
        @include('layouts.section.admin.header')
        <div class="admin-wrapper">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
    
            @yield('content') </div>
    
        

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
