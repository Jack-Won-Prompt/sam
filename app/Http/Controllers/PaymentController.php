<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private CartService $cart, private OrderService $orders) {}

    /** 토스페이먼츠 결제창 페이지 */
    public function show(Order $order)
    {
        abort_if($order->status !== 'pending', 404);
        $this->authorizeOrder($order);

        $clientKey = config('services.toss.client_key');
        $customerKey = $order->user_id ? 'CUST_' . $order->user_id : 'GUEST_' . substr(md5($order->order_number), 0, 16);
        $order->load('items');

        return view('payment.show', compact('order', 'clientKey', 'customerKey'));
    }

    /** 결제 성공 콜백 → 토스 승인(confirm) API 호출 */
    public function success(Request $request)
    {
        $paymentKey = $request->query('paymentKey');
        $orderId = $request->query('orderId');
        $amount = (int) $request->query('amount');

        $order = Order::where('order_number', $orderId)->firstOrFail();
        $this->authorizeOrder($order);

        // 금액 위변조 검증
        if ($order->total !== $amount) {
            return redirect()->route('payment.fail', ['orderId' => $orderId])
                ->with('error', '결제 금액이 일치하지 않습니다.');
        }

        $response = Http::withBasicAuth(config('services.toss.secret_key'), '')
            ->asJson()
            ->post('https://api.tosspayments.com/v1/payments/confirm', [
                'paymentKey' => $paymentKey,
                'orderId' => $orderId,
                'amount' => $amount,
            ]);

        if (! $response->successful()) {
            Log::warning('Toss confirm failed', ['body' => $response->body()]);

            $order->payment?->update([
                'status' => 'failed',
                'raw' => $response->json(),
            ]);

            return redirect()->route('payment.fail', ['orderId' => $orderId])
                ->with('error', $response->json('message') ?? '결제 승인에 실패했습니다.');
        }

        $result = $response->json();

        $this->orders->markPaid($order, [
            'paymentKey' => $paymentKey,
            'method' => $result['method'] ?? '카드',
            'approvedAt' => $result['approvedAt'] ?? now(),
            'raw' => $result,
        ]);

        // 결제 완료 → 장바구니 비우기
        $this->cart->clear();

        return redirect()->route('order.complete', $order);
    }

    /**
     * 개발/데모용 테스트 결제 완료 처리 (로컬 환경 전용)
     * 실제 토스 테스트 키가 없어도 주문 라이프사이클을 검증할 수 있게 함.
     */
    public function devComplete(Order $order)
    {
        abort_unless(app()->environment('local'), 404);
        abort_if($order->status !== 'pending', 404);
        $this->authorizeOrder($order);

        $this->orders->markPaid($order, [
            'paymentKey' => 'TEST_' . strtoupper(\Illuminate\Support\Str::random(12)),
            'method' => '테스트결제',
            'approvedAt' => now(),
            'raw' => ['dev' => true],
        ]);

        $this->cart->clear();

        return redirect()->route('order.complete', $order);
    }

    /** 결제 실패 콜백 */
    public function fail(Request $request)
    {
        $message = $request->query('message') ?? session('error') ?? '결제가 취소되었거나 실패했습니다.';
        $orderId = $request->query('orderId');

        return view('payment.fail', compact('message', 'orderId'));
    }

    protected function authorizeOrder(Order $order): void
    {
        // 회원 주문이면 본인만, 비회원 주문이면 통과(주문번호로 접근)
        if ($order->user_id && auth()->id() !== $order->user_id) {
            abort(403);
        }
    }
}
