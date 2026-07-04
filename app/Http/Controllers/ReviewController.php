<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:4096',
        ]);

        // 구매 이력 확인 (결제 이후 상태의 주문에 해당 상품 포함)
        $purchased = Order::where('user_id', auth()->id())
            ->whereIn('status', ['paid', 'preparing', 'shipped', 'delivered'])
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        if (! $purchased) {
            return back()->with('error', '구매하신 상품에만 리뷰를 작성할 수 있습니다.');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('reviews', 'public');
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'rating' => $data['rating'],
            'content' => $data['content'],
            'image' => $data['image'] ?? null,
        ]);

        return back()->with('success', '리뷰가 등록되었습니다. 감사합니다!');
    }

    public function destroy(Review $review)
    {
        abort_unless($review->user_id === auth()->id() || auth()->user()->is_admin, 403);
        $review->delete();

        return back()->with('success', '리뷰가 삭제되었습니다.');
    }
}
