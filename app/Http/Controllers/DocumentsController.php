<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use PDF;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentsController extends Controller
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
        return view('user.documents');
    }

    public function getDocuments()
    {
        $document = Document::where('companyId', '=', auth()->user()->companyId)
            ->where('user_email', '=', auth()->user()->email)
            ->select('name', 'created_at', 'id', 'read');

        return datatables::eloquent($document)
            ->setRowId('id')
            ->make(true);
    }

    public function storePdf(Request $request)
    {
        if (!isset($request->pdf)) {
            return back()->withErrors([__('Insert at least one photo or PDF!')]);
        }

        $doc = Document::create([
            'name' => $request->name,
            'user_email' => auth()->user()->email,
            'user_name' => auth()->user()->name,
            'companyId' => auth()->user()->companyId,
            'read' => true
        ]);

        $doc->addFromMediaLibraryRequest($request->pdf)
            ->toMediaCollection('pdf');

        return back()->with('message', __('Document successfully submitted!'));
    }

    public function storePhotos(Request $request)
    {
        if (!isset($request->photos)) {
            return back()->withErrors([__('Insert at least one photo or PDF!')]);
        }

        $doc = Document::create([
            'name' => $request->name,
            'user_email' => auth()->user()->email,
            'user_name' => auth()->user()->name,
            'companyId' => auth()->user()->companyId,
            'read' => true
        ]);

        $doc->addFromMediaLibraryRequest($request->photos)
            ->toMediaCollection('pdf_temp');

        $data = Media::where('model_id', $doc->id)
            ->where('collection_name', 'pdf_temp')
            ->get();

        $pdf = PDF::loadView('pdf.document', compact('data'))->save('pdf_temp/' . $data[0]->id . '.pdf');

        foreach ($data as $img) {
            $img->delete();
        }

        $doc->addMedia('pdf_temp/' . $data[0]->id . '.pdf')
            ->usingName($request->name)
            ->toMediaCollection('pdf'); // oltre ad associare il PDF allo User, cancella anche il PDF nella cartella in cui era prima

        return back()->with('message', __('Document successfully submitted!'));
    }

    public function download($id)
    {
        $doc = Document::find($id);

        $doc->read = true;
        $doc->save();

        $media = Media::where('model_type', 'App\Models\Document')
            ->where('model_id', $id)
            ->where('collection_name', 'pdf')
            ->get();

        return response()->download($media[0]->getPath(), $doc->name . '.pdf');
    }
}
