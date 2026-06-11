<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientAuthController extends Controller
{
    public function showLoginForm()
    {
        // View đăng nhập dành riêng cho client
        return view('auth.client.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Thử thách đăng nhập thông tin cơ bản trước
        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            
            $client = Auth::guard('client')->user();

            // Nếu tài khoản CHƯA ĐƯỢC DUYỆT
            if (!$client->is_approved) {
                // Đăng xuất ngay lập tức
                Auth::guard('client')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Trả về thông báo lỗi cụ thể
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. You will receive an email once approved.',
                ])->onlyInput('email');
            }

            // Nếu đã duyệt, tiến hành vào hệ thống bình thường
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
        ]);

        Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_approved' => false, // Tài khoản tạo mới mặc định chưa được duyệt
        ]);

        // BỎ DÒNG: Auth::guard('client')->login($client);

        // Chuyển hướng về trang login kèm thông báo chờ duyệt
        return redirect()->route('login')->with('verified_status', 'Registration successful! Please wait for Admin approval before logging in.');
    }

    public function serveSecureHtml($key)
    {
        if (!Storage::exists("public/html/{$key}.html")) {
            abort(404);
        }
        $fileContent = Storage::get("public/html/{$key}.html");

        return response($fileContent)->header('Content-Type', 'text/html');
    }

    public function dashboard()
    {
        $user = Auth::guard('client')->user() ?? Auth::guard('web')->user();

        $htmlSetting = Setting::where('key', 'client_dashboard_html')->first();

        return view('pages.client.dashboard.index', compact('user', 'htmlSetting'));
    }
}
