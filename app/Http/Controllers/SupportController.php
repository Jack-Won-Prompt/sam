<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Inquiry;
use App\Models\Notice;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /** 공지사항 목록 */
    public function notices()
    {
        $notices = Notice::orderByDesc('is_pinned')->latest()->paginate(10);

        return view('support.notices', compact('notices'));
    }

    public function notice(Notice $notice)
    {
        $notice->increment('views');

        return view('support.notice-show', compact('notice'));
    }

    /** FAQ */
    public function faq(Request $request)
    {
        $categories = Faq::where('is_active', true)->distinct()->pluck('category');
        $active = $request->get('cat');

        $faqs = Faq::where('is_active', true)
            ->when($active, fn ($q) => $q->where('category', $active))
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        return view('support.faq', compact('faqs', 'categories', 'active'));
    }

    /** 1:1 문의 목록 (회원 본인 것) */
    public function inquiries()
    {
        $inquiries = Inquiry::where('user_id', auth()->id())->latest()->paginate(10);

        return view('support.inquiries', compact('inquiries'));
    }

    public function inquiryCreate()
    {
        return view('support.inquiry-create');
    }

    public function inquiryStore(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:30',
            'title' => 'required|string|max:150',
            'content' => 'required|string|max:2000',
            'is_secret' => 'nullable|boolean',
            'email' => 'nullable|email',
        ]);

        Inquiry::create([
            'user_id' => auth()->id(),
            'name' => auth()->user()->name ?? '회원',
            'email' => $data['email'] ?? auth()->user()->email,
            'category' => $data['category'],
            'title' => $data['title'],
            'content' => $data['content'],
            'is_secret' => $request->boolean('is_secret'),
            'status' => 'pending',
        ]);

        return redirect()->route('support.inquiries')->with('success', '문의가 등록되었습니다. 빠르게 답변드리겠습니다.');
    }
}
