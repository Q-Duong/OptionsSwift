<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Liên kết với ID của khách hàng
            $table->unsignedBigInteger('client_id'); 
            
            // Thông tin gói cước
            $table->string('plan_type'); // 1_month, 3_months, lifetime
            $table->decimal('amount', 10, 2); // Số tiền (ví dụ: 49.00)
            
            // Trạng thái đơn: pending, paid, failed, cancelled
            $table->string('status')->default('pending'); 
            
            // Tên cổng thanh toán (ví dụ: stripe, crypto, bank_transfer)
            $table->string('payment_method')->nullable(); 
            
            $table->timestamps();

            // Ràng buộc khóa ngoại (Tùy chọn, nếu bảng khách hàng của bác tên là clients)
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
