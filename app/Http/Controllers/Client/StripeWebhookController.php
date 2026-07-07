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
        $client = $this->getUserByStripeId($payload['data']['object']['customer']);
        $amountPaid = $payload['data']['object']['amount_paid'] / 100;

        if ($client && $amountPaid > 0) {
            
            // 1. LẤY TOÀN BỘ CÁC DÒNG TRONG HÓA ĐƠN
            $lines = data_get($payload, 'data.object.lines.data', []);
            $planName = 'Nâng cấp / Bù trừ gói cước'; // Tên mặc định dự phòng

            // 2. LẶP ĐỂ TÌM TÊN GÓI CHÍNH XÁC
            foreach ($lines as $line) {
                $desc = data_get($line, 'description', '');
                
                // Bỏ qua các dòng proration (bù trừ) của Stripe
                if (!Str::contains($desc, ['Unused time', 'Remaining time'])) {
                    
                    // Có chữ '× ' thì cắt, không có thì lấy nguyên chuỗi
                    $planName = Str::contains($desc, '× ') 
                        ? trim(Str::after($desc, '× ')) 
                        : $desc;
                    break; // Tìm thấy tên gói thật thì dừng vòng lặp luôn
                }
            }

            $orderCode = 'OS-' . strtoupper(Str::random(8));

            // 3. Lưu lịch sử đơn hàng
            \App\Models\Order::create([
                'order_code' => $orderCode,
                'client_id'  => $client->id,
                'plan_type'  => $planName,
                'amount'     => $amountPaid,
                'status'     => 'completed',
            ]);

            \Illuminate\Support\Facades\Log::info("Thanh toán thành công: {$orderCode} | Gói: {$planName} - {$client->email}");

            // 4. Gửi email
            // SendPaymentReceiptJob::dispatch($client, $orderCode, $amountPaid)->onQueue('emails');
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
