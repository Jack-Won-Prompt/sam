@extends('layouts.admin')

@section('title', '주문 상세')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">← 주문 목록</a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- 주문 상품 --}}
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 flex justify-between items-center">
                <h2 class="font-bold text-neutral-800">주문 상품</h2>
                <span class="text-sm text-neutral-500">{{ $order->order_number }}</span>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-neutral-100">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="px-5 py-3">{{ $item->product_name }}
                                @if ($item->option_name)<span class="text-neutral-400 text-xs">/ {{ $item->option_name }}</span>@endif
                            </td>
                            <td class="px-5 py-3 text-right text-neutral-500">{{ number_format($item->price) }}원 × {{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-right font-medium w-32">{{ number_format($item->subtotal) }}원</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-neutral-50 text-sm">
                    <tr><td colspan="2" class="px-5 py-2 text-right text-neutral-500">상품금액</td><td class="px-5 py-2 text-right">{{ number_format($order->subtotal) }}원</td></tr>
                    <tr><td colspan="2" class="px-5 py-2 text-right text-neutral-500">배송비</td><td class="px-5 py-2 text-right">{{ number_format($order->shipping_fee) }}원</td></tr>
                    <tr><td colspan="2" class="px-5 py-3 text-right font-bold">결제금액</td><td class="px-5 py-3 text-right font-bold text-brand-700">{{ number_format($order->total) }}원</td></tr>
                </tfoot>
            </table>
        </div>

        {{-- 배송/주문자 정보 --}}
        <div class="grid sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-neutral-200 p-5 text-sm">
                <h3 class="font-bold text-neutral-800 mb-3">주문자</h3>
                <p>{{ $order->orderer_name }}</p>
                <p class="text-neutral-500">{{ $order->orderer_phone }}</p>
                <p class="text-neutral-500">{{ $order->orderer_email }}</p>
            </div>
            <div class="bg-white rounded-xl border border-neutral-200 p-5 text-sm">
                <h3 class="font-bold text-neutral-800 mb-3">배송지</h3>
                <p>{{ $order->receiver_name }} ({{ $order->receiver_phone }})</p>
                <p class="text-neutral-500 mt-1">[{{ $order->postcode }}] {{ $order->address1 }} {{ $order->address2 }}</p>
                @if ($order->delivery_message)<p class="text-neutral-500 mt-1">메시지: {{ $order->delivery_message }}</p>@endif
            </div>
        </div>
    </div>

    {{-- 상태/결제 --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-neutral-200 p-5">
            <h3 class="font-bold text-neutral-800 mb-3">주문 상태</h3>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex gap-2">
                @csrf @method('PUT')
                <select name="status" class="flex-1 rounded-md border-neutral-300 text-sm">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn-brand py-2 px-4 text-sm">변경</button>
            </form>
            <p class="text-xs text-neutral-400 mt-2">주문일시: {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>

        {{-- 발송 처리 (송장) --}}
        <div class="bg-white rounded-xl border border-neutral-200 p-5">
            <h3 class="font-bold text-neutral-800 mb-3">배송 / 송장</h3>
            @if ($order->tracking_number)
                <p class="text-sm mb-3"><b>{{ $order->courier_name }}</b> {{ $order->tracking_number }}
                    @if ($order->shipped_at)<span class="text-neutral-400 text-xs block">{{ $order->shipped_at->format('Y-m-d H:i') }} 발송</span>@endif
                </p>
            @endif
            <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="space-y-2">
                @csrf @method('PUT')
                <select name="courier" class="w-full rounded-md border-neutral-300 text-sm">
                    @foreach ($couriers as $key => $c)
                        <option value="{{ $key }}" @selected($order->courier===$key)>{{ $c['name'] }}</option>
                    @endforeach
                </select>
                <input name="tracking_number" value="{{ $order->tracking_number }}" placeholder="송장번호" class="w-full rounded-md border-neutral-300 text-sm">
                <button class="btn-brand w-full py-2 text-sm">{{ $order->tracking_number ? '송장 수정' : '발송 처리' }}</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-5 text-sm">
            <h3 class="font-bold text-neutral-800 mb-3">결제 정보</h3>
            @if ($order->payment)
                <dl class="space-y-1.5">
                    <div class="flex justify-between"><dt class="text-neutral-500">상태</dt><dd class="font-medium">{{ $order->payment->status }}</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">수단</dt><dd>{{ $order->payment->method ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">금액</dt><dd>{{ number_format($order->payment->amount) }}원</dd></div>
                    @if ($order->payment->approved_at)
                        <div class="flex justify-between"><dt class="text-neutral-500">승인일시</dt><dd>{{ $order->payment->approved_at->format('Y-m-d H:i') }}</dd></div>
                    @endif
                    @if ($order->payment->payment_key)
                        <div class="pt-1 text-xs text-neutral-400 break-all">key: {{ $order->payment->payment_key }}</div>
                    @endif
                </dl>
            @else
                <p class="text-neutral-400">결제 정보가 없습니다.</p>
            @endif
        </div>
    </div>
</div>
@endsection
