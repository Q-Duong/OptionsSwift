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
        
        // Lấy lịch sử giao dịch, sắp xếp mới nhất lên đầu
        $orders = Order::where('client_id', $client->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        // Tính ngày còn lại
        $daysLeft = 0;
        if ($client->expires_at) {
            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($client->expires_at), false);
            $daysLeft = $daysLeft < 0 ? 0 : $daysLeft + 1; 
        }

        // Kiểm tra Trial (Chưa từng thanh toán bill nào)
        $isTrial = $client->status === 'approved' && $orders->where('status', 'paid')->count() == 0;

        return view('pages.client.profile.index', compact('client', 'orders', 'daysLeft', 'isTrial'));
    }
}
