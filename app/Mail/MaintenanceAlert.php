<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaintenanceAlert extends Mailable
{
    use Queueable, SerializesModels;
    protected $maints;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($maints)
    {
        $this->maints = $maints;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Upcoming maintenances'))
            ->view('email.maintenance')
            ->with([
                'maints' => $this->maints,
            ]);
    }
}
