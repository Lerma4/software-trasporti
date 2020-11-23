<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class APIUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getUsers()
    {
        $users = User::where('companyId', '=', auth('admin')->user()->companyId)->with('licenses')->select('id', 'name', 'email', 'group');
        return datatables::eloquent($users)
            ->setRowId('id')
            ->make(true);
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users', 'max:100'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        // NON USO IL VALIDATOR PERCHE' MI DA ERRORE

        $i = 1;

        $checkLicense = [];

        while (request('license_' . $i) != NULL) {
            array_push($checkLicense, request('license_' . $i));
            $i++;
        }

        if (count($checkLicense) != count(array_unique($checkLicense))) {
            return response()
                ->json(['errors' => [__('Two or more licenses have the same name')]]);
        }

        $user = new User;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->group = $request->group;
        $user->password = Hash::make($request->newPassword);
        $user->companyId = auth('admin')->user()->companyId;

        $user->save();

        $i = 1;

        while (request('license_' . $i) != NULL) {
            $license = new License;

            $license->name = request('license_' . $i);
            $license->deadline = request('deadline_' . $i);
            $license->user_id = $user->id;

            $license->save();

            $i++;
        }

        return response()->json(['success' => __('Form successfully submitted!')]);
    }

    public function editUser(Request $request)
    {
        if ($request->password == NULL) {
            $validator = Validator::make($request->all(), [
                'name' => 'max:100',
                'email' => ['email', Rule::unique('users')->ignore($request->id_user), 'max:100']
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'max:100',
                'email' => ['email', Rule::unique('users')->ignore($request->id_user), 'max:100'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
        }

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        // NON USO IL VALIDATOR PERCHE' MI DA ERRORE

        $i = 1;

        $checkLicense = [];

        while (request('license_' . $i) != NULL) {
            array_push($checkLicense, request('license_' . $i));
            $i++;
        }

        if (count($checkLicense) != count(array_unique($checkLicense))) {
            return response()
                ->json(['errors' => [__('Two or more licenses have the same name')]]);
        }

        $user = User::findOrfail($request->id_user)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'group' => $request->group
            ]);

        if ($request->password != NULL) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $i = 1;

        License::where('user_id', $request->id_user)->delete();

        while (request('license_' . $i) != NULL) {
            $license = new License;

            $license->name = request('license_' . $i);
            $license->deadline = request('deadline_' . $i);
            $license->user_id = $request->id_user;

            $license->save();

            $i++;
        }

        return response()->json(['success' => __('User successfully updated!')]);
    }

    public function deleteUser(Request $request)
    {
        User::destroy($request->users);
        License::whereIn('user_id', $request->users)->delete();

        return response()->json(['success' => count($request->users) . __(' record/s successfully deleted!')]);
    }
}
