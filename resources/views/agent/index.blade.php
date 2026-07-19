@extends('layouts.shop')

@section('title', '구매 대행자 | 마이페이지')

@section('content')
<div class="container-shop py-10 max-w-3xl">
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 border-b border-neutral-200 pb-4 mb-8">
        <h1 class="text-2xl font-bold text-neutral-800">구매 대행자</h1>
        <a href="{{ route('order.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">주문내역</a>
        <a href="{{ route('points.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">적립금</a>
    </div>

    {{-- 캐쉬백 잔액 --}}
    <div class="bg-brand-700 text-white rounded-xl p-6 mb-8 flex items-center justify-between">
        <div>
            <p class="text-sm text-white/80">보유 캐쉬백</p>
            <p class="text-3xl font-extrabold mt-1">{{ number_format($user->cashback_balance) }}<span class="text-lg">원</span></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-white/80">캐쉬백 비율</p>
            <p class="text-2xl font-bold mt-1">{{ $user->cashback_rate }}%</p>
        </div>
    </div>

    {{-- 구매자 추가 --}}
    <section class="bg-white border border-neutral-200 rounded-xl p-6 mb-8">
        <h2 class="font-bold text-neutral-800 mb-4">구매자(소매처) 추가</h2>
        <form method="POST" action="{{ route('agent.buyers.store') }}" class="grid sm:grid-cols-2 gap-3">
            @csrf
            <div>
                <label class="text-xs text-neutral-500">소매처 *</label>
                <input name="store_name" value="{{ old('store_name') }}" required placeholder="예) 청담 건강원" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                @error('store_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs text-neutral-500">구매자 이름 *</label>
                <input name="name" value="{{ old('name') }}" required placeholder="예) 홍길동" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs text-neutral-500">사업자번호</label>
                <input name="biz_number" value="{{ old('biz_number') }}" placeholder="000-00-00000" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
            </div>
            <div>
                <label class="text-xs text-neutral-500">전화번호</label>
                <input name="phone" value="{{ old('phone') }}" placeholder="010-0000-0000" class="mt-1 w-full rounded-md border-neutral-300 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button class="btn-brand w-full sm:w-auto px-6">구매자 추가</button>
            </div>
        </form>
    </section>

    {{-- 구매자 목록 --}}
    <section class="bg-white border border-neutral-200 rounded-xl overflow-hidden mb-8">
        <h2 class="font-bold text-neutral-800 px-6 pt-5 pb-3">내 구매자 <span class="text-neutral-400 text-sm">({{ $buyers->count() }}명)</span></h2>
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-neutral-500 text-left">
                <tr>
                    <th class="px-5 py-3 font-medium">소매처</th>
                    <th class="px-5 py-3 font-medium">구매자</th>
                    <th class="px-5 py-3 font-medium">사업자번호</th>
                    <th class="px-5 py-3 font-medium">전화번호</th>
                    <th class="px-5 py-3 font-medium text-right">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($buyers as $b)
                    <tr>
                        <td class="px-5 py-3 font-medium text-neutral-800">{{ $b->store_name }}</td>
                        <td class="px-5 py-3 text-neutral-700">{{ $b->name }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $b->biz_number ?: '-' }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $b->phone ?: '-' }}</td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('agent.buyers.destroy', $b) }}" onsubmit="return confirm('이 구매자를 삭제할까요?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">삭제</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-neutral-400">등록된 구매자가 없습니다. 위에서 추가해 주세요.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- 캐쉬백 내역 --}}
    <section class="bg-white border border-neutral-200 rounded-xl overflow-hidden">
        <h2 class="font-bold text-neutral-800 px-6 pt-5 pb-3">캐쉬백 내역</h2>
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-neutral-500 text-left">
                <tr>
                    <th class="px-5 py-3 font-medium">내용</th>
                    <th class="px-5 py-3 font-medium text-right">증감</th>
                    <th class="px-5 py-3 font-medium text-right">잔액</th>
                    <th class="px-5 py-3 font-medium">일시</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($history as $h)
                    <tr>
                        <td class="px-5 py-3 text-neutral-700">{{ $h->reason }}</td>
                        <td class="px-5 py-3 text-right font-semibold {{ $h->amount > 0 ? 'text-brand-600' : 'text-red-500' }}">
                            {{ $h->amount > 0 ? '+' : '' }}{{ number_format($h->amount) }}원
                        </td>
                        <td class="px-5 py-3 text-right text-neutral-500">{{ number_format($h->balance) }}원</td>
                        <td class="px-5 py-3 text-neutral-400">{{ $h->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-neutral-400">캐쉬백 내역이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
