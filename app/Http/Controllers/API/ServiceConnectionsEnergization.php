<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceConnectionTimeframes;
use App\Models\ServiceConnectionImages;
use App\Models\ServiceConnectionCrew;
use App\Models\MastPoles;
use App\Models\IDGenerator;

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

    public function updateDownloadedServiceConnectionStatus(Request $request) {
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

        $crew = ServiceConnectionCrew::find($request['CrewAssigned']);

        $dateTimeDownloaded = date('Y-m-d H:i:s');

        foreach($serviceConnections as $item) {
            // CREATE LOG
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateIDandRandString();
            $timeFrame->ServiceConnectionId = $item->id;
            $timeFrame->UserId = $request['User'];
            $timeFrame->Status = $request['Status'];
            $timeFrame->Notes = 'Application downloaded by crew ' . $request['CrewAssigned'];

            $timeFrame->save();
        }

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
            ->update(['Status' => 'Downloaded by Crew']);
        
        return response()->json(['res' => 'ok'], $this->successStatus);   
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

    public function updateEnergized(Request $request) {
        $serviceConnections = ServiceConnections::find($request['id']);
        $serviceConnections->Status = $request['Status'];
        $serviceConnections->DateTimeLinemenArrived = $request['DateTimeLinemenArrived'];
        $serviceConnections->DateTimeOfEnergization = $request['DateTimeOfEnergization'];
        $serviceConnections->Notes = $request['Notes'];

        if ($serviceConnections->save()) {
            return response()->json(['success' => 'Upload Success'], $this->successStatus);             
        } else {
            return response()->json(['error' => 'Error Uploading Data ID ' . $request['id']], 404); 
        }
    }

    public function createTimeFrames(Request $request) {
        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = $request['id'];
        $timeFrame->ServiceConnectionId = $request['ServiceConnectionId'];
        $timeFrame->UserId = $request['User'];
        $timeFrame->Status = $request['Status'];
        $timeFrame->created_at = $request['created_at'];
        $timeFrame->updated_at = $request['updated_at'];
        $timeFrame->Notes = 'Crew arrived at ' . date('F d, Y h:i:s A', strtotime($request['ArrivalDate'])) . '<br>' . 'Performed energization attempt at ' . date('F d, Y h:i:s A', strtotime($request['EnergizationDate'])) . '<br>' . $request['Reason'];
            
        if ($timeFrame->save()) {
            return response()->json(['success' => 'Upload Success'], $this->successStatus);             
        } else {
            return response()->json(['error' => 'Error Uploading Data ID ' . $request['ServiceConnectionId']], 404); 
        }
    }

    public function saveUploadedImages(Request $request) {
        if ($files = $request->file('file')) {
            
            $path = $request->file->storeAs('public/documents/' . $request['svcId'] . '/images', $request->file->getClientOriginalName() . '.' . $request->file->extension());
    
            $imgs = new ServiceConnectionImages;
            $imgs->id = IDGenerator::generateRandString(90);
            $imgs->Photo = $request->file->getClientOriginalName() . '.' . $request->file->extension();
            $imgs->ServiceConnectionId = $request['svcId'];
            $imgs->save();
                
            return response()->json([
                "success" => true,
                "file" => $path
            ], 200);
    
        } else {
            return response()->json([
                "success" => false,
                "file" => ''
          ], 401);
        }
    }

    public function receiveMastPoles(Request $request) {
        $mastPole = MastPoles::where('ServiceConnectionId', $request['ServiceConnectionId'])
            ->where('Latitude', $request['Latitude'])
            ->where('Longitude', $request['Longitude'])
            ->first();

        if ($mastPole != null) {

        } else {
            $mastPole = new MastPoles;
            $mastPole->id = $request['id'];
            $mastPole->ServiceConnectionId = $request['ServiceConnectionId'];
            $mastPole->Latitude = $request['Latitude'];
            $mastPole->Longitude = $request['Longitude'];
            $mastPole->PoleRemarks = $request['PoleRemarks'];
            $mastPole->DateTimeTaken = $request['DateTimeTaken'];
            $mastPole->save();
        }

        return response()->json('ok', 200);
    }
}