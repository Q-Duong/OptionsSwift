<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ClientProfileController extends Controller
{
    public function profile()
    {
        $client = Auth::guard('client')->user();

        $orders = Order::where('client_id', $client->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return view('pages.client.profile.index', compact('client', 'orders'));
    }
    
}
