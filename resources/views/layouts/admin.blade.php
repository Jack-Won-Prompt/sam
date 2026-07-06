<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '관리자') | 강원산양삼 admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 font-sans" x-data="{ sidebar: true }">
<div class="flex min-h-screen">
    {{-- 사이드바 --}}
    <aside class="w-60 bg-brand-900 text-brand-100 flex-col hidden md:flex">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-6 h-16 border-b border-brand-800">
            <span class="text-xl">🌿</span>
            <span class="font-bold text-white">산양삼 <span class="text-gold-400">admin</span></span>
        </a>
        @php
            $nav = [
                ['admin.dashboard', 'admin.dashboard', '대시보드', '📊'],
                ['admin.analytics.index', 'admin.analytics.*', '방문 통계', '📈'],
                ['admin.products.index', 'admin.products.*', '상품 관리', '📦'],
                ['admin.categories.index', 'admin.categories.*', '카테고리', '🗂️'],
                ['admin.orders.index', 'admin.orders.*', '주문 관리', '🧾'],
                ['admin.members.index', 'admin.members.*', '회원 관리', '👤'],
                ['admin.coupons.index', 'admin.coupons.*', '쿠폰 관리', '🎟️'],
                ['admin.banners.index', 'admin.banners.*', '배너 관리', '🖼️'],
                ['admin.notices.index', 'admin.notices.*', '공지사항', '📢'],
                ['admin.faqs.index', 'admin.faqs.*', 'FAQ 관리', '❓'],
                ['admin.inquiries.index', 'admin.inquiries.*', '1:1 문의', '💬'],
                ['admin.questions.index', 'admin.questions.*', '상품 문의', '🙋'],
                ['admin.returns.index', 'admin.returns.*', '교환/반품', '🔄'],
            ];
        @endphp
        <nav class="flex-1 py-4">
            @foreach ($nav as [$route, $pattern, $label, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-brand-800
                          {{ request()->routeIs($pattern) ? 'bg-brand-800 text-white border-l-4 border-gold-400' : '' }}">
                    <span>{{ $icon }}</span>{{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-brand-800">
            <a href="{{ route('home') }}" class="block text-xs text-brand-300 hover:text-white mb-2">← 쇼핑몰로 이동</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="text-xs text-brand-300 hover:text-white">로그아웃</button>
            </form>
        </div>
    </aside>

    {{-- 콘텐츠 --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-neutral-200 flex items-center justify-between px-6">
            <h1 class="font-bold text-neutral-800">@yield('title', '관리자')</h1>
            <span class="text-sm text-neutral-500">{{ auth()->user()->name }}님</span>
        </header>

        <main class="flex-1 p-6">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-brand-50 border border-brand-200 px-4 py-3 text-sm text-brand-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
