<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBannedClient
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Chỉ check khi khách đã đăng nhập
        if (Auth::guard('client')->check()) {
            
            /** @var \App\Models\Client $user */
            $user = Auth::guard('client')->user();

            // 2. Chốt chặn: Bị Admin khóa mõm
            if ($user->status === 'denied') {
                Auth::guard('client')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your account has been suspended by the administrator. Please contact support for further assistance.');
            }
        }

        return $next($request);
    }
}