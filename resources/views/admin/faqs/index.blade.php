@extends('layouts.admin')

@section('title', 'FAQ 관리')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-3">
        @forelse ($faqs as $faq)
            <div class="bg-white rounded-xl border border-neutral-200 p-4">
                <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <div class="flex gap-2">
                        <input name="category" value="{{ $faq->category }}" class="w-28 rounded-md border-neutral-300 text-sm">
                        <input name="question" value="{{ $faq->question }}" class="flex-1 rounded-md border-neutral-300 text-sm font-medium">
                        <input name="sort_order" type="number" value="{{ $faq->sort_order }}" class="w-16 rounded-md border-neutral-300 text-sm">
                    </div>
                    <textarea name="answer" rows="2" class="w-full rounded-md border-neutral-300 text-sm">{{ $faq->answer }}</textarea>
                    <div class="flex items-center justify-between">
                        <label class="text-xs flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($faq->is_active) class="rounded text-brand-600"> 노출</label>
                        <button class="btn-outline py-1.5 px-4 text-xs">저장</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('삭제할까요?')" class="text-right mt-1">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-500 hover:underline">삭제</button>
                </form>
            </div>
        @empty
            <p class="text-neutral-400 py-12 text-center">등록된 FAQ가 없습니다.</p>
        @endforelse
    </div>

    <div>
        <div class="bg-white rounded-xl border border-neutral-200 p-6 sticky top-6">
            <h2 class="font-bold text-neutral-800 mb-4">FAQ 추가</h2>
            <form method="POST" action="{{ route('admin.faqs.store') }}" class="space-y-3">
                @csrf
                <input name="category" required placeholder="분류 (배송/결제/상품...)" class="w-full rounded-md border-neutral-300 text-sm">
                <input name="question" required placeholder="질문" class="w-full rounded-md border-neutral-300 text-sm">
                <textarea name="answer" rows="4" required placeholder="답변" class="w-full rounded-md border-neutral-300 text-sm"></textarea>
                <input name="sort_order" type="number" value="0" class="w-full rounded-md border-neutral-300 text-sm">
                <button class="btn-brand w-full py-2.5 text-sm">추가</button>
            </form>
        </div>
    </div>
</div>
@endsection
