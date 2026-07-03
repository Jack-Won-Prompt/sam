<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        $parents = Category::whereNull('parent_id')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = true;

        Category::create($data);

        return back()->with('success', '카테고리가 추가되었습니다.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        return back()->with('success', '카테고리가 수정되었습니다.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', '카테고리가 삭제되었습니다.');
    }

    private function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'cat-' . Str::random(5);
        $slug = $base;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
