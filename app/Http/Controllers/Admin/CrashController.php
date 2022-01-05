<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;

class CrashController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        $plates = Truck::select('plate', 'km')
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->where(function ($q) {
                $q->where('type', 'like', 'motrice%')->orWhere('type', 'trattore');
            })
            ->orderBy('plate', 'asc')
            ->get();

        $plates_semi = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('type', 'like', 'semirimorchio%')
            ->orderBy('plate', 'asc')
            ->get('plate');

        $users = User::where('companyId', '=', auth('admin')->user()->companyId)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.pages.crash', ['plates' => $plates, 'plates_semi' => $plates_semi, 'users' => $users]);
    }
}
