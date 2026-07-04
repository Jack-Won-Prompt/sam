@extends('layouts.shop')

@section('title', '주문조회 결과 | 강원 산양삼')

@section('content')
<div class="container-shop py-10 max-w-2xl">
    <nav class="text-sm text-neutral-500 mb-4">
        <a href="{{ route('order.track') }}" class="hover:text-brand-700">주문조회</a>
        <span class="mx-1.5">/</span>
        <span class="text-neutral-800">{{ $order->order_number }}</span>
    </nav>

    <h1 class="text-2xl font-bold text-neutral-800 mb-6">주문조회 결과</h1>

    @include('partials.order-detail')

    <div class="mt-6 text-center">
        <a href="{{ route('order.track') }}" class="btn-outline">다른 주문 조회</a>
    </div>
</div>
@endsection
