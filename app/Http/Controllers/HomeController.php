<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ServiceAccounts;

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
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('*')
                    ->orderBy('ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchNewServiceConnections(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->where(function($query) {
                        $query->where('CRM_ServiceConnections.Status', "For Inspection")
                            ->orWhere('CRM_ServiceConnections.Status', "For Re-Inspection");
                    })
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',)
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
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
                    ->where(function ($query) {
                        $query->where('CRM_ServiceConnections.Trash', 'No')
                            ->orWhereNull('CRM_ServiceConnections.Trash');
                    })
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

    public function fetchInspectionReport(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('users')
                    ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('roles.name', 'Inspector')
                    ->select([
                        'users.name',
                        DB::raw("(SELECT COUNT(x.id) FROM CRM_ServiceConnections x 
                        LEFT JOIN CRM_ServiceConnectionInspections y ON x.id=y.ServiceConnectionId
                        WHERE x.Status='For Inspection' AND x.Trash IS NULL AND y.Inspector=users.id) AS Total")    
                    ])
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchInspectionLargeLoad(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->where('CRM_ServiceConnections.Status', 'Forwarded To Planning')
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',)
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchBomLargeLoad(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->where('CRM_ServiceConnections.Status', 'For BoM')
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',)
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }

    public function fetchTransformerLargeLoad(Request $request) {
        if ($request->ajax()) {            
            $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->where('CRM_ServiceConnections.Status', 'For Transformer and Pole Assigning')
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',)
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

            echo json_encode($data);
        }
    }
}
