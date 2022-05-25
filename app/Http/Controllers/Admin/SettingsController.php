<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Bitfumes\Multiauth\Model\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function index()
    {
        $company = auth('admin')->user()->company->name;

        return view('admin.settings', ['company' => $company]);
    }

    public function setLang(Request $request)
    {
        $user = Admin::findOrFail(auth('admin')->user()->id);

        $user->update(['language' => $request->lang]);

        return back();
    }

    public function emailChange(Request $request)
    {
        $validator = Admin::where('email', $request->email)
            ->count();

        if ($validator > 0) {
            return response()
                ->json(['errors' => [__('There is another admin with this email!')]]);
        };

        $admin = Admin::findOrFail(auth('admin')->user()->id);

        $admin->email = $request->email;

        $admin->save();

        return response()->json(['success' => __("Email successfully changed!")]);
    }

    public function companyChange(Request $request)
    {
        $validator = Company::where('name', $request->company)
            ->count();

        if ($validator > 0) {
            return response()
                ->json(['errors' => [__('There is another company with this name')]]);
        };

        $company = Company::findOrFail(auth('admin')->user()->companyId);

        $company->update(['name' => $request->company]);

        $users = User::where('companyId', auth('admin')->user()->companyId)
            ->update(['company' => $request->company]);

        return response()->json(['success' => __("Company's name successfully changed!")]);
    }

    public function pswChange(Request $request)
    {
        $user = Admin::findOrFail(auth('admin')->user()->id);

        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()
                ->json(['errors' => [__('The old password is wrong')]]);
        };

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', 'min:8']
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => __('Password successfully changed!')]);
    }
}
