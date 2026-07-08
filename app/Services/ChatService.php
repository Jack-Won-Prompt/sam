<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

class ChatService
{
    /** 메시지 추가 + 카운터 갱신 + 실시간 브로드캐스트 */
    public function send(ChatConversation $conv, string $sender, string $body, ?int $userId = null): ChatMessage
    {
        $message = ChatMessage::create([
            'conversation_id' => $conv->id,
            'sender' => $sender,
            'user_id' => $userId,
            'body' => $body,
            'created_at' => now(),
        ]);

        $conv->forceFill([
            'last_message_at' => now(),
            'status' => 'open',
            'unread_admin' => $sender === 'customer' ? $conv->unread_admin + 1 : 0,
            'unread_customer' => $sender === 'admin' ? $conv->unread_customer + 1 : 0,
        ])->save();

        // BROADCAST_CONNECTION=null 이면 no-op (Pusher 키 설정 후 실시간 동작)
        try {
            broadcast(new MessageSent($message, $conv->channel));
        } catch (\Throwable $e) {
            // 브로드캐스트 실패가 메시지 저장에 영향 주지 않도록
        }

        return $message;
    }
}
