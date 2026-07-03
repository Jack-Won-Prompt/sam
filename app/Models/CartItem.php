<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'product_id', 'product_option_id', 'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    /** 이 항목 단가 (상품 실판매가 + 옵션 추가금) */
    public function getUnitPriceAttribute(): int
    {
        $base = $this->product?->current_price ?? 0;
        $add = $this->option?->price_add ?? 0;
        return max(0, $base + $add);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->unit_price * $this->quantity;
    }
}
