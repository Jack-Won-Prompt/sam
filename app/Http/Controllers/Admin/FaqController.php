<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('category')->orderBy('sort_order')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:30',
            'question' => 'required|string|max:200',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_active'] = true;

        Faq::create($data);

        return back()->with('success', 'FAQ가 추가되었습니다.');
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'category' => 'required|string|max:30',
            'question' => 'required|string|max:200',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $faq->update($data);

        return back()->with('success', 'FAQ가 수정되었습니다.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ가 삭제되었습니다.');
    }
}
