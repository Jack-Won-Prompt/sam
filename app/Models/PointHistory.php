<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    protected $fillable = ['user_id', 'order_id', 'amount', 'balance', 'reason'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
