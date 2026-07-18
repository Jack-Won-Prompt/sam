{{-- 실시간 상담 채팅 위젯 (모든 페이지 우측 하단) --}}
<div x-data="chatWidget()" x-init="boot()" class="fixed z-[60] right-4 bottom-24 md:bottom-6 md:right-6">
    {{-- 채팅 패널 --}}
    <div x-show="open" x-cloak x-transition.origin.bottom.right
         class="absolute bottom-16 right-0 w-[calc(100vw-2rem)] max-w-sm h-[70vh] max-h-[520px] bg-white rounded-2xl shadow-2xl border border-neutral-200 flex flex-col overflow-hidden">
        {{-- 헤더 --}}
        <div class="bg-brand-700 text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div>
                <p class="font-bold">🌿 실시간 상담</p>
                <p class="text-xs text-white/70">평일 09:00~18:00 · 보통 몇 분 내 답변</p>
            </div>
            <button @click="open = false" class="text-white/80 hover:text-white text-xl">✕</button>
        </div>

        {{-- 메시지 --}}
        <div x-ref="msgs" class="flex-1 overflow-y-auto p-4 space-y-3 bg-neutral-50">
            <div class="text-center">
                <span class="inline-block bg-white border border-neutral-200 text-neutral-500 text-xs px-3 py-1.5 rounded-full">
                    무엇을 도와드릴까요? 편하게 문의해 주세요 😊
                </span>
            </div>
            <template x-for="m in messages" :key="m.id">
                <div :class="m.sender === 'customer' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="max-w-[78%]">
                        <div :class="m.sender === 'customer' ? 'bg-brand-600 text-white' : 'bg-white border border-neutral-200 text-neutral-800'"
                             class="rounded-2xl px-3.5 py-2 text-sm whitespace-pre-line break-words" x-text="m.body"></div>
                        <p class="text-[10px] text-neutral-400 mt-1" :class="m.sender === 'customer' ? 'text-right' : ''" x-text="m.time"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- 입력 --}}
        <form @submit.prevent="send()" class="p-3 border-t border-neutral-200 flex gap-2 shrink-0 bg-white">
            <input x-model="input" type="text" placeholder="메시지를 입력하세요"
                   class="flex-1 rounded-full border-neutral-300 text-sm py-2 focus:border-brand-500 focus:ring-brand-500">
            <button type="submit" class="w-10 h-10 rounded-full bg-brand-700 text-white shrink-0 hover:bg-brand-800">↑</button>
        </form>
    </div>

    {{-- 플로팅 버튼 --}}
    <button @click="toggle()" class="icon-on-dark relative w-14 h-14 rounded-full bg-brand-700 text-white shadow-lg hover:bg-brand-800 hover:scale-105 transition flex items-center justify-center">
        <svg x-show="!open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.9 9.9 0 01-4-.8L3 21l1.3-3.5A7.9 7.9 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-show="unread > 0" x-cloak x-text="unread"
              class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[11px] font-bold flex items-center justify-center"></span>
    </button>
</div>

@push('scripts')
<script>
function chatWidget() {
    return {
        open: false, loaded: false, token: '', channel: '',
        messages: [], input: '', unread: 0, lastId: 0, seen: 0, polling: null,

        boot() {
            this.token = localStorage.getItem('sam_chat_token') || '';
            this.seen = parseInt(localStorage.getItem('sam_chat_seen') || '0', 10);
            // 진행 중 대화가 있으면(토큰 존재) 백그라운드로 로드해 미확인 배지 표시
            if (this.token) this.init();
        },
        toggle() { this.open ? this.close() : this.openChat(); },
        async openChat() {
            this.open = true;
            if (!this.loaded) await this.init();
            this.markSeen();
            this.$nextTick(() => this.scrollDown());
        },
        close() { this.open = false; },
        async init() {
            try {
                const res = await fetch(`{{ route('chat.conversation') }}?token=${encodeURIComponent(this.token)}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                this.token = data.token; this.channel = data.channel;
                localStorage.setItem('sam_chat_token', this.token);
                this.messages = data.messages || [];
                this.lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;
                this.unread = this.messages.filter(m => m.sender === 'admin' && m.id > this.seen).length;
                this.loaded = true;
                this.listen();
            } catch (e) { /* 무시 */ }
        },
        listen() {
            const ch = window.hasRealtime ? window.subscribeChat(this.channel, (m) => this.receive(m)) : null;
            if (!ch) this.startPolling();
        },
        startPolling() {
            if (this.polling) return;
            this.polling = setInterval(async () => {
                try {
                    const res = await fetch(`{{ route('chat.poll') }}?token=${encodeURIComponent(this.token)}&after=${this.lastId}`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    (data.messages || []).forEach(m => this.receive(m));
                } catch (e) {}
            }, 4000);
        },
        receive(m) {
            if (this.messages.some(x => x.id === m.id)) return;
            this.messages.push(m);
            this.lastId = Math.max(this.lastId, m.id);
            if (this.open) { this.markSeen(); this.$nextTick(() => this.scrollDown()); }
            else if (m.sender === 'admin') this.unread++;
        },
        async send() {
            const body = this.input.trim();
            if (!body) return;
            this.input = '';
            const csrf = document.querySelector('meta[name=csrf-token]').content;
            try {
                const res = await fetch(`{{ route('chat.send') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ token: this.token, body }),
                });
                const data = await res.json();
                this.receive(data.message);
                this.markSeen();
            } catch (e) {}
        },
        markSeen() {
            this.seen = this.lastId; this.unread = 0;
            localStorage.setItem('sam_chat_seen', String(this.seen));
        },
        scrollDown() { const el = this.$refs.msgs; if (el) el.scrollTop = el.scrollHeight; },
    };
}
</script>
@endpush
