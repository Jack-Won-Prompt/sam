<x-guest-layout>
    <h1 class="text-xl font-bold text-neutral-800 text-center mb-1">회원가입</h1>
    <p class="text-sm text-neutral-500 text-center mb-6">가입하고 다양한 혜택을 받아보세요</p>

    {{-- 소셜 가입 --}}
    <div class="space-y-2">
        <a href="{{ route('social.redirect', 'kakao') }}"
           class="flex items-center justify-center gap-2 w-full rounded-lg py-3 text-sm font-semibold transition hover:brightness-95"
           style="background:#FEE500;color:#191600;">
            <span>💬</span> 카카오로 간편 가입
        </a>
        <a href="{{ route('social.redirect', 'naver') }}"
           class="flex items-center justify-center gap-2 w-full rounded-lg py-3 text-sm font-semibold text-white transition hover:brightness-95"
           style="background:#03C75A;">
            <span class="font-extrabold">N</span> 네이버로 간편 가입
        </a>
    </div>

    <div class="relative my-6 text-center">
        <span class="bg-white px-3 text-xs text-neutral-400 relative z-10">또는 이메일 가입</span>
        <div class="absolute inset-x-0 top-1/2 border-t border-neutral-200"></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 mb-1">이름</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="block w-full rounded-lg border-neutral-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-neutral-700 mb-1">이메일</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="block w-full rounded-lg border-neutral-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-neutral-700 mb-1">비밀번호</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="block w-full rounded-lg border-neutral-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-1">비밀번호 확인</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="block w-full rounded-lg border-neutral-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button class="btn-brand w-full py-3">회원가입</button>
    </form>

    <p class="text-center text-sm text-neutral-500 mt-6">
        이미 회원이신가요?
        <a href="{{ route('login') }}" class="text-brand-700 font-semibold hover:underline">로그인</a>
    </p>
</x-guest-layout>
