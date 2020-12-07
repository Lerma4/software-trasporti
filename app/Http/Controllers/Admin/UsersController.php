<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        $groups = Group::where('companyId', '=', auth('admin')->user()->companyId)->get('name');

        return view('admin.pages.users', ['groups' => $groups]);
    }

    public function importExcel(Request $request)
    {
        $this->validate($request, [
            'import_file'  => 'required|mimes:xls,xlsx'
        ]);

        return back()->withMessage(__('Successful import!'));
    }
}
