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
            $table->tinyInteger('status_id')->default(2)->after('bank_short_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_virtual_accounts', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
