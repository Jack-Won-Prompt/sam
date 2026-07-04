{{-- 상품 필터 (연근·가격대) : $yearOptions 필요 --}}
@php
    $priceRanges = \App\Http\Controllers\CategoryController::PRICE_RANGES;
    $selectedYears = (array) request('years', []);
    $hasFilter = request()->hasAny(['years', 'price']);
@endphp
<form method="GET" class="border border-neutral-200 rounded-lg p-4 mb-6 bg-white" x-data>
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    <div class="grid sm:grid-cols-2 gap-4">
        @if ($yearOptions->isNotEmpty())
            <div>
                <p class="text-sm font-semibold text-neutral-700 mb-2">연근</p>
                <div class="flex flex-wrap gap-x-4 gap-y-1">
                    @foreach ($yearOptions as $year)
                        <label class="flex items-center gap-1.5 text-sm text-neutral-600">
                            <input type="checkbox" name="years[]" value="{{ $year }}" @checked(in_array($year, $selectedYears))
                                   onchange="this.form.submit()" class="rounded text-brand-600">
                            {{ $year }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <p class="text-sm font-semibold text-neutral-700 mb-2">가격대</p>
            <div class="flex flex-wrap gap-x-4 gap-y-1">
                @foreach ($priceRanges as $val => $label)
                    <label class="flex items-center gap-1.5 text-sm text-neutral-600">
                        <input type="radio" name="price" value="{{ $val }}" @checked(request('price')===$val)
                               onchange="this.form.submit()" class="text-brand-600">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    @if ($hasFilter)
        <div class="mt-3 pt-3 border-t border-neutral-100">
            <a href="{{ url()->current() }}?sort={{ request('sort') }}" class="text-sm text-neutral-500 hover:text-brand-700">✕ 필터 초기화</a>
        </div>
    @endif
</form>
