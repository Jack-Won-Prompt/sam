@extends('layouts.admin')

@section('title', '상품 문의')

@section('content')
<form method="GET" class="mb-5">
    <select name="status" onchange="this.form.submit()" class="rounded-md border-neutral-300 text-sm py-2">
        <option value="">전체</option>
        <option value="pending" @selected(request('status')==='pending')>미답변</option>
        <option value="answered" @selected(request('status')==='answered')>답변완료</option>
    </select>
</form>

<div class="space-y-3">
    @forelse ($questions as $q)
        <div class="bg-white rounded-xl border border-neutral-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $q->status==='answered' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700' }}">{{ $q->status==='answered' ? '답변완료' : '미답변' }}</span>
                    <a href="{{ $q->product ? route('product.show', $q->product) : '#' }}" target="_blank" class="text-brand-700 hover:underline">{{ $q->product?->name ?? '삭제된 상품' }}</a>
                    @if ($q->is_secret)<span class="text-xs text-neutral-400">🔒</span>@endif
                </div>
                <span class="text-xs text-neutral-400">{{ $q->user?->name }} · {{ $q->created_at->format('Y-m-d') }}</span>
            </div>
            <p class="text-sm text-neutral-700 whitespace-pre-line mb-3">{{ $q->content }}</p>
            <form method="POST" action="{{ route('admin.questions.answer', $q) }}">
                @csrf @method('PUT')
                <textarea name="answer" rows="2" required placeholder="답변 입력" class="w-full rounded-md border-neutral-300 text-sm">{{ $q->answer }}</textarea>
                <div class="text-right mt-2"><button class="btn-brand py-1.5 px-4 text-sm">{{ $q->answer ? '답변 수정' : '답변 등록' }}</button></div>
            </form>
        </div>
    @empty
        <p class="text-neutral-400 py-12 text-center">상품 문의가 없습니다.</p>
    @endforelse
</div>
<div class="mt-5">{{ $questions->links() }}</div>
@endsection
