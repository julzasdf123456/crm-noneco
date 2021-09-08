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
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->where('CRM_ServiceConnections.Status', 'Approved')
                    ->whereNull('CRM_ServiceConnections.ORNumber')
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',)
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchForEnergization(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->whereNotNull('ORNumber')
                    ->where(function ($query) {
                        $query->where('Status', 'Approved')
                            ->orWhere('Status', 'Not Energized');
                    })
                    ->whereIn('id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
                    ->select('*')
                    ->orderBy('ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }
}
