<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');
        $product->load(['images', 'options', 'category', 'reviews.user', 'questions.user']);

        // 최근 본 상품 (세션, 최대 12개)
        $recent = collect(session('recently_viewed', []))
            ->reject(fn ($id) => $id == $product->id)
            ->prepend($product->id)
            ->take(12)->values()->all();
        session(['recently_viewed' => $recent]);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        // 찜 여부
        $isWished = auth()->check() && Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)->exists();

        // 리뷰 작성 가능 여부 (구매 이력)
        $canReview = auth()->check() && Order::where('user_id', auth()->id())
            ->whereIn('status', ['paid', 'preparing', 'shipped', 'delivered'])
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        return view('product.show', compact('product', 'related', 'isWished', 'canReview'));
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->get('q', ''));

        $products = Product::active()
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('name', 'like', "%{$keyword}%")
                        ->orWhere('short_description', 'like', "%{$keyword}%")
                        ->orWhere('origin', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('product.search', compact('products', 'keyword'));
    }
}
