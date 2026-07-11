@extends('layouts.shop')

@section('title', $product->name . ' | 강원 산양삼')
@section('meta_description', $product->short_description)

@section('content')
@php
    $optionsData = $product->options->map(fn ($o) => [
        'id' => $o->id, 'name' => $o->name, 'priceAdd' => (int) $o->price_add,
    ])->values();
    $basePrice = $product->current_price;
@endphp

<div class="container-shop py-8"
     x-data="productDetail({
        productId: {{ $product->id }},
        basePrice: {{ $basePrice }},
        hasOptions: {{ $product->options->isNotEmpty() ? 'true' : 'false' }},
        options: {{ Illuminate\Support\Js::from($optionsData) }},
        addUrl: '{{ route('cart.add') }}',
        cartUrl: '{{ route('cart.index') }}',
        checkoutUrl: '{{ route('checkout.index') }}',
     })">

    <nav class="text-sm text-neutral-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-brand-700">홈</a>
        @if ($product->category)
            <span class="mx-1.5">/</span>
            <a href="{{ route('category.show', $product->category) }}" class="hover:text-brand-700">{{ $product->category->name }}</a>
        @endif
    </nav>

    <div class="grid md:grid-cols-2 gap-10">
        {{-- 이미지 --}}
        <div>
            <x-thumb :product="$product" class="aspect-square rounded-xl shadow-sm {{ $product->thumbnail ? 'zoom-frame' : '' }}" />
            @if ($product->images->isNotEmpty())
                <div class="grid grid-cols-5 gap-2 mt-3">
                    @foreach ($product->images->take(5) as $img)
                        <div class="aspect-square rounded-md overflow-hidden border border-neutral-200">
                            <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 정보 --}}
        <div>
            @if ($product->origin)
                <span class="inline-block text-xs font-semibold text-brand-700 bg-brand-50 px-2.5 py-1 rounded">{{ $product->origin }}</span>
            @endif
            <h1 class="mt-3 text-2xl font-bold text-neutral-900 leading-snug">{{ $product->name }}</h1>
            <p class="mt-2 text-neutral-500">{{ $product->short_description }}</p>

            @if ($product->reviews->isNotEmpty())
                <a href="#reviews" class="mt-2 inline-flex items-center gap-1.5 text-sm">
                    <span class="text-gold-500">
                        @for ($s = 1; $s <= 5; $s++){{ $s <= round($product->avg_rating) ? '★' : '☆' }}@endfor
                    </span>
                    <span class="font-semibold text-neutral-700">{{ $product->avg_rating }}</span>
                    <span class="text-neutral-400">리뷰 {{ $product->reviews->count() }}개</span>
                </a>
            @endif

            <div class="mt-5 flex items-baseline gap-3">
                @if ($product->discount_rate > 0)
                    <span class="text-2xl font-bold text-red-500">{{ $product->discount_rate }}%</span>
                    <span class="text-3xl font-extrabold text-neutral-900">{{ number_format($product->current_price) }}<span class="text-lg">원</span></span>
                    <span class="text-neutral-400 line-through">{{ number_format($product->price) }}원</span>
                @else
                    <span class="text-3xl font-extrabold text-neutral-900">{{ number_format($product->current_price) }}<span class="text-lg">원</span></span>
                @endif
            </div>

            {{-- 상품 정보 요약 --}}
            <dl class="mt-6 border-t border-neutral-200 divide-y divide-neutral-100 text-sm">
                @foreach ([['재배지역', $product->origin], ['연근', $product->cultivation_years], ['규격/중량', $product->weight], ['배송', '5만원 이상 무료 (미만 3,000원)']] as [$k, $v])
                    @if ($v)
                        <div class="flex py-3">
                            <dt class="w-28 text-neutral-500">{{ $k }}</dt>
                            <dd class="text-neutral-800 font-medium">{{ $v }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>

            {{-- 옵션 선택 --}}
            @if ($product->options->isNotEmpty())
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-neutral-700 mb-2">옵션 선택</label>
                    <select @change="addOption($event.target.value); $event.target.value=''"
                            class="w-full rounded-md border-neutral-300 text-sm py-2.5">
                        <option value="">- 옵션을 선택하세요 -</option>
                        @foreach ($product->options as $o)
                            <option value="{{ $o->id }}">
                                {{ $o->name }}
                                @if ($o->price_add != 0)（{{ $o->price_add > 0 ? '+' : '' }}{{ number_format($o->price_add) }}원）@endif
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- 선택된 항목 리스트 --}}
            <div class="mt-4 space-y-2">
                <template x-for="(line, idx) in lines" :key="line.key">
                    <div class="flex items-center gap-3 bg-neutral-50 border border-neutral-200 rounded-md p-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-neutral-800" x-text="line.label"></p>
                            <p class="text-xs text-neutral-500" x-text="number(line.unit) + '원'"></p>
                        </div>
                        <div class="flex items-center border border-neutral-300 rounded">
                            <button type="button" @click="dec(idx)" class="w-8 h-8 text-neutral-500">−</button>
                            <span class="w-8 text-center text-sm" x-text="line.qty"></span>
                            <button type="button" @click="inc(idx)" class="w-8 h-8 text-neutral-500">＋</button>
                        </div>
                        <p class="w-24 text-right text-sm font-bold" x-text="number(line.unit * line.qty) + '원'"></p>
                        <button type="button" @click="removeLine(idx)" class="text-neutral-400 hover:text-red-500">✕</button>
                    </div>
                </template>
            </div>

            {{-- 합계 --}}
            <div class="mt-5 flex items-center justify-between border-t border-neutral-200 pt-4">
                <span class="text-neutral-600">총 상품금액 (<span x-text="totalQty"></span>개)</span>
                <span class="text-2xl font-extrabold text-brand-700"><span x-text="number(totalPrice)"></span>원</span>
            </div>

            {{-- 버튼 --}}
            <div class="mt-5 flex gap-3">
                {{-- 찜하기 --}}
                <form method="POST" action="{{ route('wishlist.toggle', $product) }}"
                      onsubmit="return {{ auth()->check() ? 'true' : 'false' }} || (window.location='{{ route('login') }}', false)">
                    @csrf
                    <button type="submit" title="찜하기"
                            class="h-full aspect-square px-4 rounded-md border {{ ($isWished ?? false) ? 'border-red-300 text-red-500 bg-red-50' : 'border-neutral-300 text-neutral-400' }} hover:border-red-400 hover:text-red-500 transition">
                        <span class="text-2xl">{{ ($isWished ?? false) ? '♥' : '♡' }}</span>
                    </button>
                </form>
                <button type="button" @click="submit('cart')" :disabled="loading"
                        class="btn-outline flex-1 py-4 text-base disabled:opacity-50">장바구니</button>
                <button type="button" @click="submit('buy')" :disabled="loading"
                        class="btn-brand flex-1 py-4 text-base disabled:opacity-50">바로 구매</button>
            </div>
            <p x-show="error" x-text="error" class="mt-3 text-sm text-red-500"></p>
        </div>
    </div>

    {{-- 상세 설명 --}}
    <div class="mt-16">
        <h2 class="text-lg font-bold text-neutral-800 border-b-2 border-brand-700 pb-2 mb-6">상품 상세정보</h2>
        <div class="prose max-w-none text-neutral-700 leading-relaxed
                    prose-headings:text-brand-800 prose-strong:text-neutral-900 prose-li:my-1">
            {!! $product->description !!}
        </div>
    </div>

    {{-- 리뷰 --}}
    @include('partials.reviews')

    {{-- 상품 Q&A --}}
    @include('partials.qna')

    {{-- 무농약 품질 인증 --}}
    @include('partials.quality-cert')

    {{-- 배송/교환·반품 안내 --}}
    @include('partials.shipping-policy')

    {{-- 재배지 실사 --}}
    @include('partials.farm-origin')

    {{-- 연관 상품 --}}
    @if ($related->isNotEmpty())
    <div class="mt-16">
        <h2 class="text-lg font-bold text-neutral-800 mb-6">함께 보면 좋은 상품</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
            @foreach ($related as $rel)
                <x-product-card :product="$rel" />
            @endforeach
        </div>
    </div>
    @endif

    {{-- 모바일 구매바 여백 --}}
    <div class="h-20 md:hidden"></div>

    {{-- 모바일 하단 고정 구매바 --}}
    <div class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-neutral-200 px-4 py-3 flex items-center gap-3"
         style="box-shadow: 0 -4px 20px -8px rgba(0,0,0,0.12);">
        <div class="flex-1">
            <p class="text-[11px] text-neutral-400 leading-none">총 상품금액</p>
            <p class="text-lg font-extrabold text-brand-700 leading-tight"><span x-text="number(totalPrice)"></span>원</p>
        </div>
        <button type="button" @click="submit('cart')" :disabled="loading"
                class="btn-outline py-3 px-4 text-sm disabled:opacity-50">장바구니</button>
        <button type="button" @click="submit('buy')" :disabled="loading"
                class="btn-brand py-3 px-5 text-sm disabled:opacity-50">바로 구매</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productDetail(config) {
    return {
        ...config,
        lines: [],
        loading: false,
        error: '',
        init() {
            // 옵션이 없는 상품은 기본 1개 라인 자동 추가
            if (!this.hasOptions) {
                this.lines.push({ key: 'base', optionId: null, label: '기본 상품', unit: this.basePrice, qty: 1 });
            }
        },
        addOption(optId) {
            if (!optId) return;
            const opt = this.options.find(o => o.id == optId);
            if (!opt) return;
            const exist = this.lines.find(l => l.optionId == opt.id);
            if (exist) { exist.qty++; return; }
            this.lines.push({
                key: 'opt-' + opt.id, optionId: opt.id, label: opt.name,
                unit: this.basePrice + opt.priceAdd, qty: 1,
            });
        },
        inc(i) { this.lines[i].qty++; },
        dec(i) { if (this.lines[i].qty > 1) this.lines[i].qty--; },
        removeLine(i) { this.lines.splice(i, 1); },
        get totalQty() { return this.lines.reduce((s, l) => s + l.qty, 0); },
        get totalPrice() { return this.lines.reduce((s, l) => s + l.unit * l.qty, 0); },
        number(n) { return Number(n).toLocaleString('ko-KR'); },
        async submit(mode) {
            this.error = '';
            if (this.lines.length === 0) { this.error = '옵션을 선택해주세요.'; return; }
            this.loading = true;
            const token = document.querySelector('meta[name=csrf-token]').content;
            try {
                let count = null;
                for (const line of this.lines) {
                    const res = await fetch(this.addUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body: JSON.stringify({ product_id: this.productId, product_option_id: line.optionId, quantity: line.qty }),
                    });
                    if (!res.ok) throw new Error('담기 실패');
                    const data = await res.json().catch(() => ({}));
                    if (typeof data.count === 'number') count = data.count;
                }
                if (mode === 'buy') {
                    window.location = this.checkoutUrl;
                    return;
                }
                // 장바구니: 페이지 이동 없이 토스트 + 카트 뱃지 애니메이션
                this.loading = false;
                if (window.bumpCart) window.bumpCart(count);
                if (window.samToast) window.samToast('장바구니에 담았습니다 🛒');
            } catch (e) {
                this.error = '처리 중 오류가 발생했습니다. 다시 시도해주세요.';
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
