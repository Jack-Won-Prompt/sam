<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;

class CouponService
{
    /**
     * 쿠폰 검증 → ['ok'=>bool, 'message'=>string, 'coupon'=>?Coupon, 'discount'=>int]
     */
    public function validate(?string $code, int $subtotal, ?User $user): array
    {
        $code = trim((string) $code);
        if ($code === '') {
            return ['ok' => false, 'message' => '쿠폰 코드를 입력하세요.', 'coupon' => null, 'discount' => 0];
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            return ['ok' => false, 'message' => '존재하지 않는 쿠폰입니다.', 'coupon' => null, 'discount' => 0];
        }

        if (! $coupon->isUsable($subtotal)) {
            $msg = $subtotal < $coupon->min_order_amount
                ? number_format($coupon->min_order_amount) . '원 이상 구매 시 사용 가능합니다.'
                : '사용할 수 없는 쿠폰입니다. (기간/한도 확인)';
            return ['ok' => false, 'message' => $msg, 'coupon' => null, 'discount' => 0];
        }

        // 회원당 1회 사용 제한
        if ($user) {
            $alreadyUsed = Order::where('user_id', $user->id)
                ->where('coupon_code', $code)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->exists();
            if ($alreadyUsed) {
                return ['ok' => false, 'message' => '이미 사용한 쿠폰입니다.', 'coupon' => null, 'discount' => 0];
            }
        }

        $discount = $coupon->calcDiscount($subtotal);

        return ['ok' => true, 'message' => number_format($discount) . '원 할인 적용', 'coupon' => $coupon, 'discount' => $discount];
    }
}
