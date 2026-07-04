<!DOCTYPE html>
<html lang="ko">
<head><meta charset="utf-8"></head>
<body style="margin:0;background:#f5f5f5;font-family:'Malgun Gothic',sans-serif;color:#333;">
    <div style="max-width:600px;margin:0 auto;background:#fff;">
        <div style="background:#1f5fd0;color:#fff;padding:24px 28px;">
            <h1 style="margin:0;font-size:20px;">🌿 강원산양삼</h1>
        </div>
        <div style="padding:28px;">
            <h2 style="font-size:18px;margin:0 0 8px;">주문이 완료되었습니다</h2>
            <p style="color:#666;margin:0 0 20px;">{{ $order->orderer_name }}님, 주문해 주셔서 감사합니다.</p>

            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr><td style="color:#888;padding:6px 0;">주문번호</td><td style="text-align:right;font-weight:bold;">{{ $order->order_number }}</td></tr>
                <tr><td style="color:#888;padding:6px 0;">주문일시</td><td style="text-align:right;">{{ $order->created_at->format('Y-m-d H:i') }}</td></tr>
            </table>

            <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">

            @foreach ($order->items as $item)
                <table style="width:100%;font-size:14px;margin-bottom:8px;">
                    <tr>
                        <td>{{ $item->product_name }}@if($item->option_name)<span style="color:#999;">/ {{ $item->option_name }}</span>@endif × {{ $item->quantity }}</td>
                        <td style="text-align:right;font-weight:bold;">{{ number_format($item->subtotal) }}원</td>
                    </tr>
                </table>
            @endforeach

            <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">

            <table style="width:100%;font-size:14px;">
                <tr><td style="color:#888;padding:4px 0;">상품금액</td><td style="text-align:right;">{{ number_format($order->subtotal) }}원</td></tr>
                <tr><td style="color:#888;padding:4px 0;">배송비</td><td style="text-align:right;">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee).'원' : '무료' }}</td></tr>
                <tr><td style="font-weight:bold;padding:8px 0;font-size:16px;">결제금액</td><td style="text-align:right;font-weight:bold;font-size:16px;color:#1f5fd0;">{{ number_format($order->total) }}원</td></tr>
            </table>

            <div style="background:#f8f9fa;border-radius:8px;padding:16px;margin-top:16px;font-size:13px;color:#555;">
                <b>배송지</b><br>
                {{ $order->receiver_name }} ({{ $order->receiver_phone }})<br>
                [{{ $order->postcode }}] {{ $order->address1 }} {{ $order->address2 }}
            </div>
        </div>
        <div style="background:#222;color:#999;padding:20px 28px;font-size:12px;">
            (주)강원산양삼 · 고객센터 1588-0000<br>
            본 메일은 발신 전용입니다.
        </div>
    </div>
</body>
</html>
