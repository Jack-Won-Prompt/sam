@extends('layouts.admin')

@section('title', '회원 관리')

@section('content')
<form method="GET" class="flex gap-2 mb-5">
    <input name="q" value="{{ request('q') }}" placeholder="이름/이메일/연락처 검색" class="rounded-md border-neutral-300 text-sm py-2 w-64">
    <button class="btn-outline py-2 px-4 text-sm">검색</button>
</form>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">이름</th>
                <th class="px-4 py-3 font-medium">이메일</th>
                <th class="px-4 py-3 font-medium">연락처</th>
                <th class="px-4 py-3 font-medium text-center">주문수</th>
                <th class="px-4 py-3 font-medium text-center">구분</th>
                <th class="px-4 py-3 font-medium text-center">대행자</th>
                <th class="px-4 py-3 font-medium text-right">캐쉬백</th>
                <th class="px-4 py-3 font-medium">가입일</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($members as $member)
                <tr class="hover:bg-neutral-50 cursor-pointer" onclick="location.href='{{ route('admin.members.show', $member) }}'">
                    <td class="px-4 py-3 font-medium">{{ $member->name }}</td>
                    <td class="px-4 py-3 text-neutral-600">{{ $member->email }}</td>
                    <td class="px-4 py-3 text-neutral-600">{{ $member->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">{{ $member->orders_count }}</td>
                    <td class="px-4 py-3 text-center">
                        @if ($member->is_admin)
                            <span class="text-xs bg-gold-500 text-white px-2 py-0.5 rounded-full">관리자</span>
                        @else
                            <span class="text-xs text-neutral-500">일반</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if ($member->is_agent)
                            <span class="text-xs bg-brand-600 text-white px-2 py-0.5 rounded-full">대행 {{ $member->cashback_rate }}%</span>
                        @else
                            <span class="text-xs text-neutral-300">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right {{ $member->cashback_balance > 0 ? 'font-semibold text-brand-700' : 'text-neutral-400' }}">
                        {{ number_format($member->cashback_balance) }}원
                    </td>
                    <td class="px-4 py-3 text-neutral-400">{{ $member->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-neutral-400">회원이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $members->links() }}</div>
@endsection
