<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Jobs\CreationUserEmailJob;
use App\Models\Company;
use App\Models\License;
use App\Models\User;
use Carbon\Carbon;
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

    public function getUsers($group = NULL)
    {
        $users = User::when($group != NULL && $group != '', function ($query) use ($group) {
            return $query->where('group', $group);
        })
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->with('licenses')
            ->select('id', 'name', 'email', 'group');

        return datatables::eloquent($users)
            ->setRowId('id')
            ->make(true);
    }

    public function getLicenses($group = NULL)
    {
        $licenses = License::where('deadline', '<', Carbon::now()->addDays(16))->distinct('user_id')->get('user_id');

        $user_id = [];

        foreach ($licenses as $license) {
            array_push($user_id, $license->user_id);
        }

        $users = User::when($group != NULL && $group != '', function ($query) use ($group) {
            return $query->where('group', $group);
        })
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->whereIn('id', $user_id)
            ->with('licenses')
            ->select('id', 'name', 'email', 'group');

        return datatables::eloquent($users)
            ->setRowId('id')
            ->make(true);
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:100'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where('companyId', auth('admin')->user()->companyId),
                'max:100'
            ],
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
        $user->password = Hash::make($request->password);
        $user->companyId = auth('admin')->user()->companyId;
        $user->company = auth('admin')->user()->company->name;

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

        $send_mail = $request->email;

        dispatch(new CreationUserEmailJob($send_mail, $request->password, auth('admin')->user()->company->name));

        return response()->json(['success' => __('Form successfully submitted!')]);
    }

    public function editUser(Request $request)
    {
        if ($request->password == NULL) {
            $validator = Validator::make($request->all(), [
                'name' => 'max:100',
                'email' => [
                    'email',
                    Rule::unique('users')
                        ->where('companyId', auth('admin')->user()->companyId)
                        ->ignore($request->id_user),
                    'max:100'
                ]
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'max:100',
                'email' => [
                    'email',
                    Rule::unique('users')->where('companyId', auth('admin')
                        ->user()->companyId)
                        ->ignore($request->id_user),
                    'max:100'
                ],
                'password' => ['required', 'confirmed', 'min:8']
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
        $user = User::findOrfail($request->id_user);
        $user
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
