<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'payment_key', 'toss_order_id', 'method',
        'amount', 'status', 'approved_at', 'raw',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'raw' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
