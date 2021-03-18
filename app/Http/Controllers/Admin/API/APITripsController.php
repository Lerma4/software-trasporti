<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class APITripsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getTrips($dateTo = null, $dateFrom = null)
    {
        if ($dateTo == 'nullValue') {
            $dateTo = null;
        };
        if ($dateFrom == 'nullValue') {
            $dateFrom = null;
        };

        $result = Trip::when($dateFrom != null && $dateFrom != '', function ($query) use ($dateFrom) {
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

    public function autocompleteCity(Request $request)
    {
        $search = $request->search;

        if ($search == '') {
            $cities = City::orderby('name', 'asc')->select('name', 'prov')->limit(5)->get();
        } else {
            $cities = City::orderby('name', 'asc')->select('name', 'prov')->where('name', 'like', $search . '%')->limit(5)->get();
        }

        $response = array();
        foreach ($cities as $city) {
            $response[] = array("label" => $city->name . " (" . "$city->prov" . ")");
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        // VARIABILI UTILI

        $truck = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->first();

        $truck_s = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate_s)
            ->first();

        $nextTrip = Trip::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->where('date', '>', $request->date)
            ->first();

        $user = User::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('email', $request->email)
            ->first();

        $error = '';

        // CHECK

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'exists:users,email', 'email'],
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:1'],
            'fuel' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'plate' => ['required', 'exists:trucks,plate'],
            'plate_s' => ['nullable', 'exists:trucks,plate'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        if ($request->km > 1000) {
            $error = __("Il numero di km inserito non è valido");
            return response()
                ->json(['errors' => [$error]]);
        }

        // CASO 1 : IL VIAGGIO INSERITO è L'ULTIMO

        if ($nextTrip == NULL) {
            $truck->km += $request->km;
            $truck->save();
            $km = $truck->km;
        }

        // CASO 2 : IL VIAGGIO INSERITO NON è L'ULTIMO

        if ($nextTrip != NULL) {
            if ($request->km >= $nextTrip->distance) {
                $error = __("Il numero di km inserito non è valido (km troppo elevati rispetto al viaggio successivo)");
                return response()
                    ->json(['errors' => [$error]]);
            }
            $nextTrip->distance -= $request->km;
            $nextTrip->save();
            $km = $nextTrip->km - $nextTrip->distance;
        }

        // AGGIORNAMENTO SEMIRIMORCHIO

        if ($request->plate_s != NULL) {
            $truck_s->km += $request->km;
            $truck_s->save();
        }

        // CREAZIONE VIAGGIO

        $i = 1;
        $stops = '';
        while (request('stop_' . $i) != NULL) {
            if ($i == 1) {
                $stops .= request('stop_' . $i);
                $i++;
            } else {
                $stops .= ' , ' . request('stop_' . $i);
                $i++;
            }
        }

        Trip::create([
            'companyId' => auth('admin')->user()->companyId,
            'user_email' => $request->email,
            'name' => $user->name,
            'date' => $request->date,
            'type' => $request->type,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'container' => $request->container,
            'garage' => $request->garage,
            'start' => $request->start,
            'destination' => $request->destination,
            'stops' => $stops,
            'km' => $km,
            'distance' => $request->km,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'note' => $request->note,
        ]);

        return response()->json(['success' => [__('Trip successfully inserted!')]]);
    }

    public function edit(Request $request)
    {
    }

    public function delete(Request $request)
    {
        $trips = Trip::whereIn('id', $request->trips)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($trips as $trip) {
            $nextTrip = Trip::where('companyId', '=', auth('admin')->user()->companyId)
                ->where('plate', $trip->plate)
                ->where('date', '>=', $trip->date)
                ->where('km', '>', $trip->km)
                ->first();

            if ($nextTrip == NULL) {
                $truck = Truck::where('companyId', '=', auth('admin')->user()->companyId)
                    ->where('plate', $trip->plate)
                    ->first();
                $truck->km -= $trip->distance;
                $truck->save();
            } else {
                $nextTrip->distance += $trip->distance;

                $nextTrip->save();
            }

            // aggiorno il semirimorchio

            if ($trip->plate_s != NULL) {
                $semi = Truck::where('companyId', '=', auth('admin')->user()->companyId)
                    ->where('plate', $trip->plate_s)
                    ->first();
                $semi->km -= $trip->distance;
                $semi->save();
            }
        }

        Trip::destroy($request->trips);

        return response()->json(['success' => count($request->trips) . __(' trip/s successfully deleted!')]);
    }
}
