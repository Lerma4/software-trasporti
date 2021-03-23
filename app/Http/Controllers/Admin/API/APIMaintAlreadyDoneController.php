<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Expiration;
use App\Models\MaintAlreadyDone;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class APIMaintAlreadyDoneController extends Controller
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

        $result = MaintAlreadyDone::when($dateFrom != null && $dateFrom != '', function ($query) use ($dateFrom) {
            return $query->where(function ($query) use ($dateFrom) {
                $query->where('date', '>', $dateFrom)
                    ->orwhere('date', $dateFrom);
            });
        })
            ->when($dateTo != null && $dateTo != '', function ($query) use ($dateTo) {
                return $query->where(function ($query) use ($dateTo) {
                    $query->where('date', '<', $dateTo)
                        ->orwhere('date', $dateTo);
                });
            })
            ->where('companyId', '=', auth('admin')->user()->companyId);

        return DataTables::eloquent($result)
            ->setRowId('id')
            ->make(true);
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
            'km' => ['nullable', 'numeric', 'min:1'],
            'notes' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $truck = Truck::where('companyId', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->first();

        if ($truck->km < $request->km) {
            return response()
                ->json(['errors' => [__("I km inseriti sono superiori a quelli attuali del mezzo")]]);
        }

        $maint = MaintAlreadyDone::create([
            'date' => $request->date,
            'plate' => $request->plate,
            'type' => $request->type,
            'km' => $request->km,
            'garage' => $request->garage,
            'price' => $request->price,
            'notes' => $request->notes,
            'companyId' => auth('admin')->user()->companyId,
        ]);

        $maint->save();

        return response()->json(['success' => __('Form successfully submitted!')]);
    }

    /**
     * Edit
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plate' => [
                'required',
                'max:30'
            ],
            'type' => ['required', 'max:30'],
            'date' => ['required'],
            'km' => ['nullable', 'numeric', 'min:1'],
            'notes' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $truck = Truck::where('companyId', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->first();

        if ($truck->km < $request->km) {
            return response()
                ->json(['errors' => [__("I km inseriti sono superiori a quelli attuali del mezzo")]]);
        }

        $maint = MaintAlreadyDone::findOrFail($request->id);

        $maint->update([
            'date' => $request->date,
            'plate' => $request->plate,
            'type' => $request->type,
            'km' => $request->km,
            'garage' => $request->garage,
            'price' => $request->price,
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => __('Record successfully updated!')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        MaintAlreadyDone::destroy($request->maint);

        return response()->json(['success' => count($request->maint) . __(' record/s successfully deleted!')]);
    }
}
