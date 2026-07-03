<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();           // 예: 20260703-ABC123
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 주문자
            $table->string('orderer_name');
            $table->string('orderer_phone');
            $table->string('orderer_email')->nullable();

            // 받는 사람
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('postcode', 10)->nullable();
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('delivery_message')->nullable();

            // 금액
            $table->unsignedInteger('subtotal')->default(0);    // 상품 합계
            $table->unsignedInteger('shipping_fee')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total')->default(0);       // 최종 결제금액

            // 상태
            $table->string('status')->default('pending');
            // pending(결제대기) / paid(결제완료) / preparing(상품준비중) /
            // shipped(배송중) / delivered(배송완료) / cancelled(취소) / refunded(환불)
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
