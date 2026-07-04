<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($request->slug ?: $request->name);
        $data = $this->handleThumbnail($request, $data);
        $data = $this->normalizeFlags($request, $data);

        $product = Product::create($data);
        $this->syncOptions($request, $product);
        $this->storeImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', '상품이 등록되었습니다.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        $product->load('options', 'images');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request, $product->id);
        if ($request->slug && $request->slug !== $product->slug) {
            $data['slug'] = $this->uniqueSlug($request->slug, $product->id);
        }
        $data = $this->handleThumbnail($request, $data, $product);
        $data = $this->normalizeFlags($request, $data);

        $product->update($data);
        $this->syncOptions($request, $product);
        $this->storeImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', '상품이 수정되었습니다.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', '상품이 삭제되었습니다.');
    }

    /** 추가 이미지 저장 (images[]) */
    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }
        $order = (int) $product->images()->max('sort_order');
        foreach ($request->file('images') as $file) {
            if (! $file) {
                continue;
            }
            $product->images()->create([
                'path' => $file->store('products', 'public'),
                'sort_order' => ++$order,
            ]);
        }
    }

    /** 추가 이미지 삭제 */
    public function destroyImage(\App\Models\ProductImage $image)
    {
        $image->delete();

        return back()->with('success', '이미지가 삭제되었습니다.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'origin' => 'nullable|string|max:100',
            'cultivation_years' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|max:4096',
            'images' => 'nullable|array',
            'images.*' => 'image|max:4096',
            'sort_order' => 'nullable|integer',
        ]);
    }

    private function normalizeFlags(Request $request, array $data): array
    {
        $data['is_active'] = $request->boolean('is_active');
        $data['is_best'] = $request->boolean('is_best');
        $data['is_new'] = $request->boolean('is_new');
        $data['sort_order'] = $request->integer('sort_order');

        return $data;
    }

    private function handleThumbnail(Request $request, array $data, ?Product $product = null): array
    {
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } else {
            unset($data['thumbnail']);
        }

        return $data;
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'product-' . Str::random(6);
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** 옵션 일괄 동기화 (name[], price_add[], stock[]) */
    private function syncOptions(Request $request, Product $product): void
    {
        $names = $request->input('option_name', []);
        $adds = $request->input('option_price_add', []);
        $stocks = $request->input('option_stock', []);

        $product->options()->delete();
        foreach ($names as $i => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $product->options()->create([
                'name' => $name,
                'price_add' => (int) ($adds[$i] ?? 0),
                'stock' => (int) ($stocks[$i] ?? 0),
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
