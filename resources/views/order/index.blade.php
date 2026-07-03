@extends('layouts.shop')

@section('title', '주문 내역 | 마이페이지')

@section('content')
<div class="container-shop py-10">
    <div class="flex items-center gap-6 border-b border-neutral-200 pb-4 mb-8">
        <h1 class="text-2xl font-bold text-neutral-800">주문 내역</h1>
        <a href="{{ route('profile.edit') }}" class="text-sm text-neutral-500 hover:text-brand-700">회원정보 수정</a>
    </div>

    @if ($orders->isEmpty())
        <div class="py-24 text-center text-neutral-400">주문 내역이 없습니다.</div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('order.show', $order) }}" class="block border border-neutral-200 rounded-lg p-5 bg-white hover:border-brand-400 transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-neutral-800">{{ $order->order_number }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ in_array($order->status, ['paid','preparing','shipped']) ? 'bg-brand-50 text-brand-700' :
                                   ($order->status === 'delivered' ? 'bg-neutral-100 text-neutral-600' :
                                   ($order->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600')) }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <span class="text-sm text-neutral-400">{{ $order->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        @php $firstProduct = $order->items->first()?->product; @endphp
                        @if ($firstProduct)
                            <x-thumb :product="$firstProduct" class="w-14 h-14 rounded-md shrink-0" />
                        @else
                            <div class="w-14 h-14 rounded-md shrink-0 bg-neutral-100 flex items-center justify-center text-lg">🌿</div>
                        @endif
                        <p class="text-sm text-neutral-700 flex-1">
                            {{ $order->items->first()->product_name ?? '' }}
                            @if ($order->items->count() > 1) 외 {{ $order->items->count() - 1 }}건 @endif
                        </p>
                        <span class="font-bold text-neutral-900">{{ number_format($order->total) }}원</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
