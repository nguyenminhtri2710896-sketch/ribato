<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_virtual_accounts', function (Blueprint $table) {
            $table->string('order_code', 32)->nullable()->after('order_no');
        });

        // Tạo order_code cho các VA đã có
        DB::table('user_virtual_accounts')->whereNull('order_code')->orderBy('id')->each(function ($objVa) {
            do {
                $strCode = strtoupper(Str::random(16));
            } while (DB::table('user_virtual_accounts')->where('order_code', $strCode)->exists());
            DB::table('user_virtual_accounts')->where('id', $objVa->id)->update(['order_code' => $strCode]);
        });

        Schema::table('user_virtual_accounts', function (Blueprint $table) {
            $table->unique('order_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_virtual_accounts', function (Blueprint $table) {
            $table->dropUnique(['order_code']);
            $table->dropColumn('order_code');
        });
    }
};
