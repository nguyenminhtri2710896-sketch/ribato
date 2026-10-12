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
        Schema::table('user_virtual_accounts', function (Blueprint $table) {
            // O: dùng 1 lần, M: dùng nhiều lần
            $table->string('account_type', 1)->default('M')->after('bank_short_code');
            $table->unsignedBigInteger('min_amount')->nullable()->after('account_type');
            $table->unsignedBigInteger('max_amount')->nullable()->after('min_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_virtual_accounts', function (Blueprint $table) {
            $table->dropColumn(['account_type', 'min_amount', 'max_amount']);
        });
    }
};
