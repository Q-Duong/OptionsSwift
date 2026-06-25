<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    protected $pricingPlans = [
        '3_months' => [
            'name' => 'Quarterly Plan',
            'price' => 120,
            'duration_text' => '3 months'
        ],
        '6_months' => [
            'name' => 'Semi-Annual Plan',
            'price' => 210,
            'duration_text' => '6 months'
        ],
        '12_months' => [
            'name' => 'Annual Plan',
            'price' => 360,
            'duration_text' => '12 months'
        ]
    ];

    public function pricing()
    {
        return view('pages.client.pricing');
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|in:3_months,6_months,12_months'
        ]);

        $client = Auth::guard('client')->user();

        $planKey = $request->plan_type;
        $planDetails = $this->pricingPlans[$planKey];

        $orderCode = 'OS-' . strtoupper(Str::random(8));

        $order = Order::create([
            'order_code' => $orderCode,
            'client_id' => $client->id,
            'plan_type' => $planKey,
            'amount' => $planDetails['price'],
            'status' => 'pending',
        ]);

        return redirect()->route('client.invoice', $order->order_code);
    }

    public function showInvoice($orderCode)
    {
        $client = Auth::guard('client')->user();

        $order = Order::where('order_code', $orderCode)
            ->where('client_id', $client->id)
            ->firstOrFail();

        if ($order->status === 'paid') {
            return redirect()->route('dashboard');
        }

        $planName = $this->pricingPlans[$order->plan_type]['name'];

        return view('pages.client.invoice', compact('order', 'planName'));
    }

    // Hàm này dành riêng cho hệ thống Thanh toán (Webhook/Callback) gọi vào
    public function paymentCallback(Request $request)
    {
        // Giả sử cổng thanh toán gửi mã đơn hàng qua biến 'order_code'
        $orderCode = $request->order_code;

        // Tìm đơn hàng bằng order_code
        $order = Order::where('order_code', $orderCode)->first();

        // Nếu tìm thấy đơn và đơn đang chờ thanh toán
        if ($order && $order->status === 'pending') {

            // 1. Cập nhật đơn thành Đã thanh toán
            $order->update(['status' => 'paid']);

            // 2. Tìm Khách hàng sở hữu đơn đó
            $client = $order->client;

            // 3. Tính toán thời gian hết hạn dựa vào gói họ mua
            $expiresAt = null; // Mặc định là lifetime (null)

            if ($order->plan_type === '1_month') {
                $expiresAt = \Carbon\Carbon::now()->addMonth();
            } elseif ($order->plan_type === '3_months') {
                $expiresAt = \Carbon\Carbon::now()->addMonths(3);
            }

            // 4. Nâng cấp tài khoản lên VIP và cộng ngày
            $client->update([
                'status' => 'approved',
                'expires_at' => $expiresAt
            ]);

            // Trả về thông báo cho Cổng thanh toán biết là hệ thống mình đã ghi nhận thành công
            return response()->json(['status' => 'success', 'message' => 'Account upgraded successfully']);
        }

        // Báo lỗi nếu không tìm thấy đơn hoặc đơn đã thanh toán rồi
        return response()->json(['status' => 'error', 'message' => 'Order not found or already processed'], 400);
    }

    // 1. Hàm giả lập xử lý khi khách bấm nút "Pay"
    public function processMockPayment($orderCode)
    {
        $client = Auth::guard('client')->user();

        $order = Order::where('order_code', $orderCode)
            ->where('client_id', $client->id)
            ->firstOrFail();

        // Chỉ xử lý nếu đơn hàng đang ở trạng thái pending
        if ($order->status === 'pending') {

            // 1. Cập nhật đơn thành Đã thanh toán
            $order->update(['status' => 'paid']);

            // 2. LOGIC TÍNH NGÀY HẾT HẠN THÔNG MINH (CỘNG DỒN)
            // Nếu tài khoản vẫn còn hạn, lấy ngày hết hạn cũ làm mốc để cộng thêm.
            // Nếu đã hết hạn (hoặc chưa có), lấy thời điểm hiện tại làm mốc.
            $baseDate = ($client->expires_at && \Carbon\Carbon::parse($client->expires_at)->isFuture())
                ? \Carbon\Carbon::parse($client->expires_at)
                : \Carbon\Carbon::now();

            $expiresAt = null;
            if ($order->plan_type === '3_months') {
                $expiresAt = $baseDate->copy()->addMonths(3);
            } elseif ($order->plan_type === '6_months') {
                $expiresAt = $baseDate->copy()->addMonths(6);
            } elseif ($order->plan_type === '12_months') {
                $expiresAt = $baseDate->copy()->addMonths(12);
            }

            // 3. Nâng cấp Client lên VIP và mở khóa Dashboard
            $client->update([
                'status' => 'approved',
                'expires_at' => $expiresAt
            ]);
        }

        // Thanh toán xong thì đá sang trang Success
        return redirect()->route('client.payment.success')->with('success', 'Payment successful! Your account has been upgraded.');
    }

    public function paymentSuccess()
    {
        $client = Auth::guard('client')->user();

        // Nếu lỡ khách chưa mua mà gõ URL vào đây thì đuổi về Dashboard/Pricing
        if ($client->status !== 'approved') {
            return redirect()->route('client.pricing');
        }

        return view('pages.client.payment_success');
    }
}
