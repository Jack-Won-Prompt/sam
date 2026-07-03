<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '장바구니가 비어 있습니다.');
        }

        $subtotal = $this->cart->subtotal();
        $shippingFee = $this->cart->shippingFee($subtotal);
        $total = $subtotal + $shippingFee;

        return view('checkout.index', compact('items', 'subtotal', 'shippingFee', 'total'));
    }

    public function store(Request $request)
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '장바구니가 비어 있습니다.');
        }

        $data = $request->validate([
            'orderer_name' => 'required|string|max:50',
            'orderer_phone' => 'required|string|max:20',
            'orderer_email' => 'nullable|email|max:100',
            'receiver_name' => 'required|string|max:50',
            'receiver_phone' => 'required|string|max:20',
            'postcode' => 'nullable|string|max:10',
            'address1' => 'required|string|max:200',
            'address2' => 'nullable|string|max:200',
            'delivery_message' => 'nullable|string|max:200',
        ]);

        $subtotal = $this->cart->subtotal();
        $shippingFee = $this->cart->shippingFee($subtotal);
        $total = $subtotal + $shippingFee;

        $order = DB::transaction(function () use ($data, $items, $subtotal, $shippingFee, $total) {
            $order = Order::create(array_merge($data, [
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => 0,
                'total' => $total,
                'status' => 'pending',
            ]));

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_option_id' => $item->product_option_id,
                    'product_name' => $item->product->name,
                    'option_name' => $item->option?->name,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'toss_order_id' => $order->order_number,
                'amount' => $total,
                'status' => 'ready',
            ]);

            return $order;
        });

        return redirect()->route('payment.show', $order);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
