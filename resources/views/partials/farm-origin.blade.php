{{-- 상품 상세 하단 - 재배지 실사 --}}
<section class="mt-16">
    <h2 class="text-lg font-bold text-neutral-800 border-b-2 border-brand-700 pb-2 mb-6">이 산양삼이 자란 곳</h2>
    <p class="text-neutral-600 mb-6 text-sm leading-relaxed">
        강원도 해발 700m 청정 소나무 숲에서 농약 없이 자연 그대로 재배합니다.
        붉게 익은 열매는 건강하게 자란 산양삼의 증표입니다.
    </p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach (['07', '10', '08', '15'] as $n)
            <div class="aspect-[4/3] rounded-lg overflow-hidden bg-neutral-100">
                <img src="{{ asset('storage/farm/farm-'.$n.'.jpg') }}" loading="lazy" alt="재배 현장"
                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
            </div>
        @endforeach
    </div>
</section>
