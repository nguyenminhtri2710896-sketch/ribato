<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Danh sách ngân hàng được phép tạo VA, phân cách bằng dấu phẩy (VD: MSB,TCB,MB)
            $table->string('va_bank_codes', 100)->nullable()->after('va_gateway_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('va_bank_codes');
        });
    }
};
