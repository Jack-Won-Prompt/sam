@extends('layouts.admin')

@section('title', '쿠폰 관리')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-neutral-500">발급된 쿠폰 {{ $coupons->total() }}개</p>
    <a href="{{ route('admin.coupons.create') }}" class="btn-brand py-2 px-4 text-sm">+ 쿠폰 발급</a>
</div>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">코드</th>
                <th class="px-4 py-3 font-medium">이름</th>
                <th class="px-4 py-3 font-medium">할인</th>
                <th class="px-4 py-3 font-medium">최소주문</th>
                <th class="px-4 py-3 font-medium">기간</th>
                <th class="px-4 py-3 font-medium text-center">사용</th>
                <th class="px-4 py-3 font-medium text-center">상태</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($coupons as $c)
                <tr class="hover:bg-neutral-50">
                    <td class="px-4 py-3 font-mono font-semibold text-brand-700">{{ $c->code }}</td>
                    <td class="px-4 py-3">{{ $c->name }}</td>
                    <td class="px-4 py-3">{{ $c->discount_type === 'percent' ? $c->discount_value.'%' : number_format($c->discount_value).'원' }}</td>
                    <td class="px-4 py-3 text-neutral-500">{{ number_format($c->min_order_amount) }}원</td>
                    <td class="px-4 py-3 text-neutral-500 text-xs">{{ $c->starts_at?->format('Y-m-d') ?? '-' }} ~ {{ $c->expires_at?->format('Y-m-d') ?? '무제한' }}</td>
                    <td class="px-4 py-3 text-center">{{ $c->used_count }}{{ $c->usage_limit ? '/'.$c->usage_limit : '' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $c->is_active ? 'bg-brand-50 text-brand-700' : 'bg-neutral-100 text-neutral-400' }}">{{ $c->is_active ? '활성' : '비활성' }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" onsubmit="return confirm('삭제할까요?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">삭제</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-neutral-400">발급된 쿠폰이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $coupons->links() }}</div>
@endsection
