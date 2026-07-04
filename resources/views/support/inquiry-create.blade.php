@extends('layouts.shop')

@section('title', '1:1 문의하기 | 강원 산양삼')

@section('content')
<div class="container-shop py-10 max-w-2xl">
    <h1 class="text-2xl font-bold text-neutral-800 mb-6">1:1 문의하기</h1>

    <form method="POST" action="{{ route('support.inquiry.store') }}" class="border border-neutral-200 rounded-xl bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-neutral-600 mb-1">문의 유형</label>
            <select name="category" class="w-full rounded-md border-neutral-300 text-sm">
                @foreach (['상품문의','배송문의','주문/결제','교환/반품','기타'] as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">제목 *</label>
            <input name="title" value="{{ old('title') }}" required class="w-full rounded-md border-neutral-300 text-sm">
            @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">내용 *</label>
            <textarea name="content" rows="6" required class="w-full rounded-md border-neutral-300 text-sm">{{ old('content') }}</textarea>
            @error('content')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-neutral-600 mb-1">답변 받을 이메일</label>
            <input name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full rounded-md border-neutral-300 text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm text-neutral-600">
            <input type="checkbox" name="is_secret" value="1" class="rounded text-brand-600"> 비밀글로 문의
        </label>
        <div class="flex gap-2">
            <button class="btn-brand py-2.5 px-6">문의 등록</button>
            <a href="{{ route('support.inquiries') }}" class="btn-outline py-2.5 px-6">취소</a>
        </div>
    </form>
</div>
@endsection
