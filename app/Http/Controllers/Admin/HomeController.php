<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Crash;
use App\Models\MaintAlreadyDone;
use App\Models\MaintStillToDo;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $crashes = Crash::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '>', Carbon::now()->subDays(30))
            ->get()->count();
        $oldCrashes = Crash::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '<', Carbon::now()->subDays(30))
            ->where('date', '>', Carbon::now()->subDays(60))
            ->get()->count();
        $difCrashes = $crashes - $oldCrashes;

        $trips = Trip::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '>', Carbon::now()->subDays(30))
            ->get()->count();
        $oldTrips = Trip::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '<', Carbon::now()->subDays(30))
            ->where('date', '>', Carbon::now()->subDays(60))
            ->get()->count();
        $difTrips = $trips - $oldTrips;

        $maint = MaintAlreadyDone::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '>', Carbon::now()->subDays(30))
            ->sum('price');
        $oldMaint = MaintAlreadyDone::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '<', Carbon::now()->subDays(30))
            ->where('date', '>', Carbon::now()->subDays(60))
            ->sum('price');
        $difMaint = $maint - $oldMaint;

        $fuel = Trip::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '>', Carbon::now()->subDays(30))
            ->sum('cost');
        $oldFuel = Trip::where('companyId', auth('admin')->user()->companyId)
            ->where('date', '<', Carbon::now()->subDays(30))
            ->where('date', '>', Carbon::now()->subDays(60))
            ->sum('cost');
        $difFuel = $fuel - $oldFuel;

        return view(
            'admin.pages.home',
            [
                'crashes' => $crashes,
                'difCrashes' => $difCrashes,
                'trips' => $trips,
                'difTrips' => $difTrips,
                'fuel' => $fuel,
                'difFuel' => $difFuel,
                'maint' => $maint,
                'difMaint' => $difMaint
            ]
        );
    }
}
