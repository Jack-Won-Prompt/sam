@extends('layouts.shop')

@section('title', '찜한 상품 | 마이페이지')

@section('content')
<div class="container-shop py-10">
    <div class="flex items-center gap-6 border-b border-neutral-200 pb-4 mb-8">
        <h1 class="text-2xl font-bold text-neutral-800">찜한 상품</h1>
        <a href="{{ route('order.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">주문내역</a>
        <a href="{{ route('points.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">적립금</a>
    </div>

    @if ($wishlists->isEmpty())
        <div class="py-24 text-center">
            <p class="text-4xl mb-4">♡</p>
            <p class="text-neutral-500">찜한 상품이 없습니다.</p>
            <a href="{{ route('home') }}" class="btn-brand mt-6">쇼핑하러 가기</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
            @foreach ($wishlists as $w)
                <div class="relative">
                    <x-product-card :product="$w->product" />
                    <form method="POST" action="{{ route('wishlist.toggle', $w->product) }}" class="absolute top-2 right-2">
                        @csrf
                        <button title="찜 해제" class="w-8 h-8 rounded-full bg-white/90 shadow flex items-center justify-center text-red-500 hover:bg-white">♥</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
