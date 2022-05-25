<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DocumentsController extends Controller
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
        $users = User::where('companyId', auth('admin')->user()->companyId)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.pages.documents', ['users' => $users]);
    }
}
