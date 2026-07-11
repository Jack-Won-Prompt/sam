@extends('layouts.shop')

@section('title', '재배 이야기 | 강원 산양삼')
@section('meta_description', '해발 700m 강원도 횡성 청정 소나무 숲에서 농약 없이 자연 그대로 키운 산양삼. 재배부터 채굴, 포장까지의 이야기.')
@section('og_image', asset('storage/story/farm-01.jpg'))

@section('content')
<div class="pb-16">

    {{-- Hero --}}
    <section class="relative">
        <div class="aspect-[16/9] md:aspect-[21/9] w-full overflow-hidden">
            <img src="{{ asset('storage/story/farm-01.jpg') }}" alt="강원 산양삼 재배지"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/10"></div>
        </div>
        <div class="absolute inset-0 flex items-end">
            <div class="container-shop pb-8 md:pb-14 text-white">
                <p class="text-gold-400 font-semibold tracking-wide mb-2 text-sm md:text-base">강원 산양삼 이야기</p>
                <h1 class="text-2xl md:text-4xl font-extrabold leading-tight drop-shadow">
                    산이 키운 진짜 삼,<br>자연 그대로 정직하게
                </h1>
                <p class="mt-3 text-white/80 text-sm md:text-base max-w-xl">
                    해발 700m 강원특별자치도 횡성 청정 소나무 숲.<br class="hidden md:block">
                    농약도 비료도 없이, 오직 산의 시간으로 키웁니다.
                </p>
            </div>
        </div>
    </section>

    <div class="container-shop">

        {{-- 1. 청정 재배지 --}}
        <section class="mt-14 md:mt-20">
            <div class="max-w-2xl">
                <span class="text-brand-700 font-bold text-sm">01. 청정 재배지</span>
                <h2 class="mt-2 text-xl md:text-2xl font-extrabold text-neutral-900">해발 700m, 소나무 숲이 키웁니다</h2>
                <p class="mt-3 text-neutral-600 leading-relaxed text-sm md:text-base">
                    산양삼은 심은 뒤 사람이 손대지 않아도 자랄 만큼 까다로운 환경을 요구합니다.
                    적당한 그늘과 습도, 낙엽이 쌓여 만든 부엽토 — 강원도 소나무 숲의 경사면은
                    산양삼이 뿌리내리기에 더없이 좋은 자리입니다.
                </p>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach ([
                    ['story/farm-01.jpg', '소나무 숲 경사면 재배지'],
                    ['story/farm-02.jpg', '자연 그대로의 이랑'],
                    ['story/farm-03.jpg', '햇살이 드는 재배 이랑'],
                ] as [$img, $cap])
                    <figure class="rounded-xl overflow-hidden bg-neutral-100">
                        <div class="aspect-[4/3]">
                            <img src="{{ asset('storage/' . $img) }}" loading="lazy" alt="{{ $cap }}"
                                 class="w-full h-full object-cover hover:scale-105 transition duration-500">
                        </div>
                    </figure>
                @endforeach
            </div>
        </section>

        {{-- 2. 건강의 증표, 붉은 열매 --}}
        <section class="mt-14 md:mt-20 grid md:grid-cols-2 gap-8 items-center">
            <div class="order-2 md:order-1">
                <span class="text-brand-700 font-bold text-sm">02. 자연이 준 증표</span>
                <h2 class="mt-2 text-xl md:text-2xl font-extrabold text-neutral-900">붉게 익은 열매가 알려줍니다</h2>
                <p class="mt-3 text-neutral-600 leading-relaxed text-sm md:text-base">
                    여름이면 산양삼은 붉은 열매를 맺습니다. 건강하게 뿌리내린 삼만이 피워내는 이 열매는
                    좋은 환경에서 제대로 자라고 있다는 자연의 신호입니다.
                    이 씨앗이 다시 땅에 떨어져 다음 세대의 산양삼으로 이어집니다.
                </p>
            </div>
            <div class="order-1 md:order-2 grid grid-cols-2 gap-3">
                @foreach (['story/berry-01.jpg', 'story/berry-02.jpg'] as $img)
                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-neutral-100">
                        <img src="{{ asset('storage/' . $img) }}" loading="lazy" alt="산양삼 붉은 열매"
                             class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 3. 정직한 채굴 (영상) --}}
        <section class="mt-14 md:mt-20">
            <div class="max-w-2xl">
                <span class="text-brand-700 font-bold text-sm">03. 정직한 채굴</span>
                <h2 class="mt-2 text-xl md:text-2xl font-extrabold text-neutral-900">주문 후, 그날 산에서 캡니다</h2>
                <p class="mt-3 text-neutral-600 leading-relaxed text-sm md:text-base">
                    산양삼은 잔뿌리 하나까지 온전해야 제값을 합니다. 오랜 세월 자란 삼이 상하지 않도록
                    호미로 흙을 조심스레 걷어내며 손으로 직접 캐냅니다. 미리 캐서 보관하지 않고,
                    주문을 받은 뒤 신선한 상태로 채굴해 보내드립니다.
                </p>
            </div>
            <div class="mt-6 grid md:grid-cols-2 gap-4">
                <div class="rounded-xl overflow-hidden bg-black">
                    <video controls playsinline preload="metadata"
                           poster="{{ asset('storage/story/harvest-02.jpg') }}"
                           class="w-full h-full aspect-video object-cover">
                        <source src="{{ asset('storage/story/harvest.mp4') }}" type="video/mp4">
                    </video>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach (['story/harvest-01.jpg', 'story/harvest-03.jpg'] as $img)
                        <div class="rounded-xl overflow-hidden bg-neutral-100">
                            <img src="{{ asset('storage/' . $img) }}" loading="lazy" alt="산양삼 채굴 현장"
                                 class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 4. 정성스러운 포장 --}}
        <section class="mt-14 md:mt-20 grid md:grid-cols-2 gap-8 items-center">
            <div class="grid grid-cols-2 gap-3">
                @foreach (['story/pack-02.jpg', 'story/pack-01.jpg'] as $img)
                    <div class="aspect-square rounded-xl overflow-hidden bg-neutral-100">
                        <img src="{{ asset('storage/' . $img) }}" loading="lazy" alt="산양삼 선물 포장"
                             class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
            <div>
                <span class="text-brand-700 font-bold text-sm">04. 정성스러운 포장</span>
                <h2 class="mt-2 text-xl md:text-2xl font-extrabold text-neutral-900">받는 분을 생각한 선물 포장</h2>
                <p class="mt-3 text-neutral-600 leading-relaxed text-sm md:text-base">
                    캐낸 산양삼은 살아있는 이끼와 함께 고급 케이스에 담고, 전통 황금보자기로 정성껏 감싸
                    출하합니다. 부모님과 어른신께, 소중한 분께 마음까지 전해지도록 포장합니다.
                </p>
                <a href="{{ route('collection', 'best') }}"
                   class="btn-brand inline-flex mt-5 px-6 py-3 text-sm">산양삼 선물세트 보러가기</a>
            </div>
        </section>

        {{-- 무농약 인증 배너 --}}
        <section class="mt-14 md:mt-20 rounded-2xl bg-brand-50 border border-brand-100 px-6 py-8 md:px-10 md:py-12 text-center"
                 x-data="{ zoom: null }">
            <p class="text-brand-700 font-bold text-sm">무농약 · 청정 인증</p>
            <h2 class="mt-2 text-xl md:text-2xl font-extrabold text-neutral-900">잔류농약 86개 항목, 전부 “불검출”</h2>
            <p class="mt-3 text-neutral-600 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                국가 공인기관의 특별관리임산물(산양삼) 분석 성적서로 검증된 청정 산양삼입니다.
                아래 서류를 눌러 크게 확인하실 수 있습니다.
            </p>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-5 max-w-4xl mx-auto">
                @foreach ([
                    ['cert/analysis-1.jpg', '분석 성적서 (1)', '검사항목 1~36 · 전부 합격'],
                    ['cert/analysis-2.jpg', '분석 성적서 (2)', '검사항목 37~86 · 전부 합격'],
                    ['cert/forestry.jpg',   '임업후계자 증서', '횡성군 발급'],
                ] as [$img, $title, $desc])
                    <figure>
                        <button type="button" @click="zoom = '{{ asset('storage/' . $img) }}'"
                                class="block w-full rounded-xl overflow-hidden bg-white border border-neutral-200 shadow-sm hover:shadow-lg hover:border-brand-400 transition group">
                            <div class="aspect-[3/4]">
                                <img src="{{ asset('storage/' . $img) }}" loading="lazy" alt="{{ $title }}"
                                     class="w-full h-full object-contain p-2 group-hover:scale-[1.03] transition duration-300">
                            </div>
                            <span class="block bg-neutral-50 border-t border-neutral-100 py-1.5 text-xs text-brand-700 font-semibold">🔍 크게 보기</span>
                        </button>
                        <figcaption class="mt-2">
                            <p class="text-sm font-bold text-neutral-800">{{ $title }}</p>
                            <p class="text-xs text-neutral-500">{{ $desc }}</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
            <p class="mt-6 text-[11px] text-neutral-400">
                ※ 개인정보 보호를 위해 성적서 일부(생년월일·연락처)는 마스킹 처리되었습니다.
            </p>

            {{-- 확대 보기 --}}
            <div x-show="zoom" x-cloak @keydown.escape.window="zoom = null" @click="zoom = null"
                 class="fixed inset-0 z-[60] bg-black/85 flex items-center justify-center p-4 cursor-zoom-out"
                 style="display:none;">
                <img :src="zoom" alt="인증 서류 확대" class="max-w-full max-h-full rounded shadow-2xl">
            </div>
        </section>

    </div>
</div>
@endsection
