<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceConnections;
use App\Models\ServiceConnectionInspections;
use App\Models\ServiceConnectionTimeframes;
use App\Models\IDGenerator;
use Illuminate\Support\Facades\DB;
use Validator;

class ServiceConnectionInspectionsAPI extends Controller {

    public $successStatus = 200;

    public function getServiceConnections(Request $request) {
        $serviceConnections = DB::table('CRM_ServiceConnectionInspections')
            ->leftJoin('CRM_ServiceConnections', 'CRM_ServiceConnectionInspections.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->select('CRM_ServiceConnections.*')
            ->where('CRM_ServiceConnections.Status', "For Inspection")
            ->where(function($query) {
                $query->where('CRM_ServiceConnections.Status', "For Inspection")
                    ->orWhere('CRM_ServiceConnections.Status', "For Re-Inspection");
            })
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
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
            ->leftJoin('CRM_ServiceConnections', 'CRM_ServiceConnectionInspections.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->select('CRM_ServiceConnectionInspections.*')
            ->where('CRM_ServiceConnections.Status', "For Inspection")
            ->where(function($query) {
                $query->where('CRM_ServiceConnections.Status', "For Inspection")
                    ->orWhere('CRM_ServiceConnections.Status', "For Re-Inspection");
            })
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->where('CRM_ServiceConnectionInspections.Inspector', $request['userid'])
            ->get(); 

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        }  
    }

    public function updateServiceInspections(Request $request) {
        $serviceConnectionInspections = ServiceConnectionInspections::find($request['id']);
        $serviceConnections = ServiceConnections::find($request['ServiceConnectionId']);

        $serviceConnectionInspections->SEMainCircuitBreakerAsInstalled = $request['SEMainCircuitBreakerAsInstalled'];
        $serviceConnectionInspections->SENoOfBranchesAsInstalled = $request['SENoOfBranchesAsInstalled'];
        $serviceConnectionInspections->PoleGIEstimatedDiameter = $request['PoleGIEstimatedDiameter'];
        $serviceConnectionInspections->PoleGIHeight = $request['PoleGIHeight'];
        $serviceConnectionInspections->PoleGINoOfLiftPoles = $request['PoleGINoOfLiftPoles'];
        $serviceConnectionInspections->PoleConcreteEstimatedDiameter = $request['PoleConcreteEstimatedDiameter'];
        $serviceConnectionInspections->PoleConcreteHeight = $request['PoleConcreteHeight'];
        $serviceConnectionInspections->PoleConcreteNoOfLiftPoles = $request['PoleConcreteNoOfLiftPoles'];
        $serviceConnectionInspections->PoleHardwoodEstimatedDiameter = $request['PoleHardwoodEstimatedDiameter'];
        $serviceConnectionInspections->PoleHardwoodHeight = $request['PoleHardwoodHeight'];
        $serviceConnectionInspections->PoleHardwoodNoOfLiftPoles = $request['PoleHardwoodNoOfLiftPoles'];
        $serviceConnectionInspections->PoleRemarks = $request['PoleRemarks'];
        $serviceConnectionInspections->SDWSizeAsInstalled = $request['SDWSizeAsInstalled'];
        $serviceConnectionInspections->SDWLengthAsInstalled = $request['SDWLengthAsInstalled'];
        $serviceConnectionInspections->GeoBuilding = $request['GeoBuilding'];
        $serviceConnectionInspections->GeoTappingPole = $request['GeoTappingPole'];
        $serviceConnectionInspections->GeoMeteringPole = $request['GeoMeteringPole'];
        $serviceConnectionInspections->GeoSEPole = $request['GeoSEPole'];
        $serviceConnectionInspections->FirstNeighborName = $request['FirstNeighborName'];
        $serviceConnectionInspections->FirstNeighborMeterSerial = $request['FirstNeighborMeterSerial'];
        $serviceConnectionInspections->SecondNeighborName = $request['SecondNeighborName'];
        $serviceConnectionInspections->SecondNeighborMeterSerial = $request['SecondNeighborMeterSerial'];
        $serviceConnectionInspections->Status = $request['Status'];
        $serviceConnectionInspections->DateOfVerification = $request['DateOfVerification'];
        $serviceConnectionInspections->EstimatedDateForReinspection = $request['EstimatedDateForReinspection'];
        $serviceConnectionInspections->Notes = $request['Notes'];
        $serviceConnectionInspections->Inspector = $request['Inspector'];

        $serviceConnections->Status = 'Approved';

        if ($serviceConnectionInspections->save()) {
            $serviceConnections->save();

            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $request['ServiceConnectionId'];
            $timeFrame->UserId = $request['Inspector'];
            $timeFrame->Status = 'Approved';
            $timeFrame->Notes = 'Inspection approved and is waiting for payment';
            $timeFrame->save();

            return response()->json(['ok' => 'ok'], $this->successStatus);
        } else {
            return response()->json(['error' => 'Error updating data'], 401);
        }
    }
}