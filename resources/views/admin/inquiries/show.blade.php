@extends('layouts.admin')

@section('title', '문의 상세')

@section('content')
<div class="mb-4"><a href="{{ route('admin.inquiries.index') }}" class="text-sm text-neutral-500 hover:text-brand-700">← 문의 목록</a></div>

<div class="max-w-2xl space-y-6">
    <div class="bg-white rounded-xl border border-neutral-200 p-6">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs bg-neutral-100 px-2 py-0.5 rounded">{{ $inquiry->category }}</span>
            @if ($inquiry->is_secret)<span class="text-xs text-neutral-400">🔒 비밀글</span>@endif
        </div>
        <h2 class="text-lg font-bold text-neutral-800">{{ $inquiry->title }}</h2>
        <p class="text-sm text-neutral-400 mt-1">{{ $inquiry->name }} · {{ $inquiry->email }} · {{ $inquiry->created_at->format('Y-m-d H:i') }}</p>
        <div class="mt-4 pt-4 border-t border-neutral-100 text-sm text-neutral-700 whitespace-pre-line">{{ $inquiry->content }}</div>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200 p-6">
        <h3 class="font-bold text-neutral-800 mb-3">답변</h3>
        <form method="POST" action="{{ route('admin.inquiries.answer', $inquiry) }}">
            @csrf @method('PUT')
            <textarea name="answer" rows="5" required class="w-full rounded-md border-neutral-300 text-sm">{{ $inquiry->answer }}</textarea>
            <div class="flex justify-between items-center mt-3">
                @if ($inquiry->answered_at)<span class="text-xs text-neutral-400">{{ $inquiry->answered_at->format('Y-m-d H:i') }} 답변</span>@else<span></span>@endif
                <button class="btn-brand py-2 px-6 text-sm">{{ $inquiry->answer ? '답변 수정' : '답변 등록' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
