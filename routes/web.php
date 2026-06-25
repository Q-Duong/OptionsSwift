<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Client\ClientProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\GammaController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//-------------------------------------------- Frontend --------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Route nhận tín hiệu thanh toán tự động (Webhook)
Route::post('/webhook/payment', [SubscriptionController::class, 'paymentCallback'])->name('payment.webhook');

// ==========================================
// KHU VỰC 1: CLIENT (KHÁCH HÀNG)
// ==========================================
Route::get('/terms', function () {
    return view('pages.client.terms');
})->name('terms');
// 1.1 Khách vãng lai (Guest)
Route::middleware('guest:client')->group(function () {
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register'])->name('register.submit');
});

// 1.2 Khách đã đăng nhập (Được phép xem giá & Mua hàng, chưa cần duyệt)
Route::middleware(['auth:client'])->group(function () {
    Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('client.pricing');
    Route::post('/checkout', [SubscriptionController::class, 'processCheckout'])->name('client.checkout');
    Route::get('/invoice/{order_code}', [SubscriptionController::class, 'showInvoice'])->name('client.invoice');
    Route::post('/invoice/{order_code}/pay', [SubscriptionController::class, 'processMockPayment'])->name('client.invoice.pay');
    Route::get('/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('client.payment.success');
    Route::get('/profile', [ClientProfileController::class, 'profile'])->name('client.profile');
    Route::post('/api/gamma-data', [GammaController::class, 'fetchGammaData'])->name('client.api.gamma');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');
});

// 1.3 Khách VIP (Đã đóng tiền / Đã duyệt / Còn hạn) -> Được vào Terminal
Route::middleware(['auth:client', 'client.approved'])->group(function () {
    Route::get('/dashboard', [ClientAuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/secure-html/{key}', [ClientAuthController::class, 'serveSecureHtml'])->name('html.secure');
});


// ==========================================
// KHU VỰC 2: ADMIN (QUẢN TRỊ VIÊN)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {

    // 2.1 Khách vãng lai cố vào Admin
    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    });

    // 2.2 Đã đăng nhập Admin (Tuyệt mật)
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [UserAuthController::class, 'dashboard'])->name('dashboard');

        // Phân hệ Widgets (Option Chain, Scanner)
        Route::prefix('html')->name('html.')->group(function () {
            Route::get('/', [UserAuthController::class, 'htmlIndex'])->name('index');
            Route::get('/create', [UserAuthController::class, 'htmlCreate'])->name('create');
            Route::post('/store', [UserAuthController::class, 'htmlStore'])->name('store');
            Route::get('/{id}/edit', [UserAuthController::class, 'htmlEdit'])->name('edit');
            Route::put('/{id}/update', [UserAuthController::class, 'htmlUpdate'])->name('update');
            Route::delete('/{id}/delete', [UserAuthController::class, 'htmlDelete'])->name('delete');
        });

        // Phân hệ duyệt Khách hàng
        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/all', [UserAuthController::class, 'allClients'])->name('index');
            Route::get('/pending', [UserAuthController::class, 'pendingClients'])->name('pending');
            Route::put('/{id}/update-status', [UserAuthController::class, 'updateStatus'])->name('update_status');
        });

        Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
    });
});


Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    echo ('View clear succcess');
    Artisan::call('route:clear');
    echo ('route clear is available for configuration ');
});


Route::prefix('clear')->group(function () {
    Route::get('route', [ConfigController::class, 'clearRoute']);
    Route::get('cache', [ConfigController::class, 'clearCache']);
});

Route::get('/clear/route', [ConfigController::class, 'clearRoute']);
