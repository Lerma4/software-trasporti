<?php

namespace App\Mail;

use App\Models\Expiration;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckExpirations extends Mailable
{
    use Queueable, SerializesModels;
    protected $expirations;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($expirations)
    {
        $this->expirations = $expirations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Upcoming expirations'))
            ->view('email.expirations')
            ->with([
                'expirations' => $this->expirations
            ]);
    }
}
