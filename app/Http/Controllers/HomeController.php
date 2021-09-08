<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function fetchUnassignedMeters(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->whereNotNull('ORNumber')
                    ->whereNotIn('id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
                    ->select('*')
                    ->orderBy('ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchNewServiceConnections(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->where('Status', 'Received')
                    ->select('*')
                    ->orderBy('ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchApprovedServiceConnections(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->where('Status', 'Approved')
                    ->whereNull('ORNumber')
                    ->select('*')
                    ->orderBy('ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchForEnergization(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->whereNotNull('ORNumber')
                    ->where('Status', 'Approved')
                    ->whereIn('id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
                    ->select('*')
                    ->orderBy('ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }
}
