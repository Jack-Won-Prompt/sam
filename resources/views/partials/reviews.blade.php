{{-- 상품 리뷰 : $product, $canReview 필요 --}}
<section id="reviews" class="mt-16">
    <div class="flex items-center justify-between border-b-2 border-brand-700 pb-2 mb-6">
        <h2 class="text-lg font-bold text-neutral-800">
            상품 리뷰 <span class="text-brand-700">{{ $product->reviews->count() }}</span>
        </h2>
        @if ($product->reviews->isNotEmpty())
            <div class="text-sm">
                <span class="text-gold-500">@for($s=1;$s<=5;$s++){{ $s <= round($product->avg_rating) ? '★' : '☆' }}@endfor</span>
                <span class="font-semibold ml-1">{{ $product->avg_rating }} / 5</span>
            </div>
        @endif
    </div>

    {{-- 작성 폼 --}}
    @auth
        @if ($canReview ?? false)
            <form method="POST" action="{{ route('reviews.store', $product) }}" enctype="multipart/form-data"
                  x-data="{ rating: 5 }" class="bg-neutral-50 border border-neutral-200 rounded-lg p-5 mb-8">
                @csrf
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-sm font-medium text-neutral-700">별점</span>
                    <div class="text-2xl text-gold-500 cursor-pointer">
                        <template x-for="i in 5" :key="i">
                            <span @click="rating = i" x-text="i <= rating ? '★' : '☆'"></span>
                        </template>
                    </div>
                    <input type="hidden" name="rating" :value="rating">
                </div>
                <textarea name="content" rows="3" required maxlength="1000" placeholder="상품은 어떠셨나요? 솔직한 후기를 남겨주세요."
                          class="w-full rounded-md border-neutral-300 text-sm"></textarea>
                <div class="flex items-center justify-between mt-3">
                    <input type="file" name="image" accept="image/*" class="text-sm">
                    <button class="btn-brand py-2 px-5 text-sm">리뷰 등록</button>
                </div>
            </form>
        @else
            <p class="text-sm text-neutral-400 bg-neutral-50 rounded-lg p-4 mb-8">구매하신 후 리뷰를 작성하실 수 있습니다.</p>
        @endif
    @endauth

    {{-- 목록 --}}
    @forelse ($product->reviews as $review)
        <div class="border-b border-neutral-100 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-gold-500 text-sm">@for($s=1;$s<=5;$s++){{ $s <= $review->rating ? '★' : '☆' }}@endfor</span>
                    <span class="text-sm font-medium text-neutral-700">{{ \Illuminate\Support\Str::mask($review->user->name ?? '회원', '*', 1) }}</span>
                </div>
                <span class="text-xs text-neutral-400">{{ $review->created_at->format('Y-m-d') }}</span>
            </div>
            <p class="mt-2 text-sm text-neutral-700 leading-relaxed whitespace-pre-line">{{ $review->content }}</p>
            @if ($review->image)
                <img src="{{ asset('storage/'.$review->image) }}" alt="리뷰 이미지" class="mt-3 w-32 h-32 object-cover rounded-lg">
            @endif
            @auth
                @if ($review->user_id === auth()->id() || auth()->user()->is_admin)
                    <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('삭제할까요?')" class="mt-2">
                        @csrf @method('DELETE')
                        <button class="text-xs text-neutral-400 hover:text-red-500">삭제</button>
                    </form>
                @endif
            @endauth
        </div>
    @empty
        <p class="text-center text-neutral-400 py-10 text-sm">아직 등록된 리뷰가 없습니다. 첫 리뷰를 남겨주세요!</p>
    @endforelse
</section>
