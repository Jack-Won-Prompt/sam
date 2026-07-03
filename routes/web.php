<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 고객 쇼핑몰 (프론트)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [ProductController::class, 'search'])->name('search');

Route::get('/collection/{type}', [CategoryController::class, 'collection'])->name('collection');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// 장바구니
Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add', 'add')->name('add');
    Route::put('/{item}', 'update')->name('update');
    Route::delete('/{item}', 'remove')->name('remove');
});

// 주문 / 결제
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
Route::get('/payment/toss/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/toss/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::get('/payment/{order}/dev-complete', [PaymentController::class, 'devComplete'])->name('payment.dev');

Route::get('/order/complete/{order}', [OrderController::class, 'complete'])->name('order.complete');

/*
|--------------------------------------------------------------------------
| 마이페이지 (회원 전용)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Breeze 기본 리다이렉트 대상: 권한에 따라 분기
    Route::get('/dashboard', function () {
        return auth()->user()->is_admin
            ? redirect()->route('admin.dashboard')
            : redirect()->route('order.index');
    })->name('dashboard');

    Route::get('/mypage/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/mypage/orders/{order}', [OrderController::class, 'show'])->name('order.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 관리자 백오피스
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except('show');

    Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
    Route::post('banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
    Route::put('banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');

    Route::get('members', [\App\Http\Controllers\Admin\MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [\App\Http\Controllers\Admin\MemberController::class, 'show'])->name('members.show');
});

require __DIR__.'/auth.php';
