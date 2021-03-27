<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;
use Yajra\DataTables\Facades\DataTables;

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
            ->select('name', 'created_at', 'id');

        return datatables::eloquent($document)
            ->setRowId('id')
            ->make(true);
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

        //$ext = request('file')->getClientOriginalExtension();
        $name = str_replace(' ', '', request('name'));
        //$name = $name . '.' . $ext;

        $document = Document::create([
            'companyId' => auth()->user()->companyId,
            'user_email' => auth()->user()->email,
            'user_name' => auth()->user()->name,
            'name' => $name
        ]);

        DocumentFile::whereIn('id', explode(",", $request->file_ids))
            ->update(['document_id' => $document->id]);

        return response()->json(['success' => __('Document successfully submitted!')]);
    }

    public function upload(Request $request)
    {
        $photos = [];
        foreach ($request->photos as $photo) {
            $filename = $photo->store('photos');
            $product_photo = DocumentFile::create([
                'filename' => $filename
            ]);

            $photo_object = new \stdClass();
            $photo_object->name = str_replace('photos/', '', $photo->getClientOriginalName());
            $photo_object->size = round(Storage::size($filename) / 1024, 2);
            $photo_object->fileID = $product_photo->id;
            $photos[] = $photo_object;
        }

        return response()->json(array('files' => $photos), 200);
    }
}
