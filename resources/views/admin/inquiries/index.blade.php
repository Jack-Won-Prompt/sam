@extends('layouts.admin')

@section('title', '1:1 문의')

@section('content')
<form method="GET" class="mb-5">
    <select name="status" onchange="this.form.submit()" class="rounded-md border-neutral-300 text-sm py-2">
        <option value="">전체</option>
        <option value="pending" @selected(request('status')==='pending')>미답변</option>
        <option value="answered" @selected(request('status')==='answered')>답변완료</option>
    </select>
</form>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">유형</th>
                <th class="px-4 py-3 font-medium">제목</th>
                <th class="px-4 py-3 font-medium">작성자</th>
                <th class="px-4 py-3 font-medium text-center">상태</th>
                <th class="px-4 py-3 font-medium">일시</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($inquiries as $inq)
                <tr class="hover:bg-neutral-50 cursor-pointer" onclick="location.href='{{ route('admin.inquiries.show', $inq) }}'">
                    <td class="px-4 py-3 text-neutral-500">{{ $inq->category }}</td>
                    <td class="px-4 py-3 font-medium text-neutral-800">{{ $inq->is_secret ? '🔒 ' : '' }}{{ $inq->title }}</td>
                    <td class="px-4 py-3">{{ $inq->name }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $inq->status === 'answered' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700' }}">{{ $inq->status === 'answered' ? '답변완료' : '미답변' }}</span>
                    </td>
                    <td class="px-4 py-3 text-neutral-400">{{ $inq->created_at->format('m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-neutral-400">문의가 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $inquiries->links() }}</div>
@endsection
