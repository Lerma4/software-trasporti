<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $plates = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where('type', 'motrice')
            ->orWhere('type', 'trattore')
            ->orderBy('plate', 'asc')
            ->get('plate');

        $plates_semi = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where('type', 'semirimorchio')
            ->orderBy('plate', 'asc')
            ->get('plate');

        return view('user.home', ['plates' => $plates, 'plates_semi' => $plates_semi]);
    }

    public function tripMerci(Request $request)
    {
        // CHECK ERRORI

        $i = 1;
        $stops = [];
        while (request('stop_' . $i) != NULL) {
            array_push($stops, request('stop_' . $i));
            $i++;
        }

        // CREARE IL VIAGGIO
    }
}
