<?php

namespace App\Services;

use App\Models\CashbackHistory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CashbackService
{
    /** 대행자 캐쉬백 적립 */
    public function earn(User $agent, int $amount, string $reason, ?int $orderId = null): void
    {
        if ($amount <= 0) {
            return;
        }
        DB::transaction(function () use ($agent, $amount, $reason, $orderId) {
            $agent->increment('cashback_balance', $amount);
            $agent->refresh();
            CashbackHistory::create([
                'agent_id' => $agent->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'balance' => $agent->cashback_balance,
                'reason' => $reason,
            ]);
        });
    }

    /** 대행자 캐쉬백 회수 (잔액 한도 내) */
    public function revoke(User $agent, int $amount, string $reason, ?int $orderId = null): int
    {
        $amount = min($amount, $agent->cashback_balance);
        if ($amount <= 0) {
            return 0;
        }
        DB::transaction(function () use ($agent, $amount, $reason, $orderId) {
            $agent->decrement('cashback_balance', $amount);
            $agent->refresh();
            CashbackHistory::create([
                'agent_id' => $agent->id,
                'order_id' => $orderId,
                'amount' => -$amount,
                'balance' => $agent->cashback_balance,
                'reason' => $reason,
            ]);
        });
        return $amount;
    }

    /** 결제금액·비율로 캐쉬백 금액 계산 */
    public function calc(int $total, int $ratePercent): int
    {
        return (int) floor($total * max(0, $ratePercent) / 100);
    }
}
