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
@endsection
