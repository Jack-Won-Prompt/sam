<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    protected $fillable = [
        'token', 'user_id', 'name', 'status',
        'last_message_at', 'unread_admin', 'unread_customer',
    ];

    protected $casts = ['last_message_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function ($c) {
            $c->token ??= Str::random(40);
        });
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getChannelAttribute(): string
    {
        return 'chat.' . $this->token;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? ($this->name ?: '비회원 고객');
    }
}
