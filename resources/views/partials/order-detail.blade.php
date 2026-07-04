{{-- 주문 상세 카드 (조회/완료/마이페이지 공용) : $order 필요 --}}
<div class="border border-neutral-200 rounded-lg bg-white overflow-hidden">
    <div class="px-6 py-4 border-b border-neutral-100 flex justify-between items-center">
        <span class="font-semibold text-brand-800">{{ $order->status_label }}</span>
        <span class="text-sm text-neutral-500">{{ $order->created_at->format('Y-m-d H:i') }}</span>
    </div>

    {{-- 배송 추적 --}}
    @if ($order->tracking_number)
        <div class="px-6 py-3 bg-brand-50 border-b border-brand-100 text-sm flex items-center justify-between">
            <span class="text-brand-800"><b>{{ $order->courier_name }}</b> {{ $order->tracking_number }}</span>
            @if ($order->tracking_url)
                <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="text-brand-700 underline font-medium">배송조회 →</a>
            @endif
        </div>
    @endif

    <div class="p-6 divide-y divide-neutral-100">
        @foreach ($order->items as $item)
            <div class="flex justify-between py-3 text-sm">
                <span class="text-neutral-700">{{ $item->product_name }}
                    @if ($item->option_name)<span class="text-neutral-400">/ {{ $item->option_name }}</span>@endif
                    × {{ $item->quantity }}</span>
                <span class="font-medium">{{ number_format($item->subtotal) }}원</span>
            </div>
        @endforeach
    </div>

    <dl class="px-6 pb-6 space-y-1.5 text-sm">
        <div class="flex justify-between"><dt class="text-neutral-500">상품금액</dt><dd>{{ number_format($order->subtotal) }}원</dd></div>
        <div class="flex justify-between"><dt class="text-neutral-500">배송비</dt><dd>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee).'원' : '무료' }}</dd></div>
        @if ($order->discount > 0)
            <div class="flex justify-between text-red-500"><dt>쿠폰할인</dt><dd>-{{ number_format($order->discount) }}원</dd></div>
        @endif
        @if ($order->points_used > 0)
            <div class="flex justify-between text-red-500"><dt>적립금 사용</dt><dd>-{{ number_format($order->points_used) }}원</dd></div>
        @endif
        <div class="flex justify-between text-base font-bold pt-2 border-t border-neutral-200"><dt>결제금액</dt><dd class="text-brand-700">{{ number_format($order->total) }}원</dd></div>
        @if ($order->payment_method)
            <div class="flex justify-between text-neutral-500"><dt>결제수단</dt><dd>{{ $order->payment_method }}</dd></div>
        @endif
    </dl>

    <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-100 text-sm text-neutral-600">
        <p><b>받는분</b> {{ $order->receiver_name }} ({{ $order->receiver_phone }})</p>
        <p class="mt-1"><b>배송지</b> [{{ $order->postcode }}] {{ $order->address1 }} {{ $order->address2 }}</p>
        @if ($order->delivery_message)<p class="mt-1"><b>메시지</b> {{ $order->delivery_message }}</p>@endif
        @if ($order->cancel_reason)<p class="mt-1 text-red-500"><b>취소사유</b> {{ $order->cancel_reason }}</p>@endif
    </div>
</div>
