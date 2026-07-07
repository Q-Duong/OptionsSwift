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
use Illuminate\Auth\Events\Registered;
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

            // 1. CHỐT CHẶN DENIED: Vừa đăng nhập thành công là kiểm tra ngay (Giữ nguyên nếu bác có tính năng ban acc)
            if ($client->status === 'denied') {
                Auth::guard('client')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'SYSTEM ALERT: Your account has been suspended.');
            }

            // 2. KIỂM TRA TRẠNG THÁI GÓI CƯỚC BẰNG CASHIER
            // Kiểm tra xem khách có đang dùng gói thuê bao nào không (ví dụ tên gói mặc định là 'default')
            $isSubscribed = $client->subscribed('default');

            // Kiểm tra xem khách có đang trong thời gian Trial không (nếu bác setup trial qua Cashier)
            $onTrial = $client->onGenericTrial() || $client->onTrial('default');

            if (!$isSubscribed && !$onTrial) {
                // Nếu không có gói nào đang active và cũng không trong thời gian free -> Ra đóng tiền
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
        // 1. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'plan_type' => ['nullable', 'string'], // Chứa mã Price ID từ form (VD: price_123xxx)
            'disclaimer' => ['accepted'],
        ]);

        // 2. Tạo tài khoản cục bộ (Chỉ lưu thông tin định danh)
        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        if (!empty($validated['plan_type'])) {
            session(['pending_plan_type' => $validated['plan_type']]);
        }

        // 3. Gửi email xác thực đi ngay lập tức
        event(new Registered($client));

        // 4. Đăng nhập tự động
        Auth::guard('client')->login($client);

        return redirect()->route('verification.notice');
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
