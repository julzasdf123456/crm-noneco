<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceConnections;
use App\Models\ServiceConnectionInspections;
use App\Models\ServiceConnectionTimeframes;
use App\Models\IDGenerator;
use App\Models\ServiceConnectionPayTransaction;
use App\Models\ServiceConnectionTotalPayments;
use Illuminate\Support\Facades\DB;
use Validator;

class ServiceConnectionInspectionsAPI extends Controller {

    public $successStatus = 200;

    public function getServiceConnections(Request $request) {
        $serviceConnections = DB::table('CRM_ServiceConnectionInspections')
            ->leftJoin('CRM_ServiceConnections', 'CRM_ServiceConnectionInspections.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->select('CRM_ServiceConnections.*',
                'CRM_Barangays.Barangay AS BarangayFull',
                'CRM_Towns.Town AS TownFull')
            // ->where('CRM_ServiceConnections.Status', "For Inspection")
            ->where(function($query) {
                $query->where('CRM_ServiceConnections.Status', "For Inspection")
                    ->orWhere('CRM_ServiceConnections.Status', "Re-Inspection");
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
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->select('CRM_ServiceConnectionInspections.*',
                'CRM_Barangays.Barangay AS BarangayFull',
                'CRM_Towns.Town AS TownFull')
            // ->where('CRM_ServiceConnections.Status', "For Inspection")
            ->where(function($query) {
                $query->where('CRM_ServiceConnections.Status', "For Inspection")
                    ->orWhere('CRM_ServiceConnections.Status', "Re-Inspection");
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

        $serviceConnections->Status = $request['Status'];
        // UPDATE SPANNING
        $span = isset($request['SDWLengthAsInstalled']) ? floatval($request['SDWLengthAsInstalled']) : 0;
        if ($span >= 70) {
            $serviceConnections->LongSpan = 'Yes';
        } else {
            $serviceConnections->LongSpan = 'No';
        }

        if ($serviceConnectionInspections->save()) {
            $serviceConnections->save();

            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $request['ServiceConnectionId'];
            $timeFrame->UserId = $request['Inspector'];
            $timeFrame->Status = $request['Status'];
            if ($request['Status'] == 'Approved') {
                $timeFrame->Notes = 'Inspection approved and is waiting for payment';
            } else {
                $timeFrame->Notes = $request['Notes'];
            }
            
            $timeFrame->save();

            return response()->json(['ok' => 'ok'], $this->successStatus);
        } else {
            return response()->json(['error' => 'Error updating data'], 401);
        }
    }

    public function receiveBillDeposits(Request $request) {
        if ($request['ServiceConnectionId'] != null) {
            $depostData = ServiceConnectionPayTransaction::where('ServiceConnectionId', $request['ServiceConnectionId'])
                ->where('Particular', ServiceConnections::getBillDepositId())
                ->first();

            if ($depostData != null) {
                //update bill deposit
                $depostData->Amount = $request['Amount'];
                $depostData->Total = $request['Total'];
                $depostData->save();
            } else {
                // insert bill deposit
                $depostData = new ServiceConnectionPayTransaction;
                $depostData->id = $request['id'];
                $depostData->ServiceConnectionId = $request['ServiceConnectionId'];
                $depostData->Particular = $request['Particular'];
                $depostData->Amount = $request['Amount'];
                $depostData->Vat = $request['Vat'];
                $depostData->Total = $request['Total'];
                $depostData->save();
            }

            // update total payment
            $totalPayments = ServiceConnectionTotalPayments::where('ServiceConnectionId', $request['ServiceConnectionId'])
                ->first();

            if ($totalPayments != null) {
                $transactions = ServiceConnectionPayTransaction::where('ServiceConnectionId', $request['ServiceConnectionId'])
                    ->get();
                
                $amnt = 0;
                $vat = 0;
                $ttl = 0;
                foreach($transactions as $item) {
                    $amnt += floatval($item->Amount);
                    $vat += floatval($item->Vat);
                    $ttl += floatval($item->Total);
                }

                $totalPayments->SubTotal = round($amnt, 2);
                $totalPayments->TotalVat = round($vat, 2);
                $totalPayments->Total = round($ttl, 2);
                $totalPayments->save();
            } else {
                $transactions = new ServiceConnectionPayTransaction;
                $transactions->id = IDGenerator::generateIDandRandString();
                $transactions->ServiceConnectionId = $request['ServiceConnectionId'];
                $transactions->SubTotal = $request['Amount'];
                $transactions->Total = $request['Total'];
                $transactions->save();
            }

            return response()->json($depostData, 200);
        } else {
            return response()->json(['res' => 'empty sc id'], 200);
        }
    }
}