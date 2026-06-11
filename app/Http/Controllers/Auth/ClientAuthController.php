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

        // Sử dụng guard 'client'
        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập của client không chính xác.',
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'], // Chú ý: unique trên bảng clients
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('client')->login($client);

        return redirect()->route('dashboard');
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
