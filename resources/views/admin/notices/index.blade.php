@extends('layouts.admin')

@section('title', '공지사항 관리')

@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.notices.create') }}" class="btn-brand py-2 px-4 text-sm">+ 공지 등록</a>
</div>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">제목</th>
                <th class="px-4 py-3 font-medium text-center">고정</th>
                <th class="px-4 py-3 font-medium text-center">조회</th>
                <th class="px-4 py-3 font-medium">작성일</th>
                <th class="px-4 py-3 font-medium text-center">관리</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($notices as $notice)
                <tr class="hover:bg-neutral-50">
                    <td class="px-4 py-3 font-medium text-neutral-800">{{ $notice->title }}</td>
                    <td class="px-4 py-3 text-center">{{ $notice->is_pinned ? '📌' : '' }}</td>
                    <td class="px-4 py-3 text-center text-neutral-500">{{ $notice->views }}</td>
                    <td class="px-4 py-3 text-neutral-400">{{ $notice->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1 justify-center">
                            <a href="{{ route('admin.notices.edit', $notice) }}" class="text-brand-700 text-xs px-2 hover:underline">수정</a>
                            <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" onsubmit="return confirm('삭제할까요?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-xs px-2 hover:underline">삭제</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-neutral-400">공지가 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $notices->links() }}</div>
@endsection
