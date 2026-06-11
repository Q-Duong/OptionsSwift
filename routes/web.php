<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\FileController;
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

// --- ROUTE CHO CLIENT ---
Route::middleware('guest:client')->group(function () {
    // Tên route là 'login' và 'register' (mặc định của Laravel)
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth:client,web')->group(function () {
    Route::get('/dashboard', [ClientAuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/secure-html/{key}', [ClientAuthController::class, 'serveSecureHtml'])->name('html.secure');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');
});


Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');

        // Thường admin do hệ thống cấp tài khoản, nếu không cần tự đăng ký thì bạn có thể xóa 2 dòng dưới
        Route::get('/register', [UserAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [UserAuthController::class, 'register'])->name('register.submit');
    });

    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [UserAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

        // Nhóm Route Quản lý HTML (CRUD)
        Route::get('/html', [UserAuthController::class, 'listHtml'])->name('html.list');
        Route::get('/html/create', [UserAuthController::class, 'createHtml'])->name('html.create');
        Route::post('/html/store', [UserAuthController::class, 'storeHtml'])->name('html.store');
        Route::get('/html/{id}/edit', [UserAuthController::class, 'editHtml'])->name('html.edit');
        Route::put('/html/{id}/update', [UserAuthController::class, 'updateHtml'])->name('html.update');
        Route::delete('/html/{id}/delete', [UserAuthController::class, 'deleteHtml'])->name('html.delete');
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
