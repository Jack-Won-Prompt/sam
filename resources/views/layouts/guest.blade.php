<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '강원 산양삼') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-neutral-800 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center py-10 px-4 bg-gradient-to-b from-brand-50 to-neutral-50">
            <a href="{{ url('/') }}" class="flex items-center gap-2 mb-6">
                <span class="text-3xl">🌿</span>
                <span class="text-2xl font-extrabold text-brand-800 tracking-tight">강원<span class="text-gold-500">산양삼</span></span>
            </a>

            <div class="w-full sm:max-w-md bg-white shadow-lg rounded-2xl border border-neutral-100 px-7 py-8">
                {{ $slot }}
            </div>

            <a href="{{ url('/') }}" class="mt-6 text-sm text-neutral-400 hover:text-brand-700">← 쇼핑몰로 돌아가기</a>
        </div>
    </body>
</html>
