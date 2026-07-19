<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashback_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->integer('amount');           // +적립 / -회수
            $table->unsignedInteger('balance');  // 처리 후 잔액
            $table->string('reason');
            $table->timestamps();
            $table->index('agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashback_histories');
    }
};
