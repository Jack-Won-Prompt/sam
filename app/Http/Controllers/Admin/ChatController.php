<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private ChatService $chat) {}

    public function index()
    {
        $conversations = $this->conversationList();
        $active = $conversations->first();
        if ($active) {
            $active->load(['messages' => fn ($q) => $q->orderBy('id')]);
            $active->update(['unread_admin' => 0]);
        }

        return view('admin.chats.index', compact('conversations', 'active'));
    }

    public function show(ChatConversation $conversation)
    {
        $conversations = $this->conversationList();
        $conversation->load(['messages' => fn ($q) => $q->orderBy('id')]);
        $conversation->update(['unread_admin' => 0]);

        return view('admin.chats.index', ['conversations' => $conversations, 'active' => $conversation]);
    }

    public function reply(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);
        $message = $this->chat->send($conversation, 'admin', $data['body'], auth()->id());

        return response()->json(['message' => $message->toBroadcast()]);
    }

    /** 대화 상세 폴링 (관리자용 실시간 폴백) */
    public function poll(Request $request, ChatConversation $conversation)
    {
        $after = (int) $request->get('after', 0);
        $messages = $conversation->messages()->where('id', '>', $after)->orderBy('id')->get();
        if ($messages->isNotEmpty()) {
            $conversation->update(['unread_admin' => 0]);
        }

        return response()->json(['messages' => $messages->map->toBroadcast()]);
    }

    /** 대화 목록 폴링 (JSON) */
    public function list()
    {
        return response()->json([
            'total_unread' => (int) ChatConversation::sum('unread_admin'),
            'conversations' => $this->conversationList()->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->display_name,
                'unread' => $c->unread_admin,
                'last' => optional($c->last_message_at)->format('m-d H:i'),
                'url' => route('admin.chats.show', $c),
            ]),
        ]);
    }

    private function conversationList()
    {
        return ChatConversation::with('user')
            ->whereNotNull('last_message_at')
            ->orderByDesc('last_message_at')
            ->limit(50)->get();
    }
}
