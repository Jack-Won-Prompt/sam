<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    private array $allowed = ['kakao', 'naver'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed), 404);

        if (! config("services.{$provider}.client_id")) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' 로그인이 아직 설정되지 않았습니다.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed), 404);

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', '소셜 로그인에 실패했습니다. 다시 시도해 주세요.');
        }

        // 기존 소셜계정 → 로그인 / 없으면 이메일 매칭 or 신규가입
        $user = User::where('provider', $provider)->where('provider_id', $social->getId())->first();

        if (! $user && $social->getEmail()) {
            $user = User::where('email', $social->getEmail())->first();
        }

        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $social->getId(),
                'avatar' => $social->getAvatar() ?: $user->avatar,
            ]);
        } else {
            $user = User::create([
                'name' => $social->getName() ?: ($social->getNickname() ?: '소셜회원'),
                'email' => $social->getEmail() ?: "{$provider}_{$social->getId()}@social.local",
                'provider' => $provider,
                'provider_id' => $social->getId(),
                'avatar' => $social->getAvatar(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('home'))->with('success', '로그인되었습니다.');
    }
}
