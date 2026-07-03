<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id',
        'orderer_name', 'orderer_phone', 'orderer_email',
        'receiver_name', 'receiver_phone', 'postcode', 'address1', 'address2', 'delivery_message',
        'subtotal', 'shipping_fee', 'discount', 'total',
        'status', 'payment_method', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
