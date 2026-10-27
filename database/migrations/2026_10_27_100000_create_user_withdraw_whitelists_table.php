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
        Schema::create('user_withdraw_whitelists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bank_id');
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name', 191)->nullable();
            $table->unsignedBigInteger('daily_limit');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'bank_id', 'bank_account_number'], 'uq_user_bank_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_withdraw_whitelists');
    }
};
