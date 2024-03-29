<?php

namespace App\Http\Controllers\Admin\API;

use App\Exports\UserWorkingDaysExport;
use App\Exports\WorkingDaysExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\MaintStillToDo;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Excel;
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

        $maintenances = MaintStillToDo::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->get();

        $error = '';

        // CHECK

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'exists:users,email', 'email'],
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:1'],
            'petrol_station' => ['required', 'max:40'],
            'fuel' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'adblue' => ['required', 'numeric', 'min:0'],
            'adblue_cost' => ['required', 'numeric', 'min:0'],
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

            // AGGIORNAMENTO MANUTENZIONI

            foreach ($maintenances as $maint) {
                $maint->km -= $request->km;
                $maint->save();
            }
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
            'petrol_station' => $request->petrol_station,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'adblue' => $request->adblue,
            'adblue_cost' => $request->adblue_cost,
            'note' => $request->note,
        ]);

        return response()->json(['success' => [__('Trip successfully inserted!')]]);
    }

    public function edit(Request $request)
    {
        // VARIABILI UTILI

        $truck = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->first();

        $truck_s = Truck::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate_s)
            ->first();

        $thisTrip = Trip::findOrFail($request->id);

        $nextTrip = Trip::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->where('date', '>=', $request->date)
            ->where('km', '>', $thisTrip->km)
            ->first();

        $user = User::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('email', $request->email)
            ->first();

        $maintenances = MaintStillToDo::where('companyId', '=', auth('admin')->user()->companyId)
            ->where('plate', $request->plate)
            ->get();

        $error = '';

        $kmDifference = $thisTrip->distance - $request->km;

        // CHECK

        if ($truck === null) {
            return response()->json(['errors' => [__('Non è possibile modificare un veicolo che non è più presente nella sezione "Veicoli"!')]]);
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'exists:users,email', 'email'],
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:1'],
            'petrol_station' => ['required', 'max:40'],
            'fuel' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'adblue' => ['required', 'numeric', 'min:0'],
            'adblue_cost' => ['required', 'numeric', 'min:0'],
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
            $truck->km -= $kmDifference;
            $truck->save();
            $km = $truck->km;

            // AGGIORNAMENTO MANUTENZIONI

            foreach ($maintenances as $maint) {
                $maint->km += $kmDifference;
                $maint->save();
            }
        }

        // CASO 2 : IL VIAGGIO INSERITO NON è L'ULTIMO

        if ($nextTrip != NULL) {
            if ($kmDifference >= $nextTrip->distance) {
                $error = __("Il numero di km inserito non è valido (km troppo elevati rispetto al viaggio successivo)");
                return response()
                    ->json(['errors' => [$error]]);
            }
            $nextTrip->distance += $kmDifference;
            $nextTrip->save();
            $km = $thisTrip->km - $kmDifference;
        }

        // AGGIORNAMENTO SEMIRIMORCHIO

        if ($request->plate_s != NULL) {
            $truck_s->km -= $kmDifference;
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

        $thisTrip->update([
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
            'petrol_station' => $request->petrol_station,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'adblue' => $request->adblue,
            'adblue_cost' => $request->adblue_cost,
            'note' => $request->note,
        ]);

        return response()->json(['success' => [__('Trip successfully edited!')]]);
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

                if ($truck != NULL) {
                    $truck->km -= $trip->distance;
                    $truck->save();
                }

                // AGGIORNAMENTO MANUTENZIONI

                $maintenances = MaintStillToDo::where('companyId', '=', auth('admin')->user()->companyId)
                    ->where('plate', $trip->plate)
                    ->get();

                foreach ($maintenances as $maint) {
                    $maint->km += $trip->distance;
                    $maint->save();
                }
            } else {
                $nextTrip->distance += $trip->distance;
                $nextTrip->save();
            }

            // aggiorno il semirimorchio

            if ($trip->plate_s != NULL) {
                $semi = Truck::where('companyId', '=', auth('admin')->user()->companyId)
                    ->where('plate', $trip->plate_s)
                    ->first();

                if ($semi != NULL) {
                    $semi->km -= $trip->distance;
                    $semi->save();
                }
            }
        }

        Trip::destroy($request->trips);

        return response()->json(['success' => count($request->trips) . __(' trip/s successfully deleted!')]);
    }

    public function export(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $giorniDelMese = 31;

        $viaggi = Trip::select('user_email')
            ->distinct('user_email')
            ->where('companyId', auth('admin')->user()->companyId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        if ($viaggi->count() == 0) {
            return redirect(route('admin.trips'))->with('error', "Nessun autista ha lavorato il mese selezionato");
        };

        return Excel::download(new WorkingDaysExport($month, $year, $giorniDelMese), __('workingDays_') . $month . '_' . $year . '.xlsx');
    }

    public function exportUser(Request $request)
    {
        $user = User::findOrFail($request->id);
        $viaggi = Trip::where('user_email', $user->email)
            ->where('date', '>=', $request->from)
            ->where('date', '<=', $request->to)
            ->get();


        if ($viaggi->count() == 0) {
            return redirect(route('admin.trips'))->with('error', "L'autista non ha lavorato in questo periodo");
        };

        return Excel::download(new UserWorkingDaysExport($user, $request->from, $request->to), __('workingDays_') . $user->name . '_' . '.xlsx');
    }
}
