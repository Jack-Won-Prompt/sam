<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_agent')->default(false)->after('is_admin');       // 구매 대행자 여부
            $table->unsignedTinyInteger('cashback_rate')->default(5)->after('is_agent'); // 캐쉬백 비율(%)
            $table->unsignedInteger('cashback_balance')->default(0)->after('cashback_rate'); // 누적 캐쉬백 잔액
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_agent', 'cashback_rate', 'cashback_balance']);
        });
    }
};
