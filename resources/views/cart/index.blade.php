@extends('layouts.shop')

@section('title', '장바구니 | 강원 산양삼')

@section('content')
<div class="container-shop py-10">
    <h1 class="text-2xl font-bold text-neutral-800 mb-8">장바구니</h1>

    @if ($items->isEmpty())
        <div class="py-24 text-center">
            <p class="text-4xl mb-4">🛒</p>
            <p class="text-neutral-500">장바구니에 담긴 상품이 없습니다.</p>
            <a href="{{ route('home') }}" class="btn-brand mt-6">쇼핑 계속하기</a>
        </div>
    @else
    <div class="grid lg:grid-cols-3 gap-8">
        {{-- 목록 --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach ($items as $item)
                <div class="flex gap-4 border border-neutral-200 rounded-lg p-4 bg-white">
                    <a href="{{ route('product.show', $item->product) }}" class="shrink-0">
                        <x-thumb :product="$item->product" class="w-24 h-24 rounded-md" />
                    </a>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('product.show', $item->product) }}" class="font-semibold text-neutral-800 hover:text-brand-700 line-clamp-2">
                            {{ $item->product->name }}
                        </a>
                        @if ($item->option)
                            <p class="text-sm text-neutral-500 mt-1">옵션: {{ $item->option->name }}</p>
                        @endif
                        <p class="text-sm text-neutral-500 mt-0.5">{{ number_format($item->unit_price) }}원</p>

                        <div class="flex items-center justify-between mt-3">
                            <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-2">
                                @csrf @method('PUT')
                                <div class="flex items-center border border-neutral-300 rounded">
                                    <button name="quantity" value="{{ $item->quantity - 1 }}" class="w-8 h-8 text-neutral-500">−</button>
                                    <span class="w-10 text-center text-sm">{{ $item->quantity }}</span>
                                    <button name="quantity" value="{{ $item->quantity + 1 }}" class="w-8 h-8 text-neutral-500">＋</button>
                                </div>
                            </form>
                            <span class="font-bold text-neutral-900">{{ number_format($item->subtotal) }}원</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                        @csrf @method('DELETE')
                        <button class="text-neutral-400 hover:text-red-500 text-lg">✕</button>
                    </form>
                </div>
            @endforeach
        </div>

        {{-- 요약 --}}
        <div>
            <div class="border border-neutral-200 rounded-lg p-6 bg-white sticky top-28">
                <h2 class="font-bold text-neutral-800 mb-4">주문 요약</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-neutral-500">상품금액</dt><dd class="font-medium">{{ number_format($subtotal) }}원</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">배송비</dt>
                        <dd class="font-medium">{{ $shippingFee > 0 ? number_format($shippingFee).'원' : '무료' }}</dd>
                    </div>
                    @if ($subtotal < 50000)
                        <p class="text-xs text-brand-600">{{ number_format(50000 - $subtotal) }}원 더 담으면 무료배송!</p>
                    @endif
                </dl>
                <div class="flex justify-between items-baseline border-t border-neutral-200 mt-4 pt-4">
                    <span class="font-semibold">결제예상금액</span>
                    <span class="text-2xl font-extrabold text-brand-700">{{ number_format($total) }}원</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn-brand w-full mt-6">주문하기</a>
                <a href="{{ route('home') }}" class="block text-center text-sm text-neutral-500 mt-3 hover:text-brand-700">쇼핑 계속하기</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
