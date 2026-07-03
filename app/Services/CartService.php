<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Support\Collection;

class CartService
{
    /** 현재 사용자/세션 기준 장바구니 아이템 쿼리 */
    protected function query()
    {
        if (auth()->check()) {
            return CartItem::where('user_id', auth()->id());
        }

        return CartItem::whereNull('user_id')->where('session_id', session()->getId());
    }

    /** 장바구니에 담기 */
    public function add(Product $product, ?int $optionId, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);

        $existing = $this->query()
            ->where('product_id', $product->id)
            ->where('product_option_id', $optionId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing;
        }

        return CartItem::create([
            'user_id' => auth()->id(),
            'session_id' => auth()->check() ? null : session()->getId(),
            'product_id' => $product->id,
            'product_option_id' => $optionId,
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = $this->query()->find($itemId);
        if (! $item) {
            return;
        }
        if ($quantity <= 0) {
            $item->delete();
            return;
        }
        $item->update(['quantity' => $quantity]);
    }

    public function remove(int $itemId): void
    {
        $this->query()->where('id', $itemId)->delete();
    }

    public function clear(): void
    {
        $this->query()->delete();
    }

    /** @return Collection<int,CartItem> */
    public function items(): Collection
    {
        return $this->query()
            ->with(['product.category', 'option'])
            ->latest()
            ->get()
            ->filter(fn ($i) => $i->product !== null)
            ->values();
    }

    public function count(): int
    {
        return (int) $this->query()->sum('quantity');
    }

    public function subtotal(): int
    {
        return $this->items()->sum(fn (CartItem $i) => $i->subtotal);
    }

    /** 배송비 정책: 5만원 이상 무료, 미만 3,000원 */
    public function shippingFee(?int $subtotal = null): int
    {
        $subtotal ??= $this->subtotal();
        if ($subtotal <= 0) {
            return 0;
        }
        return $subtotal >= 50000 ? 0 : 3000;
    }

    public function total(): int
    {
        $sub = $this->subtotal();
        return $sub + $this->shippingFee($sub);
    }

    /** 로그인 시 비회원 장바구니를 회원 계정으로 이관 */
    public function mergeGuestCart(int $userId): void
    {
        CartItem::whereNull('user_id')
            ->where('session_id', session()->getId())
            ->update(['user_id' => $userId, 'session_id' => null]);
    }
}
