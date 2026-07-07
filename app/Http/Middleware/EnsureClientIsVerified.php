<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureClientIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $client = Auth::guard('client')->user();

        // Nếu chưa đăng nhập, hoặc đăng nhập rồi mà chưa xác minh email
        if (!$client || !$client->hasVerifiedEmail()) {
            
            // Xử lý nếu là request gọi ngầm (Ajax)
            if ($request->expectsJson()) {
                return abort(403, 'Your email address is not verified.');
            }

            // Đá văng về trang thông báo bắt xác minh
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
