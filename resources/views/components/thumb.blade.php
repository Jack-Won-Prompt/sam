@props(['product', 'class' => 'aspect-square'])

@php
    $img = $product->thumbnail
        ? (\Illuminate\Support\Str::startsWith($product->thumbnail, ['http', '/'])
            ? $product->thumbnail
            : asset('storage/' . $product->thumbnail))
        : null;
    // 상품명 기반 자연 톤 그라디언트
    $tones = [
        ['#1f5fd0', '#3182f6'], ['#2570e6', '#579bf9'], ['#1c4fab', '#3182f6'],
        ['#1a4489', '#2570e6'], ['#3182f6', '#8bbcff'],
    ];
    $t = $tones[crc32($product->name) % count($tones)];
@endphp

@if ($img)
    <div {{ $attributes->merge(['class' => $class . ' overflow-hidden bg-neutral-100']) }}>
        <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
    </div>
@else
    <div {{ $attributes->merge(['class' => $class . ' flex flex-col items-center justify-center text-white text-center p-4']) }}
         style="background: linear-gradient(135deg, {{ $t[0] }}, {{ $t[1] }});">
        <span class="text-3xl mb-1 opacity-90">🌿</span>
        <span class="text-xs font-medium leading-tight opacity-95 line-clamp-2">{{ $product->name }}</span>
    </div>
@endif
