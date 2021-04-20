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

class APICrashController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getCrashes()
    {
        $document = Crash::where('companyId', '=', auth('admin')->user()->companyId)
            ->select('name', 'email', 'name', 'date', 'id', 'plate', 'plate_s');

        return DataTables::eloquent($document)
            ->setRowId('id')
            ->make(true);
    }

    public function download($id)
    {
        $crash = Crash::findOrFail($id);
        $data = CrashPhoto::where('crash_id', $id)
            ->get();
        $filename = __('Crash') . '_' . $crash->date->format('d-m-Y') . '_' . $crash->name . '.pdf';

        $pdf = PDF::loadView('pdf.crash', compact('data', 'crash'));
        return $pdf->download($filename);
    }

    public function upload(Request $request)
    {
        $photos = [];
        foreach ($request->upl as $photo) {
            $img = Image::make($photo)->encode(null, 50);
            $filename = auth('admin')->user()->id . 'crash_' . time() . '.' . $photo->getClientOriginalExtension();
            Storage::put($filename, $img);
            Storage::move($filename, 'public/crashes/' . $filename);
            $product_photo = CrashPhoto::create([
                'filename' => 'public/crashes/' . $filename
            ]);
            $photo_object = new \stdClass();
            $photo_object->fileID = $product_photo->id;
            $photos[] = $photo_object;
        }

        return response()->json(array('files' => $photos));
    }

    public function store(Request $request)
    {
        if ($request->file_ids == "") {
            return response()
                ->json(['errors' => [__("Insert at least one photo.")]]);
        }

        $photos = CrashPhoto::whereIn('id', explode(",", $request->file_ids))
            ->orderBy('id', 'asc')
            ->get();

        foreach ($photos as $photo) {
            if (!(File::exists(storage_path('app/' . $photo->filename)))) {
                return response()
                    ->json(['errors' => [__("Upload error, please refresh the page.")]]);
            }
        }

        $user = User::findOrFail($request->user);

        $email = $user->email;
        $name = $user->name;

        $document = Crash::create([
            'companyId' => auth('admin')->user()->companyId,
            'email' => $email,
            'name' => $name,
            'plate' => $request->plate,
            'plate_s' => $request->plate_s,
            'date' => $request->date,
            'description' => $request->description
        ]);

        CrashPhoto::whereIn('id', explode(",", $request->file_ids))
            ->update(['crash_id' => $document->id]);

        return response()->json(['success' => __('Document successfully submitted!')]);
    }
}
