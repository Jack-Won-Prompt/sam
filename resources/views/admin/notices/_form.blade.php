@php $notice = $notice ?? null; @endphp
<div class="max-w-2xl bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
    <div>
        <label class="block text-sm text-neutral-600 mb-1">제목 *</label>
        <input name="title" value="{{ old('title', $notice->title ?? '') }}" required class="w-full rounded-md border-neutral-300 text-sm">
    </div>
    <div>
        <label class="block text-sm text-neutral-600 mb-1">내용 *</label>
        <textarea name="content" rows="10" required class="w-full rounded-md border-neutral-300 text-sm">{{ old('content', $notice->content ?? '') }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $notice->is_pinned ?? false)) class="rounded text-brand-600"> 상단 고정
    </label>
    <div class="flex gap-2 pt-2">
        <button class="btn-brand py-2.5 px-6">{{ $notice ? '수정' : '등록' }}</button>
        <a href="{{ route('admin.notices.index') }}" class="btn-outline py-2.5 px-6">취소</a>
    </div>
</div>
