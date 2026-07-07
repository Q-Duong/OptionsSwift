<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy lệnh dọn dẹp (Xóa cột)
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Kiểm tra xem cột có tồn tại không rồi mới xóa để tránh lỗi
            if (Schema::hasColumn('clients', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            
            if (Schema::hasColumn('clients', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
        });
    }

    /**
     * Lệnh "Quay xe" (Phục hồi lại cột nếu muốn undo)
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Thêm lại cột như cấu trúc cũ nếu chạy lệnh rollback
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_approved')->default(0);
        });
    }
};