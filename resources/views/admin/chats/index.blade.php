@extends('layouts.admin')

@section('title', '실시간 상담')

@section('content')
<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden flex" style="height: calc(100vh - 8rem)"
     x-data="adminChat()">

    {{-- 대화 목록 --}}
    <div class="w-72 border-r border-neutral-200 flex flex-col shrink-0">
        <div class="px-4 py-3 border-b border-neutral-100 font-bold text-neutral-800 text-sm flex items-center justify-between">
            <span>대화 목록</span>
            <span class="text-xs text-neutral-400" x-text="'미확인 ' + totalUnread"></span>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-neutral-100">
            <template x-for="c in conversations" :key="c.id">
                <a :href="c.url"
                   class="block px-4 py-3 hover:bg-neutral-50"
                   :class="c.id === activeId ? 'bg-brand-50' : ''">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-neutral-800 text-sm" x-text="c.name"></span>
                        <span x-show="c.unread > 0" x-text="c.unread"
                              class="min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[11px] font-bold flex items-center justify-center"></span>
                    </div>
                    <p class="text-xs text-neutral-400 mt-0.5" x-text="c.last"></p>
                </a>
            </template>
            <p x-show="conversations.length === 0" class="p-6 text-center text-neutral-400 text-sm">대화가 없습니다.</p>
        </div>
    </div>

    {{-- 대화 내용 --}}
    <div class="flex-1 flex flex-col min-w-0">
        @if ($active)
            <div class="px-5 py-3 border-b border-neutral-100 shrink-0">
                <p class="font-bold text-neutral-800">{{ $active->display_name }}</p>
                <p class="text-xs text-neutral-400">
                    {{ $active->user ? $active->user->email : '비회원' }}
                    @if ($active->user?->phone) · {{ $active->user->phone }}@endif
                </p>
            </div>
            <div x-ref="msgs" class="flex-1 overflow-y-auto p-5 space-y-3 bg-neutral-50">
                <template x-for="m in messages" :key="m.id">
                    <div :class="m.sender === 'admin' ? 'flex justify-end' : 'flex justify-start'">
                        <div class="max-w-[70%]">
                            <div :class="m.sender === 'admin' ? 'bg-brand-600 text-white' : 'bg-white border border-neutral-200 text-neutral-800'"
                                 class="rounded-2xl px-3.5 py-2 text-sm whitespace-pre-line break-words" x-text="m.body"></div>
                            <p class="text-[10px] text-neutral-400 mt-1" :class="m.sender === 'admin' ? 'text-right' : ''" x-text="m.time"></p>
                        </div>
                    </div>
                </template>
            </div>
            <form @submit.prevent="reply()" class="p-3 border-t border-neutral-200 flex gap-2 shrink-0">
                <input x-model="input" type="text" placeholder="답장을 입력하세요"
                       class="flex-1 rounded-full border-neutral-300 text-sm py-2 focus:border-brand-500 focus:ring-brand-500">
                <button class="btn-brand rounded-full py-2 px-5 text-sm">전송</button>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-neutral-400">왼쪽에서 대화를 선택하세요.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const ADMIN_CHAT_CFG = {
    activeId: {{ $active?->id ?? 'null' }},
    channel: @json($active?->channel),
    listUrl: @json(route('admin.chats.list')),
    initial: {!! $active ? $active->messages->map->toBroadcast()->toJson() : '[]' !!},
    replyUrlBase: @json(url('admin/chats')),
};
function adminChat() {
    const cfg = ADMIN_CHAT_CFG;
    return {
        ...cfg,
        messages: cfg.initial || [],
        conversations: [],
        totalUnread: 0,
        input: '',
        lastId: 0,
        boot() {
            this.lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;
            this.$nextTick(() => this.scrollDown());
            // 활성 대화 실시간/폴링
            if (this.activeId) {
                const ch = window.hasRealtime ? window.subscribeChat(this.channel, (m) => this.receive(m)) : null;
                if (!ch) this.pollActive();
            }
            this.refreshList();
            setInterval(() => this.refreshList(), 6000);
        },
        pollActive() {
            setInterval(async () => {
                try {
                    const res = await fetch(`${this.replyUrlBase}/${this.activeId}/poll?after=${this.lastId}`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    (data.messages || []).forEach(m => this.receive(m));
                } catch (e) {}
            }, 4000);
        },
        receive(m) {
            if (this.messages.some(x => x.id === m.id)) return;
            this.messages.push(m);
            this.lastId = Math.max(this.lastId, m.id);
            this.$nextTick(() => this.scrollDown());
        },
        async refreshList() {
            try {
                const res = await fetch(this.listUrl, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                this.conversations = data.conversations;
                this.totalUnread = data.total_unread;
                if (window.updateChatBadge) window.updateChatBadge(data.total_unread);
            } catch (e) {}
        },
        async reply() {
            const body = this.input.trim();
            if (!body) return;
            this.input = '';
            const csrf = document.querySelector('meta[name=csrf-token]').content;
            try {
                const res = await fetch(`${this.replyUrlBase}/${this.activeId}/reply`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ body }),
                });
                const data = await res.json();
                this.receive(data.message);
            } catch (e) {}
        },
        scrollDown() { const el = this.$refs.msgs; if (el) el.scrollTop = el.scrollHeight; },
        init() { this.boot(); },
    };
}
</script>
@endpush
