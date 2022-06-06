<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class APIReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super;admin');
    }

    public function getReports()
    {
        $reports = Report::where('companyId', '=', auth('admin')->user()->companyId)
            ->with('user', 'truck');

        return DataTables::eloquent($reports)
            ->setRowId('id')
            ->make(true);
    }

    public function destroy(Request $request)
    {
        Report::destroy($request->reports);

        return response()->json(['success' => count($request->reports) . __(' record/s successfully deleted!')]);
    }
}
