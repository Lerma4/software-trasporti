<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Truck;
use Illuminate\Http\Request;

class TrucksController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        $groups = Group::where('companyId', '=', auth('admin')->user()->companyId)->get('name');

        return view('admin.pages.trucks', ['groups' => $groups]);
    }
}
