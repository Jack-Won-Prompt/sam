@extends('layouts.shop')

@section('title', '결제하기 | 강원 산양삼')

@section('content')
<div class="container-shop py-12 max-w-2xl">
    <div class="border border-neutral-200 rounded-xl p-6 md:p-8 bg-white">
        <div class="text-center">
            <h1 class="text-xl font-bold text-neutral-800">결제하기</h1>
            <p class="text-sm text-neutral-500 mt-1">주문번호 {{ $order->order_number }}</p>
        </div>

        {{-- 주문 요약 --}}
        <div class="mt-6 text-left border-t border-b border-neutral-100 divide-y divide-neutral-100">
            @foreach ($order->items as $item)
                <div class="flex justify-between py-2.5 text-sm">
                    <span class="text-neutral-600 line-clamp-1 pr-3">{{ $item->product_name }}
                        @if ($item->option_name)<span class="text-neutral-400">/ {{ $item->option_name }}</span>@endif
                        × {{ $item->quantity }}</span>
                    <span class="font-medium shrink-0">{{ number_format($item->subtotal) }}원</span>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between items-baseline mt-4">
            <span class="text-neutral-600">최종 결제금액</span>
            <span class="text-2xl font-extrabold text-brand-700">{{ number_format($order->total) }}원</span>
        </div>

        {{-- 토스 결제위젯 (카드/간편결제(카카오·네이버·토스페이)/계좌이체/가상계좌) --}}
        <div id="payment-method" class="mt-6"></div>
        <div id="agreement" class="mt-2"></div>

        <button id="payBtn" class="btn-brand w-full mt-6 py-4 text-base">{{ number_format($order->total) }}원 결제하기</button>
        <p id="payError" class="text-sm text-red-500 mt-3 hidden text-center"></p>

        @if (app()->environment('local'))
            <a href="{{ route('payment.dev', $order) }}"
               class="block mt-3 text-center text-sm text-neutral-500 underline hover:text-brand-700">
                [개발용] 테스트 결제 완료 처리
            </a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.tosspayments.com/v2/standard"></script>
<script>
(async function () {
    const clientKey = @json($clientKey);
    const errEl = document.getElementById('payError');
    const btn = document.getElementById('payBtn');
    const showError = (msg) => { errEl.textContent = msg; errEl.classList.remove('hidden'); };

    if (!clientKey) { showError('결제 설정(클라이언트 키)이 없습니다.'); return; }

    try {
        const tossPayments = TossPayments(clientKey);
        const widgets = tossPayments.widgets({ customerKey: @json($customerKey) });

        await widgets.setAmount({ currency: 'KRW', value: {{ $order->total }} });
        await Promise.all([
            widgets.renderPaymentMethods({ selector: '#payment-method', variantKey: 'DEFAULT' }),
            widgets.renderAgreement({ selector: '#agreement', variantKey: 'AGREEMENT' }),
        ]);

        btn.addEventListener('click', async function () {
            errEl.classList.add('hidden');
            try {
                await widgets.requestPayment({
                    orderId: @json($order->order_number),
                    orderName: @json($order->items->first()->product_name . ($order->items->count() > 1 ? ' 외 '.($order->items->count()-1).'건' : '')),
                    customerName: @json($order->orderer_name),
                    customerMobilePhone: @json(preg_replace('/[^0-9]/', '', $order->orderer_phone)),
                    successUrl: @json(route('payment.success')),
                    failUrl: @json(route('payment.fail')),
                });
            } catch (e) {
                if (e.code === 'USER_CANCEL') showError('결제를 취소하셨습니다.');
                else showError(e.message || '결제 중 오류가 발생했습니다.');
            }
        });
    } catch (e) {
        showError('결제창을 불러오지 못했습니다. 테스트/운영 키를 확인해 주세요.');
    }
})();
</script>
@endpush
