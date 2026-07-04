@extends('layouts.shop')

@section('title', '공지사항 | 강원 산양삼')

@section('content')
<div class="container-shop py-10 max-w-3xl">
    <h1 class="text-2xl font-bold text-neutral-800 mb-6">고객센터</h1>
    @include('partials.support-nav', ['current' => 'notices'])

    <div class="divide-y divide-neutral-100 border-t border-neutral-200">
        @forelse ($notices as $notice)
            <a href="{{ route('support.notice', $notice) }}" class="flex items-center gap-3 py-4 hover:bg-neutral-50 px-2">
                @if ($notice->is_pinned)<span class="text-xs bg-brand-700 text-white px-2 py-0.5 rounded">공지</span>@endif
                <span class="flex-1 text-neutral-800 font-medium">{{ $notice->title }}</span>
                <span class="text-xs text-neutral-400">{{ $notice->created_at->format('Y-m-d') }}</span>
            </a>
        @empty
            <p class="py-16 text-center text-neutral-400">등록된 공지사항이 없습니다.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $notices->links() }}</div>
</div>
@endsection
