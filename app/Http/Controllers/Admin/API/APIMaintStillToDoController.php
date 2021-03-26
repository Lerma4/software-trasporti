<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Expiration;
use App\Models\MaintAlreadyDone;
use App\Models\MaintStillToDo;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class APIMaintStillToDoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getMaint()
    {
        $result = MaintStillToDo::where('companyId', '=', auth('admin')->user()->companyId)
            ->orderBy('km', 'asc');

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
            'km' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'renew' => ['nullable', 'numeric', 'min:1'],
            'notes' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        MaintStillToDo::create([
            'plate' => $request->plate,
            'type' => $request->type,
            'km' => $request->km,
            'renew' => $request->renew,
            'notes' => $request->notes,
            'companyId' => auth('admin')->user()->companyId,
        ]);

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
            'km' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'renew' => ['nullable', 'numeric', 'min:1'],
            'notes' => ['max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $maint = MaintStillToDo::findOrFail($request->id);

        $maint->update([
            'plate' => $request->plate,
            'type' => $request->type,
            'km' => $request->km,
            'renew' => $request->renew,
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
        MaintStillToDo::destroy($request->maint);

        return response()->json(['success' => count($request->maint) . __(' record/s successfully deleted!')]);
    }

    // CONFERMA DELLA ESECUZIONE DI UNA MANUTENZIONE

    public function confirm(Request $request)
    {
        $maint = MaintStillToDo::findOrFail($request->id);
        $truck = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $maint->plate)
            ->first();

        if ($truck->km < $request->km) {
            return response()
                ->json(['errors' => ['I km inseriti sono superiori ai km totali del mezzo']]);
        }

        MaintAlreadyDone::create([
            'date' => $request->date,
            'plate' => $maint->plate,
            'type' => $maint->type,
            'km' => $request->km,
            'garage' => $request->garage,
            'price' => $request->price,
            'notes' => $request->notes,
            'companyId' => auth('admin')->user()->companyId,
        ]);

        if ($maint->renew != NULL) {
            MaintStillToDo::create([
                'plate' => $maint->plate,
                'type' => $maint->type,
                'km' => $maint->renew,
                'renew' => $maint->renew,
                'notes' => $request->notes,
                'companyId' => auth('admin')->user()->companyId,
            ]);
        }

        $maint->delete();

        return response()->json(['success' => __('Successful operation!')]);
    }
}
