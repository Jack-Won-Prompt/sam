<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request)
    {
        // 하위 카테고리 포함 상품 조회
        $categoryIds = collect([$category->id])
            ->merge($category->children->pluck('id'))
            ->all();

        $query = Product::active()->whereIn('category_id', $categoryIds);

        // 정렬
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('category.show', compact('category', 'products', 'sort'));
    }

    /** 베스트/신상품/특가 등 특수 목록 */
    public function collection(string $type, Request $request)
    {
        $titles = ['best' => '베스트 상품', 'new' => '신상품', 'sale' => '특가 상품'];
        abort_unless(isset($titles[$type]), 404);

        $query = Product::active();
        match ($type) {
            'best' => $query->where('is_best', true),
            'new' => $query->where('is_new', true),
            'sale' => $query->whereNotNull('sale_price'),
        };

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('category.collection', [
            'title' => $titles[$type],
            'type' => $type,
            'products' => $products,
        ]);
    }
}
