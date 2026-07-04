<?php

namespace App\Services;

use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointService
{
    /** 적립 */
    public function earn(User $user, int $amount, string $reason, ?int $orderId = null): void
    {
        if ($amount <= 0) {
            return;
        }
        DB::transaction(function () use ($user, $amount, $reason, $orderId) {
            $user->increment('points', $amount);
            $user->refresh();
            PointHistory::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'balance' => $user->points,
                'reason' => $reason,
            ]);
        });
    }

    /** 사용/차감 (잔액 한도 내에서) */
    public function use(User $user, int $amount, string $reason, ?int $orderId = null): int
    {
        $amount = min($amount, $user->points);
        if ($amount <= 0) {
            return 0;
        }
        DB::transaction(function () use ($user, $amount, $reason, $orderId) {
            $user->decrement('points', $amount);
            $user->refresh();
            PointHistory::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => -$amount,
                'balance' => $user->points,
                'reason' => $reason,
            ]);
        });
        return $amount;
    }
}
