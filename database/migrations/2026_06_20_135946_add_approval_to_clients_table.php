<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Cột trạng thái: pending, approved, denied
            $table->string('status')->default('pending'); 
            // Cột lưu thời gian hết hạn truy cập
            $table->timestamp('expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['status', 'expires_at']);
        });
    }
};
