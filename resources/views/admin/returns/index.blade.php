@extends('layouts.admin')

@section('title', '교환/반품 관리')

@section('content')
<form method="GET" class="mb-5">
    <select name="status" onchange="this.form.submit()" class="rounded-md border-neutral-300 text-sm py-2">
        <option value="">전체 상태</option>
        @foreach ($statuses as $k => $v)
            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
        @endforeach
    </select>
</form>

<div class="space-y-3">
    @forelse ($returns as $ret)
        <div class="bg-white rounded-xl border border-neutral-200 p-5">
            <div class="flex items-center justify-between mb-2 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded {{ $ret->type==='return' ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600' }}">{{ $ret->type_label }}</span>
                    <a href="{{ $ret->order ? route('admin.orders.show', $ret->order) : '#' }}" class="text-brand-700 hover:underline">{{ $ret->order?->order_number }}</a>
                    <span class="text-neutral-500">· {{ $ret->reason }}</span>
                </div>
                <span class="text-xs text-neutral-400">{{ $ret->user?->name }} · {{ $ret->created_at->format('Y-m-d') }}</span>
            </div>
            @if ($ret->detail)<p class="text-sm text-neutral-600 whitespace-pre-line mb-3">{{ $ret->detail }}</p>@endif
            <form method="POST" action="{{ route('admin.returns.status', $ret) }}" class="flex flex-wrap items-center gap-2">
                @csrf @method('PUT')
                <select name="status" class="rounded-md border-neutral-300 text-sm">
                    @foreach ($statuses as $k => $v)
                        <option value="{{ $k }}" @selected($ret->status===$k)>{{ $v }}</option>
                    @endforeach
                </select>
                <input name="admin_memo" value="{{ $ret->admin_memo }}" placeholder="처리 메모" class="flex-1 min-w-40 rounded-md border-neutral-300 text-sm">
                <button class="btn-brand py-1.5 px-4 text-sm">저장</button>
            </form>
        </div>
    @empty
        <p class="text-neutral-400 py-12 text-center">교환/반품 신청이 없습니다.</p>
    @endforelse
</div>
<div class="mt-5">{{ $returns->links() }}</div>
@endsection
