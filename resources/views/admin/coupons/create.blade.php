@extends('layouts.admin')

@section('title', '쿠폰 발급')

@section('content')
<form method="POST" action="{{ route('admin.coupons.store') }}" class="max-w-2xl bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm text-neutral-600 mb-1">쿠폰 코드 *</label>
            <input name="code" value="{{ old('code') }}" required placeholder="WELCOME10" class="w-full rounded-md border-neutral-300 text-sm uppercase">
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">쿠폰명 *</label>
            <input name="name" value="{{ old('name') }}" required placeholder="신규가입 축하 쿠폰" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm text-neutral-600 mb-1">할인 유형 *</label>
            <select name="discount_type" class="w-full rounded-md border-neutral-300 text-sm">
                <option value="fixed">정액(원)</option>
                <option value="percent">정률(%)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">할인 값 *</label>
            <input name="discount_value" type="number" value="{{ old('discount_value') }}" required placeholder="10000 또는 10" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">최대 할인액(정률시)</label>
            <input name="max_discount" type="number" value="{{ old('max_discount') }}" placeholder="선택" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm text-neutral-600 mb-1">최소 주문금액</label>
            <input name="min_order_amount" type="number" value="{{ old('min_order_amount', 0) }}" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">전체 사용 제한(수량)</label>
            <input name="usage_limit" type="number" value="{{ old('usage_limit') }}" placeholder="무제한" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <div></div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm text-neutral-600 mb-1">시작일</label>
            <input name="starts_at" type="date" value="{{ old('starts_at') }}" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">만료일</label>
            <input name="expires_at" type="date" value="{{ old('expires_at') }}" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600"> 활성화
    </label>

    <div class="flex gap-2 pt-2">
        <button class="btn-brand py-2.5 px-6">발급</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn-outline py-2.5 px-6">취소</a>
    </div>
</form>
@endsection
