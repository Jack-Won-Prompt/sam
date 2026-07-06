@extends('layouts.shop')

@section('title', '결제하기 | 강원 산양삼')

@section('content')
<div class="container-shop py-12 max-w-lg"
     x-data="{ method: '카드' }">
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

        {{-- 결제수단 선택 --}}
        <div class="mt-6">
            <p class="text-sm font-semibold text-neutral-700 mb-2">결제수단</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach (['카드' => '신용/체크카드', '계좌이체' => '계좌이체', '가상계좌' => '무통장(가상계좌)', '휴대폰' => '휴대폰'] as $val => $label)
                    <button type="button" @click="method = '{{ $val }}'"
                            :class="method === '{{ $val }}' ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-neutral-300 text-neutral-600'"
                            class="border rounded-lg py-2.5 text-sm font-medium transition">{{ $label }}</button>
                @endforeach
            </div>
            <p class="text-xs text-neutral-400 mt-2">💡 <b>카드</b> 선택 시 결제창에서 카카오페이·네이버페이·토스페이 등 <b>간편결제</b>도 이용할 수 있습니다.</p>
        </div>

        <button type="button" @click="window.tossPay(method)" class="btn-brand w-full mt-6 py-4 text-base">{{ number_format($order->total) }}원 결제하기</button>
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
<script src="https://js.tosspayments.com/v1/payment"></script>
<script>
    window.tossPay = function (method) {
        const clientKey = @json($clientKey);
        const errEl = document.getElementById('payError');
        const showError = (msg) => { errEl.textContent = msg; errEl.classList.remove('hidden'); };

        if (!clientKey) { showError('결제 설정(클라이언트 키)이 없습니다.'); return; }

        const tossPayments = TossPayments(clientKey);
        tossPayments.requestPayment(method || '카드', {
            amount: {{ $order->total }},
            orderId: @json($order->order_number),
            orderName: @json($order->items->first()->product_name . ($order->items->count() > 1 ? ' 외 '.($order->items->count()-1).'건' : '')),
            customerName: @json($order->orderer_name),
            customerMobilePhone: @json(preg_replace('/[^0-9]/', '', $order->orderer_phone)),
            successUrl: @json(route('payment.success')),
            failUrl: @json(route('payment.fail')),
        }).catch(function (error) {
            if (error.code === 'USER_CANCEL') showError('결제를 취소하셨습니다.');
            else showError(error.message || '결제 중 오류가 발생했습니다.');
        });
    };
</script>
@endpush
