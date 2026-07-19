@extends('layouts.admin')

@section('title', '회원 상세')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.members.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">← 회원 목록</a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-neutral-200 p-6">
        <h2 class="font-bold text-neutral-800 mb-4">{{ $member->name }}
            @if ($member->is_admin)<span class="text-xs bg-gold-500 text-white px-2 py-0.5 rounded-full align-middle">관리자</span>@endif
        </h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-neutral-500">이메일</dt><dd>{{ $member->email }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">연락처</dt><dd>{{ $member->phone ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">적립금</dt><dd>{{ number_format($member->points) }}P</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">가입일</dt><dd>{{ $member->created_at->format('Y-m-d') }}</dd></div>
        </dl>
        @if ($member->address1)
            <div class="mt-4 pt-4 border-t border-neutral-100 text-sm text-neutral-600">
                <p class="text-neutral-500 mb-1">기본 배송지</p>
                <p>[{{ $member->postcode }}] {{ $member->address1 }} {{ $member->address2 }}</p>
            </div>
        @endif
    </div>

    {{-- 구매 대행자 설정 --}}
    <div class="bg-white rounded-xl border border-neutral-200 p-6">
        <h2 class="font-bold text-neutral-800 mb-4">구매 대행자 설정</h2>
        <form method="POST" action="{{ route('admin.members.update', $member) }}" class="space-y-4">
            @csrf @method('PUT')
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_agent" value="1" {{ $member->is_agent ? 'checked' : '' }}
                       class="rounded border-neutral-300 text-brand-600">
                <span class="font-medium text-neutral-700">구매 대행자로 지정</span>
            </label>
            <div>
                <label class="text-sm text-neutral-500">캐쉬백 비율 (%)</label>
                <input type="number" name="cashback_rate" value="{{ $member->cashback_rate }}" min="0" max="100"
                       class="mt-1 w-full rounded-md border-neutral-300 text-sm">
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-neutral-100">
                <span class="text-sm text-neutral-500">보유 캐쉬백</span>
                <span class="font-semibold text-brand-700">{{ number_format($member->cashback_balance) }}원</span>
            </div>
            <button class="btn-brand w-full py-2.5 text-sm">저장</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-neutral-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-neutral-100">
            <h2 class="font-bold text-neutral-800">주문 내역 ({{ $member->orders->count() }})</h2>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-neutral-100">
                @forelse ($member->orders as $order)
                    <tr class="hover:bg-neutral-50 cursor-pointer" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                        <td class="px-5 py-3 text-brand-700 font-medium">{{ $order->order_number }}</td>
                        <td class="px-5 py-3">{{ $order->status_label }}</td>
                        <td class="px-5 py-3 text-right font-medium">{{ number_format($order->total) }}원</td>
                        <td class="px-5 py-3 text-neutral-400">{{ $order->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td class="px-5 py-12 text-center text-neutral-400">주문 내역이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($member->is_agent)
    <div class="grid lg:grid-cols-2 gap-6 mt-6">
        {{-- 관리 구매자(소매처) --}}
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800">관리 구매자 ({{ $member->buyers->count() }})</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 text-neutral-500 text-left">
                    <tr><th class="px-5 py-2.5 font-medium">소매처</th><th class="px-5 py-2.5 font-medium">구매자</th><th class="px-5 py-2.5 font-medium">사업자번호</th><th class="px-5 py-2.5 font-medium">전화</th></tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($member->buyers as $b)
                        <tr>
                            <td class="px-5 py-3 font-medium text-neutral-800">{{ $b->store_name }}</td>
                            <td class="px-5 py-3 text-neutral-700">{{ $b->name }}</td>
                            <td class="px-5 py-3 text-neutral-500">{{ $b->biz_number ?: '-' }}</td>
                            <td class="px-5 py-3 text-neutral-500">{{ $b->phone ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-neutral-400">등록된 구매자가 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 대행 주문 --}}
        <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800">대행 주문 ({{ $agentOrders->count() }})</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 text-neutral-500 text-left">
                    <tr><th class="px-5 py-2.5 font-medium">주문번호</th><th class="px-5 py-2.5 font-medium">구매자</th><th class="px-5 py-2.5 font-medium text-right">결제</th><th class="px-5 py-2.5 font-medium text-right">캐쉬백</th></tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($agentOrders as $o)
                        <tr class="hover:bg-neutral-50 cursor-pointer" onclick="location.href='{{ route('admin.orders.show', $o) }}'">
                            <td class="px-5 py-3 text-brand-700 font-medium">{{ $o->order_number }}</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $o->buyer?->store_name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right">{{ number_format($o->total) }}원</td>
                            <td class="px-5 py-3 text-right font-semibold text-brand-700">{{ number_format($o->cashback) }}원</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-neutral-400">대행 주문이 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
