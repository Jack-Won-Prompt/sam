<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $shippingFee = $this->cart->shippingFee($subtotal);
        $total = $subtotal + $shippingFee;

        return view('cart.index', compact('items', 'subtotal', 'shippingFee', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_option_id' => 'nullable|exists:product_options,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::active()->findOrFail($data['product_id']);
        $this->cart->add($product, $data['product_option_id'] ?? null, $data['quantity'] ?? 1);

        if ($request->wantsJson()) {
            return response()->json(['count' => $this->cart->count()]);
        }

        return redirect()->route('cart.index')->with('success', '장바구니에 담았습니다.');
    }

    public function update(Request $request, int $item)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cart->updateQuantity($item, $data['quantity']);

        return redirect()->route('cart.index');
    }

    public function remove(int $item)
    {
        $this->cart->remove($item);

        return redirect()->route('cart.index')->with('success', '상품을 삭제했습니다.');
    }
}
