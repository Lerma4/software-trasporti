<?php

namespace App\Jobs;

use App\Mail\MaintenanceAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class MaintenanceAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $send_mail, $maints;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($send_mail, $maints)
    {
        $this->send_mail = $send_mail;
        $this->maints = $maints;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new MaintenanceAlert($this->maints);
        Mail::to($this->send_mail)->send($email);
    }
}
