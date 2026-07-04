<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    public function index(Request $request)
    {
        $questions = ProductQuestion::with('product', 'user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.questions.index', compact('questions'));
    }

    public function answer(Request $request, ProductQuestion $question)
    {
        $data = $request->validate(['answer' => 'required|string|max:1000']);

        $question->update([
            'answer' => $data['answer'],
            'answered_at' => now(),
            'status' => 'answered',
        ]);

        return back()->with('success', '답변이 등록되었습니다.');
    }
}
