<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '강원 산양삼 | 산이 키운 진짜 삼')</title>
    <meta name="description" content="@yield('meta_description', '해발 700m 강원도 청정 산속에서 자연 그대로 키운 산양삼 전문 쇼핑몰')">

    {{-- Open Graph (SNS 공유) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="강원 산양삼">
    <meta property="og:title" content="@yield('title', '강원 산양삼 | 산이 키운 진짜 삼')">
    <meta property="og:description" content="@yield('meta_description', '해발 700m 강원도 청정 산속에서 자연 그대로 키운 산양삼')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('storage/farm/farm-07.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-neutral-50 font-sans">

    @include('partials.header')

    <main class="min-h-[60vh]">
        @if (session('success'))
            <div class="container-shop mt-4">
                <div class="rounded-md bg-brand-50 border border-brand-200 px-4 py-3 text-sm text-brand-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="container-shop mt-4">
                <div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
