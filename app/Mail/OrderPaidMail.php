<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[강원산양삼] 주문이 완료되었습니다 ({$this->order->order_number})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-paid',
            with: ['order' => $this->order],
        );
    }
}
