<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy lệnh tạo khóa ngoại
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Trói cột client_id vào cột id của bảng clients
            // cascadeOnDelete() giúp tự dọn rác nếu tài khoản client bị xóa
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')
                  ->cascadeOnDelete();
        });
        
        // Bác có thể làm tương tự cho bảng subscription_items nếu muốn trói chặt
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->foreign('subscription_id')
                  ->references('id')
                  ->on('subscriptions')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Lệnh "Quay xe" (Gỡ khóa ngoại)
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });
        
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
        });
    }
};