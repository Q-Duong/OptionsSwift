<?php

namespace App\Http\Controllers;

class ConfigController extends Controller
{
    public function clearRoute()
    {
        \Artisan::call('route:clear');
        echo ('route clear is available for configuration ');
    }

    public function clearCache()
    {
        \Artisan::call('config:cache');
        echo ('Config cache is available for configuration ');
    }
}
