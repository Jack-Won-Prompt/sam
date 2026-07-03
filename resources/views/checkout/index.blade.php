@extends('layouts.shop')

@section('title', '주문/결제 | 강원 산양삼')

@section('content')
<div class="container-shop py-10">
    <h1 class="text-2xl font-bold text-neutral-800 mb-8">주문/결제</h1>

    <form method="POST" action="{{ route('checkout.store') }}" class="grid lg:grid-cols-3 gap-8">
        @csrf
        <div class="lg:col-span-2 space-y-8">
            {{-- 주문자 --}}
            <section class="border border-neutral-200 rounded-lg p-6 bg-white">
                <h2 class="font-bold text-neutral-800 mb-4">주문자 정보</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-neutral-600">이름 *</label>
                        <input name="orderer_name" value="{{ old('orderer_name', auth()->user()->name ?? '') }}" required class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                        @error('orderer_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-neutral-600">연락처 *</label>
                        <input name="orderer_phone" value="{{ old('orderer_phone', auth()->user()->phone ?? '') }}" required placeholder="010-0000-0000" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                        @error('orderer_phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">이메일</label>
                        <input name="orderer_email" value="{{ old('orderer_email', auth()->user()->email ?? '') }}" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                    </div>
                </div>
            </section>

            {{-- 배송지 --}}
            <section class="border border-neutral-200 rounded-lg p-6 bg-white"
                     x-data="{ same: false, copy() {
                        if (this.same) {
                            document.querySelector('[name=receiver_name]').value = document.querySelector('[name=orderer_name]').value;
                            document.querySelector('[name=receiver_phone]').value = document.querySelector('[name=orderer_phone]').value;
                        }
                     }}">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-neutral-800">배송지 정보</h2>
                    <label class="text-sm text-neutral-500 flex items-center gap-1.5">
                        <input type="checkbox" x-model="same" @change="copy" class="rounded border-neutral-300 text-brand-600"> 주문자와 동일
                    </label>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-neutral-600">받는분 *</label>
                        <input name="receiver_name" value="{{ old('receiver_name') }}" required class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                        @error('receiver_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-neutral-600">연락처 *</label>
                        <input name="receiver_phone" value="{{ old('receiver_phone') }}" required class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                        @error('receiver_phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-neutral-600">우편번호</label>
                        <input name="postcode" value="{{ old('postcode', auth()->user()->postcode ?? '') }}" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">주소 *</label>
                        <input name="address1" value="{{ old('address1', auth()->user()->address1 ?? '') }}" required class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                        @error('address1')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">상세주소</label>
                        <input name="address2" value="{{ old('address2', auth()->user()->address2 ?? '') }}" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">배송 메시지</label>
                        <input name="delivery_message" value="{{ old('delivery_message') }}" placeholder="예) 부재 시 문 앞에 놓아주세요" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                    </div>
                </div>
            </section>

            {{-- 주문 상품 --}}
            <section class="border border-neutral-200 rounded-lg p-6 bg-white">
                <h2 class="font-bold text-neutral-800 mb-4">주문 상품 ({{ $items->count() }})</h2>
                <div class="divide-y divide-neutral-100">
                    @foreach ($items as $item)
                        <div class="flex gap-3 py-3">
                            <x-thumb :product="$item->product" class="w-16 h-16 rounded-md shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-neutral-800 line-clamp-1">{{ $item->product->name }}</p>
                                @if ($item->option)<p class="text-xs text-neutral-500">{{ $item->option->name }}</p>@endif
                                <p class="text-xs text-neutral-500">{{ number_format($item->unit_price) }}원 × {{ $item->quantity }}</p>
                            </div>
                            <span class="text-sm font-semibold">{{ number_format($item->subtotal) }}원</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- 결제 요약 --}}
        <div>
            <div class="border border-neutral-200 rounded-lg p-6 bg-white sticky top-28">
                <h2 class="font-bold text-neutral-800 mb-4">결제 금액</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-neutral-500">상품금액</dt><dd class="font-medium">{{ number_format($subtotal) }}원</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">배송비</dt><dd class="font-medium">{{ $shippingFee > 0 ? number_format($shippingFee).'원' : '무료' }}</dd></div>
                </dl>
                <div class="flex justify-between items-baseline border-t border-neutral-200 mt-4 pt-4">
                    <span class="font-semibold">최종 결제금액</span>
                    <span class="text-2xl font-extrabold text-brand-700">{{ number_format($total) }}원</span>
                </div>
                <button class="btn-brand w-full mt-6">{{ number_format($total) }}원 결제하기</button>
                <p class="text-xs text-neutral-400 mt-3 text-center">주문 내용을 확인했으며 결제에 동의합니다.</p>
            </div>
        </div>
    </form>
</div>
@endsection
