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
            $filename = time() . '.' . $photo->getClientOriginalExtension();
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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:50', 'unique:documents'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
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
        $filename = "documents/" . $name . ".pdf";

        foreach ($data as $photo) {
            $path = "photos/" . $photo->filename;
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        DocumentFile::whereIn('id', explode(",", $request->file_ids))
            ->delete(['document_id' => $document->id]);

        Storage::put("public/" . $filename, $content);

        DocumentFile::create([
            'filename' => $filename,
            'document_id' => $document->id
        ]);

        return response()->json(['success' => __('Document successfully submitted!')]);
    }

    public function download($id)
    {
        $pdf_data = Document::findOrFail($id);
        $location = public_path("storage/" . $pdf_data->pdf->filename);
        // Optional: serve the file under a different filename:
        $filename = $pdf_data->name . '.pdf';
        // optional headers
        $headers = [];
        return response()->download($location, $filename, $headers);
    }
}
