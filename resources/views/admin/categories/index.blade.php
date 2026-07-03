@extends('layouts.admin')

@section('title', '카테고리 관리')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    {{-- 목록 --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach ($categories as $cat)
            <div class="bg-white rounded-xl border border-neutral-200 p-5">
                <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="flex items-center gap-2">
                    @csrf @method('PUT')
                    <span class="text-neutral-400 text-sm w-8">#{{ $cat->sort_order }}</span>
                    <input name="name" value="{{ $cat->name }}" class="rounded-md border-neutral-300 text-sm font-semibold flex-1">
                    <input name="sort_order" type="number" value="{{ $cat->sort_order }}" class="w-16 rounded-md border-neutral-300 text-sm">
                    <label class="text-xs flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($cat->is_active) class="rounded text-brand-600"> 노출</label>
                    <button class="btn-outline py-1.5 px-3 text-xs">저장</button>
                </form>
                @if ($cat->children->isNotEmpty())
                    <div class="mt-3 pl-10 space-y-2">
                        @foreach ($cat->children as $child)
                            <div class="flex items-center gap-2">
                                <span class="text-neutral-300">└</span>
                                <form method="POST" action="{{ route('admin.categories.update', $child) }}" class="flex items-center gap-2 flex-1">
                                    @csrf @method('PUT')
                                    <input name="name" value="{{ $child->name }}" class="rounded-md border-neutral-300 text-sm flex-1">
                                    <input name="sort_order" type="number" value="{{ $child->sort_order }}" class="w-16 rounded-md border-neutral-300 text-sm">
                                    <label class="text-xs flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($child->is_active) class="rounded text-brand-600"> 노출</label>
                                    <button class="btn-outline py-1.5 px-3 text-xs">저장</button>
                                </form>
                                <form method="POST" action="{{ route('admin.categories.destroy', $child) }}" onsubmit="return confirm('삭제하시겠습니까?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 text-xs px-1">삭제</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- 추가 --}}
    <div>
        <div class="bg-white rounded-xl border border-neutral-200 p-6 sticky top-6">
            <h2 class="font-bold text-neutral-800 mb-4">카테고리 추가</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">상위 카테고리</label>
                    <select name="parent_id" class="w-full rounded-md border-neutral-300 text-sm">
                        <option value="">최상위(대분류)</option>
                        @foreach ($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">이름 *</label>
                    <input name="name" required class="w-full rounded-md border-neutral-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">정렬 순서</label>
                    <input name="sort_order" type="number" value="0" class="w-full rounded-md border-neutral-300 text-sm">
                </div>
                <button class="btn-brand w-full py-2.5 text-sm">추가</button>
            </form>
        </div>
    </div>
</div>
@endsection
