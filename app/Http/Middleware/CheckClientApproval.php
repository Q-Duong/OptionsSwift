<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientApproval
{
    public function handle(Request $request, Closure $next)
    {
        // ==========================================
        // LỚP 1: KIỂM TRA ĐĂNG NHẬP
        // ==========================================
        if (!Auth::guard('client')->check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\Client $user */
        $user = Auth::guard('client')->user();

        // ==========================================
        // 2. ĐẶC QUYỀN VIP (Ngoại lệ không thu tiền)
        // ==========================================
        // Vượt qua kiểm tra thanh toán ngay lập tức nếu là tài khoản VIP
        if ($user->is_vip) {
            return $next($request);
        }

        // ==========================================
        // 3. XỬ LÝ KHÁCH HÀNG BÌNH THƯỜNG (STRIPE)
        // ==========================================
        // Xử lý độ trễ (Race Condition) khi trình duyệt về đích trước Webhook
        $isJustRedirectedFromStripe = $request->has('trial') || $request->has('payment');
        

        // Hàm subscribed('default') của Cashier lo toàn bộ logic:
        // Đang dùng thử / Đang trả phí / Đã hủy nhưng còn Grace Period
        if (!$user->subscribed('default') && !$isJustRedirectedFromStripe) {
            return redirect()->route('client.pricing')
                ->with('warning', 'Your access has expired or no active subscription found. Please choose a plan to continue.');
        }

        return $next($request);
    }
}
