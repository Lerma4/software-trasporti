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
            ->select('name', 'created_at', 'id');

        return datatables::eloquent($document)
            ->setRowId('id')
            ->make(true);
    }

    public function upload(Request $request)
    {
        $photos = [];
        foreach ($request->upl as $photo) {
            $img = Image::make($photo)->encode(null, 50);
            $filename = auth()->user()->id . 'doc_' . time() . '.' . $photo->getClientOriginalExtension();
            Storage::put($filename, $img);
            Storage::move($filename, 'photos/' . $filename);
            $product_photo = DocumentFile::create([
                'filename' => $filename
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

        $oldDocuments = Document::where('companyId', auth()->user()->companyId)
            ->where('user_email', auth()->user()->email)
            ->where('name', $request->name)
            ->count();

        if ($oldDocuments != 0) {
            return response()
                ->json(['errors' => [__("Already exists a document with this name.")]]);
        }

        $name = str_replace(' ', '', request('name'));

        $document = Document::create([
            'companyId' => auth()->user()->companyId,
            'user_email' => auth()->user()->email,
            'user_name' => auth()->user()->name,
            'name' => $name
        ]);

        // CREAZIONE PDF

        $data = DocumentFile::whereIn('id', explode(",", $request->file_ids))
            ->orderBy('id', 'asc')
            ->get();

        $pdf = PDF::loadView('pdf.document', compact('data'));
        $content = $pdf->download()->getOriginalContent();
        $filename = "public/documents/" . auth()->user()->id . $name . ".pdf";

        foreach ($data as $photo) {
            $path = "photos/" . $photo->filename;
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        DocumentFile::whereIn('id', explode(",", $request->file_ids))
            ->delete(['document_id' => $document->id]);

        Storage::put($filename, $content);

        DocumentFile::create([
            'filename' => $filename,
            'document_id' => $document->id
        ]);

        return response()->json(['success' => __('Document successfully submitted!')]);
    }

    public function download($id)
    {
        $pdf_data = Document::findOrFail($id);
        $location = storage_path("app/" . $pdf_data->pdf->filename);
        // Optional: serve the file under a different filename:
        $filename = $pdf_data->name . '.pdf';
        // optional headers
        $headers = [];
        return response()->download($location, $filename, $headers);
    }
}
