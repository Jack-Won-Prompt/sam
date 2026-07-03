@extends('layouts.shop')

@section('title', "'".$keyword."' 검색결과 | 강원 산양삼")

@section('content')
<div class="container-shop py-8">
    <div class="border-b border-neutral-200 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-neutral-800">
            <span class="text-brand-700">'{{ $keyword }}'</span> 검색결과
        </h1>
        <p class="text-sm text-neutral-500 mt-1">총 {{ $products->total() }}개 상품</p>
    </div>

    @if ($products->isEmpty())
        <div class="py-24 text-center text-neutral-400">
            검색 결과가 없습니다.<br>
            <a href="{{ route('home') }}" class="text-brand-700 underline mt-2 inline-block">홈으로 돌아가기</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    @endif
</div>
@endsection
