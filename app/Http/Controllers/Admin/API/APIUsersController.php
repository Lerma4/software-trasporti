<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class APIUsersController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        $users = User::with('licenses')->select('id', 'name', 'email', 'group');
        return datatables::eloquent($users)
            ->setRowId('id')
            ->make(true);
    }
}