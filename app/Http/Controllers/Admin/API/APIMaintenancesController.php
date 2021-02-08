<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Expiration;
use App\Models\Maintenance;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class APIMaintenancesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getMaint($dateTo = null, $dateFrom = null)
    {
        if ($dateTo == 'nullValue') {
            $dateTo = null;
        };
        if ($dateFrom == 'nullValue') {
            $dateFrom = null;
        };

        $result = Maintenance::when($dateTo != null && $dateTo != '', function ($query) use ($dateTo) {
            return $query->where('date', '<', $dateTo);
        })
            ->when($dateFrom != null && $dateFrom != '', function ($query) use ($dateFrom) {
                return $query->where('date', '>', $dateFrom);
            })
            ->where('companyId', '=', auth('admin')->user()->companyId);

        return DataTables::eloquent($result)
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
                'max:30'
            ],
            'type' => ['required', 'max:30'],
            'date' => ['required'],
            'description' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $maint = Maintenance::create([
            'plate' => $request->plate,
            'type' => $request->type,
            'garage' => $request->garage,
            'price' => $request->price,
            'km' => $request->km,
            'date' => $request->date,
            'description' => $request->description,
            'period' => $request->period,
            'alert' => $request->alert,
            'companyId' => auth('admin')->user()->companyId,
        ]);

        $maint->save();

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
            'group' => ['exists:groups,name'],
            'description' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }


        $truck = Truck::findOrFail($request->id_truck);

        $truck->update([
            'plate' => $request->plate,
            'type' => $request->type,
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
        Maintenance::destroy($request->maint);

        return response()->json(['success' => count($request->maint) . __(' record/s successfully deleted!')]);
    }
}
