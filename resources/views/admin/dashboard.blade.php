@extends('layouts.admin')

@section('title', '대시보드')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @php
        $cards = [
            ['오늘 주문', number_format($stats['orders_today']).'건', 'bg-brand-700'],
            ['전체 주문', number_format($stats['orders_total']).'건', 'bg-brand-600'],
            ['누적 매출', number_format($stats['sales_total']).'원', 'bg-gold-600'],
            ['처리 대기(결제완료)', number_format($stats['pending']).'건', 'bg-amber-600'],
            ['등록 상품', number_format($stats['products']).'개', 'bg-neutral-700'],
            ['가입 회원', number_format($stats['members']).'명', 'bg-neutral-600'],
        ];
    @endphp
    @foreach ($cards as [$label, $value, $bg])
        <div class="rounded-xl {{ $bg }} text-white p-5">
            <p class="text-sm text-white/80">{{ $label }}</p>
            <p class="text-2xl font-extrabold mt-2">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
        <h2 class="font-bold text-neutral-800">최근 주문</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-700 hover:underline">전체보기</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-5 py-3 font-medium">주문번호</th>
                <th class="px-5 py-3 font-medium">주문자</th>
                <th class="px-5 py-3 font-medium">상품</th>
                <th class="px-5 py-3 font-medium text-right">금액</th>
                <th class="px-5 py-3 font-medium">상태</th>
                <th class="px-5 py-3 font-medium">일시</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($recentOrders as $order)
                <tr class="hover:bg-neutral-50">
                    <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand-700 hover:underline">{{ $order->order_number }}</a></td>
                    <td class="px-5 py-3">{{ $order->orderer_name }}</td>
                    <td class="px-5 py-3 text-neutral-600">{{ Str::limit($order->items->first()?->product_name, 20) }}@if($order->items->count()>1) 외 {{ $order->items->count()-1 }}@endif</td>
                    <td class="px-5 py-3 text-right font-medium">{{ number_format($order->total) }}원</td>
                    <td class="px-5 py-3">{{ $order->status_label }}</td>
                    <td class="px-5 py-3 text-neutral-400">{{ $order->created_at->format('m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-neutral-400">주문이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
