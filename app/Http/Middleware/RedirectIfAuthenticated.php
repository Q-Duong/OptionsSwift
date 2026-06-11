<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // Nếu tài khoản đã đăng nhập với guard 'client'
                if ($guard === 'client') {
                    return redirect()->route('dashboard'); // Chuyển thẳng vào dashboard của client
                }

                // Nếu tài khoản đã đăng nhập với guard 'web' (Admin)
                if ($guard === 'web') {
                    return redirect()->route('admin.dashboard'); // Chuyển thẳng vào dashboard của admin
                }

                // Cấu hình mặc định cũ của Laravel nếu không khớp các guard trên
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
