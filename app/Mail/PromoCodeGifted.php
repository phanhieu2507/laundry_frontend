<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromoCodeGifted extends Mailable
{
    use Queueable, SerializesModels;
    public $quantity;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $quantity)
    {
        $this->user = $user;
        $this->quantity = $quantity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.promocodeGifted')
                    ->with([
                        'quantity' => $this->quantity
                    ])
                    ->subject('Promo Code Gift');
    }
}
