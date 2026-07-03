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

        return view('home', compact('banners', 'bestProducts', 'newProducts', 'mainCategories'));
    }
}
