<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $inquiries = Inquiry::with('user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function answer(Request $request, Inquiry $inquiry)
    {
        $data = $request->validate(['answer' => 'required|string|max:2000']);

        $inquiry->update([
            'answer' => $data['answer'],
            'answered_at' => now(),
            'status' => 'answered',
        ]);

        return back()->with('success', '답변이 등록되었습니다.');
    }
}
