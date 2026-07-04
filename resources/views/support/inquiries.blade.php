@extends('layouts.shop')

@section('title', '1:1 문의 | 강원 산양삼')

@section('content')
<div class="container-shop py-10 max-w-3xl">
    <h1 class="text-2xl font-bold text-neutral-800 mb-6">고객센터</h1>
    @include('partials.support-nav', ['current' => 'inquiries'])

    <div class="flex justify-end mb-4">
        <a href="{{ route('support.inquiry.create') }}" class="btn-brand py-2 px-4 text-sm">문의하기</a>
    </div>

    <div class="border-t border-neutral-200 divide-y divide-neutral-100" x-data="{ open: null }">
        @forelse ($inquiries as $inq)
            <div>
                <button @click="open === {{ $inq->id }} ? open = null : open = {{ $inq->id }}" class="w-full flex items-center gap-3 py-4 text-left">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $inq->status === 'answered' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $inq->status === 'answered' ? '답변완료' : '접수' }}
                    </span>
                    <span class="flex-1 font-medium text-neutral-800">{{ $inq->is_secret ? '🔒 ' : '' }}{{ $inq->title }}</span>
                    <span class="text-xs text-neutral-400">{{ $inq->created_at->format('Y-m-d') }}</span>
                </button>
                <div x-show="open === {{ $inq->id }}" x-cloak class="pb-5 space-y-3">
                    <div class="bg-neutral-50 rounded-lg p-4 text-sm text-neutral-700 whitespace-pre-line">{{ $inq->content }}</div>
                    @if ($inq->answer)
                        <div class="bg-brand-50 rounded-lg p-4 text-sm text-neutral-700">
                            <p class="font-semibold text-brand-700 mb-1">답변</p>
                            <p class="whitespace-pre-line">{{ $inq->answer }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="py-16 text-center text-neutral-400">등록한 문의가 없습니다.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $inquiries->links() }}</div>
</div>
@endsection
