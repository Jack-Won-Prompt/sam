<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();   // 한 줄 요약
            $table->longText('description')->nullable();        // 상세 설명(HTML)
            $table->string('origin')->nullable();               // 원산지/재배지역 (예: 강원도 홍천)
            $table->string('cultivation_years')->nullable();    // 재배 연근 (예: 5년근)
            $table->string('weight')->nullable();               // 중량/규격
            $table->unsignedInteger('price');                   // 정상가
            $table->unsignedInteger('sale_price')->nullable();  // 할인가
            $table->unsignedInteger('stock')->default(0);       // 기본 재고
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_best')->default(false);
            $table->boolean('is_new')->default(false);
            $table->unsignedInteger('shipping_fee')->default(0); // 개별 배송비(0=기본정책)
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
