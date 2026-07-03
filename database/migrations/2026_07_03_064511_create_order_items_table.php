<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_option_id')->nullable()->constrained('product_options')->nullOnDelete();

            // 주문 시점 스냅샷 (상품 정보가 바뀌어도 주문 내역 보존)
            $table->string('product_name');
            $table->string('option_name')->nullable();
            $table->unsignedInteger('price');                   // 단가(옵션 반영)
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('subtotal');                // price * quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
