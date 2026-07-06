@extends('layouts.admin')

@section('title', '방문 경로')

@section('content')
<div class="mb-4"><a href="{{ route('admin.analytics.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">← 방문 통계</a></div>

<div class="max-w-2xl">
    {{-- 방문자 요약 --}}
    <div class="bg-white rounded-xl border border-neutral-200 p-5 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-bold text-neutral-800">
                    @if ($user)<span class="text-brand-700">{{ $user->name }}</span> ({{ $user->email }})@else 비회원 방문자 @endif
                </p>
                <p class="text-xs text-neutral-400 mt-1">
                    IP {{ $views->first()->ip }} · {{ $views->first()->device === 'mobile' ? '📱 모바일' : '🖥 데스크톱' }} ·
                    총 {{ $views->count() }}페이지
                </p>
            </div>
            <div class="text-right text-xs text-neutral-400">
                <p>{{ $views->first()->created_at->format('Y-m-d H:i') }} 시작</p>
                <p>{{ $views->last()->created_at->format('H:i') }} 종료</p>
            </div>
        </div>
        @if ($views->first()->referer)
            <p class="mt-3 pt-3 border-t border-neutral-100 text-sm text-neutral-600">유입: {{ $views->first()->referer }}</p>
        @endif
    </div>

    {{-- 이동 경로 타임라인 --}}
    <div class="bg-white rounded-xl border border-neutral-200 p-6">
        <h2 class="font-bold text-neutral-800 mb-5">이동 경로</h2>
        <ol class="relative border-l-2 border-brand-100 ml-2">
            @foreach ($views as $i => $v)
                <li class="mb-5 ml-5 last:mb-0">
                    <span class="absolute -left-[9px] w-4 h-4 rounded-full bg-brand-600 border-2 border-white"></span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-neutral-400 w-12 shrink-0">{{ $v->created_at->format('H:i:s') }}</span>
                        <span class="font-medium text-neutral-800">{{ $v->label }}</span>
                        @if ($i === 0)<span class="text-[10px] bg-brand-50 text-brand-700 px-1.5 py-0.5 rounded">진입</span>@endif
                    </div>
                    <a href="{{ url($v->path) }}" target="_blank" class="text-xs text-neutral-400 hover:text-brand-700 ml-14 block truncate">{{ $v->path }}</a>
                </li>
            @endforeach
        </ol>
    </div>
</div>
@endsection
