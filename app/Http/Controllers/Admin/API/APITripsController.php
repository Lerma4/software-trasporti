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

    public function create(Request $request)
    {
        // VARIABILI UTILI

        $truck = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('plate', $request->plate)
            ->get();

        $truck_s = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('plate', $request->plate_s)
            ->first();

        $lastTrip = Trip::where('companyId', '=', auth()->user()->companyId)
            ->where('plate', $request->plate)
            ->latest()
            ->first();

        $distance = $request->km - $truck[0]->km;

        $error = '';

        // CHECK

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'exists:users,email', 'email'],
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:0'],
            'fuel' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'plate' => ['required', 'exists:trucks,plate'],
            'plate_s' => ['nullable', 'exists:trucks,plate'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        if ($truck[0]->km >= $request->km) {
            $error = __("Il numero di km inserito è inferiore o uguale a quelli che aveva già il veicolo");
            return response()
                ->json(['errors' => [$error]]);
        } elseif (($request->km - $truck[0]->km) > 1000) {
            $error = __("Il numero di km inserito è troppo elevato rispetto ai km precedenti del veicolo");
            return response()
                ->json(['errors' => [$error]]);
        } elseif ($lastTrip != NULL) {
            if ($request->date < $lastTrip->date) {
                $error = __("Con questo mezzo è già stato inserito un viaggio dopo questa data");
                return response()
                    ->json(['errors' => [$error]]);
            }
        }

        // AGGIORNAMENTO VEICOLI

        $truck[0]->km = $request->km;
        $truck[0]->save();

        if ($truck_s[0] != NULL) {
            $truck_s[0]->km += $distance;
            $truck_s[0]->save();
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
            'companyId' => auth()->user()->companyId,
            'user_email' => $request->email,
            'name' => auth()->user()->name,
            'date' => $request->date,
            'type' => $request->type,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'container' => $request->container,
            'start' => $request->start,
            'destination' => $request->destination,
            'stops' => $stops,
            'km' => $request->km,
            'distance' => $distance,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'note' => $request->note,
        ]);

        return response()->json(['success' => [__('Trip successfully inserted!')]]);
    }
}
