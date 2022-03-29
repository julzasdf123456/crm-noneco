<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateReadingsRequest;
use App\Models\ReadingSchedules;
use App\Models\Readings;
use App\Models\Bills;
use App\Models\Towns;
use App\Models\Barangays;
use App\Models\ServiceAccounts;
use App\Models\Rates;
use App\Models\ReadingImages;

class ReadAndBillAPI extends Controller {
    public $successStatus = 200;

    public function getUndownloadedSchedules(Request $request) {
        $readingSchedules = ReadingSchedules::where('MeterReader', $request['MeterReaderId'])
            // ->where('ScheduledDate', '>=', date('Y-m-d'))
            ->whereNull('Status')
            ->select('id',
                    'AreaCode',
                    'GroupCode',
                    DB::raw("CAST (ServicePeriod AS VARCHAR) AS ServicePeriod"),
                    'ScheduledDate',
                    'MeterReader',
                    'created_at',
                    'updated_at',
                    'Status')
            ->get();

        return response()->json($readingSchedules, $this->successStatus);
    }

    public function updateDownloadedStatus(Request $request) {
        $readingSchedules = ReadingSchedules::find($request['id']);
        $readingSchedules->Status = 'Downloaded';
        $readingSchedules->save();

        return response()->json(['response' => 'ok'], $this->successStatus);
    }

    public function downloadAccounts(Request $request) {
        $accounts = ServiceAccounts::where('Town', $request['AreaCode'])
            ->where('GroupCode', $request['GroupCode'])
            ->get();

        $prevMonth = date('Y-m-01', strtotime($request['ServicePeriod'] . ' -1 month'));

        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_ServiceAccounts.Town', $request['AreaCode'])
            ->where('Billing_ServiceAccounts.GroupCode', $request['GroupCode'])
            ->whereNotIn('Billing_ServiceAccounts.id', DB::table('Billing_Readings')->where('ServicePeriod', $request['ServicePeriod'])->pluck('AccountNumber'))
            ->whereNotIn('Billing_ServiceAccounts.AccountType', ['PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE'])
            ->where(function ($query) {
                $query->where(function($queryX) {
                        $queryX->where('Billing_ServiceAccounts.AccountExpiration', '>', date('Y-m-d'))
                            ->where('Billing_ServiceAccounts.AccountRetention', 'Temporary');
                    })
                    ->orWhere('Billing_ServiceAccounts.AccountRetention', 'Permanent')
                    ->orWhereNull('Billing_ServiceAccounts.AccountExpiration');
            })
            ->select('Billing_ServiceAccounts.id', 
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.Multiplier',
                'Billing_ServiceAccounts.Coreloss',
                'Billing_ServiceAccounts.AccountType',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.AreaCode',
                'Billing_ServiceAccounts.GroupCode',
                'Billing_ServiceAccounts.Town',
                'Billing_ServiceAccounts.Barangay',
                'Billing_ServiceAccounts.Latitude',
                'Billing_ServiceAccounts.Longitude',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.SeniorCitizen',
                'Billing_ServiceAccounts.Evat5Percent',
                'Billing_ServiceAccounts.Ewt2Percent',
                'CRM_Towns.Town as TownFull',
                'CRM_Barangays.Barangay as BarangayFull',
                'Billing_ServiceAccounts.Purok',
                'Billing_Collectibles.Balance',
                DB::raw("(SELECT TOP 1 Amount FROM Billing_ArrearsLedgerDistribution WHERE AccountNumber=Billing_ServiceAccounts.id AND IsPaid IS NULL AND ServicePeriod='" . $request['ServicePeriod'] . "') AS ArrearsLedger"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                DB::raw("(SELECT TOP 1 CAST(ReadingTimestamp AS DATE) FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS ReadingTimestamp"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                DB::raw("(SELECT SUM(CAST(NetAmount AS DECIMAL(10, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.id)) AS ArrearsTotal"),
                DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"))
            ->get();

        /**
         * CHECK IF RATE IS AVAILABLE
         */
        $rates = Rates::where('ServicePeriod', $request['ServicePeriod']) 
            ->get();

        if (count($rates) > 0) {
            return response()->json($accounts, $this->successStatus);
        } else {
            return response()->json([], 404);
        }        
    }

    public function downloadRates(Request $request) {
        /**
         * GET ALL RATES FOR THE PAST 3 MONTHS
         */
        $rates = Rates::where('ServicePeriod', $request['ServicePeriod']) 
            ->get();

        return response()->json($rates, $this->successStatus);
    }

    public function receiveReadings(Request $request) {
        $input = $request->all();

        $readings = Readings::where('ServicePeriod', $input['ServicePeriod'])
            ->where('AccountNumber')
            ->first();
        
        if ($readings != null) {
            // update
            $reading = Readings::update($request->all(), $readings->id);
        } else {
            //create
            $reading = Readings::create($input);
        }

        return response()->json(['res' => 'ok'], $this->successStatus);
    }

    public function receiveBills(Request $request) {
        $input = $request->all();

        $bills = Bills::where('ServicePeriod', $input['ServicePeriod'])
            ->where('AccountNumber')
            ->first();
        
        if ($bills != null) {
            // update
            $bill = Bills::update($request->all(), $bills->id);
        } else {
            //create
            $bill = Bills::create($input);
        }

        return response()->json(['res' => 'ok'], $this->successStatus);
    }

    public function saveReadingImages(Request $request) {
        if ($files = $request->file('file')) {
            
            $path = $request->file->storeAs('public/documents/' . $request['AccountNumber'] . '/images', $request->file->getClientOriginalName() . '.' . $request->file->extension());
    
            $imgs = new ReadingImages;
            $imgs->id = $request['Id'];
            $imgs->Photo = $request->file->getClientOriginalName() . '.' . $request->file->extension();
            $imgs->ServicePeriod = $request['ServicePeriod'];
            $imgs->AccountNumber = $request['AccountNumber'];
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
}