<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    function showLoginForm(){
        if(Auth::check()){
            return Redirect::route('dashboard.index');
        }else{
            return view('pages.admin.login.index');
        }
    }
    function login(Request $request){
        $data=$request->all();
        if(Auth::attempt([
                'email' => $data['account_username'],
                'password' => $data['account_password']
            ],true)){
            return Redirect::route('dashboard.index');
        }else{
            return Redirect::route('login')->with('error','Tài khoản hoặc mật khẩu không đúng');
        }
   }
}
