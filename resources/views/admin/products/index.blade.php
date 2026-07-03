@extends('layouts.admin')

@section('title', '상품 관리')

@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="상품명 검색" class="rounded-md border-neutral-300 text-sm py-2 w-64">
        <button class="btn-outline py-2 px-4 text-sm">검색</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-brand py-2 px-4 text-sm">+ 상품 등록</a>
</div>

<div class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-neutral-50 text-neutral-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">상품</th>
                <th class="px-4 py-3 font-medium">카테고리</th>
                <th class="px-4 py-3 font-medium text-right">가격</th>
                <th class="px-4 py-3 font-medium text-right">재고</th>
                <th class="px-4 py-3 font-medium text-center">상태</th>
                <th class="px-4 py-3 font-medium text-center">관리</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse ($products as $product)
                <tr class="hover:bg-neutral-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <x-thumb :product="$product" class="w-12 h-12 rounded-md shrink-0" />
                            <div>
                                <p class="font-medium text-neutral-800 line-clamp-1">{{ $product->name }}</p>
                                <div class="flex gap-1 mt-0.5">
                                    @if($product->is_best)<span class="text-[10px] bg-gold-500 text-white px-1.5 rounded">BEST</span>@endif
                                    @if($product->is_new)<span class="text-[10px] bg-brand-600 text-white px-1.5 rounded">NEW</span>@endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-neutral-600">{{ $product->category?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        @if ($product->sale_price)
                            <span class="text-red-500 font-medium">{{ number_format($product->sale_price) }}</span>
                            <span class="text-neutral-400 line-through text-xs block">{{ number_format($product->price) }}</span>
                        @else
                            {{ number_format($product->price) }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right {{ $product->stock < 10 ? 'text-red-500 font-semibold' : '' }}">{{ number_format($product->stock) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $product->is_active ? 'bg-brand-50 text-brand-700' : 'bg-neutral-100 text-neutral-400' }}">
                            {{ $product->is_active ? '판매중' : '숨김' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1 justify-center">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-700 hover:underline text-xs px-2">수정</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs px-2">삭제</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-neutral-400">등록된 상품이 없습니다.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $products->links() }}</div>
@endsection
