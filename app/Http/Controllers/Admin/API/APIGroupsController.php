<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class APIGroupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getGroups()
    {
        $groups = Group::where('companyId', '=', auth('admin')->user()->companyId);
        return datatables::eloquent($groups)
            ->setRowId('id')
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => ['max:150'],
            'name' => [
                'required',
                Rule::unique('groups')->where('companyId', auth('admin')->user()->companyId),
                'max:50'
            ],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'companyId' => auth('admin')->user()->companyId,
        ]);

        $group->save();

        return response()->json(['success' => __('Form successfully submitted!')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => ['max:150'],
            'name' => [
                'required',
                Rule::unique('groups')
                    ->where('companyId', auth('admin')->user()->companyId)
                    ->ignore($request->id_group),
                'max:50'
            ],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $group = Group::findOrFail($request->id_group);

        $users = User::where('group', $group->name);
        $trucks = Truck::where('group', $group->name);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $users->update(['group' => $request->name]);
        $trucks->update(['group' => $request->name]);

        return response()->json(['success' => __('Group successfully updated!')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $groups = Group::whereIn('id', $request->groups)->get('name');

        $groupsArray = [];

        foreach ($groups as $group) {
            array_push($groupsArray, $group->name);
        }

        Group::destroy($request->groups);

        $users = User::whereIn('group', $groupsArray);
        $trucks = Truck::whereIn('group', $groupsArray);

        $users->update(['group' => NULL]);
        $trucks->update(['group' => NULL]);

        return response()->json(['success' => count($request->groups) . __(' record/s successfully deleted!')]);
    }
}
