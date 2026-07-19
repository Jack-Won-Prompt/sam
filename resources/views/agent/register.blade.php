@extends('layouts.shop')

@section('title', '구매 대행자 | 마이페이지')

@section('content')
<div class="container-shop py-10 max-w-2xl">
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 border-b border-neutral-200 pb-4 mb-8">
        <h1 class="text-2xl font-bold text-neutral-800">구매 대행자</h1>
        <a href="{{ route('order.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">주문내역</a>
        <a href="{{ route('points.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">적립금</a>
    </div>

    <div class="bg-white border border-neutral-200 rounded-xl p-8 text-center">
        <div class="w-14 h-14 mx-auto rounded-full bg-brand-50 flex items-center justify-center mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4 0-7 2-7 5h14c0-3-3-5-7-5z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-neutral-900">구매 대행자로 등록하세요</h2>
        <p class="mt-3 text-sm text-neutral-600 leading-relaxed">
            여러 <strong>소매처(구매자)</strong>를 대신해 산양삼을 구매하고,
            매 주문마다 <strong class="text-brand-700">결제금액의 일정 비율을 캐쉬백</strong>으로 적립받는
            구매 대행 프로그램입니다.
        </p>
        <ul class="mt-5 text-sm text-neutral-600 text-left max-w-sm mx-auto space-y-2">
            <li class="flex gap-2"><span class="text-brand-600">✓</span> 한 명의 대행자가 여러 구매자(소매처)를 관리</li>
            <li class="flex gap-2"><span class="text-brand-600">✓</span> 주문 시 구매자를 지정하면 자동으로 캐쉬백 적립</li>
            <li class="flex gap-2"><span class="text-brand-600">✓</span> 기본 캐쉬백 5% (등급/협의에 따라 조정)</li>
        </ul>

        <form method="POST" action="{{ route('agent.register') }}" class="mt-7">
            @csrf
            <button class="btn-brand w-full sm:w-auto px-8">구매 대행자 등록하기</button>
        </form>
    </div>
</div>
@endsection
