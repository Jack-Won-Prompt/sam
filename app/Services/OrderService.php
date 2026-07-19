<?php

namespace App\Services;

use App\Mail\OrderPaidMail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    /** 결제 완료 처리: 주문/결제 상태 갱신 + 재고 차감 + 적립금 지급 + 메일 */
    public function markPaid(Order $order, array $paymentData = []): void
    {
        if ($order->status === 'paid') {
            return; // 중복 방지
        }

        DB::transaction(function () use ($order, $paymentData) {
            $order->payment?->update([
                'payment_key' => $paymentData['paymentKey'] ?? ($order->payment->payment_key ?? null),
                'method' => $paymentData['method'] ?? '카드',
                'status' => 'done',
                'approved_at' => $paymentData['approvedAt'] ?? now(),
                'raw' => $paymentData['raw'] ?? ['dev' => true],
            ]);

            $order->update([
                'status' => 'paid',
                'payment_method' => $paymentData['method'] ?? ($order->payment_method ?? '카드'),
                'paid_at' => now(),
            ]);

            $order->load('items');
            $order->decrementStock();

            // 적립금 지급 (결제금액의 1%) + 사용 쿠폰/적립 확정
            if ($order->user_id && $order->user) {
                $earn = (int) floor($order->total * 0.01);
                if ($earn > 0) {
                    app(PointService::class)->earn($order->user, $earn, "주문적립 ({$order->order_number})", $order->id);
                }
            }

            // 구매 대행 주문 → 대행자 캐쉬백 적립
            if ($order->agent_id && $order->agent && $order->cashback > 0) {
                app(CashbackService::class)->earn(
                    $order->agent, $order->cashback,
                    "대행 주문 캐쉬백 ({$order->order_number})", $order->id
                );
            }
        });

        // 메일 (실패해도 주문에 영향 없게)
        try {
            $to = $order->orderer_email ?: $order->user?->email;
            if ($to) {
                Mail::to($to)->send(new OrderPaidMail($order));
            }
        } catch (\Throwable $e) {
            Log::warning('주문 메일 발송 실패: ' . $e->getMessage());
        }
    }

    /** 주문 취소/환불: 토스 결제취소 + 재고 복구 + 적립금 회수/복구 */
    public function cancel(Order $order, string $reason, bool $refund = false): array
    {
        if (! $order->isCancellable() && ! $refund) {
            return ['ok' => false, 'message' => '취소할 수 없는 주문 상태입니다.'];
        }

        // 이미 결제된 건 토스 결제취소 호출
        if ($order->payment && $order->payment->status === 'done' && $order->payment->payment_key) {
            $secret = config('services.toss.secret_key');
            $res = Http::withBasicAuth($secret, '')->asJson()->post(
                "https://api.tosspayments.com/v1/payments/{$order->payment->payment_key}/cancel",
                ['cancelReason' => $reason]
            );

            if (! $res->successful()) {
                // 개발용 테스트결제(payment_key TEST_...)는 실제 취소 API가 없으므로 통과 처리
                $isDevKey = str_starts_with((string) $order->payment->payment_key, 'TEST_');
                if (! $isDevKey) {
                    Log::warning('토스 취소 실패: ' . $res->body());
                    return ['ok' => false, 'message' => $res->json('message') ?? '결제 취소에 실패했습니다.'];
                }
            } else {
                $order->payment->update(['status' => 'canceled', 'raw' => $res->json()]);
            }
        }

        DB::transaction(function () use ($order, $reason, $refund) {
            $order->load('items');
            if (in_array($order->status, ['paid', 'preparing', 'shipped', 'delivered'])) {
                $order->restoreStock();
            }
            // 지급 적립금 회수 + 사용 적립금 환급
            if ($order->user_id && $order->user) {
                $ps = app(PointService::class);
                $earned = (int) floor($order->total * 0.01);
                if ($earned > 0) {
                    $ps->use($order->user, min($earned, $order->user->points), "주문취소 적립금 회수 ({$order->order_number})", $order->id);
                }
                if ($order->points_used > 0) {
                    $ps->earn($order->user, $order->points_used, "주문취소 적립금 환급 ({$order->order_number})", $order->id);
                }
            }
            // 대행 주문 캐쉬백 회수
            if ($order->agent_id && $order->agent && $order->cashback > 0) {
                app(CashbackService::class)->revoke(
                    $order->agent, $order->cashback,
                    "주문취소 캐쉬백 회수 ({$order->order_number})", $order->id
                );
            }
            // 쿠폰 사용횟수 복구
            if ($order->coupon_code) {
                \App\Models\Coupon::where('code', $order->coupon_code)->where('used_count', '>', 0)->decrement('used_count');
            }
            $order->update([
                'status' => $refund ? 'refunded' : 'cancelled',
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ]);
        });

        return ['ok' => true, 'message' => '주문이 취소되었습니다.'];
    }

    /** 발송 처리 */
    public function ship(Order $order, string $courier, string $trackingNumber): void
    {
        $order->update([
            'courier' => $courier,
            'tracking_number' => $trackingNumber,
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }
}
