<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckClientApproval
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Kiểm tra đăng nhập qua Guard 'client'
        if (!Auth::guard('client')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('client')->user();

        // ==========================================
        // CHỐT CHẶN: TÀI KHOẢN BỊ KHÓA (DENIED)
        // ==========================================
        if ($user->status === 'denied') {
            // Hủy mọi phiên làm việc
            Auth::guard('client')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Điều hướng về Login với thông báo mạnh mẽ
            return redirect()->route('login')
                ->with('error', 'Your account has been suspended by the administrator. Please contact support for further assistance.');
        }

        // ==========================================
        // LOGIC KIỂM TRA HẾT HẠN (TRIAL / PRO)
        // ==========================================
        $isExpired = $user->expires_at !== null && \Carbon\Carbon::now()->greaterThanOrEqualTo(\Carbon\Carbon::parse($user->expires_at));

        if ($user->status !== 'approved' || $isExpired) {
            // Cập nhật trạng thái expired nếu quá hạn
            if ($isExpired && $user->status === 'approved') {
                $user->update(['status' => 'expired']);
            }

            return redirect()->route('client.pricing')
                ->with('warning', 'Your access has expired. Please choose a plan to continue.');
        }

        return $next($request);
    }
}
