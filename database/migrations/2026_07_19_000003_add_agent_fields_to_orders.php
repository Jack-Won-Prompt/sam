<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 구매 대행 주문: 대행자와 구매자(소매처)
            $table->foreignId('agent_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('buyer_id')->nullable()->after('agent_id')->constrained('buyers')->nullOnDelete();
            $table->unsignedInteger('cashback')->default(0)->after('total'); // 이 주문의 대행자 캐쉬백
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agent_id');
            $table->dropConstrainedForeignId('buyer_id');
            $table->dropColumn('cashback');
        });
    }
};
