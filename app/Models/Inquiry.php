<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'category', 'title', 'content',
        'is_secret', 'answer', 'answered_at', 'status',
    ];

    protected $casts = [
        'is_secret' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
