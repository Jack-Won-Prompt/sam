@extends('layouts.shop')

@section('content')

{{-- 히어로 배너 슬라이더 --}}
@if ($banners->isNotEmpty())
<section x-data="{
        active: 0,
        total: {{ $banners->count() }},
        next() { this.active = (this.active + 1) % this.total },
        prev() { this.active = (this.active - 1 + this.total) % this.total },
    }"
    x-init="setInterval(() => next(), 5000)"
    class="relative overflow-hidden">
    <div class="relative h-[300px] md:h-[420px]">
        @foreach ($banners as $i => $banner)
            <a href="{{ $banner->link ?? '#' }}"
               x-show="active === {{ $i }}" x-transition.opacity.duration.700ms
               class="absolute inset-0 flex items-center"
               style="background: {{ $banner->image ? 'center/cover url('.asset('storage/'.$banner->image).')' : ($banner->bg_color ?? '#1f5fd0') }};">
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
                <div class="container-shop text-white relative">
                    <p class="text-sm md:text-base font-medium text-white/80 mb-3">GANGWON WILD GINSENG</p>
                    <h2 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow">{{ $banner->title }}</h2>
                    @if ($banner->subtitle)
                        <p class="mt-4 text-base md:text-xl text-white/90">{{ $banner->subtitle }}</p>
                    @endif
                    <span class="inline-flex mt-8 items-center gap-2 bg-white text-brand-800 font-semibold px-6 py-3 rounded-full text-sm">
                        상품 보러가기 →
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- 화살표 --}}
    <button @click="prev" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/25 text-white hover:bg-black/40 hidden md:flex items-center justify-center">‹</button>
    <button @click="next" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/25 text-white hover:bg-black/40 hidden md:flex items-center justify-center">›</button>

    {{-- 인디케이터 --}}
    <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2">
        @foreach ($banners as $i => $banner)
            <button @click="active = {{ $i }}" :class="active === {{ $i }} ? 'bg-white w-6' : 'bg-white/50 w-2'"
                    class="h-2 rounded-full transition-all"></button>
        @endforeach
    </div>
</section>
@endif

{{-- 신뢰 배지 --}}
<section class="bg-brand-50 border-b border-brand-100">
    <div class="container-shop grid grid-cols-2 md:grid-cols-4 gap-4 py-6 text-center">
        @foreach ([['🏔️','청정 강원도 재배'], ['🌱','무농약 자연재배'], ['🚚','5만원 이상 무료배송'], ['🔒','안전한 간편결제']] as [$icon, $label])
            <div class="flex flex-col items-center gap-1">
                <span class="text-2xl">{{ $icon }}</span>
                <span class="text-sm font-medium text-brand-800">{{ $label }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- 카테고리 바로가기 --}}
<section class="container-shop py-12">
    <h2 class="text-xl md:text-2xl font-bold text-center text-neutral-800 mb-8">카테고리</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach ($mainCategories as $cat)
            <a href="{{ route('category.show', $cat) }}"
               class="group flex flex-col items-center gap-3 rounded-xl border border-neutral-200 bg-white py-8 hover:border-brand-400 hover:shadow-md transition">
                <span class="text-4xl group-hover:scale-110 transition">
                    @switch($cat->slug)
                        @case('sanyangsam') 🌿 @break
                        @case('gift') 🎁 @break
                        @case('processed') 🧃 @break
                        @default 🌾
                    @endswitch
                </span>
                <span class="font-semibold text-neutral-700 group-hover:text-brand-700">{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- 베스트 상품 --}}
@if ($bestProducts->isNotEmpty())
<section class="container-shop py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <p class="text-gold-500 font-semibold text-sm">BEST</p>
            <h2 class="text-xl md:text-2xl font-bold text-neutral-800">이달의 베스트 산양삼</h2>
        </div>
        <a href="{{ route('collection', 'best') }}" class="text-sm text-neutral-500 hover:text-brand-700">더보기 →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
        @foreach ($bestProducts as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endif

{{-- 브랜드 스토리 --}}
<section class="my-12 bg-brand-800 text-white">
    <div class="container-shop py-16 text-center max-w-3xl">
        <p class="text-gold-400 font-semibold mb-3">OUR STORY</p>
        <h2 class="text-2xl md:text-3xl font-bold leading-snug">산이 키운 진짜 삼,<br>강원도의 시간을 담았습니다</h2>
        <p class="mt-6 text-white/80 leading-relaxed">
            강원산양삼은 해발 700m 이상 고지대의 청정 산림에서 사람의 손길을 최소화하고
            자연 그대로의 힘으로 산양삼을 재배합니다. 오랜 세월 산이 품어 키운 건강함을
            정직하게 전해드립니다.
        </p>
    </div>
</section>

{{-- 신상품 --}}
@if ($newProducts->isNotEmpty())
<section class="container-shop py-8 mb-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <p class="text-brand-600 font-semibold text-sm">NEW</p>
            <h2 class="text-xl md:text-2xl font-bold text-neutral-800">새로 들어온 상품</h2>
        </div>
        <a href="{{ route('collection', 'new') }}" class="text-sm text-neutral-500 hover:text-brand-700">더보기 →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
        @foreach ($newProducts as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endif

@endsection
