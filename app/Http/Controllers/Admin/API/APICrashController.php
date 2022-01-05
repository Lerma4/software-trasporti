<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Crash;
use App\Models\CrashPhoto;
use App\Models\User;
use File;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Storage;
use PDF;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class APICrashController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getCrashes()
    {
        $document = Crash::where('companyId', '=', auth('admin')->user()->companyId)
            ->select('name', 'email', 'name', 'date', 'id', 'plate', 'plate_s', 'description', 'read');

        return DataTables::eloquent($document)
            ->setRowId('id')
            ->make(true);
    }

    public function download($id)
    {
        $doc = Crash::find($id);

        $doc->read = true;
        $doc->save();

        $media = Media::where('model_type', 'App\Models\Crash')
            ->where('model_id', $id)
            ->where('collection_name', 'crash')
            ->get();

        return response()->download($media[0]->getPath(), $doc->name . '.pdf');
    }

    public function store(Request $request)
    {
        if (!isset($request->photos)) {
            return back()->withErrors([__('Insert at least one photo!')]);
        }

        $user = User::findOrFail($request->user);

        $email = $user->email;
        $name = $user->name;

        $crash = Crash::create([
            'companyId' => auth('admin')->user()->companyId,
            'email' => $email,
            'name' => $name,
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

        $pdf = PDF::loadView('pdf.crash', compact('crash', 'data'))->save('pdf_temp/' . $data[0]->id . '.pdf');

        foreach ($data as $img) {
            $img->delete();
        }

        $crash->addMedia('pdf_temp/' . $data[0]->id . '.pdf')
            ->usingName($request->date . '_' . $request->plate . '_' . $user->name)
            ->toMediaCollection('crash'); // oltre ad associare il PDF allo User, cancella anche il PDF nella cartella in cui era prima

        return back()->with('message', __('Document successfully submitted!'));
    }

    public function delete(Request $request)
    {
        foreach ($request->ids as $id) {
            Crash::find($id)->delete();
        }

        return response()->json(['success' => __('Document/s successfully deleted!')]);
    }

    public function edit(Request $request)
    {
        $user = User::where('email', $request->email)->get();
        $plate_s = $request->plate_s;

        if ($plate_s == '') {
            $plate_s = null;
        }

        Crash::where('id', $request->id)
            ->update([
                'email' => $request->email,
                'name' => $user[0]->name,
                'date' => $request->date,
                'plate' => $request->plate,
                'plate_s' => $plate_s
            ]);

        return response()->json(['success' => __('Document/s successfully edited!')]);
    }
}
