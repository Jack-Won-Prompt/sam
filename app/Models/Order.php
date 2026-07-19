<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'agent_id', 'buyer_id',
        'orderer_name', 'orderer_phone', 'orderer_email',
        'receiver_name', 'receiver_phone', 'postcode', 'address1', 'address2', 'delivery_message',
        'subtotal', 'shipping_fee', 'discount', 'points_used', 'coupon_code', 'total', 'cashback',
        'status', 'payment_method', 'paid_at',
        'courier', 'tracking_number', 'shipped_at', 'cancel_reason', 'cancelled_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending'   => '결제대기',
        'paid'      => '결제완료',
        'preparing' => '상품준비중',
        'shipped'   => '배송중',
        'delivered' => '배송완료',
        'cancelled' => '주문취소',
        'refunded'  => '환불완료',
    ];

    // 택배사 조회 URL (송장추적)
    public const COURIERS = [
        'cj' => ['name' => 'CJ대한통운', 'track' => 'https://trace.cjlogistics.com/next/tracking.html?wblNo='],
        'hanjin' => ['name' => '한진택배', 'track' => 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&schLang=KR&wblnumText2='],
        'lotte' => ['name' => '롯데택배', 'track' => 'https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo='],
        'post' => ['name' => '우체국택배', 'track' => 'https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1='],
        'logen' => ['name' => '로젠택배', 'track' => 'https://www.ilogen.com/web/personal/trace/'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 구매 대행자 */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /** 대행 구매자(소매처) */
    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    /** 교환/반품 신청 가능 여부 (배송중/배송완료) */
    public function isReturnable(): bool
    {
        return in_array($this->status, ['shipped', 'delivered']);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getCourierNameAttribute(): ?string
    {
        return self::COURIERS[$this->courier]['name'] ?? $this->courier;
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (! $this->courier || ! $this->tracking_number || ! isset(self::COURIERS[$this->courier])) {
            return null;
        }
        return self::COURIERS[$this->courier]['track'] . $this->tracking_number;
    }

    public function isCancellable(): bool
    {
        // 결제대기/결제완료/상품준비중 단계까지 취소 가능
        return in_array($this->status, ['pending', 'paid', 'preparing']);
    }

    /** 재고 차감 (결제 완료 시 1회) */
    public function decrementStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->product_option_id) {
                ProductOption::whereKey($item->product_option_id)->decrement('stock', $item->quantity);
            }
            if ($item->product_id) {
                Product::whereKey($item->product_id)->decrement('stock', $item->quantity);
            }
        }
    }

    /** 재고 복구 (취소/환불 시 1회) */
    public function restoreStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->product_option_id) {
                ProductOption::whereKey($item->product_option_id)->increment('stock', $item->quantity);
            }
            if ($item->product_id) {
                Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }
        }
    }
}
