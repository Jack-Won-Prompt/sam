@extends('layouts.shop')

@section('title', '주문/결제 | 강원 산양삼')

@section('content')
<div class="container-shop py-10">
    <h1 class="text-2xl font-bold text-neutral-800 mb-8">주문/결제</h1>

    <form method="POST" action="{{ route('checkout.store') }}" class="grid lg:grid-cols-3 gap-8">
        @csrf
        <div class="lg:col-span-2 space-y-8">
            {{-- 구매 대행 (대행자 전용) --}}
            @if ($agentBuyers->isNotEmpty())
                <section class="border-2 border-brand-200 rounded-lg p-6 bg-brand-50"
                         x-data="{ buyer: '{{ old('buyer_id') }}', total: {{ $total }}, rate: {{ $cashbackRate }} }">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-brand-800">구매 대행 주문</h2>
                        <span class="text-xs font-semibold text-brand-700 bg-white border border-brand-200 rounded-full px-2.5 py-1">캐쉬백 {{ $cashbackRate }}%</span>
                    </div>
                    <label class="text-sm text-neutral-600">어느 구매자(소매처)를 위한 주문인가요?</label>
                    <select name="buyer_id" x-model="buyer" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                        <option value="">직접 구매 (대행 아님)</option>
                        @foreach ($agentBuyers as $b)
                            <option value="{{ $b->id }}">{{ $b->store_name }} · {{ $b->name }}{{ $b->phone ? ' ('.$b->phone.')' : '' }}</option>
                        @endforeach
                    </select>
                    <p x-show="buyer" x-cloak class="mt-3 text-sm text-brand-800">
                        이 주문으로 적립될 캐쉬백:
                        <strong x-text="Math.floor(total * rate / 100).toLocaleString() + '원'"></strong>
                        <span class="text-xs text-neutral-500">(최종 결제금액 기준, 결제완료 시 적립)</span>
                    </p>
                    <p class="mt-2 text-xs text-neutral-500">구매자 관리는 <a href="{{ route('agent.index') }}" class="text-brand-600 underline">구매 대행자 페이지</a>에서 할 수 있습니다.</p>
                </section>
            @endif

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
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">우편번호</label>
                        <div class="flex gap-2 mt-1">
                            <input id="postcode" name="postcode" value="{{ old('postcode', auth()->user()->postcode ?? '') }}" readonly placeholder="우편번호 찾기 클릭"
                                   class="w-40 rounded-md border-neutral-300 bg-neutral-50 text-sm">
                            <button type="button" onclick="openPostcode()" class="btn-outline py-2 px-4 text-sm whitespace-nowrap">우편번호 찾기</button>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">주소 *</label>
                        <input id="address1" name="address1" value="{{ old('address1', auth()->user()->address1 ?? '') }}" required readonly
                               class="mt-1 w-full rounded-md border-neutral-300 bg-neutral-50 text-sm">
                        @error('address1')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm text-neutral-600">상세주소</label>
                        <input id="address2" name="address2" value="{{ old('address2', auth()->user()->address2 ?? '') }}" placeholder="상세주소 입력 (동/호수 등)" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
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
        <div x-data="checkout({ subtotal: {{ $subtotal }}, shipping: {{ $shippingFee }}, userPoints: {{ $userPoints ?? 0 }}, couponUrl: '{{ route('checkout.coupon') }}' })">
            <div class="border border-neutral-200 rounded-lg p-6 bg-white sticky top-28">
                <h2 class="font-bold text-neutral-800 mb-4">결제 금액</h2>

                {{-- 쿠폰 --}}
                <div class="mb-3">
                    <label class="block text-xs text-neutral-500 mb-1">쿠폰</label>
                    <div class="flex gap-2">
                        <input type="text" name="coupon_code" x-model="couponCode" placeholder="쿠폰 코드"
                               class="flex-1 rounded-md border-neutral-300 text-sm py-2">
                        <button type="button" @click="applyCoupon" class="btn-outline py-2 px-3 text-sm whitespace-nowrap">적용</button>
                    </div>
                    <p class="text-xs mt-1" :class="couponOk ? 'text-brand-600' : 'text-red-500'" x-text="couponMsg"></p>
                </div>

                {{-- 적립금 --}}
                @auth
                <div class="mb-4">
                    <label class="block text-xs text-neutral-500 mb-1">적립금 사용 (보유 <span class="font-semibold text-brand-700">{{ number_format($userPoints ?? 0) }}P</span>)</label>
                    <div class="flex gap-2">
                        <input type="number" name="points_used" x-model.number="pointsUsed" min="0" max="{{ $userPoints ?? 0 }}" placeholder="0"
                               @input="clampPoints" class="flex-1 rounded-md border-neutral-300 text-sm py-2">
                        <button type="button" @click="useAllPoints" class="btn-outline py-2 px-3 text-sm whitespace-nowrap">전액</button>
                    </div>
                </div>
                @endauth

                <dl class="space-y-2 text-sm border-t border-neutral-100 pt-4">
                    <div class="flex justify-between"><dt class="text-neutral-500">상품금액</dt><dd class="font-medium">{{ number_format($subtotal) }}원</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">배송비</dt><dd class="font-medium">{{ $shippingFee > 0 ? number_format($shippingFee).'원' : '무료' }}</dd></div>
                    <div class="flex justify-between text-red-500" x-show="discount > 0"><dt>쿠폰할인</dt><dd>-<span x-text="won(discount)"></span>원</dd></div>
                    <div class="flex justify-between text-red-500" x-show="pointsUsed > 0"><dt>적립금</dt><dd>-<span x-text="won(pointsUsed)"></span>원</dd></div>
                </dl>
                <div class="flex justify-between items-baseline border-t border-neutral-200 mt-4 pt-4">
                    <span class="font-semibold">최종 결제금액</span>
                    <span class="text-2xl font-extrabold text-brand-700"><span x-text="won(total)"></span>원</span>
                </div>
                <button class="btn-brand w-full mt-6"><span x-text="won(total)"></span>원 결제하기</button>
                <p class="text-xs text-neutral-400 mt-3 text-center">주문 내용을 확인했으며 결제에 동의합니다.</p>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
