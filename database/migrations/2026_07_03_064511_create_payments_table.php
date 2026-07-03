<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_key')->nullable();          // 토스 paymentKey
            $table->string('toss_order_id')->nullable();        // 토스에 넘긴 orderId
            $table->string('method')->nullable();               // 카드/가상계좌/간편결제 등
            $table->unsignedInteger('amount');
            $table->string('status')->default('ready');
            // ready / done / canceled / failed
            $table->timestamp('approved_at')->nullable();
            $table->json('raw')->nullable();                    // 토스 응답 원본
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
