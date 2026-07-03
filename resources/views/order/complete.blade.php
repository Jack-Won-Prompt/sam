@extends('layouts.shop')

@section('title', '주문 완료 | 강원 산양삼')

@section('content')
<div class="container-shop py-16 max-w-2xl">
    <div class="text-center">
        <p class="text-5xl mb-4">🎉</p>
        <h1 class="text-2xl font-bold text-neutral-800">주문이 완료되었습니다</h1>
        <p class="text-neutral-500 mt-2">주문번호 <span class="font-semibold text-brand-700">{{ $order->order_number }}</span></p>
    </div>

    <div class="mt-10 border border-neutral-200 rounded-lg bg-white overflow-hidden">
        <div class="px-6 py-4 bg-brand-50 border-b border-brand-100 flex justify-between items-center">
            <span class="font-semibold text-brand-800">{{ $order->status_label }}</span>
            <span class="text-sm text-neutral-500">{{ $order->created_at->format('Y-m-d H:i') }}</span>
        </div>
        <div class="p-6">
            <div class="divide-y divide-neutral-100">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-neutral-700">{{ $item->product_name }}
                            @if ($item->option_name)<span class="text-neutral-400">/ {{ $item->option_name }}</span>@endif
                            × {{ $item->quantity }}</span>
                        <span class="font-medium">{{ number_format($item->subtotal) }}원</span>
                    </div>
                @endforeach
            </div>
            <dl class="mt-4 pt-4 border-t border-neutral-200 space-y-1.5 text-sm">
                <div class="flex justify-between"><dt class="text-neutral-500">상품금액</dt><dd>{{ number_format($order->subtotal) }}원</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">배송비</dt><dd>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee).'원' : '무료' }}</dd></div>
                <div class="flex justify-between text-base font-bold pt-2"><dt>결제금액</dt><dd class="text-brand-700">{{ number_format($order->total) }}원</dd></div>
            </dl>
        </div>
        <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-100 text-sm text-neutral-600">
            <p><b>받는분</b> {{ $order->receiver_name }} ({{ $order->receiver_phone }})</p>
            <p class="mt-1"><b>배송지</b> [{{ $order->postcode }}] {{ $order->address1 }} {{ $order->address2 }}</p>
        </div>
    </div>

    <div class="mt-8 flex gap-3 justify-center">
        @auth
            <a href="{{ route('order.index') }}" class="btn-outline">주문 내역 보기</a>
        @endauth
        <a href="{{ route('home') }}" class="btn-brand">쇼핑 계속하기</a>
    </div>
</div>
@endsection
