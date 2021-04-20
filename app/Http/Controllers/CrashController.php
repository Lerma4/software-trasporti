<?php

namespace App\Http\Controllers;

use App\Models\Crash;
use App\Models\CrashPhoto;
use App\Models\Truck;
use File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use PDF;
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
                $q->where('type', 'motrice')->orWhere('type', 'trattore');
            })
            ->orderBy('plate', 'asc')
            ->get();

        $plates_semi = Truck::where('companyId', '=', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->where('type', 'semirimorchio')
            ->orderBy('plate', 'asc')
            ->get('plate');

        return view('user.crash', ['plates' => $plates, 'plates_semi' => $plates_semi]);
    }

    public function upload(Request $request)
    {
        $photos = [];
        foreach ($request->upl as $photo) {
            $img = Image::make($photo)->encode(null, 50);
            $filename = auth()->user()->id . 'crash_' . time() . '.' . $photo->getClientOriginalExtension();
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

        $email = auth()->user()->email;
        $name = auth()->user()->name;

        $document = Crash::create([
            'companyId' => auth()->user()->companyId,
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
