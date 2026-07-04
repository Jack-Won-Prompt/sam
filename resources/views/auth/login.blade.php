<x-guest-layout>
    <h1 class="text-xl font-bold text-neutral-800 text-center mb-1">로그인</h1>
    <p class="text-sm text-neutral-500 text-center mb-6">강원산양삼에 오신 것을 환영합니다</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    {{-- 소셜 로그인 --}}
    <div class="space-y-2">
        <a href="{{ route('social.redirect', 'kakao') }}"
           class="flex items-center justify-center gap-2 w-full rounded-lg py-3 text-sm font-semibold transition hover:brightness-95"
           style="background:#FEE500;color:#191600;">
            <span>💬</span> 카카오로 3초 만에 시작하기
        </a>
        <a href="{{ route('social.redirect', 'naver') }}"
           class="flex items-center justify-center gap-2 w-full rounded-lg py-3 text-sm font-semibold text-white transition hover:brightness-95"
           style="background:#03C75A;">
            <span class="font-extrabold">N</span> 네이버로 시작하기
        </a>
    </div>

    <div class="relative my-6 text-center">
        <span class="bg-white px-3 text-xs text-neutral-400 relative z-10">또는 이메일 로그인</span>
        <div class="absolute inset-x-0 top-1/2 border-t border-neutral-200"></div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-neutral-700 mb-1">이메일</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="block w-full rounded-lg border-neutral-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-neutral-700 mb-1">비밀번호</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full rounded-lg border-neutral-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center text-sm text-neutral-600">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500">
                <span class="ms-2">로그인 상태 유지</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-neutral-500 hover:text-brand-700 underline" href="{{ route('password.request') }}">
                    비밀번호 찾기
                </a>
            @endif
        </div>

        <button class="btn-brand w-full py-3">로그인</button>
    </form>

    <p class="text-center text-sm text-neutral-500 mt-6">
        아직 회원이 아니신가요?
        <a href="{{ route('register') }}" class="text-brand-700 font-semibold hover:underline">회원가입</a>
    </p>
</x-guest-layout>
