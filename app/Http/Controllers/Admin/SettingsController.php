<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return view('admin.settings');
    }

    public function setLang(Request $request)
    {
        $user = Admin::findOrFail(auth('admin')->user()->id);

        $user->update(['language' => $request->lang]);

        return back();
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
