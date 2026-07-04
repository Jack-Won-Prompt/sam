@extends('layouts.shop')

@section('title', '비회원 주문조회 | 강원 산양삼')

@section('content')
<div class="container-shop py-16 max-w-md">
    <h1 class="text-2xl font-bold text-neutral-800 text-center mb-2">주문조회</h1>
    <p class="text-sm text-neutral-500 text-center mb-8">주문번호와 주문자 연락처로 조회하실 수 있습니다.</p>

    <form method="POST" action="{{ route('order.track.submit') }}" class="border border-neutral-200 rounded-xl bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-neutral-600 mb-1">주문번호</label>
            <input name="order_number" value="{{ old('order_number') }}" required placeholder="예: 20260704-ABC123"
                   class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">주문자 연락처</label>
            <input name="orderer_phone" value="{{ old('orderer_phone') }}" required placeholder="010-0000-0000"
                   class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <button class="btn-brand w-full">주문 조회</button>
    </form>

    @auth
        <p class="text-center text-sm text-neutral-500 mt-4">
            회원이신가요? <a href="{{ route('order.index') }}" class="text-brand-700 underline">마이페이지 주문내역</a>
        </p>
    @endauth
</div>
@endsection
