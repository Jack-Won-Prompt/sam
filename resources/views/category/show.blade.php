@extends('layouts.shop')

@section('title', $category->name . ' | 강원 산양삼')

@section('content')
<div class="container-shop py-8">
    {{-- 브레드크럼 --}}
    <nav class="text-sm text-neutral-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-brand-700">홈</a>
        <span class="mx-1.5">/</span>
        @if ($category->parent)
            <a href="{{ route('category.show', $category->parent) }}" class="hover:text-brand-700">{{ $category->parent->name }}</a>
            <span class="mx-1.5">/</span>
        @endif
        <span class="text-neutral-800 font-medium">{{ $category->name }}</span>
    </nav>

    <div class="flex items-center justify-between border-b border-neutral-200 pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800">{{ $category->name }}</h1>
            <p class="text-sm text-neutral-500 mt-1">총 {{ $products->total() }}개 상품</p>
        </div>
        <form method="GET" class="text-sm">
            <select name="sort" onchange="this.form.submit()" class="rounded-md border-neutral-300 text-sm py-2">
                <option value="latest" @selected($sort==='latest')>최신순</option>
                <option value="price_asc" @selected($sort==='price_asc')>낮은 가격순</option>
                <option value="price_desc" @selected($sort==='price_desc')>높은 가격순</option>
                <option value="name" @selected($sort==='name')>이름순</option>
            </select>
        </form>
    </div>

    {{-- 하위 카테고리 칩 --}}
    @if ($category->children->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach ($category->children as $child)
                <a href="{{ route('category.show', $child) }}"
                   class="px-4 py-1.5 rounded-full border border-neutral-300 text-sm text-neutral-600 hover:border-brand-500 hover:text-brand-700">
                    {{ $child->name }}
                </a>
            @endforeach
        </div>
    @endif

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
