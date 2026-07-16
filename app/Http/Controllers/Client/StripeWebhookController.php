<?php

namespace App\Http\Controllers\Client;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends CashierController
{
    /**
     * Lắng nghe sự kiện: Khách hàng thanh toán thành công (Bao gồm gia hạn tháng/năm)
     */
    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        // 1. Lấy thông tin hóa đơn từ Stripe
        $invoice = $payload['data']['object'];
        $stripeInvoiceId = $invoice['id']; // ID độc nhất của hóa đơn (VD: in_1ABC234...)
        
        // ==========================================
        // CHỐT CHẶN VÀNG: KIỂM TRA TRÙNG LẶP (DUPLICATE)
        // ==========================================
        // Nếu mã hóa đơn này đã tồn tại trong DB -> Bỏ qua ngay lập tức để tránh đúp dòng
        if (Order::where('order_code', $stripeInvoiceId)->exists()) {
            \Illuminate\Support\Facades\Log::info("Bỏ qua Webhook: Hóa đơn {$stripeInvoiceId} đã được lưu trước đó.");
            return parent::handleInvoicePaymentSucceeded($payload);
        }

        $client = $this->getUserByStripeId($invoice['customer']);
        $invoiceTotal = $invoice['total'] / 100;

        // Chỉ lưu hóa đơn nếu khách hàng tồn tại và số tiền thanh toán thực sự > 0
        if ($client && $invoiceTotal !== 0) {
            
            $lines = data_get($invoice, 'lines.data', []);
            $planName = 'Downgrade / Update Subscription';

            foreach ($lines as $line) {
                $desc = data_get($line, 'description', '');
                if (!\Illuminate\Support\Str::contains($desc, ['Unused time', 'Remaining time', 'Proration'])) {
                    $planName = \Illuminate\Support\Str::contains($desc, '× ') 
                        ? trim(\Illuminate\Support\Str::after($desc, '× ')) 
                        : $desc;
                    break;
                }
            }

            // Ghi sổ sách vào Database (Dùng chính Invoice ID của Stripe làm Order Code)
            Order::create([
                'order_code' => $stripeInvoiceId, 
                'client_id'  => $client->id,
                'plan_type'  => $planName,
                'amount'     => $invoiceTotal, 
                'status'     => 'paid',
            ]);

            \Illuminate\Support\Facades\Log::info("Ghi nhận hóa đơn: {$stripeInvoiceId} | Gói: {$planName} | Khách: {$client->email}");

            // Gửi email biên lai cho khách (Bác có thể mở comment dòng dưới nếu cần)
            // SendPaymentReceiptJob::dispatch($client, $stripeInvoiceId, $amountPaid)->onQueue('emails');
        }

        return parent::handleInvoicePaymentSucceeded($payload);
    }

    /**
     * Lắng nghe sự kiện: Trừ tiền thất bại (Thẻ hết tiền, bị khóa...)
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $stripeId = $payload['data']['object']['customer'];

        // Với hóa đơn thất bại, Stripe dùng trường 'amount_due' (số tiền đáng lẽ phải trả)
        $amountFailed = $payload['data']['object']['amount_due'] / 100;

        $lineItem = data_get($payload, 'data.object.lines.data.0');
        $rawDescription = data_get($lineItem, 'description', 'Unknown Plan');
        $planName = Str::after($rawDescription, '× ');

        $client = Client::where('stripe_id', $stripeId)->first();

        if ($client) {
            $orderCode = 'OS-' . strtoupper(Str::random(8));

            // 1. Lưu hóa đơn lỗi vào DB để khách thấy trên giao diện Profile
            Order::create([
                'order_code' => $orderCode,
                'client_id'  => $client->id,
                'plan_type'  => trim($planName),
                'amount'     => $amountFailed,
                'status'     => 'failed',
            ]);

            // 2. Đổi trạng thái tài khoản (nếu bảng clients bác đang dùng cột status để check)
            $client->update([
                'status' => 'past_due' // Trạng thái nợ tiền
            ]);

            Log::warning("Thanh toán THẤT BẠI: {$orderCode} | Gói: {$planName} - {$client->email}");

            // 3. Bắn Email nhắc nhở (Bác tự tạo Job này nhé, giống cái Receipt lúc nãy)
            // SendPaymentFailedJob::dispatch($client, $orderCode)->onQueue('emails');
        }

        return parent::handleInvoicePaymentFailed($payload);
    }
}
