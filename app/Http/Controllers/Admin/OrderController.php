<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('order_number', 'like', "%{$request->q}%")
                        ->orWhere('orderer_name', 'like', "%{$request->q}%")
                        ->orWhere('receiver_name', 'like', "%{$request->q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items', 'payment', 'user');

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::STATUSES,
            'couriers' => Order::COURIERS,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::STATUSES)),
        ]);

        // 취소/환불은 서비스 통해 재고복구/결제취소 처리
        if (in_array($data['status'], ['cancelled', 'refunded'])) {
            $result = $this->orders->cancel($order, '관리자 처리', $data['status'] === 'refunded');
            return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
        }

        $order->update(['status' => $data['status']]);

        return back()->with('success', '주문 상태가 변경되었습니다.');
    }

    /** 발송 처리 (송장 등록) */
    public function ship(Request $request, Order $order)
    {
        $data = $request->validate([
            'courier' => 'required|string',
            'tracking_number' => 'required|string|max:50',
        ]);

        $this->orders->ship($order, $data['courier'], $data['tracking_number']);

        return back()->with('success', '발송 처리되었습니다.');
    }
}
