<header x-data="{ mobileOpen: false }" class="bg-white border-b border-neutral-200 sticky top-0 z-40">
    {{-- 상단 유틸 바 --}}
    <div class="bg-brand-800 text-brand-50 text-xs">
        <div class="container-shop flex h-9 items-center justify-end gap-4">
            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">관리자</a>
                @endif
                <a href="{{ route('order.index') }}" class="hover:text-white">마이페이지</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="hover:text-white">로그아웃</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-white">로그인</a>
                <a href="{{ route('register') }}" class="hover:text-white">회원가입</a>
            @endauth
            <span class="hidden sm:inline text-brand-200">고객센터 1588-0000</span>
        </div>
    </div>

    {{-- 메인 헤더 --}}
    <div class="container-shop flex items-center gap-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            <span class="text-2xl">🌿</span>
            <span class="text-xl font-extrabold text-brand-800 tracking-tight">강원<span class="text-gold-500">산양삼</span></span>
        </a>

        {{-- 검색 --}}
        <form action="{{ route('search') }}" method="GET" class="flex-1 max-w-xl hidden md:block">
            <div class="flex items-center rounded-full border-2 border-brand-700 overflow-hidden">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="찾으시는 상품을 검색해보세요"
                    class="flex-1 border-0 px-5 py-2.5 text-sm focus:ring-0">
                <button class="px-5 text-brand-700" aria-label="검색">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                </button>
            </div>
        </form>

        {{-- 아이콘 --}}
        <div class="flex items-center gap-5 ml-auto">
            <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center text-neutral-600 hover:text-brand-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-[11px] mt-0.5">장바구니</span>
                @if ($cartCount > 0)
                    <span class="absolute -top-1.5 right-1 bg-gold-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ $cartCount }}</span>
                @endif
            </a>
            @auth
                <a href="{{ route('order.index') }}" class="flex flex-col items-center text-neutral-600 hover:text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-[11px] mt-0.5">마이페이지</span>
                </a>
            @endauth
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-neutral-700">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    {{-- 카테고리 네비게이션 --}}
    <nav class="border-t border-neutral-200 hidden md:block">
        <div class="container-shop flex items-center gap-1">
            <a href="{{ route('collection', 'best') }}" class="px-4 py-3 text-sm font-bold text-red-600 hover:text-red-700">🔥 베스트</a>
            @foreach ($navCategories as $cat)
                <div class="relative group">
                    <a href="{{ route('category.show', $cat) }}"
                       class="block px-4 py-3 text-sm font-semibold text-neutral-700 hover:text-brand-700">
                        {{ $cat->name }}
                    </a>
                    @if ($cat->children->isNotEmpty())
                        <div class="absolute left-0 top-full hidden group-hover:block bg-white border border-neutral-200 rounded-b-md shadow-lg min-w-[160px] py-2 z-50">
                            @foreach ($cat->children as $child)
                                <a href="{{ route('category.show', $child) }}"
                                   class="block px-4 py-2 text-sm text-neutral-600 hover:bg-brand-50 hover:text-brand-700">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            <a href="{{ route('collection', 'sale') }}" class="px-4 py-3 text-sm font-semibold text-neutral-700 hover:text-brand-700">특가</a>
        </div>
    </nav>

    {{-- 모바일 메뉴 --}}
    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-neutral-200 bg-white">
        <form action="{{ route('search') }}" method="GET" class="p-3">
            <input type="text" name="q" placeholder="상품 검색" class="w-full rounded-md border-neutral-300 text-sm">
        </form>
        <nav class="pb-3">
            <a href="{{ route('collection', 'best') }}" class="block px-4 py-2.5 text-sm font-bold text-red-600">🔥 베스트</a>
            @foreach ($navCategories as $cat)
                <a href="{{ route('category.show', $cat) }}" class="block px-4 py-2.5 text-sm font-semibold text-neutral-700">{{ $cat->name }}</a>
            @endforeach
        </nav>
    </div>
</header>
