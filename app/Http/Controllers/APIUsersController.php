<?php

namespace App\Http\Controllers;

use App\Models\User;
use DataTables;

class APIUsersController extends Controller
{
    public function getUsers()
    {
        $query = User::select('name', 'email', 'group');
        return DataTables::of($query)->make(true);
    }
}
