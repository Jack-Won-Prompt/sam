<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data = $this->handleImage($request, $data);
        $data['is_active'] = $request->boolean('is_active', true);

        Banner::create($data);

        return back()->with('success', '배너가 추가되었습니다.');
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->validateData($request);
        $data = $this->handleImage($request, $data);
        $data['is_active'] = $request->boolean('is_active');

        $banner->update($data);

        return back()->with('success', '배너가 수정되었습니다.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return back()->with('success', '배너가 삭제되었습니다.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'subtitle' => 'nullable|string|max:200',
            'link' => 'nullable|string|max:255',
            'position' => 'required|in:main_slider,main_sub',
            'bg_color' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
        ]);
    }

    private function handleImage(Request $request, array $data): array
    {
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        } else {
            unset($data['image']);
        }

        return $data;
    }
}
