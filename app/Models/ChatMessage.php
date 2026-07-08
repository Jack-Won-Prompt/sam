<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['conversation_id', 'sender', 'user_id', 'body', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /** 프론트 전송용 배열 */
    public function toBroadcast(): array
    {
        return [
            'id' => $this->id,
            'sender' => $this->sender,
            'body' => $this->body,
            'time' => optional($this->created_at)->format('H:i'),
        ];
    }
}
