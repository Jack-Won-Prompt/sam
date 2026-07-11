{{-- 무농약 품질 인증 · 분석 성적서 --}}
<section class="mt-16" x-data="{ zoom: null }">
    <h2 class="text-lg font-bold text-neutral-800 border-b-2 border-brand-700 pb-2 mb-6">믿고 드실 수 있는 이유</h2>

    {{-- 핵심 지표 --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        @foreach ([
            ['86', '종', '잔류농약 검사', '전 항목 불검출(ND)'],
            ['0', '건', '농약 검출', '무농약 청정 재배'],
            ['20', '년+', '재배 경력', '임업후계자 인증 농가'],
        ] as [$num, $unit, $label, $sub])
            <div class="rounded-xl bg-brand-50 border border-brand-100 px-3 py-5 text-center">
                <p class="text-2xl md:text-3xl font-extrabold text-brand-700 leading-none">
                    {{ $num }}<span class="text-base font-bold">{{ $unit }}</span>
                </p>
                <p class="mt-2 text-xs md:text-sm font-semibold text-neutral-800">{{ $label }}</p>
                <p class="mt-0.5 text-[11px] text-neutral-500 leading-tight">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    <p class="text-neutral-600 mb-6 text-sm leading-relaxed">
        강원특별자치도 횡성군 청정 산지에서 <strong class="text-neutral-800">농약 없이</strong> 재배합니다.
        국가 공인기관의 <strong class="text-neutral-800">특별관리임산물(산양삼) 분석 성적서</strong>에서
        DDT·BHC 등 잔류농약 <strong class="text-neutral-800">86개 항목 전부 “불검출(ND)/합격”</strong> 판정을 받았습니다.
    </p>

    {{-- 인증 서류 --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ([
            ['cert/analysis-1.jpg', '분석 성적서 (1)', '잔류농약 검사항목 1~36'],
            ['cert/analysis-2.jpg', '분석 성적서 (2)', '잔류농약 검사항목 37~86'],
            ['cert/forestry.jpg',   '임업후계자 증서', '횡성군 발급 · 재배 자격'],
        ] as [$path, $title, $desc])
            <figure>
                <button type="button" @click="zoom = '{{ asset('storage/' . $path) }}'"
                        class="block w-full aspect-[3/4] rounded-lg overflow-hidden bg-white border border-neutral-200 hover:border-brand-400 hover:shadow-md transition group">
                    <img src="{{ asset('storage/' . $path) }}" loading="lazy" alt="{{ $title }}"
                         class="w-full h-full object-contain p-1.5 group-hover:scale-[1.02] transition duration-300">
                </button>
                <figcaption class="mt-2 text-center">
                    <p class="text-sm font-semibold text-neutral-800">{{ $title }}</p>
                    <p class="text-xs text-neutral-500">{{ $desc }}</p>
                </figcaption>
            </figure>
        @endforeach
    </div>

    <p class="mt-4 text-[11px] text-neutral-400 leading-relaxed">
        ※ 개인정보 보호를 위해 성적서 일부(생년월일·연락처)는 마스킹 처리되었습니다.
        본 인증은 재배 농가의 품질 관리 자료이며, 식품의 효능·효과를 보증하지 않습니다.
    </p>

    {{-- 확대 보기 --}}
    <div x-show="zoom" x-cloak @keydown.escape.window="zoom = null" @click="zoom = null"
         class="fixed inset-0 z-[60] bg-black/80 flex items-center justify-center p-4 cursor-zoom-out"
         style="display:none;">
        <img :src="zoom" alt="인증 서류 확대" class="max-w-full max-h-full rounded shadow-2xl">
    </div>
</section>
