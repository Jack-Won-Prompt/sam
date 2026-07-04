@extends('layouts.shop')

@section('title', $notice->title . ' | 공지사항')

@section('content')
<div class="container-shop py-10 max-w-3xl">
    <h1 class="text-2xl font-bold text-neutral-800 mb-6">고객센터</h1>
    @include('partials.support-nav', ['current' => 'notices'])

    <div class="border-t-2 border-neutral-800 pt-5">
        <h2 class="text-xl font-bold text-neutral-900">{{ $notice->title }}</h2>
        <p class="text-sm text-neutral-400 mt-2">{{ $notice->created_at->format('Y-m-d') }} · 조회 {{ $notice->views }}</p>
        <div class="prose max-w-none mt-6 text-neutral-700 leading-relaxed whitespace-pre-line border-t border-neutral-100 pt-6">{!! nl2br(e($notice->content)) !!}</div>
    </div>

    <div class="mt-8">
        <a href="{{ route('support.notices') }}" class="btn-outline">목록으로</a>
    </div>
</div>
@endsection
