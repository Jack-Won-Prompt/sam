<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuestion extends Model
{
    protected $fillable = ['product_id', 'user_id', 'content', 'is_secret', 'answer', 'answered_at', 'status'];

    protected $casts = [
        'is_secret' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
