<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

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
        $order->load('items', 'payment', 'returns');

        return view('order.show', compact('order'));
    }

    /** 주문 취소 (회원) */
    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! $order->isCancellable()) {
            return back()->with('error', '이미 배송이 시작되어 취소할 수 없습니다. 고객센터로 문의해 주세요.');
        }

        $reason = $request->input('reason', '고객 요청');
        $result = $this->orders->cancel($order, $reason);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    /** 적립금 내역 (회원) */
    public function points()
    {
        $histories = auth()->user()->pointHistories()->paginate(20);

        return view('mypage.points', compact('histories'));
    }

    /** 비회원 주문 조회 폼 */
    public function trackForm()
    {
        return view('order.track');
    }

    /** 비회원 주문 조회 처리 */
    public function track(Request $request)
    {
        $data = $request->validate([
            'order_number' => 'required|string',
            'orderer_phone' => 'required|string',
        ]);

        $order = Order::where('order_number', $data['order_number'])
            ->where('orderer_phone', $data['orderer_phone'])
            ->with('items')
            ->first();

        if (! $order) {
            return back()->with('error', '주문번호 또는 연락처가 일치하지 않습니다.')->withInput();
        }

        return view('order.track-result', compact('order'));
    }
}
