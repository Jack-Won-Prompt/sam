@extends('layouts.shop')

@section('title', '주문 상세 | 마이페이지')

@section('content')
<div class="container-shop py-10 max-w-2xl">
    <nav class="text-sm text-neutral-500 mb-4">
        <a href="{{ route('order.index') }}" class="hover:text-brand-700">주문 내역</a>
        <span class="mx-1.5">/</span>
        <span class="text-neutral-800">{{ $order->order_number }}</span>
    </nav>

    <h1 class="text-2xl font-bold text-neutral-800 mb-6">주문 상세</h1>

    @include('partials.order-detail')

    {{-- 교환/반품 신청 이력 --}}
    @if ($order->returns->isNotEmpty())
        <div class="mt-6 space-y-2">
            @foreach ($order->returns as $ret)
                <div class="flex items-center justify-between bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-3 text-sm">
                    <span><b>{{ $ret->type_label }}</b> 신청 · {{ $ret->reason }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-brand-50 text-brand-700">{{ $ret->status_label }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 교환/반품 신청 --}}
    @if ($order->isReturnable())
        <div class="mt-4 text-right" x-data="{ open: false }">
            <button @click="open = true" class="btn-outline text-sm">교환/반품 신청</button>
            <div x-show="open" x-cloak class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display:none">
                <div @click.outside="open = false" class="bg-white rounded-xl p-6 max-w-sm w-full text-left">
                    <h3 class="font-bold text-neutral-800 mb-3">교환/반품 신청</h3>
                    <form method="POST" action="{{ route('order.return', $order) }}" class="space-y-3">
                        @csrf
                        <select name="type" class="w-full rounded-md border-neutral-300 text-sm">
                            <option value="return">반품</option>
                            <option value="exchange">교환</option>
                        </select>
                        <select name="reason" class="w-full rounded-md border-neutral-300 text-sm">
                            <option>상품 불량/파손</option>
                            <option>오배송</option>
                            <option>신선도 문제</option>
                            <option>단순 변심</option>
                            <option>기타</option>
                        </select>
                        <textarea name="detail" rows="3" placeholder="상세 사유 (선택)" class="w-full rounded-md border-neutral-300 text-sm"></textarea>
                        <div class="flex gap-2 justify-end">
                            <button type="button" @click="open = false" class="btn-outline py-2 px-4 text-sm">닫기</button>
                            <button class="btn-brand py-2 px-4 text-sm">신청</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($order->isCancellable())
        <div class="mt-6 text-right"
             x-data="{ open: false }">
            <button @click="open = true" class="btn-outline text-sm text-red-600 border-red-200 hover:border-red-400">주문 취소</button>

            <div x-show="open" x-cloak class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display:none">
                <div @click.outside="open = false" class="bg-white rounded-xl p-6 max-w-sm w-full text-left">
                    <h3 class="font-bold text-neutral-800 mb-3">주문을 취소하시겠습니까?</h3>
                    <form method="POST" action="{{ route('order.cancel', $order) }}">
                        @csrf
                        <textarea name="reason" rows="2" placeholder="취소 사유 (선택)" class="w-full rounded-md border-neutral-300 text-sm mb-3"></textarea>
                        <div class="flex gap-2 justify-end">
                            <button type="button" @click="open = false" class="btn-outline py-2 px-4 text-sm">닫기</button>
                            <button class="btn-brand py-2 px-4 text-sm bg-red-600 hover:bg-red-700">취소하기</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
