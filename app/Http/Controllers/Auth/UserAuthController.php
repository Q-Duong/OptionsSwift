<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        // Trả về view HTML đăng nhập mà bạn đã có
        return view('auth.user.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // SỬA Ở ĐÂY: Đảm bảo chuyển hướng về đúng route của admin
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showRegisterForm()
    {
        return view('auth.user.register');
    }

    public function register(Request $request)
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Kiểm tra trùng lặp trong bảng users
            'password' => ['required', 'string', 'min:8', 'confirmed'], // Yêu cầu trường password_confirmation ở HTML
        ]);

        // 2. Tạo User mới
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Tự động đăng nhập sau khi tạo thành công
        Auth::guard('web')->login($user);

        // 4. Chuyển hướng về trang quản trị
        return redirect()->route('admin.dashboard');
    }

    // Hiển thị danh sách các khối HTML
    public function dashboard()
    {
        return view('pages.admin.dashboard');
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ PHÂN HỆ HTML WIDGETS
    // ==========================================
    public function htmlIndex()
    {
        $settings = Setting::latest()->get();
        return view('pages.admin.html.index', compact('settings'));
    }

    public function htmlCreate()
    {
        return view('pages.admin.html.form');
    }

    public function htmlStore(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'unique:settings,key'],
            'value' => ['required', 'string']
        ]);

        $setting = Setting::create($validated);
        $this->generateStaticHtmlFile($setting);

        return redirect()->route('admin.html.index')->with('success', 'Widget created and deployed successfully!');
    }

    public function htmlEdit($id)
    {
        $setting = Setting::findOrFail($id);
        return view('pages.admin.html.form', compact('setting'));
    }

    public function htmlUpdate(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $validated = $request->validate([
            'key' => ['required', 'string', 'unique:settings,key,' . $id],
            'value' => ['required', 'string']
        ]);

        $setting->update($validated);
        $this->generateStaticHtmlFile($setting);

        return redirect()->route('admin.html.index')->with('success', 'Widget updated successfully!');
    }

    public function htmlDelete($id)
    {
        $setting = Setting::findOrFail($id);
        Storage::delete("html/{$setting->key}.html"); // Xóa file tĩnh vật lý
        $setting->delete();

        return redirect()->route('pages.admin.html.index')->with('success', 'Widget deleted completely!');
    }

    private function generateStaticHtmlFile($setting)
    {
        $html = $setting->value;

        $compressedHtml = preg_replace('/(\s)+/s', '\\1', $html);
        $compressedHtml = str_replace(["\r", "\n", "\t"], '', $compressedHtml);

        Storage::put("public/html/{$setting->key}.html", $compressedHtml);
    }


    // ==========================================
    // CÁC HÀM XỬ LÝ PHÂN HỆ CLIENT APPROVALS
    // ==========================================
    public function pendingClients() {
        $pendingClients = Client::where('is_approved', false)->latest()->get();
        return view('pages.admin.clients.pending', compact('pendingClients'));
    }

    public function approveClient($id) {
        $client = Client::findOrFail($id);
        $client->update(['is_approved' => true]);

        return redirect()->route('admin.clients.pending')->with('success', "Account for {$client->name} approved!");
    }
}
