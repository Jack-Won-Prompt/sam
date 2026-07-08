<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private ChatService $chat) {}

    /** 현재 대화 조회/생성 (위젯 오픈 시) */
    public function conversation(Request $request)
    {
        $conv = $this->resolve($request, create: true);

        // 고객이 열었으니 고객 미확인 초기화
        if ($conv->unread_customer > 0) {
            $conv->update(['unread_customer' => 0]);
        }

        return response()->json([
            'token' => $conv->token,
            'channel' => $conv->channel,
            'messages' => $conv->messages()->orderBy('id')->get()->map->toBroadcast(),
        ]);
    }

    /** 고객 메시지 전송 */
    public function send(Request $request)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);
        $conv = $this->resolve($request, create: true);

        $message = $this->chat->send($conv, 'customer', $data['body'], auth()->id());

        return response()->json(['message' => $message->toBroadcast()]);
    }

    /** 폴링: 특정 id 이후 새 메시지 */
    public function poll(Request $request)
    {
        $conv = $this->resolve($request, create: false);
        if (! $conv) {
            return response()->json(['messages' => []]);
        }

        $after = (int) $request->get('after', 0);
        $messages = $conv->messages()->where('id', '>', $after)->orderBy('id')->get();

        // 관리자 답장을 고객이 받아갔으니 미확인 초기화
        if ($conv->unread_customer > 0) {
            $conv->update(['unread_customer' => 0]);
        }

        return response()->json(['messages' => $messages->map->toBroadcast()]);
    }

    /** 인증/토큰으로 대화 찾기 (없으면 생성) */
    private function resolve(Request $request, bool $create): ?ChatConversation
    {
        $token = $request->input('token') ?: $request->query('token');

        if ($token) {
            $conv = ChatConversation::where('token', $token)->first();
            if ($conv) {
                // 로그인 상태면 대화에 회원 연결
                if (auth()->check() && ! $conv->user_id) {
                    $conv->update(['user_id' => auth()->id(), 'name' => auth()->user()->name]);
                }
                return $conv;
            }
        }

        // 로그인 회원은 진행 중 대화 재사용
        if (auth()->check()) {
            $conv = ChatConversation::where('user_id', auth()->id())->latest('last_message_at')->first();
            if ($conv) {
                return $conv;
            }
        }

        if (! $create) {
            return null;
        }

        return ChatConversation::create([
            'user_id' => auth()->id(),
            'name' => auth()->user()->name ?? null,
            'status' => 'open',
        ]);
    }
}
