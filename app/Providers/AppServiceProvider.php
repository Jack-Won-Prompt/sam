<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 헤더/네비에서 쓰는 공용 데이터 공유
        View::composer(['layouts.shop', 'partials.*'], function ($view) {
            $navCategories = Category::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')
                ->get();

            $view->with('navCategories', $navCategories)
                ->with('cartCount', app(CartService::class)->count());
        });

        // 로그인 시 비회원 장바구니를 회원 계정으로 병합
        Event::listen(Login::class, function (Login $event) {
            app(CartService::class)->mergeGuestCart($event->user->getAuthIdentifier());
        });
    }
}
