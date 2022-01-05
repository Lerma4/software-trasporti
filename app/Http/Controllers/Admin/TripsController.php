<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        $users = User::select('name', 'email', 'id')
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->orderBy('name', 'asc')
            ->get();

        $plates = Truck::select('plate', 'km')
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->where('type', 0)
            ->orWhere('type', 1)
            ->orderBy('plate', 'asc')
            ->get();

        $plates_semi = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('type', 2)
            ->orderBy('plate', 'asc')
            ->get('plate');

        return view('admin.pages.trips', ['plates' => $plates, 'plates_semi' => $plates_semi, 'users' => $users]);
    }
}
