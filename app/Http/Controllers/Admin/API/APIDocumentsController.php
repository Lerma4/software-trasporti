<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use PDF;

class APIDocumentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getDocuments()
    {
        $document = Document::where('companyId', '=', auth('admin')->user()->companyId)
            ->select('name', 'user_email', 'user_name', 'created_at', 'id');

        return datatables::eloquent($document)
            ->setRowId('id')
            ->make(true);
    }

    public function upload(Request $request)
    {
        $files = [];
        foreach ($request->upl as $file) {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            if ($file->getClientOriginalExtension() != "pdf") {
                $img = Image::make($file)->encode(null, 50);
                Storage::put($filename, $img);
                Storage::move($filename, 'photos/' . $filename);
                $product_photo = DocumentFile::create([
                    'filename' => $filename
                ]);
            } else {
                $path = $file->store('public/documents');
                $product_photo = DocumentFile::create([
                    'filename' => $path
                ]);
            }
            $photo_object = new \stdClass();
            $photo_object->fileID = $product_photo->id;
            $files[] = $photo_object;
        }

        return response()->json(array('files' => $files));
    }

    public function store(Request $request)
    {
        if ($request->file_ids == "") {
            return response()
                ->json(['errors' => [__("Insert at least one photo or PDF.")]]);
        }

        $user = User::findOrFail($request->user);
        $oldDocuments = Document::where('user_email', $user->email)
            ->where('name', $request->name)
            ->count();

        if ($oldDocuments != 0) {
            return response()
                ->json(['errors' => [__("Already exists a document with this name.")]]);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $name = str_replace(' ', '', request('name'));


        $document = Document::create([
            'companyId' => auth('admin')->user()->companyId,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'name' => $name
        ]);

        // LEGO IL DOCUMENTO AL PDF

        switch ($request->format) {
            case 'pdf':
                error_log($document->id);
                DocumentFile::where('id', $request->file_ids)
                    ->update(['document_id' => $document->id]);
                break;

            case 'photos':
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
                    ->delete();

                Storage::put("public/" . $filename, $content);

                DocumentFile::create([
                    'filename' => $filename,
                    'document_id' => $document->id
                ]);
                break;

            default:
                # non fa niente
                break;
        }

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

    public function delete(Request $request)
    {
        $pdf_data = Document::whereIn('id', $request->ids)
            ->get();

        foreach ($pdf_data as $data) {
            if (Storage::exists($data->pdf->filename)) {
                Storage::delete($data->pdf->filename);
            }
        }

        Document::whereIn('id', $request->ids)
            ->delete();

        return response()->json(['success' => __('Document/s successfully deleted!')]);
    }

    public function edit(Request $request)
    {
        $user = User::where('email', $request->user)
            ->first();
        $oldDocuments = Document::where('user_email', $user->email)
            ->where('name', $request->name)
            ->count();

        if ($oldDocuments != 0) {
            return response()
                ->json(['errors' => [__("Already exists a document with this name.")]]);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $name = str_replace(' ', '', request('name'));

        $document = Document::findOrFail($request->id)
            ->update([
                'user_email' => $user->email,
                'user_name' => $user->name,
                'name' => $name
            ]);

        return response()->json(['success' => __('Document/s successfully deleted!')]);
    }
}
