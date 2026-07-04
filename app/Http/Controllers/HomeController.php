<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->where('position', 'main_slider')->orderBy('sort_order')->get();

        $bestProducts = Product::active()->where('is_best', true)->latest()->take(8)->get();
        $newProducts = Product::active()->where('is_new', true)->latest()->take(8)->get();

        // 진열용 대표 카테고리 (상위 카테고리)
        $mainCategories = Category::whereNull('parent_id')->where('is_active', true)
            ->orderBy('sort_order')->get();

        // 최근 본 상품 (세션)
        $recentIds = session('recently_viewed', []);
        $recentProducts = collect();
        if (! empty($recentIds)) {
            $recentProducts = Product::active()->whereIn('id', $recentIds)->get()
                ->sortBy(fn ($p) => array_search($p->id, $recentIds))->values();
        }

        return view('home', compact('banners', 'bestProducts', 'newProducts', 'mainCategories', 'recentProducts'));
    }
}
