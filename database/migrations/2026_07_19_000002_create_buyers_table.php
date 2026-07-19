<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            // 소속 구매 대행자 (1 대행자 : N 구매자)
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('store_name');            // 소매처
            $table->string('name');                  // 구매자 이름
            $table->string('biz_number')->nullable(); // 사업자번호
            $table->string('phone')->nullable();     // 전화번호
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
