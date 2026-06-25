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
use Carbon\Carbon;

class UserAuthController extends Controller
{
    // ==========================================
    // PHÂN HỆ 1: XÁC THỰC ADMIN (LOGIN/REGISTER)
    // ==========================================
    public function showLoginForm()
    {
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
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác hoặc không có quyền truy cập.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        return view('pages.admin.dashboard');
    }

    // ==========================================
    // PHÂN HỆ 2: QUẢN LÝ KHỐI DỮ LIỆU WIDGETS
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
            'title' => ['nullable', 'string', 'max:255'], // Admin có thể nhập tên đẹp cho Menu
            'value' => ['required', 'string']
        ]);

        $setting = Setting::create($validated);
        $this->generateStaticHtmlFile($setting);

        return redirect()->route('admin.html.index')->with('success', 'Khởi tạo và biên dịch Widget thành công!');
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
            'title' => ['nullable', 'string', 'max:255'],
            'value' => ['required', 'string']
        ]);

        $setting->update($validated);
        $this->generateStaticHtmlFile($setting);

        return redirect()->route('admin.html.index')->with('success', 'Đã cập nhật thay đổi và biên dịch lại Widget!');
    }

    public function htmlDelete($id)
    {
        $setting = Setting::findOrFail($id);
        // Nhớ lưu file ở thư mục public/html để Client dễ đọc qua route secure
        Storage::delete("public/html/{$setting->key}.html");
        $setting->delete();

        return redirect()->route('admin.html.index')->with('success', 'Đã xóa bỏ hoàn toàn Widget khỏi hệ thống.');
    }

    private function generateStaticHtmlFile($setting)
    {
        $html = $setting->value;

        // Nén HTML để tối ưu tốc độ load cho Scanner
        $compressedHtml = preg_replace('/[ \t]+/', ' ', $html);
        $compressedHtml = preg_replace('/[\r\n]+/', "\n", $compressedHtml);

        Storage::put("public/html/{$setting->key}.html", $compressedHtml);
    }


    // ==========================================
    // PHÂN HỆ 3: QUẢN LÝ VÀ PHÊ DUYỆT KHÁCH HÀNG
    // ==========================================
    public function allClients()
    {
        $clients = Client::orderBy('created_at', 'desc')->get();
        return view('pages.admin.clients.index', compact('clients'));
    }

    public function pendingClients()
    {
        $pendingClients = Client::where('status', 'pending')
            ->whereNull('expires_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.admin.clients.pending', compact('pendingClients'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,denied',
            'duration' => 'nullable|string'
        ]);

        $client = Client::findOrFail($id);
        $client->status = $request->status;

        // Xử lý thời hạn truy cập nếu được duyệt
        if ($request->status === 'approved') {
            switch ($request->duration) {
                case '7_days':
                    $client->expires_at = Carbon::now()->addDays(7);
                    break;
                case '1_month':
                    $client->expires_at = Carbon::now()->addMonth();
                    break;
                case '3_months':
                    $client->expires_at = Carbon::now()->addMonths(3);
                    break;
                case 'lifetime':
                    $client->expires_at = null; // Trọn đời
                    break;
                default:
                    $client->expires_at = null;
                    break;
            }
        } else {
            // Nếu Deny hoặc Pending thì xóa ngày hết hạn (Reset)
            $client->expires_at = null;
        }

        $client->save();

        return back()->with('success', "Đã cập nhật trạng thái truy cập cho [{$client->name}] thành công!");
    }
}
