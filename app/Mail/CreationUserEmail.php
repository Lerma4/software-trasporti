<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreationUserEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $password, $company;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $company)
    {
        $this->password = $password;
        $this->company = $company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Welcome in ') . config('app.name'))
            ->view('email.user')
            ->with([
                'password' => $this->password,
                'company' => $this->company
            ]);
    }
}
