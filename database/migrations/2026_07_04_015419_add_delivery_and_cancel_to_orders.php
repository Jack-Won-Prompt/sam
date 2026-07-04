<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 배송
            $table->string('courier')->nullable()->after('payment_method');       // 택배사
            $table->string('tracking_number')->nullable()->after('courier');       // 송장번호
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            // 취소/환불
            $table->string('cancel_reason')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
            // 프로모션(2차 연동 대비)
            $table->unsignedInteger('points_used')->default(0)->after('discount');
            $table->string('coupon_code')->nullable()->after('points_used');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier', 'tracking_number', 'shipped_at', 'cancel_reason', 'cancelled_at', 'points_used', 'coupon_code']);
        });
    }
};
