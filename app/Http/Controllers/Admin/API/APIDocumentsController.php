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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class APIDocumentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getDocuments()
    {
        $document = Document::where('companyId', '=', auth('admin')->user()->companyId)
            ->select('name', 'user_email', 'user_name', 'created_at', 'id', 'read', 'author');

        return datatables::eloquent($document)
            ->setRowId('id')
            ->make(true);
    }

    public function upload(Request $request)
    {
        $files = [];
        foreach ($request->upl as $file) {
            $filename = auth('admin')->user()->id . 'doc_' . time() . '.' . $file->getClientOriginalExtension();
            if ($file->getClientOriginalExtension() != "pdf") {
                $img = Image::make($file)->encode(null, 50);
                Storage::put($filename, $img);
                Storage::move($filename, 'photos/' . $filename);
                $product_photo = DocumentFile::create([
                    'filename' => $filename
                ]);
            } else {
                $path = $file->storeAs('public/documents', $filename);
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

    public function storePdf(Request $request)
    {
        if (!isset($request->pdf)) {
            return back()->withErrors([__('Insert at least one photo or PDF!')]);
        }

        $user = User::find($request->user);

        $doc = Document::create([
            'name' => $request->name,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'companyId' => $user->companyId,
            'author' => 0,
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

        $user = User::find($request->user);

        $doc = Document::create([
            'name' => $request->name,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'companyId' => $user->companyId,
            'author' => 0,
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

        $media = Media::where('model_type', 'App\Models\Document')
            ->where('model_id', $id)
            ->where('collection_name', 'pdf')
            ->get();

        return response()->download($media[0]->getPath(), $doc->name . '.pdf');
    }

    public function delete(Request $request)
    {
        foreach ($request->ids as $id) {
            Document::find($id)->delete();
        }

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
