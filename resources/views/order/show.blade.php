@extends('layouts.shop')

@section('title', '주문 상세 | 마이페이지')

@section('content')
<div class="container-shop py-10 max-w-2xl">
    <nav class="text-sm text-neutral-500 mb-4">
        <a href="{{ route('order.index') }}" class="hover:text-brand-700">주문 내역</a>
        <span class="mx-1.5">/</span>
        <span class="text-neutral-800">{{ $order->order_number }}</span>
    </nav>

    <h1 class="text-2xl font-bold text-neutral-800 mb-6">주문 상세</h1>

    <div class="border border-neutral-200 rounded-lg bg-white overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 flex justify-between items-center">
            <span class="font-semibold text-brand-800">{{ $order->status_label }}</span>
            <span class="text-sm text-neutral-500">{{ $order->created_at->format('Y-m-d H:i') }}</span>
        </div>
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
            <div class="flex justify-between text-base font-bold pt-2 border-t border-neutral-200"><dt>결제금액</dt><dd class="text-brand-700">{{ number_format($order->total) }}원</dd></div>
            @if ($order->payment_method)
                <div class="flex justify-between text-neutral-500"><dt>결제수단</dt><dd>{{ $order->payment_method }}</dd></div>
            @endif
        </dl>
        <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-100 text-sm text-neutral-600">
            <p><b>받는분</b> {{ $order->receiver_name }} ({{ $order->receiver_phone }})</p>
            <p class="mt-1"><b>배송지</b> [{{ $order->postcode }}] {{ $order->address1 }} {{ $order->address2 }}</p>
            @if ($order->delivery_message)<p class="mt-1"><b>메시지</b> {{ $order->delivery_message }}</p>@endif
        </div>
    </div>
</div>
@endsection
