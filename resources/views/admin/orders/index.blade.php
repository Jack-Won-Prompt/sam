@extends('layouts.admin')

@section('title', '주문 관리')

@section('content')
<form method="GET" class="flex flex-wrap gap-2 mb-5">
    <select name="status" onchange="this.form.submit()" class="rounded-md border-neutral-300 text-sm py-2">
        <option value="">전체 상태</option>
        @foreach ($statuses as $key => $label)
            <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
        @endforeach
    </select>
    <input name="q" value="{{ request('q') }}" placeholder="주문번호/주문자/받는분" class="rounded-md border-neutral-300 text-sm py-2 w-64">
    <button class="btn-outline py-2 px-4 text-sm">검색</button>
</form>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">주문번호</th>
                <th class="px-4 py-3 font-medium">주문자</th>
                <th class="px-4 py-3 font-medium">상품</th>
                <th class="px-4 py-3 font-medium text-right">금액</th>
                <th class="px-4 py-3 font-medium">상태</th>
                <th class="px-4 py-3 font-medium">일시</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($orders as $order)
                <tr class="hover:bg-neutral-50 cursor-pointer" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                    <td class="px-4 py-3 font-medium text-brand-700">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->orderer_name }}<br><span class="text-xs text-neutral-400">{{ $order->orderer_phone }}</span></td>
                    <td class="px-4 py-3 text-neutral-600">{{ Str::limit($order->items->first()?->product_name, 18) }}@if($order->items->count()>1) 외 {{ $order->items->count()-1 }}@endif</td>
                    <td class="px-4 py-3 text-right font-medium">{{ number_format($order->total) }}원</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ in_array($order->status, ['paid','preparing','shipped']) ? 'bg-brand-50 text-brand-700' :
                               ($order->status==='delivered' ? 'bg-neutral-100 text-neutral-600' :
                               ($order->status==='pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600')) }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-neutral-400">{{ $order->created_at->format('m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-neutral-400">주문이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $orders->links() }}</div>
@endsection
