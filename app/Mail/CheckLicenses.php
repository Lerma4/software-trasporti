<?php

namespace App\Mail;

use App\Models\Expiration;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckLicenses extends Mailable
{
    use Queueable, SerializesModels;
    protected $licenses;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($licenses)
    {
        $this->licenses = $licenses;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Expiring licences'))
            ->view('email.licenses')
            ->with([
                'licenses' => $this->licenses
            ]);
    }
}
