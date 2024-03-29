<?php

namespace App\Http\Controllers;

use App\Jobs\MaintenanceAlertJob;
use App\Mail\MaintenanceAlert;
use App\Models\City;
use App\Models\MaintStillToDo;
use App\Models\Trip;
use App\Models\Truck;
use Bitfumes\Multiauth\Model\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $plates = Truck::select('plate', 'km')
            ->where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where(function ($q) {
                $q->where('type', 0)->orWhere('type', 1);
            })
            ->orderBy('plate', 'asc')
            ->get();

        $plates_semi = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where('type', 2)
            ->orderBy('plate', 'asc')
            ->get('plate');

        return view('user.home', ['plates' => $plates, 'plates_semi' => $plates_semi]);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->search;

        if ($search == '') {
            $employees = City::orderby('name', 'asc')->select('name', 'prov')->limit(5)->get();
        } else {
            $employees = City::orderby('name', 'asc')->select('name', 'prov')->where('name', 'like', $search . '%')->limit(5)->get();
        }

        $response = array();
        foreach ($employees as $employee) {
            $response[] = array("label" => $employee->name . " (" . "$employee->prov" . ")");
        }

        return response()->json($response);
    }

    public function tripMerci(Request $request)
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

        $maintenances = MaintStillToDo::where('companyId', '=', auth()->user()->companyId)
            ->where('plate', $request->plate)
            ->orWhere('plate', $request->plate_s)
            ->get();

        $distance = $request->km - $truck[0]->km;

        $error = '';

        // CHECK

        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:0'],
            'petrol_station' => ['required'],
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

        if ($truck_s != NULL) {
            $truck_s->km += $distance;
            $truck_s->save();
        }

        foreach ($maintenances as $maint) {
            $maint->km -= $distance;
            $maint->save();
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
            'user_email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'date' => $request->date,
            'type' => 0,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'container' => $request->container,
            'start' => $request->start,
            'destination' => $request->destination,
            'stops' => $stops,
            'km' => $request->km,
            'distance' => $distance,
            'petrol_station' => $request->petrol_station,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'adblue' => $request->adblue,
            'adblue_cost' => $request->adblue_cost,
            'note' => $request->note,
        ]);

        // invio mail per controllo manutenzioni

        $admin = Admin::where('companyId', auth()->user()->companyId)
            ->first();

        $maints = MaintStillToDo::where('companyId', auth()->user()->companyId)
            ->where('km', '<', 1000)
            ->where('mail', false)
            ->get();

        if (count($maints) > 0) {
            dispatch(new MaintenanceAlertJob($admin->email, $maints));
            foreach ($maints as $maint) {
                $maint->mail = true;
                $maint->save();
            }
        }

        return response()->json(['success' => [__('Trip successfully inserted!')]]);
    }

    public function tripOfficina(Request $request)
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

        $maintenances = MaintStillToDo::where('companyId', '=', auth()->user()->companyId)
            ->where('plate', $request->plate)
            ->get();

        $distance = $request->km - $truck[0]->km;

        $error = '';

        // CHECK

        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:0'],
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

        if ($truck_s != NULL) {
            $truck_s->km += $distance;
            $truck_s->save();
        }

        foreach ($maintenances as $maint) {
            $maint->km -= $distance;
            $maint->save();
        }

        Trip::create([
            'companyId' => auth()->user()->companyId,
            'user_email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'date' => $request->date,
            'type' => 1,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'start' => $request->start,
            'destination' => $request->destination,
            'garage' => $request->garage,
            'km' => $request->km,
            'distance' => $distance,
            'petrol_station' => $request->petrol_station,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'adblue' => $request->adblue,
            'adblue_cost' => $request->adblue_cost,
            'note' => $request->note,
        ]);

        // invio mail per controllo manutenzioni

        $admin = Admin::where('companyId', auth()->user()->companyId)
            ->first();

        $maints = MaintStillToDo::where('companyId', auth()->user()->companyId)
            ->where('km', '<', 1000)
            ->where('mail', false)
            ->get();

        if (count($maints) > 0) {
            dispatch(new MaintenanceAlertJob($admin->email, $maints));
            foreach ($maints as $maint) {
                $maint->mail = true;
                $maint->save();
            }
        }

        return response()->json(['success' => [__('Trip successfully inserted!')]]);
    }

    public function tripVuoto(Request $request)
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

        $maintenances = MaintStillToDo::where('companyId', '=', auth()->user()->companyId)
            ->where('plate', $request->plate)
            ->get();

        $distance = $request->km - $truck[0]->km;

        $error = '';

        // CHECK

        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'start' => ['required', 'max:40'],
            'destination' => ['required', 'max:40'],
            'km' => ['required', 'numeric', 'min:0'],
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

        if ($truck_s != NULL) {
            $truck_s->km += $distance;
            $truck_s->save();
        }

        foreach ($maintenances as $maint) {
            $maint->km -= $distance;
            $maint->save();
        }

        Trip::create([
            'companyId' => auth()->user()->companyId,
            'user_email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'date' => $request->date,
            'type' => 2,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'start' => $request->start,
            'destination' => $request->destination,
            'km' => $request->km,
            'distance' => $distance,
            'petrol_station' => $request->petrol_station,
            'fuel' => $request->fuel,
            'cost' => $request->cost,
            'adblue' => $request->adblue,
            'adblue_cost' => $request->adblue_cost,
            'note' => $request->note,
        ]);

        // invio mail per controllo manutenzioni

        $admin = Admin::where('companyId', auth()->user()->companyId)
            ->first();

        $maints = MaintStillToDo::where('companyId', auth()->user()->companyId)
            ->where('km', '<', 1000)
            ->where('mail', false)
            ->get();

        if (count($maints) > 0) {
            dispatch(new MaintenanceAlertJob($admin->email, $maints));
            foreach ($maints as $maint) {
                $maint->mail = true;
                $maint->save();
            }
        }

        return response()->json(['success' => [__('Trip successfully inserted!')]]);
    }
}
