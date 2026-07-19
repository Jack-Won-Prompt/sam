@extends('layouts.shop')

@section('title', '적립금 | 마이페이지')

@section('content')
<div class="container-shop py-10 max-w-2xl">
    <div class="flex items-center gap-6 border-b border-neutral-200 pb-4 mb-8">
        <h1 class="text-2xl font-bold text-neutral-800">적립금</h1>
        <a href="{{ route('order.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">주문내역</a>
        <a href="{{ route('wishlist.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">찜한 상품</a>
        <a href="{{ route('agent.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">구매 대행자</a>
    </div>

    <div class="bg-brand-700 text-white rounded-xl p-6 mb-8 text-center">
        <p class="text-sm text-white/80">보유 적립금</p>
        <p class="text-3xl font-extrabold mt-1">{{ number_format(auth()->user()->points) }}<span class="text-lg">P</span></p>
    </div>

    <div class="bg-white border border-neutral-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-neutral-500 text-left">
                <tr><th class="px-5 py-3 font-medium">내용</th><th class="px-5 py-3 font-medium text-right">증감</th><th class="px-5 py-3 font-medium text-right">잔액</th><th class="px-5 py-3 font-medium">일시</th></tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($histories as $h)
                    <tr>
                        <td class="px-5 py-3 text-neutral-700">{{ $h->reason }}</td>
                        <td class="px-5 py-3 text-right font-semibold {{ $h->amount > 0 ? 'text-brand-600' : 'text-red-500' }}">
                            {{ $h->amount > 0 ? '+' : '' }}{{ number_format($h->amount) }}P
                        </td>
                        <td class="px-5 py-3 text-right text-neutral-500">{{ number_format($h->balance) }}P</td>
                        <td class="px-5 py-3 text-neutral-400">{{ $h->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-neutral-400">적립금 내역이 없습니다.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $histories->links() }}</div>
</div>
@endsection