function openPostcode() {
    new daum.Postcode({
        oncomplete: function (data) {
            // 도로명 주소 우선, 없으면 지번
            var addr = data.roadAddress || data.jibunAddress;
            document.getElementById('postcode').value = data.zonecode;
            document.getElementById('address1').value = addr;
            document.getElementById('address2').focus();
        }
    }).open();
}

function checkout(cfg) {
    return {
        ...cfg,
        couponCode: '',
        discount: 0,
        pointsUsed: 0,
        couponMsg: '',
        couponOk: false,
        get total() {
            return Math.max(0, this.subtotal + this.shipping - this.discount - (this.pointsUsed || 0));
        },
        won(n) { return Number(n || 0).toLocaleString('ko-KR'); },
        clampPoints() {
            let max = Math.min(this.userPoints, this.subtotal + this.shipping - this.discount);
            if (this.pointsUsed > max) this.pointsUsed = max;
            if (this.pointsUsed < 0 || isNaN(this.pointsUsed)) this.pointsUsed = 0;
        },
        useAllPoints() {
            this.pointsUsed = Math.min(this.userPoints, this.subtotal + this.shipping - this.discount);
        },
        async applyCoupon() {
            if (!this.couponCode.trim()) { this.couponMsg = '쿠폰 코드를 입력하세요.'; this.couponOk = false; return; }
            const token = document.querySelector('meta[name=csrf-token]').content;
            try {
                const res = await fetch(this.couponUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ code: this.couponCode }),
                });
                const data = await res.json();
                this.couponOk = data.ok;
                this.couponMsg = data.message;
                this.discount = data.ok ? data.discount : 0;
                this.clampPoints();
            } catch (e) {
                this.couponMsg = '쿠폰 확인 중 오류가 발생했습니다.'; this.couponOk = false;
            }
        },
    };
}
</script>
@endpush
