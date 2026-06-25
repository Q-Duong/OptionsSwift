<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClientAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.client.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'disclaimer' => ['accepted'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $client = Auth::guard('client')->user();

            // 1. CHỐT CHẶN DENIED: Vừa đăng nhập thành công là kiểm tra ngay
            if ($client->status === 'denied') {
                Auth::guard('client')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'SYSTEM ALERT: Your account has been suspended. Please contact support.');
            }

            // 2. KIỂM TRA TRẠNG THÁI HOẠT ĐỘNG (PENDING / EXPIRED)
            $isExpired = $client->expires_at && Carbon::now()->greaterThan(Carbon::parse($client->expires_at));

            if ($client->status !== 'approved' || $isExpired) {
                // Nếu là tài khoản hết hạn, update status cho sạch data
                if ($isExpired && $client->status === 'approved') {
                    $client->update(['status' => 'expired']);
                }
                return redirect()->route('client.pricing');
            }

            // 3. MỌI THỨ OK -> DASHBOARD
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegisterForm()
    {
        return view('auth.client.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'selected_plan' => ['nullable', 'in:3_months,6_months,12_months'],
            'disclaimer' => ['accepted'],
        ]);

        // Kiểm tra xem khách đang đi luồng Mua Gói hay luồng Dùng Thử
        $hasPlan = !empty($validated['selected_plan']);

        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),

            // LUỒNG TÁCH BẠCH Ở ĐÂY:
            // Có chọn gói -> 'pending' (Khóa mỏ, chờ thanh toán)
            // Không chọn gói -> 'approved' (Cho xài thử 7 ngày)
            'status' => $hasPlan ? 'pending' : 'approved',
            'expires_at' => $hasPlan ? null : \Carbon\Carbon::now()->addDays(7),
        ]);

        Auth::guard('client')->login($client);

        // ==========================================
        // LUỒNG 2: KHÁCH CÓ Ý ĐỊNH MUA GÓI NGAY
        // ==========================================
        if ($hasPlan) {
            $planKey = $validated['selected_plan'];

            $price = match ($planKey) {
                '3_months' => 120,
                '6_months' => 210,
                '12_months' => 360,
                default => 0
            };

            $orderCode = 'OS-' . strtoupper(Str::random(8));
            Order::create([
                'order_code' => $orderCode,
                'client_id' => $client->id,
                'plan_type' => $planKey,
                'amount' => $price,
                'status' => 'pending',
            ]);

            return redirect()->route('client.invoice', $orderCode)
                ->with('success', 'Account created! Please complete your payment to activate your plan.');
        }

        // ==========================================
        // LUỒNG 1: KHÁCH CHỈ MUỐN DÙNG THỬ
        // ==========================================
        return redirect()->route('dashboard')
            ->with('success', 'Welcome to Options Swift! Your 7-day free trial has been activated.');
    }

    public function serveSecureHtml($key)
    {
        if (!Storage::exists("public/html/{$key}.html")) abort(404);
        return response(Storage::get("public/html/{$key}.html"))->header('Content-Type', 'text/html');
    }

    public function dashboard()
    {
        $user = Auth::guard('client')->user();
        $widgets = Setting::orderBy('id', 'desc')->get();
        return view('pages.client.dashboard.index', compact('user', 'widgets'));
    }
}
