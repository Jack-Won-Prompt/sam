@php
    $product = $product ?? null;
    $opts = old('option_name')
        ? collect(old('option_name'))->map(fn ($n, $i) => (object)[
            'name' => $n, 'price_add' => old('option_price_add')[$i] ?? 0, 'stock' => old('option_stock')[$i] ?? 0,
        ])
        : ($product?->options ?? collect());
@endphp

<div class="grid lg:grid-cols-3 gap-6">
    {{-- 기본 정보 --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
            <h2 class="font-bold text-neutral-800">기본 정보</h2>

            <div>
                <label class="block text-sm text-neutral-600 mb-1">상품명 *</label>
                <input name="name" value="{{ old('name', $product->name ?? '') }}" required class="w-full rounded-md border-neutral-300 text-sm">
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">카테고리</label>
                    <select name="category_id" class="w-full rounded-md border-neutral-300 text-sm">
                        <option value="">선택</option>
                        @foreach ($categories as $cat)
                            @if ($cat->parent_id === null)
                                <optgroup label="{{ $cat->name }}">
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? null) == $cat->id)>{{ $cat->name }} (대분류)</option>
                                    @foreach ($categories->where('parent_id', $cat->id) as $child)
                                        <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id ?? null) == $child->id)>└ {{ $child->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">슬러그(URL)</label>
                    <input name="slug" value="{{ old('slug', $product->slug ?? '') }}" placeholder="비우면 자동 생성" class="w-full rounded-md border-neutral-300 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm text-neutral-600 mb-1">한 줄 요약</label>
                <input name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" class="w-full rounded-md border-neutral-300 text-sm">
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">재배지역</label>
                    <input name="origin" value="{{ old('origin', $product->origin ?? '') }}" placeholder="강원특별자치도 홍천군" class="w-full rounded-md border-neutral-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">연근</label>
                    <input name="cultivation_years" value="{{ old('cultivation_years', $product->cultivation_years ?? '') }}" placeholder="5년근" class="w-full rounded-md border-neutral-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 mb-1">규격/중량</label>
                    <input name="weight" value="{{ old('weight', $product->weight ?? '') }}" placeholder="뿌리당 8~12g" class="w-full rounded-md border-neutral-300 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm text-neutral-600 mb-1">상세 설명 (HTML 가능)</label>
                <textarea name="description" rows="8" class="w-full rounded-md border-neutral-300 text-sm font-mono">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>

        {{-- 옵션 --}}
        <div class="bg-white rounded-xl border border-neutral-200 p-6"
             x-data="{ rows: {{ Illuminate\Support\Js::from($opts->map(fn($o)=>['name'=>$o->name,'price_add'=>(int)$o->price_add,'stock'=>(int)$o->stock])->values()) }} }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-neutral-800">옵션</h2>
                <button type="button" @click="rows.push({name:'',price_add:0,stock:0})" class="btn-outline py-1.5 px-3 text-xs">+ 옵션 추가</button>
            </div>
            <div class="space-y-2">
                <template x-for="(row, i) in rows" :key="i">
                    <div class="flex gap-2 items-center">
                        <input :name="'option_name['+i+']'" x-model="row.name" placeholder="옵션명 (예: 10뿌리 선물세트)" class="flex-1 rounded-md border-neutral-300 text-sm">
                        <input :name="'option_price_add['+i+']'" x-model.number="row.price_add" type="number" placeholder="추가금액" class="w-28 rounded-md border-neutral-300 text-sm">
                        <input :name="'option_stock['+i+']'" x-model.number="row.stock" type="number" placeholder="재고" class="w-24 rounded-md border-neutral-300 text-sm">
                        <button type="button" @click="rows.splice(i,1)" class="text-red-500 px-2">✕</button>
                    </div>
                </template>
                <p x-show="rows.length === 0" class="text-sm text-neutral-400 py-2">옵션이 없으면 단일 상품으로 판매됩니다.</p>
            </div>
        </div>
    </div>

    {{-- 사이드 --}}
    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-neutral-200 p-6 space-y-4">
            <h2 class="font-bold text-neutral-800">판매 정보</h2>
            <div>
                <label class="block text-sm text-neutral-600 mb-1">정상가 *</label>
                <input name="price" type="number" value="{{ old('price', $product->price ?? '') }}" required class="w-full rounded-md border-neutral-300 text-sm">
            </div>
            <div>
                <label class="block text-sm text-neutral-600 mb-1">할인가</label>
                <input name="sale_price" type="number" value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="비우면 할인 없음" class="w-full rounded-md border-neutral-300 text-sm">
            </div>
            <div>
                <label class="block text-sm text-neutral-600 mb-1">재고 *</label>
                <input name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" required class="w-full rounded-md border-neutral-300 text-sm">
            </div>
            <div>
                <label class="block text-sm text-neutral-600 mb-1">정렬 순서</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="w-full rounded-md border-neutral-300 text-sm">
            </div>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-6 space-y-3">
            <h2 class="font-bold text-neutral-800">노출 설정</h2>
            @foreach ([['is_active','판매중', $product->is_active ?? true], ['is_best','베스트', $product->is_best ?? false], ['is_new','신상품', $product->is_new ?? false]] as [$field, $label, $default])
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $default)) class="rounded border-neutral-300 text-brand-600">
                    {{ $label }}
                </label>
            @endforeach
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-6">
            <h2 class="font-bold text-neutral-800 mb-3">대표 이미지</h2>
            @if ($product?->thumbnail)
                <img src="{{ asset('storage/' . $product->thumbnail) }}" class="w-full aspect-square object-cover rounded-md mb-3" alt="">
            @endif
            <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm">
            <p class="text-xs text-neutral-400 mt-2">미등록 시 상품명 기반 플레이스홀더가 표시됩니다.</p>
        </div>

        {{-- 추가 이미지(상세 갤러리) --}}
        <div class="bg-white rounded-xl border border-neutral-200 p-6">
            <h2 class="font-bold text-neutral-800 mb-3">추가 이미지 (상세)</h2>
            @if ($product && $product->images->isNotEmpty())
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach ($product->images as $img)
                        <div class="relative group" id="pimg-{{ $img->id }}">
                            <img src="{{ asset('storage/'.$img->path) }}" class="w-full aspect-square object-cover rounded-md" alt="">
                            <button type="button" onclick="deleteProductImage({{ $img->id }})"
                                    class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/60 text-white text-xs">✕</button>
                        </div>
                    @endforeach
                </div>
            @endif
            <input type="file" name="images[]" accept="image/*" multiple class="w-full text-sm">
            <p class="text-xs text-neutral-400 mt-2">여러 장 선택 가능 (상품 상세페이지에 표시)</p>
        </div>

        <div class="flex gap-2">
            <button class="btn-brand flex-1 py-3">{{ $product ? '수정' : '등록' }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-outline py-3">취소</a>
        </div>
    </div>
</div>
