<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceConnectionsEnergization extends Controller {

    public $successStatus = 200;

    public function getForEnergizationData() {
        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_ServiceConnectionInspections', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionInspections.ServiceConnectionId')
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Status', 'Approved')
                    ->orWhere('CRM_ServiceConnections.Status', 'Not Energized');
            })
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->whereIn('CRM_ServiceConnections.id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
            ->select('CRM_ServiceConnections.*')
            ->orderBy('CRM_ServiceConnections.ServiceAccountName')
            ->get();

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        } 
    }

    public function getInspectionsForEnergizationData() {
        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_ServiceConnectionInspections', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionInspections.ServiceConnectionId')
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Status', 'Approved')
                    ->orWhere('CRM_ServiceConnections.Status', 'Not Energized');
            })
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->whereIn('CRM_ServiceConnections.id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
            ->select('CRM_ServiceConnectionInspections.*')
            ->orderBy('CRM_ServiceConnections.ServiceAccountName')
            ->get();

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        } 
    }

}