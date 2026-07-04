{{-- 상품 Q&A : $product 필요 --}}
<section id="qna" class="mt-16">
    <div class="flex items-center justify-between border-b-2 border-brand-700 pb-2 mb-6">
        <h2 class="text-lg font-bold text-neutral-800">상품 문의 <span class="text-brand-700">{{ $product->questions->count() }}</span></h2>
    </div>

    {{-- 작성 --}}
    @auth
        <form method="POST" action="{{ route('questions.store', $product) }}" class="bg-neutral-50 border border-neutral-200 rounded-lg p-5 mb-8">
            @csrf
            <textarea name="content" rows="3" required maxlength="1000" placeholder="상품에 궁금한 점을 남겨주세요. (예: 몇 년근인가요? 배송은 얼마나 걸리나요?)"
                      class="w-full rounded-md border-neutral-300 text-sm"></textarea>
            <div class="flex items-center justify-between mt-3">
                <label class="flex items-center gap-1.5 text-sm text-neutral-600">
                    <input type="checkbox" name="is_secret" value="1" class="rounded text-brand-600"> 비밀글
                </label>
                <button class="btn-brand py-2 px-5 text-sm">문의 등록</button>
            </div>
        </form>
    @else
        <p class="text-sm text-neutral-500 bg-neutral-50 rounded-lg p-4 mb-8">
            상품 문의는 <a href="{{ route('login') }}" class="text-brand-700 underline">로그인</a> 후 이용하실 수 있습니다.
        </p>
    @endauth

    {{-- 목록 --}}
    <div class="divide-y divide-neutral-100 border-t border-neutral-200">
        @forelse ($product->questions as $q)
            @php
                $isOwner = auth()->check() && (auth()->id() === $q->user_id || auth()->user()->is_admin);
                $hidden = $q->is_secret && ! $isOwner;
            @endphp
            <div class="py-4">
                <div class="flex items-start gap-2">
                    <span class="text-xs px-2 py-0.5 rounded-full shrink-0 {{ $q->status === 'answered' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $q->status === 'answered' ? '답변완료' : '미답변' }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-neutral-800 whitespace-pre-line">
                            {{ $hidden ? '🔒 비밀글입니다.' : $q->content }}
                        </p>
                        <p class="text-xs text-neutral-400 mt-1">
                            {{ \Illuminate\Support\Str::mask($q->user->name ?? '회원', '*', 1) }} · {{ $q->created_at->format('Y-m-d') }}
                        </p>
                        @if ($q->answer && ! $hidden)
                            <div class="mt-3 bg-brand-50 rounded-lg p-3 text-sm text-neutral-700">
                                <span class="font-semibold text-brand-700">답변</span>
                                <p class="mt-1 whitespace-pre-line">{{ $q->answer }}</p>
                            </div>
                        @endif
                    </div>
                    @if ($isOwner)
                        <form method="POST" action="{{ route('questions.destroy', $q) }}" onsubmit="return confirm('삭제할까요?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-neutral-400 hover:text-red-500 shrink-0">삭제</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-neutral-400 py-10 text-sm">등록된 문의가 없습니다.</p>
        @endforelse
    </div>
</section>
