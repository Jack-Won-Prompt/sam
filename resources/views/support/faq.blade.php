@extends('layouts.shop')

@section('title', '자주 묻는 질문 | 강원 산양삼')

@section('content')
<div class="container-shop py-10 max-w-3xl">
    <h1 class="text-2xl font-bold text-neutral-800 mb-6">고객센터</h1>
    @include('partials.support-nav', ['current' => 'faq'])

    {{-- 카테고리 필터 --}}
    @if ($categories->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('support.faq') }}" class="px-4 py-1.5 rounded-full text-sm border {{ !$active ? 'border-brand-500 text-brand-700 bg-brand-50' : 'border-neutral-300 text-neutral-600' }}">전체</a>
            @foreach ($categories as $cat)
                <a href="{{ route('support.faq', ['cat' => $cat]) }}" class="px-4 py-1.5 rounded-full text-sm border {{ $active === $cat ? 'border-brand-500 text-brand-700 bg-brand-50' : 'border-neutral-300 text-neutral-600' }}">{{ $cat }}</a>
            @endforeach
        </div>
    @endif

    <div class="border-t border-neutral-200" x-data="{ open: null }">
        @forelse ($faqs as $faq)
            <div class="border-b border-neutral-100">
                <button @click="open === {{ $faq->id }} ? open = null : open = {{ $faq->id }}"
                        class="w-full flex items-center gap-3 py-4 text-left">
                    <span class="text-brand-700 font-bold">Q</span>
                    <span class="flex-1 font-medium text-neutral-800">{{ $faq->question }}</span>
                    <span class="text-neutral-400" x-text="open === {{ $faq->id }} ? '−' : '+'"></span>
                </button>
                <div x-show="open === {{ $faq->id }}" x-cloak class="pb-4 pl-7 text-sm text-neutral-600 leading-relaxed whitespace-pre-line bg-neutral-50 rounded-b-md">{{ $faq->answer }}</div>
            </div>
        @empty
            <p class="py-16 text-center text-neutral-400">등록된 FAQ가 없습니다.</p>
        @endforelse
    </div>
</div>
@endsection
