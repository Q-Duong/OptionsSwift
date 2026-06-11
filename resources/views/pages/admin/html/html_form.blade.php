<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($setting) ? 'Chỉnh sửa' : 'Thêm mới' }} Khối HTML</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .admin-container { padding: 40px 5%; max-width: 1000px; margin: 0 auto; }
        .form-card { background: var(--card-bg); padding: 40px; border-radius: 8px; border-top: 3px solid #ff4d4d; }
        .form-card h2 { margin-bottom: 30px; font-style: italic; color: #ff4d4d; }
        textarea {
            width: 100%; height: 350px; background: #05080a; color: #59ea1e;
            border: 1px solid #2a343c; padding: 15px; font-family: monospace;
            font-size: 14px; border-radius: 4px; resize: vertical;
        }
        textarea:focus { outline: none; border-color: #ff4d4d; }
        .error-text { color: #ff4d4d; font-size: 13px; margin-top: 5px; display: block; }
    </style>
</head>
<body>

    <header>
        <div class="logo-container">
            <span style="font-size: 24px; font-weight: bold; font-style: italic;">ADMIN <span style="color: #ff4d4d;">PANEL</span></span>
        </div>
        <nav class="nav-links">
            <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted);">Quay lại Danh sách</a>
        </nav>
    </header>

    <div class="admin-container">
        <div class="form-card">
            <h2>{{ isset($setting) ? 'Chỉnh Sửa Khối HTML' : 'Thêm Mới Khối HTML' }}</h2>
            
            <form action="{{ isset($setting) ? route('admin.html.update', $setting->id) : route('admin.html.store') }}" method="POST">
                @csrf
                @if(isset($setting))
                    @method('PUT') @endif
                
                <div class="form-group">
                    <label>Key (Tên định danh - VD: client_dashboard_html, client_footer_html)</label>
                    <input type="text" name="key" value="{{ old('key', $setting->key ?? '') }}" placeholder="Nhập tên key viết liền không dấu..." required>
                    @error('key') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Nội Dung HTML</label>
                    <textarea name="value" placeholder="<div class='custom-widget'>...</div>" required>{{ old('value', $setting->value ?? '') }}</textarea>
                    @error('value') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn-primary" style="background: #ff4d4d; max-width: 250px;">
                    {{ isset($setting) ? 'Cập Nhật Thay Đổi' : 'Tạo Mới' }}
                </button>
            </form>
        </div>
    </div>

</body>
</html>