<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'content' => 'required|string|max:1000',
            'is_secret' => 'nullable|boolean',
        ]);

        ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'content' => $data['content'],
            'is_secret' => $request->boolean('is_secret'),
            'status' => 'pending',
        ]);

        return back()->with('success', '상품 문의가 등록되었습니다.')->withFragment('qna');
    }

    public function destroy(ProductQuestion $question)
    {
        abort_unless($question->user_id === auth()->id() || auth()->user()->is_admin, 403);
        $question->delete();

        return back()->with('success', '문의가 삭제되었습니다.')->withFragment('qna');
    }
}
