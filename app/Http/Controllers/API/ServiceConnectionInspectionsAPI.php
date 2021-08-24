<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\DB;
use Validator;

class ServiceConnectionInspectionsAPI extends Controller {

    public $successStatus = 200;

    public function getServiceConnections(Request $request) {
        $serviceConnections = DB::table('CRM_ServiceConnectionInspections')
            ->join('CRM_ServiceConnections', 'CRM_ServiceConnectionInspections.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->select('CRM_ServiceConnections.*')
            ->where('CRM_ServiceConnectionInspections.Inspector', $request['userid'])
            ->get(); 

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        }  
    }

    public function getServiceInspections(Request $request) {
        $serviceConnections = DB::table('CRM_ServiceConnectionInspections')
            ->join('CRM_ServiceConnections', 'CRM_ServiceConnectionInspections.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->select('CRM_ServiceConnectionInspections.*')
            ->where('CRM_ServiceConnectionInspections.Inspector', $request['userid'])
            ->where(function($query) {
                $query->where('CRM_ServiceConnectionInspections.Status', 'FOR INSPECTION')
                    ->orWhere('CRM_ServiceConnectionInspections.Status', 'FOR RE-INSPECTION');
            })
            ->get(); 

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        }  
    }

}