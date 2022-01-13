<?php

namespace App\Jobs;

use App\Mail\CheckLicenses;
use App\Models\License;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class CheckLicensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $send_mail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($send_mail)
    {
        $this->send_mail = $send_mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $users = User::select('id')
            ->where('companyId', 1)
            ->get();

        $ids = [];
        foreach ($users as $user) {
            array_push($ids, $user->id);
        }

        $licenses = License::whereIn('user_id', $ids)
            ->where('deadline', '<', Carbon::now()->addDays(15))
            ->where('mail', 0)
            ->orderBy('deadline', 'ASC')
            ->get();

        if (count($licenses) > 0) {
            foreach ($licenses as $license) {
                $license->mail = 1;
                $license->save();
            }

            $email = new CheckLicenses($licenses);
            Mail::to($this->send_mail)->send($email);
        }
    }
}
