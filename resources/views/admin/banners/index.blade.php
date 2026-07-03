@extends('layouts.admin')

@section('title', '배너 관리')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        @forelse ($banners as $banner)
            <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
                <div class="h-28 flex items-center px-6 text-white"
                     style="background: {{ $banner->image ? 'center/cover url('.asset('storage/'.$banner->image).')' : ($banner->bg_color ?? '#1f5fd0') }};">
                    <div>
                        <p class="text-lg font-bold drop-shadow">{{ $banner->title }}</p>
                        <p class="text-sm text-white/80">{{ $banner->subtitle }}</p>
                    </div>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-3">
                        @csrf @method('PUT')
                        <input name="title" value="{{ $banner->title }}" placeholder="제목" class="rounded-md border-neutral-300 text-sm">
                        <input name="subtitle" value="{{ $banner->subtitle }}" placeholder="부제목" class="rounded-md border-neutral-300 text-sm">
                        <input name="link" value="{{ $banner->link }}" placeholder="링크 URL" class="rounded-md border-neutral-300 text-sm">
                        <select name="position" class="rounded-md border-neutral-300 text-sm">
                            <option value="main_slider" @selected($banner->position==='main_slider')>메인 슬라이더</option>
                            <option value="main_sub" @selected($banner->position==='main_sub')>메인 서브</option>
                        </select>
                        <input name="bg_color" value="{{ $banner->bg_color }}" placeholder="#1f5c3d" class="rounded-md border-neutral-300 text-sm">
                        <input name="sort_order" type="number" value="{{ $banner->sort_order }}" class="rounded-md border-neutral-300 text-sm">
                        <input type="file" name="image" accept="image/*" class="text-sm sm:col-span-2">
                        <label class="text-sm flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($banner->is_active) class="rounded text-brand-600"> 노출</label>
                        <div class="sm:col-span-2 flex justify-end">
                            <button class="btn-outline py-1.5 px-4 text-sm">저장</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('삭제?')" class="text-right mt-1">
                        @csrf @method('DELETE')
                        <button class="text-red-500 text-sm px-2 py-1.5">삭제</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-neutral-400 py-12 text-center">등록된 배너가 없습니다.</p>
        @endforelse
    </div>

    <div>
        <div class="bg-white rounded-xl border border-neutral-200 p-6 sticky top-6">
            <h2 class="font-bold text-neutral-800 mb-4">배너 추가</h2>
            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input name="title" required placeholder="제목 *" class="w-full rounded-md border-neutral-300 text-sm">
                <input name="subtitle" placeholder="부제목" class="w-full rounded-md border-neutral-300 text-sm">
                <input name="link" placeholder="링크 URL (예: /category/gift)" class="w-full rounded-md border-neutral-300 text-sm">
                <select name="position" class="w-full rounded-md border-neutral-300 text-sm">
                    <option value="main_slider">메인 슬라이더</option>
                    <option value="main_sub">메인 서브</option>
                </select>
                <input name="bg_color" value="#1f5c3d" placeholder="배경색" class="w-full rounded-md border-neutral-300 text-sm">
                <input name="sort_order" type="number" value="0" class="w-full rounded-md border-neutral-300 text-sm">
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">이미지 (선택)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm">
                </div>
                <button class="btn-brand w-full py-2.5 text-sm">추가</button>
            </form>
        </div>
    </div>
</div>
@endsection
