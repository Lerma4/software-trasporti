<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\Request;

class MaintenancesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        $trucks = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->distinct()
            ->get('plate');

        return view('admin.pages.maintenances', ['trucks' => $trucks]);
    }
}
