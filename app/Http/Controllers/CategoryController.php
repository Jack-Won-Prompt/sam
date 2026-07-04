<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** 가격대 필터 옵션 */
    public const PRICE_RANGES = [
        '0-50000' => '5만원 이하',
        '50000-100000' => '5만~10만원',
        '100000-200000' => '10만~20만원',
        '200000-' => '20만원 이상',
    ];

    /** 연근·가격대 필터 적용 */
    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('years')) {
            $query->whereIn('cultivation_years', (array) $request->get('years'));
        }
        if ($request->filled('price') && isset(self::PRICE_RANGES[$request->get('price')])) {
            [$min, $max] = array_pad(explode('-', $request->get('price')), 2, '');
            $query->where(function ($q) use ($min, $max) {
                if ($min !== '') {
                    $q->whereRaw('COALESCE(sale_price, price) >= ?', [(int) $min]);
                }
                if ($max !== '') {
                    $q->whereRaw('COALESCE(sale_price, price) <= ?', [(int) $max]);
                }
            });
        }
    }

    public function show(Category $category, Request $request)
    {
        // 하위 카테고리 포함 상품 조회
        $categoryIds = collect([$category->id])
            ->merge($category->children->pluck('id'))
            ->all();

        $query = Product::active()->whereIn('category_id', $categoryIds);

        $this->applyFilters($query, $request);

        // 정렬
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // 필터 옵션 (해당 카테고리 상품의 연근 목록)
        $yearOptions = Product::active()->whereIn('category_id', $categoryIds)
            ->whereNotNull('cultivation_years')->where('cultivation_years', '!=', '-')
            ->distinct()->orderBy('cultivation_years')->pluck('cultivation_years');

        return view('category.show', compact('category', 'products', 'sort', 'yearOptions'));
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
