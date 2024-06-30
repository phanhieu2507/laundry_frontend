<?php

namespace App\Mail;

use App\Models\RequestOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $requestOrder;
    public function __construct(RequestOrder $requestOrder)
    {
        $this->requestOrder = $requestOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->view('emails.status_updated')
        ->subject('Request Status Updated')
        ->with([
            'requestOrder' => $this->requestOrder
        ]);
    }
}
