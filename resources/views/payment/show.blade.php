@extends('layouts.shop')

@section('title', '결제하기 | 강원 산양삼')

@section('content')
<div class="container-shop py-12 max-w-lg">
    <div class="border border-neutral-200 rounded-xl p-8 bg-white text-center">
        <p class="text-3xl mb-3">💳</p>
        <h1 class="text-xl font-bold text-neutral-800">결제 진행</h1>
        <p class="text-sm text-neutral-500 mt-1">주문번호 {{ $order->order_number }}</p>

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

        <button id="payBtn" class="btn-brand w-full mt-8 py-4 text-base">{{ number_format($order->total) }}원 결제하기</button>
        <p id="payError" class="text-sm text-red-500 mt-3 hidden"></p>

        @if (app()->environment('local'))
            <a href="{{ route('payment.dev', $order) }}"
               class="block mt-3 text-sm text-neutral-500 underline hover:text-brand-700">
                [개발용] 테스트 결제 완료 처리 (실 결제키 없이 주문 완료)
            </a>
        @endif

        <div class="mt-6 text-xs text-neutral-400 leading-relaxed bg-neutral-50 rounded-md p-3">
            토스페이먼츠 <b>테스트 결제</b>입니다. 카드 결제 시 실제 청구되지 않으며,<br>
            테스트 카드번호로 승인 테스트가 가능합니다.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.tosspayments.com/v1/payment"></script>
<script>
    document.getElementById('payBtn').addEventListener('click', function () {
        const clientKey = @json($clientKey);
        if (!clientKey) {
            const el = document.getElementById('payError');
            el.textContent = '결제 설정(클라이언트 키)이 없습니다. 관리자에게 문의하세요.';
            el.classList.remove('hidden');
            return;
        }
        const tossPayments = TossPayments(clientKey);
        tossPayments.requestPayment('카드', {
            amount: {{ $order->total }},
            orderId: @json($order->order_number),
            orderName: @json($order->items->first()->product_name . ($order->items->count() > 1 ? ' 외 '.($order->items->count()-1).'건' : '')),
            customerName: @json($order->orderer_name),
            successUrl: @json(route('payment.success')),
            failUrl: @json(route('payment.fail')),
        }).catch(function (error) {
            const el = document.getElementById('payError');
            if (error.code === 'USER_CANCEL') {
                el.textContent = '결제를 취소하셨습니다.';
            } else {
                el.textContent = error.message || '결제 중 오류가 발생했습니다.';
            }
            el.classList.remove('hidden');
        });
    });
</script>
@endpush
