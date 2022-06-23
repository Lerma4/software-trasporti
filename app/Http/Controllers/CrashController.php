<?php

namespace App\Http\Controllers;

use App\Models\Crash;
use App\Models\CrashPhoto;
use App\Models\Truck;
use File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use PDF;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class CrashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $plates = Truck::select('plate', 'km')
            ->where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where(function ($q) {
                $q->where('type', '1')->orWhere('type', '0');
            })
            ->orderBy('plate', 'asc')
            ->get();

        $plates_semi = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where('type', '2')
            ->orderBy('plate', 'asc')
            ->get('plate');

        return view('user.crash', ['plates' => $plates, 'plates_semi' => $plates_semi]);
    }

    public function store(Request $request)
    {
        if (!isset($request->photos)) {
            return back()->withErrors([__('Insert at least one photo!')]);
        }

        $crash = Crash::create([
            'companyId' => auth()->user()->companyId,
            'email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'date' => $request->date,
            'description' => $request->description
        ]);

        $crash->addFromMediaLibraryRequest($request->photos)
            ->toMediaCollection('pdf_temp');

        $data = Media::where('model_id', $crash->id)
            ->where('collection_name', 'pdf_temp')
            ->get();

        foreach ($data as $photo) {
            $image = Image::make($photo->getPath());
            $image->save($photo->getPath(), 20);
        }

        $pdf = PDF::loadView('pdf.crash', compact('crash', 'data'))->save('pdf_temp/' . $data[0]->id . '.pdf');

        foreach ($data as $img) {
            $img->delete();
        }

        $crash->addMedia('pdf_temp/' . $data[0]->id . '.pdf')
            ->usingName($request->date . '_' . $request->plate . '_' . auth()->user()->name)
            ->toMediaCollection('crash'); // oltre ad associare il PDF allo User, cancella anche il PDF nella cartella in cui era prima

        return back()->with('message', __('Document successfully submitted!'));
    }
}
