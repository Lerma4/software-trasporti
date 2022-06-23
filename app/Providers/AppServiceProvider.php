<?php

namespace App\Providers;

use App\Models\Crash;
use App\Models\Document;
use App\Models\Expiration;
use App\Models\License;
use App\Models\MaintStillToDo;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // NOTIFICHE USERS

        // NOTIFICHE DOCUMENTI
        $docs = Document::select('user_email', 'companyId')
            ->where('read', false)
            ->get();

        View::share('docs_notifications', $docs);

        // NOTIFICHE ADMIN

        // NOTIFICHE PATENTI
        $licenses = License::whereDate('deadline', '<=', Carbon::now()->addDays(15))
            ->with('user')
            ->get();

        View::share('licenses_notifications', $licenses);

        // NOTIFICHE SCADENZE MEZZI
        $expirations = Expiration::whereDate('deadline', '<=', Carbon::now()->addDays(15))
            ->with('truck')
            ->get();

        View::share('expirations_notifications', $expirations);

        // NOTIFICHE MANUTENZIONI
        $maint = MaintStillToDo::select('companyId')
            ->where('km', '<=', 1000)
            ->get();

        View::share('maint_notifications', $maint);

        // NOTIFICHE INCIDENTI
        $crash = Crash::select('companyId')
            ->where('read', false)
            ->get();

        View::share('crash_notifications', $crash);

        // NOTIFICHE INCIDENTI
        $reports = Report::select('companyId')
            ->where('read', false)
            ->get();

        View::share('reports_notifications', $reports);
    }
}
