<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Expiration;
use App\Models\MaintAlreadyDone;
use App\Models\MaintStillToDo;
use App\Models\Trip;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class APITrucksController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getTrucks($group = null, $type = null)
    {
        if ($group == 'nullValue') {
            $group = null;
        };
        if ($type == 'nullValue') {
            $type = null;
        };

        $groups = Truck::when($group != null && $group != '', function ($query) use ($group) {
            return $query->where('group', $group);
        })
            ->when($type != null && $type != '', function ($query) use ($type) {
                return $query->where('type', 'like', $type . '%');
            })
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->with('expirations');
        return DataTables::eloquent($groups)
            ->setRowId('id')
            ->make(true);
    }

    public function getExpirations($group = NULL, $type = NULL)
    {
        if ($group == 'nullValue') {
            $group = null;
        };
        if ($type == 'nullValue') {
            $type = null;
        };

        $exps = Expiration::where('deadline', '<', Carbon::now()->addDays(16))->distinct('truck_id')->get('truck_id');

        $truck_id = [];

        foreach ($exps as $exp) {
            array_push($truck_id, $exp->truck_id);
        }

        $groups = Truck::when($group != NULL && $group != '', function ($query) use ($group) {
            return $query->where('group', $group);
        })
            ->when($type != NULL && $type != '', function ($query) use ($type) {
                return $query->where('type', 'like', $type . '%');
            })
            ->where('companyId', '=', auth('admin')->user()->companyId)
            ->whereIn('id', $truck_id)
            ->with('expirations');
        return DataTables::eloquent($groups)
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
            'plate' => [
                'required',
                Rule::unique('trucks')->where('companyId', auth('admin')->user()->companyId),
                'max:30'
            ],
            'type' => ['required', 'max:30'],
            'km' => ['required'],
            'chassis' => ['max:30'],
            'brand' => ['max:30'],
            'model' => ['max:30'],
            'group' => ['exists:groups,name', 'nullable'],
            'description' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        // NON USO IL VALIDATOR PERCHE' MI DA ERRORE

        $i = 1;

        $checkExp = [];

        while (request('expiration_' . $i) != NULL) {
            array_push($checkExp, request('expiration_' . $i));
            $i++;
        }

        if (count($checkExp) != count(array_unique($checkExp))) {
            return response()
                ->json(['errors' => [__('Two or more expirations have the same name')]]);
        }

        // FINE CONTROLLI

        $truck = Truck::create([
            'plate' => $request->plate,
            'type' => $request->type,
            'chassis' => $request->chassis,
            'brand' => $request->brand,
            'model' => $request->model,
            'km' => $request->km,
            'group' => $request->group,
            'description' => $request->description,
            'companyId' => auth('admin')->user()->companyId,
        ]);

        $truck->save();

        $i = 1;

        while (request('expiration_' . $i) != NULL) {
            $expiration = new Expiration;

            $expiration->name = request('expiration_' . $i);
            $expiration->description = request('description_' . $i);
            $expiration->deadline = request('deadline_' . $i);
            $expiration->truck_id = $truck->id;

            $expiration->save();

            $i++;
        }

        return response()->json(['success' => __('Form successfully submitted!')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show()
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
            'plate' => [
                'required',
                Rule::unique('trucks')
                    ->where('companyId', auth('admin')->user()->companyId)
                    ->ignore($request->id_truck),
                'max:30'
            ],
            'type' => ['required', 'max:30'],
            'km' => ['required'],
            'brand' => ['max:30'],
            'model' => ['max:30'],
            'description' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $truck = Truck::findOrFail($request->id_truck);

        $old_truck = $truck->plate;

        $truck->update([
            'plate' => $request->plate,
            'type' => $request->type,
            'chassis' => $request->chassis,
            'brand' => $request->brand,
            'model' => $request->model,
            'km' => $request->km,
            'group' => $request->group,
            'description' => $request->description,
            'companyId' => auth('admin')->user()->companyId,
        ]);

        Expiration::where('truck_id', $request->id_truck)->delete();

        $i = 1;

        while (request('expiration_' . $i) != NULL) {
            $expiration = new Expiration;

            $expiration->name = request('expiration_' . $i);
            $expiration->description = request('description_' . $i);
            $expiration->deadline = request('deadline_' . $i);
            $expiration->truck_id = $truck->id;

            $expiration->save();

            $i++;
        }

        //aggiorno manutenzioni e viaggi

        if ($old_truck != $request->plate) {
            MaintStillToDo::where('plate', $old_truck)
                ->update([
                    'plate' => $request->plate,
                ]);

            MaintAlreadyDone::where('plate', $old_truck)
                ->update([
                    'plate' => $request->plate,
                ]);

            Trip::where('plate', $old_truck)
                ->update([
                    'plate' => $request->plate,
                ]);

            Trip::where('plate_s', $old_truck)
                ->update([
                    'plate_s' => $request->plate,
                ]);
        }

        return response()->json(['success' => __('Truck successfully updated!')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
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
        $trucks_plates = [];
        $trucks = Truck::whereIn('id', $request->trucks)->get();
        foreach ($trucks as $truck) {
            array_push($trucks_plates, $truck->plate);
        };

        Truck::destroy($request->trucks);
        Expiration::whereIn('truck_id', $request->trucks)->delete();
        MaintStillToDo::where('companyId', auth('admin')->user()->companyId)
            ->whereIn('plate', $trucks_plates)
            ->delete();

        return response()->json(['success' => count($request->trucks) . __(' record/s successfully deleted!')]);
    }
}
