@extends('layouts.default_auth')
@section('title', isset($setting) ? 'Edit Code Block' : 'Create New Widget')

@push('styles')
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; background: #05080a; border: 1px solid var(--border-color); color: #fff; border-radius: 4px; font-size: 14px; }
    .form-group textarea { height: 400px; font-family: monospace; color: var(--primary-neon); resize: vertical; line-height: 1.5; }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--admin-accent); }
    .error-text { color: var(--admin-accent); font-size: 12px; margin-top: 5px; display: block; }
    
    /* Box hướng dẫn code */
    .helper-box { background: rgba(255, 255, 255, 0.02); border: 1px dashed var(--border-color); border-radius: 6px; padding: 20px; margin-top: 30px; }
    .helper-box h4 { color: var(--text-muted); font-size: 13px; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; }
    .code-snippet { background: #000; padding: 15px; border-radius: 4px; border-left: 3px solid var(--admin-accent); font-family: monospace; font-size: 13px; color: #a0aab2; margin-bottom: 15px; overflow-x: auto; line-height: 1.6; }
    .code-snippet span.hl { color: var(--primary-neon); }
    .code-snippet span.str { color: #f1fa8c; }
</style>
@endpush

@section('content')
<div class="panel-card" style="max-width: 1000px; margin: 0 auto;">
    <div class="panel-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 25px;">
        <h2>{{ isset($setting) ? 'Modify System Widget' : 'Build Custom Data Block' }}</h2>
        <a href="{{ route('admin.html.index') }}" style="color: var(--text-muted); font-size: 13px; text-decoration: none;">&larr; Back to List</a>
    </div>

    <form action="{{ isset($setting) ? route('admin.html.update', $setting->id) : route('admin.html.store') }}" method="POST">
        @csrf
        @if(isset($setting))
            @method('PUT')
        @endif
        
        <div class="form-group">
            <label>Identifier Key (Quy tắc đặt tên)</label>
            <input type="text" name="key" value="{{ old('key', $setting->key ?? '') }}" placeholder="Nhập: market_scanner, option_chain, HOẶC mã cổ phiếu custom (AMD, NFLX)..." required {{ isset($setting) ? 'readonly' : '' }}>
            @error('key') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Raw HTML & JavaScript Source</label>
            <textarea name="value" placeholder="Nhập mã HTML vào đây..." required spellcheck="false">{{ old('value', $setting->value ?? '') }}</textarea>
            @error('value') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="action-btn btn-add" style="width: 100%; padding: 15px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
            {{ isset($setting) ? 'Commit Realtime Updates' : 'Deploy To Storage File' }}
        </button>
    </form>

    <div class="helper-box">
        <h4>💡 Developer Cheat Sheet (Hướng dẫn Setup Hệ thống)</h4>
        
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><strong>1. Cấu hình 2 File Cốt Lõi (Bắt buộc phải tạo):</strong></p>
        <ul style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li>Key: <code style="color:var(--primary-neon); font-weight:bold;">market_scanner</code> &rarr; Dán mã HTML của bảng Scanner vào đây. Đây sẽ là màn hình chính.</li>
            <li>Key: <code style="color:var(--primary-neon); font-weight:bold;">option_chain</code> &rarr; Dán mã HTML của Option Chain vào đây. Đây là file "Mẫu Vạn Năng" xử lý dữ liệu cho mọi mã cổ phiếu.</li>
        </ul>

        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><strong>2. Cách thêm Mã Cổ Phiếu Custom vào Menu Tìm Kiếm:</strong></p>
        <ul style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px; padding-left: 20px; line-height: 1.6;">
            <li>Giao diện bên ngoài đã ghim sẵn 9 mã phổ biến (SPY, QQQ, AAPL, MSFT, NVDA, TSLA, AMZN, META).</li>
            <li>Nếu muốn thêm một mã mới (Ví dụ: AMD), bạn chỉ cần tạo Widget mới có Key là <code style="color:var(--primary-neon)">AMD</code>.</li>
            <li><strong>Lưu ý:</strong> Ô nhập HTML Code bên trên bạn chỉ cần gõ nội dung bất kỳ (VD: gõ chữ <code style="color:#fff">1</code> hoặc <code style="color:#fff">OK</code>). Hệ thống sẽ tự động ghép tên AMD vào file <code style="color:var(--primary-neon)">option_chain</code> để lấy dữ liệu!</li>
        </ul>

        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;"><strong>3. Tích hợp Mở Cửa Sổ Pop-up từ Scanner:</strong></p>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">Để khách hàng bấm nút "VIEW IN CHAIN" trong bảng Scanner và bật được Cửa sổ Pop-up đè lên màn hình (thay vì văng ra Tab mới), hãy tìm hàm <code style="color:#fff">drawerViewChain()</code> bên trong code của khối <strong>market_scanner</strong> và sửa lại y hệt như sau:</p>
        
        <div class="code-snippet">
<span class="hl">function</span> drawerViewChain() {<br>
&nbsp;&nbsp;if(!selectedSym) return;<br><br>
&nbsp;&nbsp;<span class="hl">const</span> symbol = selectedSym;<br>
&nbsp;&nbsp;<span class="hl">const</span> expiry = selectedTradeDate();<br><br>
&nbsp;&nbsp;<span style="color:#50627e;">// Bắn tín hiệu PostMessage ra ngoài Dashboard Laravel để gọi Pop-up</span><br>
&nbsp;&nbsp;window.parent.postMessage({<br>
&nbsp;&nbsp;&nbsp;&nbsp;action: <span class="str">'openFlowPopup'</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;ticker: symbol,<br>
&nbsp;&nbsp;&nbsp;&nbsp;expiry: expiry<br>
&nbsp;&nbsp;}, <span class="str">'*'</span>);<br>
}
        </div>
    </div>
</div>
@endsection