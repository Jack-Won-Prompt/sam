@extends('layouts.shop')

@section('title', $title . ' | 강원 산양삼')

@section('content')
<div class="container-shop py-8">
    <nav class="text-sm text-neutral-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-brand-700">홈</a>
        <span class="mx-1.5">/</span>
        <span class="text-neutral-800 font-medium">{{ $title }}</span>
    </nav>

    <div class="border-b border-neutral-200 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-neutral-800">{{ $title }}</h1>
        <p class="text-sm text-neutral-500 mt-1">총 {{ $products->total() }}개 상품</p>
    </div>

    @if ($products->isEmpty())
        <div class="py-24 text-center text-neutral-400">등록된 상품이 없습니다.</div>
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
