<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    /** 주문 완료 페이지 */
    public function complete(Order $order)
    {
        if ($order->user_id && auth()->id() !== $order->user_id) {
            abort(403);
        }
        $order->load('items');

        return view('order.complete', compact('order'));
    }

    /** 마이페이지 - 주문 목록 (회원 전용) */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('order.index', compact('orders'));
    }

    /** 주문 상세 (회원 전용) */
    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items', 'payment');

        return view('order.show', compact('order'));
    }
}
