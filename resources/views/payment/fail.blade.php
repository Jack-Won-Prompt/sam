@extends('layouts.shop')

@section('title', '결제 실패 | 강원 산양삼')

@section('content')
<div class="container-shop py-16 max-w-lg text-center">
    <p class="text-5xl mb-4">😢</p>
    <h1 class="text-xl font-bold text-neutral-800">결제에 실패했습니다</h1>
    <p class="text-sm text-neutral-500 mt-2">{{ $message }}</p>
    @if ($orderId)
        <p class="text-xs text-neutral-400 mt-1">주문번호 {{ $orderId }}</p>
    @endif
    <div class="mt-8 flex gap-3 justify-center">
        <a href="{{ route('cart.index') }}" class="btn-outline">장바구니로</a>
        <a href="{{ route('home') }}" class="btn-brand">홈으로</a>
    </div>
</div>
@endsection
