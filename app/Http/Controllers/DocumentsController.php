<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            'name' => ['required', 'max:50'],
            'filename' => ['required'],
            'filename.*' => ['max:10000', 'mimes:jpg,jpeg,png,pdf,HEIF'],
        ]);

        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }

        $ext = request('file')->getClientOriginalExtension();
        $name = str_replace(' ', '', request('name'));
        $name = $name . '.' . $ext;

        Document::create([
            'companyId' => auth()->user()->companyId,
            'user_email' => auth()->user()->email,
            'user_name' => auth()->user()->name,
            'name' => $name,
            'ext' => $ext
        ]);

        return response()->json(['success' => __('Document successfully submitted!')]);
    }
}
