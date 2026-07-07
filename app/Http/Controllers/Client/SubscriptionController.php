<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function pricing()
    {
        $plans = [
            [
                'name' => 'Quarterly Flow',
                'price' => '$120',
                'breakdown' => 'Just $40 per month',
                'id' => env('STRIPE_PRICE_QUARTERLY'),
            ],
            [
                'name' => 'Semi-Annual Flow',
                'price' => '$210',
                'breakdown' => 'Save 12% - Just $35 per month',
                'id' => env('STRIPE_PRICE_SEMI_ANNUAL'),
                'popular' => true,
            ],
            [
                'name' => 'Annual Flow',
                'price' => '$360',
                'breakdown' => 'Save 25% - Just $30 per month',
                'id' => env('STRIPE_PRICE_ANNUAL'),
            ],
        ];

        $features = [
            'Live Option Flow Scanner',
            'Gamma Levels & Block Alerts',
            'Institutional Data Feed',
            'Option Chain Imbalance, Pressure & GEX analysis',
            'Estimated Hedge Shares',
        ];

        $client = Auth::guard('client')->user();
        $currentPlanId = $client && $client->subscription('default')
            ? $client->subscription('default')->stripe_price
            : null;

        return view('pages.client.pricing', compact('plans', 'features', 'currentPlanId'));
    }

    // public function checkout(Request $request)
    // {
    //     /** @var \App\Models\Client $client */
    //     $client = Auth::guard('client')->user();

    //     // 1. ĐÓN GÓI CƯỚC: Ưu tiên lấy từ URL, nếu trống (do vừa click link email về) thì lôi trong Session ra
    //     $planId = $request->query('plan_type') ?? session('pending_plan_type');

    //     // Xóa ngay Session sau khi lấy để giữ bộ nhớ sạch sẽ, tránh side-effect cho lần mua sau
    //     if (session()->has('pending_plan_type')) {
    //         session()->forget('pending_plan_type');
    //     }

    //     // CẤU HÌNH ĐỊA CHỈ TRẢ VỀ SAU KHI THANH TOÁN
    //     $successUrl = route('dashboard') . '?payment=success';
    //     $cancelUrl = route('client.pricing');

    //     // 2. NẾU KHÁCH ĐÃ CÓ SUBSCRIPTION (Đang dùng Trial hoặc đang dùng Pro)
    //     // Dùng hàm checkout trực tiếp từ $client để Stripe tự nhận diện yêu cầu cập nhật (Swap)
    //     if ($client->subscribed('default')) {

    //         // Nếu có truyền plan mới thì nâng cấp lên plan đó, nếu không thì giữ nguyên
    //         if ($planId) {
    //             return $client->newSubscription('default', $planId)
    //                 ->checkout([
    //                     'success_url' => $successUrl,
    //                     'cancel_url'  => $cancelUrl,
    //                 ]);
    //         }

    //         // Trường hợp chỉ muốn vào Billing Portal để tự quản lý (tránh lỗi nếu không truyền plan)
    //         return $client->redirectToBillingPortal(route('dashboard'));
    //     }

    //     // 3. NẾU KHÁCH MỚI HOÀN TOÀN (CHƯA CÓ GÓI)

    //     // Luồng mua gói cụ thể (Đã lấy được từ URL hoặc Session)
    //     if ($planId) {
    //         return $client->newSubscription('default', $planId)
    //             ->checkout([
    //                 'success_url' => $successUrl,
    //                 'cancel_url'  => $cancelUrl,
    //             ]);
    //     }

    //     // Luồng mặc định: Dùng thử 7 ngày (ép nhập thẻ) nếu hoàn toàn không tìm thấy gói nào
    //     $defaultPriceId = env('STRIPE_PRICE_ID');

    //     if (!$defaultPriceId) {
    //         return redirect()->route('client.pricing')->with('error', 'Vui lòng chọn một gói cước để tiến hành thanh toán.');
    //     }

    //     return $client->newSubscription('default', $defaultPriceId)
    //         ->trialDays(7)
    //         ->checkout([
    //             'success_url' => $successUrl,
    //             'cancel_url'  => $cancelUrl,
    //         ]);
    // }

    public function checkout(Request $request)
    {
        /** @var \App\Models\Client $client */
        $client = Auth::guard('client')->user();

        // ==========================================
        // 1. ĐÓN GÓI CƯỚC TỪ URL HOẶC SESSION
        // ==========================================
        $planId = $request->query('plan_type') ?? session('pending_plan_type');

        // Lấy xong thì xóa Session ngay cho sạch
        if (session()->has('pending_plan_type')) {
            session()->forget('pending_plan_type');
        }

        $successUrl = route('dashboard') . '?payment=success';
        $cancelUrl = route('client.pricing');

        // ==========================================
        // 2. NẾU KHÁCH ĐÃ CÓ GÓI (UPGRADE / DOWNGRADE)
        // ==========================================
        if ($client->subscribed('default')) {

            if ($planId) {
                $subscription = $client->subscription('default');
                
                // Tránh lỗi: Khách bấm mua lại đúng gói đang dùng
                if ($subscription->stripe_price === $planId) {
                    return redirect()->route('client.pricing')
                        ->with('warning', 'You are already using this plan.');
                }

                try {
                    // ĐỔI GÓI VÀ THU TIỀN CHÊNH LỆCH LẬP TỨC
                    $subscription->swapAndInvoice($planId);
                    
                    return redirect()->route('dashboard')
                        ->with('payment', 'success')
                        ->with('success', 'Upgrade successful! Transaction has been processed.');
                        
                } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) {
                    // Xử lý bảo mật thẻ 3D Secure (Yêu cầu nhập OTP từ ngân hàng)
                    return redirect()->route(
                        'cashier.payment',
                        [$e->payment->id, 'redirect' => route('dashboard')]
                    );
                } catch (\Exception $e) {
                    // Lỗi thẻ hết tiền hoặc bị khóa
                    \Illuminate\Support\Facades\Log::error('Swap Plan Error: ' . $e->getMessage());
                    return redirect()->route('client.pricing')
                        ->with('error', 'Transaction failed. Please check your payment card or balance again.');
                }
            }

            // Nếu không truyền mã gói mới -> Mở cổng quản lý thanh toán của Stripe (Billing Portal)
            return $client->redirectToBillingPortal(route('dashboard'));
        }

        // ==========================================
        // 3. NẾU KHÁCH MỚI HOÀN TOÀN (MUA MỚI / DÙNG THỬ)
        // ==========================================

        // Khách chọn mua thẳng một gói cụ thể
        if ($planId) {
            return $client->newSubscription('default', $planId)
                ->checkout([
                    'success_url' => $successUrl,
                    'cancel_url'  => $cancelUrl,
                ]);
        }

        // Luồng mặc định: Dùng thử 7 ngày (ép nhập thẻ)
        $defaultPriceId = env('STRIPE_PRICE_ID');
        
        if (!$defaultPriceId) {
            return redirect()->route('client.pricing')->with('error', 'Please select a plan to proceed with payment.');
        }

        return $client->newSubscription('default', $defaultPriceId)
            ->trialDays(7)
            ->checkout([
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
            ]);
    }

    public function cancel(Request $request)
    {
        $client = Auth::guard('client')->user();
        $subscription = $client->subscription('default');

        // Kiểm tra xem khách có gói đang Active không và chưa bấm hủy trước đó
        if ($subscription && $subscription->active() && !$subscription->canceled()) {

            // Lệnh này báo với Stripe: "Dừng gia hạn, nhưng cho dùng nốt đến cuối kỳ"
            $subscription->cancel();

            return back()->with('success', 'Your subscription has been canceled. You can still access your Pro features until the end of your billing period.');
        }

        return back()->with('error', 'No active subscription found to cancel.');
    }

    public function resume(Request $request)
    {
        $client = Auth::guard('client')->user();
        $subscription = $client->subscription('default');

        if ($subscription && $subscription->canceled() && $subscription->onGracePeriod()) {
            // Lệnh thần thánh của Cashier giúp hồi sinh gói cước
            $subscription->resume(); 
            
            return redirect()->back()->with('success', 'Great! Your subscription has been resumed successfully.');
        }

        return redirect()->back()->with('error', 'Unable to resume subscription at this time.');
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
}
