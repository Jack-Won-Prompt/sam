<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::orderByDesc('is_pinned')->latest()->paginate(20);

        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(Request $request)
    {
        Notice::create($this->validated($request));

        return redirect()->route('admin.notices.index')->with('success', '공지가 등록되었습니다.');
    }

    public function edit(Notice $notice)
    {
        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $notice->update($this->validated($request));

        return redirect()->route('admin.notices.index')->with('success', '공지가 수정되었습니다.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();

        return back()->with('success', '공지가 삭제되었습니다.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
        ]);
        $data['is_pinned'] = $request->boolean('is_pinned');

        return $data;
    }
}
