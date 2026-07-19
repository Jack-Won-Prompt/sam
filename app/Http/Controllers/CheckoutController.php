<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\CashbackService;
use App\Services\CouponService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CouponService $coupons,
        private PointService $points,
    ) {}

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '장바구니가 비어 있습니다.');
        }

        $subtotal = $this->cart->subtotal();
        $shippingFee = $this->cart->shippingFee($subtotal);
        $total = $subtotal + $shippingFee;
        $user = auth()->user();
        $userPoints = $user->points ?? 0;

        // 구매 대행자면 대행 구매자 목록 + 캐쉬백 비율 제공
        $agentBuyers = ($user && $user->is_agent)
            ? $user->buyers()->where('is_active', true)->get()
            : collect();
        $cashbackRate = ($user && $user->is_agent) ? (int) $user->cashback_rate : 0;

        return view('checkout.index', compact('items', 'subtotal', 'shippingFee', 'total', 'userPoints', 'agentBuyers', 'cashbackRate'));
    }

    /** 쿠폰 적용 (AJAX) */
    public function applyCoupon(Request $request)
    {
        $subtotal = $this->cart->subtotal();
        $result = $this->coupons->validate($request->input('code'), $subtotal, auth()->user());

        return response()->json([
            'ok' => $result['ok'],
            'message' => $result['message'],
            'discount' => $result['discount'],
        ]);
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
            'coupon_code' => 'nullable|string|max:50',
            'points_used' => 'nullable|integer|min:0',
            'buyer_id' => 'nullable|integer',
        ]);

        $subtotal = $this->cart->subtotal();
        $shippingFee = $this->cart->shippingFee($subtotal);
        $user = auth()->user();

        // 쿠폰 할인
        $discount = 0;
        $couponCode = null;
        $coupon = null;
        if (! empty($data['coupon_code'])) {
            $cres = $this->coupons->validate($data['coupon_code'], $subtotal, $user);
            if ($cres['ok']) {
                $discount = $cres['discount'];
                $coupon = $cres['coupon'];
                $couponCode = $coupon->code;
            }
        }

        // 적립금 사용 (회원, 잔액·주문금액 한도)
        $pointsUsed = 0;
        if ($user && ! empty($data['points_used'])) {
            $maxUse = max(0, $subtotal + $shippingFee - $discount);
            $pointsUsed = min((int) $data['points_used'], $user->points, $maxUse);
        }

        $total = max(0, $subtotal + $shippingFee - $discount - $pointsUsed);

        // 구매 대행 주문: 대행자가 자신의 구매자를 지정한 경우 캐쉬백 산정
        $agentId = null;
        $buyerId = null;
        $cashback = 0;
        if ($user && $user->is_agent && ! empty($data['buyer_id'])) {
            $buyer = $user->buyers()->where('is_active', true)->find($data['buyer_id']);
            if ($buyer) {
                $agentId = $user->id;
                $buyerId = $buyer->id;
                $cashback = app(CashbackService::class)->calc($total, (int) $user->cashback_rate);
            }
        }

        $order = DB::transaction(function () use ($data, $items, $subtotal, $shippingFee, $discount, $pointsUsed, $couponCode, $coupon, $total, $user, $agentId, $buyerId, $cashback) {
            $order = Order::create(array_merge(
                collect($data)->except(['coupon_code', 'points_used', 'buyer_id'])->toArray(),
                [
                    'order_number' => $this->generateOrderNumber(),
                    'user_id' => $user?->id,
                    'agent_id' => $agentId,
                    'buyer_id' => $buyerId,
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'discount' => $discount,
                    'points_used' => $pointsUsed,
                    'coupon_code' => $couponCode,
                    'total' => $total,
                    'cashback' => $cashback,
                    'status' => 'pending',
                ]
            ));

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

            // 적립금 즉시 차감 (취소 시 환급)
            if ($user && $pointsUsed > 0) {
                $this->points->use($user, $pointsUsed, "주문 사용 ({$order->order_number})", $order->id);
            }
            // 쿠폰 사용 카운트
            if ($coupon) {
                $coupon->increment('used_count');
            }

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
