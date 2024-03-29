<?php

namespace App\Jobs;

use App\Mail\CreationUserEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class CreationUserEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $send_mail, $password, $company;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($send_mail, $password, $company)
    {
        $this->send_mail = $send_mail;
        $this->password = $password;
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new CreationUserEmail($this->password, $this->company);
        Mail::to($this->send_mail)->send($email);
    }
}
