<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class LocalizationController extends Controller
{
    public function locale()
    {
        $locale = Request::segment(2);
        
        if(in_array($locale, ['en'])){
            App::setLocale($locale);
        }else{
            App::setLocale('vn');
            $locale = '';
        }
        return Redirect::to('/'.$locale);
    }
}
