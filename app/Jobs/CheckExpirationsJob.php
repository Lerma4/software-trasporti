<?php

namespace App\Jobs;

use App\Mail\CheckExpirations;
use App\Models\Expiration;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class CheckExpirationsJob implements ShouldQueue
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

        $trucks = Truck::select('id')
            ->where('companyId', 1)
            ->get();

        $ids = [];
        foreach ($trucks as $truck) {
            array_push($ids, $truck->id);
        }

        $expirations = Expiration::whereIn('truck_id', $ids)
            ->where('deadline', '<', Carbon::now()->addDays(15))
            ->where('mail', 0)
            ->orderBy('deadline', 'ASC')
            ->get();

        if (count($expirations) > 0) {
            foreach ($expirations as $expiration) {
                $expiration->mail = 1;
                $expiration->save();
            }

            $email = new CheckExpirations($expirations);
            Mail::to($this->send_mail)->send($email);
        }
    }
}
