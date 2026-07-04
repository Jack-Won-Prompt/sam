@props(['product'])

<a href="{{ route('product.show', $product) }}" class="group block">
    <div class="product-card-media relative rounded-lg overflow-hidden border border-neutral-200 bg-white transition-all duration-300 group-hover:shadow-xl group-hover:-translate-y-1 group-hover:border-brand-200">
        <x-thumb :product="$product" class="aspect-square" />

        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if ($product->discount_rate > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded">{{ $product->discount_rate }}%</span>
            @endif
            @if ($product->is_new)
                <span class="bg-brand-600 text-white text-xs font-bold px-2 py-0.5 rounded">NEW</span>
            @endif
            @if ($product->is_best)
                <span class="bg-gold-500 text-white text-xs font-bold px-2 py-0.5 rounded">BEST</span>
            @endif
        </div>
    </div>

    <div class="mt-3 px-0.5">
        @if ($product->origin)
            <p class="text-xs text-brand-600 font-medium">{{ $product->origin }}</p>
        @endif
        <h3 class="mt-1 text-sm font-semibold text-neutral-800 line-clamp-2 leading-snug group-hover:text-brand-700">
            {{ $product->name }}
        </h3>
        <div class="mt-2 flex items-baseline gap-2">
            @if ($product->discount_rate > 0)
                <span class="text-red-500 font-bold">{{ $product->discount_rate }}%</span>
                <span class="text-lg font-extrabold text-neutral-900">{{ number_format($product->current_price) }}<span class="text-sm font-medium">원</span></span>
                <span class="text-xs text-neutral-400 line-through">{{ number_format($product->price) }}원</span>
            @else
                <span class="text-lg font-extrabold text-neutral-900">{{ number_format($product->current_price) }}<span class="text-sm font-medium">원</span></span>
            @endif
        </div>
    </div>
</a>
