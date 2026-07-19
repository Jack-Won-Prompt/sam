<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbackHistory extends Model
{
    protected $fillable = [
        'agent_id', 'order_id', 'amount', 'balance', 'reason',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
