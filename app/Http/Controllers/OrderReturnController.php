<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;

class OrderReturnController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! $order->isReturnable()) {
            return back()->with('error', '배송 중/완료된 주문만 교환·반품 신청이 가능합니다.');
        }

        $data = $request->validate([
            'type' => 'required|in:exchange,return',
            'reason' => 'required|string|max:100',
            'detail' => 'nullable|string|max:1000',
        ]);

        OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'reason' => $data['reason'],
            'detail' => $data['detail'] ?? null,
            'status' => 'requested',
        ]);

        return back()->with('success', '교환/반품 신청이 접수되었습니다. 확인 후 연락드리겠습니다.');
    }
}
