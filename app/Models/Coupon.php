<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'discount_type', 'discount_value',
        'min_order_amount', 'max_discount', 'starts_at', 'expires_at',
        'usage_limit', 'used_count', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    /** 이 쿠폰이 지금 유효한가 (기간/활성/사용한도) */
    public function isUsable(int $orderAmount): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->endOfDay()->isPast()) {
            return false;
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }
        if ($orderAmount < $this->min_order_amount) {
            return false;
        }
        return true;
    }

    /** 할인액 계산 */
    public function calcDiscount(int $orderAmount): int
    {
        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $orderAmount);
        }
        // percent
        $discount = (int) floor($orderAmount * $this->discount_value / 100);
        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }
        return min($discount, $orderAmount);
    }
}
