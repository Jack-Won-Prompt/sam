<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                    // 쿠폰코드
            $table->string('name');
            $table->enum('discount_type', ['fixed', 'percent']); // 정액 / 정률
            $table->unsignedInteger('discount_value');           // 금액 또는 %
            $table->unsignedInteger('min_order_amount')->default(0);
            $table->unsignedInteger('max_discount')->nullable(); // 정률 시 최대 할인액
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();  // 전체 사용 제한(null=무제한)
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
