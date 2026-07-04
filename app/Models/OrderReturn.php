<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $fillable = ['order_id', 'user_id', 'type', 'reason', 'detail', 'status', 'admin_memo'];

    public const TYPES = ['exchange' => '교환', 'return' => '반품'];
    public const STATUSES = [
        'requested' => '접수',
        'approved' => '승인',
        'rejected' => '거절',
        'completed' => '완료',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
