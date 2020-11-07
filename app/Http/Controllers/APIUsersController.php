<?php

namespace App\Http\Controllers;

use App\Models\User;

class APIUsersController extends Controller
{
    public function getUsers()
    {
        $query = User::select('name', 'email', 'group');
        return datatables($query)->make(true);
    }
}
