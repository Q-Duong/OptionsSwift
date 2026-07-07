<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientApproval
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Kiểm tra xem khách có đăng nhập chưa
        if (!Auth::guard('client')->check()) {
            return redirect()->route('login');
        }

        // Ép kiểu để VS Code không gạch đỏ (như bài học hôm trước)
        /** @var \App\Models\Client $user */
        $user = Auth::guard('client')->user();

        // 2. Xử lý "Race Condition" của Stripe
        $isJustRedirectedFromStripe = $request->has('trial') || $request->has('payment');

        // Hàm subscribed('default') là siêu năng lực của Cashier. Nó trả về TRUE nếu:
        // 1. Đang trong 7 ngày dùng thử (trialing)
        // 2. Đang dùng gói trả phí bình thường (active)
        // 3. Đã bấm hủy nhưng vẫn còn hạn sử dụng (grace period)
        if (!$user->subscribed('default') && !$isJustRedirectedFromStripe) {

            // Xóa dòng update status = 'expired' đi vì Stripe tự lo việc đó
            return redirect()->route('client.pricing')
                ->with('warning', 'Your access has expired or no active subscription found. Please choose a plan to continue.');
        }

        return $next($request);
    }
}
