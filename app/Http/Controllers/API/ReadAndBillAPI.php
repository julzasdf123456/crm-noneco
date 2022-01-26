<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ReadingSchedules;
use App\Models\Towns;
use App\Models\Barangays;
use App\Models\ServiceAccounts;
use App\Models\Rates;

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
}