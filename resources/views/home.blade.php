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
               class="absolute inset-0 flex items-center overflow-hidden"
               style="background: {{ $banner->bg_color ?? '#1f5fd0' }};">
                @if ($banner->image)
                    <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}"
                         class="kenburns absolute inset-0 w-full h-full object-cover">
                @endif
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
<section class="bg-white border-b border-neutral-100">
    <div class="container-shop grid grid-cols-2 md:grid-cols-4 gap-4 py-6">
        @php
            $trust = [
                ['청정 강원도 재배', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 20h18M5 20l4.2-8 3 4.4L15 12l4 8"/>'],
                ['무농약 자연재배', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21V11M12 21c-4 0-6.5-2.5-6.5-6.5C9.5 14.5 12 17 12 21zM12 15c0-3.6 2.4-6 6-6 0 3.6-2.4 6-6 6z"/>'],
                ['5만원 이상 무료배송', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 6h11v9H3zM14 9h3.5L21 12v3h-7zM7.5 18.5a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6zM17 18.5a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6z"/>'],
                ['안전한 간편결제', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 3l7 3v5c0 4-3 7-7 9-4-2-7-5-7-9V6l7-3zM9 12l2 2 4-4"/>'],
            ];
        @endphp
        @foreach ($trust as [$label, $path])
            <div class="flex items-center justify-center gap-2.5">
                <svg class="w-7 h-7 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $path !!}</svg>
                <span class="text-sm font-semibold text-neutral-700">{{ $label }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- 연근별 산양삼 라인업 --}}
@if ($lineup->isNotEmpty())
<section class="container-shop py-8" data-reveal>
    <div class="text-center mb-8">
        <p class="text-gold-500 font-semibold text-sm">LINEUP</p>
        <h2 class="text-xl md:text-2xl font-bold text-neutral-800">연근별 산양삼</h2>
        <p class="text-neutral-500 mt-2 text-sm">6년근부터 15년근까지, 원하시는 연근을 선택하세요</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-8">
        @foreach ($lineup as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endif

{{-- 국가 공인 품질검사 인증 (합격증·검사서) --}}
<section class="container-shop py-12" x-data="{ zoom: null }" data-reveal>
    <div class="text-center mb-8">
        <p class="text-brand-600 font-semibold text-sm">QUALITY CERTIFIED</p>
        <h2 class="text-xl md:text-2xl font-bold text-neutral-800">국가 공인 품질검사 합격</h2>
        <p class="text-neutral-500 mt-2 text-sm">
            한국임업진흥원 특별관리임산물 품질검사 · <strong class="text-brand-700">잔류농약 164종 전 항목 불검출(ND)</strong>
        </p>
    </div>

    {{-- 정식 제품 강조 --}}
    <div class="mx-auto max-w-3xl mb-8 rounded-xl bg-brand-700 text-white px-5 py-5 text-center shadow-sm">
        <p class="text-base md:text-xl font-extrabold leading-snug">
            <span class="text-gold-400">품질검사 합격증</span>이 없는 산양삼은
            <span class="underline decoration-gold-400 decoration-2 underline-offset-4">정식 제품이 아닙니다</span>
        </p>
        <p class="mt-2 text-sm text-white/85 leading-relaxed">
            강원산양삼은 모든 상품에 국가 공인 <strong class="text-white">품질검사 합격증</strong>을 발급받은
            정식 제품만을 판매합니다. 구매 전 반드시 합격증을 확인하세요.
        </p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 md:gap-4">
        @foreach ([
            ['cert/quality-pass.jpg', '품질검사 합격증'],
            ['cert/quality-proof.jpg', '품질검사 결과증명서'],
            ['cert/report-01.jpg', '분석성적서 (1)'],
            ['cert/report-02.jpg', '분석성적서 (2)'],
            ['cert/report-03.jpg', '분석성적서 (3)'],
            ['cert/report-04.jpg', '분석성적서 (4)'],
        ] as [$img, $cap])
            <figure>
                <button type="button" @click="zoom = '{{ asset('storage/' . $img) }}'"
                        class="block w-full aspect-[3/4] rounded-lg overflow-hidden bg-white border border-neutral-200 hover:border-brand-400 hover:shadow-md transition group">
                    <img src="{{ asset('storage/' . $img) }}" loading="lazy" alt="{{ $cap }}"
                         class="w-full h-full object-contain p-1 group-hover:scale-[1.03] transition duration-300">
                </button>
                <figcaption class="mt-1.5 text-center text-xs text-neutral-600 font-medium">{{ $cap }}</figcaption>
            </figure>
        @endforeach
    </div>
    <p class="mt-4 text-center text-[11px] text-neutral-400">
        ※ 개인정보 보호를 위해 성적서 일부(생년월일·연락처)는 마스킹 처리되었습니다. 이미지를 누르면 크게 볼 수 있습니다.
    </p>

    {{-- 확대 --}}
    <div x-show="zoom" x-cloak @keydown.escape.window="zoom = null" @click="zoom = null"
         class="fixed inset-0 z-[100] bg-black/85 flex items-center justify-center p-4 cursor-zoom-out" style="display:none;">
        <img :src="zoom" alt="인증 서류 확대" class="max-w-full max-h-full rounded shadow-2xl">
    </div>
</section>

{{-- 브랜드 스토리 + 재배 현장 영상 --}}
<section class="my-12 bg-brand-800 text-white" data-reveal>
    <div class="container-shop py-16 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <p class="text-gold-400 font-semibold mb-3">OUR STORY</p>
            <h2 class="text-2xl md:text-3xl font-bold leading-snug">산이 키운 진짜 삼,<br>강원도의 시간을 담았습니다</h2>
            <p class="mt-6 text-white/80 leading-relaxed">
                강원산양삼은 해발 700m 이상 고지대의 청정 산림에서 사람의 손길을 최소화하고
                자연 그대로의 힘으로 산양삼을 재배합니다. 오랜 세월 산이 품어 키운 건강함을
                정직하게 전해드립니다.
            </p>
        </div>

        {{-- 재배 현장 영상 (포스터 → 클릭 재생) --}}
        <div x-data="{ playing: false }" class="relative rounded-xl overflow-hidden shadow-2xl aspect-video bg-black">
            <button x-show="!playing" @click="playing = true" class="absolute inset-0 group">
                <img src="{{ asset('storage/farm/farm-video-poster.jpg') }}" alt="재배 현장" class="w-full h-full object-cover">
                <span class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition"></span>
                <span class="absolute inset-0 flex items-center justify-center">
                    <span class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center text-brand-700 text-xl pl-1 group-hover:scale-110 transition">▶</span>
                </span>
                <span class="absolute bottom-3 left-4 text-xs bg-black/50 px-3 py-1 rounded-full">재배 현장 영상 보기</span>
            </button>
            <template x-if="playing">
                <video src="{{ asset('storage/farm/farm-video.mp4') }}" controls autoplay playsinline
                       class="w-full h-full object-contain bg-black"></video>
            </template>
        </div>
    </div>
</section>

{{-- 재배 환경 갤러리 (라이트박스) --}}
<section class="container-shop py-12" x-data="farmGallery()" data-reveal>
    <div class="text-center mb-8">
        <p class="text-brand-600 font-semibold text-sm">GINSENG FARM</p>
        <h2 class="text-xl md:text-2xl font-bold text-neutral-800">산양삼이 자라는 곳</h2>
        <p class="text-neutral-500 mt-2 text-sm">강원도 깊은 소나무 숲, 붉게 익은 산양삼 열매</p>
    </div>

    <div class="grid grid-cols-3 md:grid-cols-6 gap-2 md:gap-3">
        @for ($i = 1; $i <= 12; $i++)
            @php $n = sprintf('%02d', $i); @endphp
            <button type="button" @click="open({{ $i - 1 }})"
                    class="aspect-square rounded-lg overflow-hidden group focus:outline-none">
                <img src="{{ asset('storage/farm/farm-'.$n.'-thumb.jpg') }}" loading="lazy" alt="재배지 사진 {{ $i }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            </button>
        @endfor
    </div>

    {{-- 라이트박스 --}}
    <div x-show="isOpen" x-cloak @keydown.escape.window="close()" @keydown.arrow-right.window="next()" @keydown.arrow-left.window="prev()"
         class="fixed inset-0 z-[100] bg-black/90 flex items-center justify-center" style="display:none">
        <button @click="close()" class="absolute top-4 right-6 text-white/80 hover:text-white text-3xl">✕</button>
        <button @click="prev()" class="absolute left-2 md:left-6 text-white/70 hover:text-white text-5xl px-2">‹</button>
        <img :src="images[current]" alt="" class="max-h-[85vh] max-w-[90vw] object-contain rounded shadow-2xl">
        <button @click="next()" class="absolute right-2 md:right-6 text-white/70 hover:text-white text-5xl px-2">›</button>
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 text-white/70 text-sm">
            <span x-text="current + 1"></span> / <span x-text="images.length"></span>
        </div>
    </div>
</section>

{{-- 신상품 --}}
@if ($newProducts->isNotEmpty())
<section class="container-shop py-8 mb-8" data-reveal>
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

{{-- 최근 본 상품 --}}
@if (isset($recentProducts) && $recentProducts->isNotEmpty())
<section class="container-shop py-8 mb-8" data-reveal>
    <h2 class="text-lg font-bold text-neutral-800 mb-6">최근 본 상품</h2>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-x-3 gap-y-6">
        @foreach ($recentProducts as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endif

{{-- 정품 인증 QR (실시간 상담창 위 · 합격증 2D 바코드 → 재배이력 조회) --}}
<a href="https://sam.kofpi.or.kr/mob/selectCltvaHstrSearch.do?qtyTestSeqNo=93468d9c2c92dd22a12be976549d4c6268c26c2a8c47d2e50173df52467ff"
   target="_blank" rel="noopener"
   class="fixed right-4 md:right-6 bottom-40 md:bottom-24 z-[60] group"
   title="품질검사 합격증 정품 인증 조회">
    <div class="bg-white rounded-xl shadow-lg border border-neutral-200 p-2 w-[4.5rem] md:w-24 text-center hover:shadow-xl hover:border-brand-300 transition">
        <img src="{{ asset('storage/cert/qr-code.png') }}" alt="정품 인증 QR" class="w-full rounded">
        <p class="mt-1 text-[10px] font-bold text-brand-700 leading-tight">정품 인증 조회</p>
    </div>
</a>

@endsection

@push('scripts')
<script>
function farmGallery() {
    return {
        isOpen: false,
        current: 0,
        images: [
            @for ($i = 1; $i <= 16; $i++)
                "{{ asset('storage/farm/farm-'.sprintf('%02d', $i).'.jpg') }}",
            @endfor
        ],
        open(i) { this.current = i; this.isOpen = true; document.body.style.overflow = 'hidden'; },
        close() { this.isOpen = false; document.body.style.overflow = ''; },
        next() { this.current = (this.current + 1) % this.images.length; },
        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
    };
}
</script>
@endpush
