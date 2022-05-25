<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        return view('admin.pages.reports');
    }
}
