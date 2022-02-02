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
        $accounts = ServiceAccounts::where('AreaCode', $request['AreaCode'])
            ->where('GroupCode', $request['GroupCode'])
            ->get();

        $prevMonth = date('Y-m-01', strtotime($request['ServicePeriod'] . ' -1 month'));

        $accounts = DB::table('Billing_ServiceAccounts')
            ->where('AreaCode', $request['AreaCode'])
            ->where('GroupCode', $request['GroupCode'])
            ->select('id', 
                'ServiceAccountName',
                'Multiplier',
                'Coreloss',
                'AccountType',
                'AccountStatus',
                'AreaCode',
                'GroupCode',
                'Town',
                'Barangay',
                'Latitude',
                'Longitude',
                'OldAccountNo',
                'SequenceCode',
                DB::raw("(SELECT KwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"))
            ->get();

        return response()->json($accounts, $this->successStatus);
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