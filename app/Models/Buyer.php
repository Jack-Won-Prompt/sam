<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $fillable = [
        'agent_id', 'store_name', 'name', 'biz_number', 'phone', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** 소속 구매 대행자 */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /** 화면 표기용 라벨 (소매처 · 이름) */
    public function getLabelAttribute(): string
    {
        return trim($this->store_name . ' · ' . $this->name, ' ·');
    }
}
