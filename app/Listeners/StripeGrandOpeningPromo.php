<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use Stripe\StripeClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StripeGrandOpeningPromo
{
    public function handle(WebhookReceived $event)
    {
        // return;
        $payload = $event->payload;
        $type = $payload['type'];

        // 1. ĐỔI GÁC: Chỉ bắt sự kiện Hóa Đơn Đã Thanh Toán (invoice.paid)
        // Sự kiện này bao trọn cả mua mới qua Checkout lẫn 1-Click Upsell
        if (!in_array($type, ['invoice.paid', 'invoice.payment_succeeded'])) {
            return;
        }

        // 2. Cài đặt thời hạn chót của chương trình (15/07/2026)
        $promoEndDate = Carbon::parse('2026-07-20 23:59:59');
        if (now()->greaterThan($promoEndDate)) {
            return;
        }

        $invoice = $payload['data']['object'];
        $amountPaid = $invoice['amount_paid'] ?? 0;
        $customerId = $invoice['customer'] ?? null;
        $billingReason = $invoice['billing_reason'] ?? '';

        // 3. ĐIỀU KIỆN VÀNG: Chỉ tặng cho Mua mới hoặc Nâng cấp gói
        // Bỏ qua 'subscription_cycle' (Gia hạn định kỳ) để không bị tặng lặp lại vào năm sau
        $isQualifyingPurchase = in_array($billingReason, [
            'subscription_create',
            'subscription_update'
        ]);

        if ($amountPaid > 0 && $customerId && $isQualifyingPurchase) {
            try {
                $stripe = new StripeClient(config('services.stripe.secret'));

                // 1. Kéo thông tin khách hàng từ Stripe về
                $customerDetails = $stripe->customers->retrieve($customerId);

                // 2. CHỐT CHẶN VÀNG: Kiểm tra xem đã đóng dấu nhận quà chưa?
                if (isset($customerDetails->metadata['promo_received']) && $customerDetails->metadata['promo_received'] == 'true') {
                    Log::info("Khách hàng {$customerId} đã nhận KM trước đó, bỏ qua.");
                    return; // Đã nhận rồi thì quay xe luôn!
                }

                // 3. Nếu chưa nhận thì tiến hành bơm tiền
                $stripe->customers->createBalanceTransaction(
                    $customerId,
                    [
                        'amount'      => -$amountPaid,
                        'currency'    => $invoice['currency'],
                        'description' => 'Grand Opening Promo: 100% Credit for next renewal',
                    ]
                );

                // 4. ĐÓNG DẤU ĐÃ NHẬN QUÀ VÀO METADATA CỦA KHÁCH
                $stripe->customers->update($customerId, [
                    'metadata' => ['promo_received' => 'true']
                ]);

                Log::info("Đã tặng khuyến mãi thành công cho Customer ID: {$customerId}");
            } catch (\Exception $e) {
                Log::error("Lỗi tặng khuyến mãi Stripe: " . $e->getMessage());
            }
        }
    }
}
