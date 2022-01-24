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
            ->where('ScheduledDate', '>=', date('Y-m-d'))
            ->get();

        return response()->json($readingSchedules, $this->successStatus);
    }

    public function downloadAccounts(Request $request) {
        $accounts = ServiceAccounts::where('AreaCode', $request['AreaCode'])
            ->where('GroupCode', $request['GroupCode'])
            ->get();

        return response()->json($accounts, $this->successStatus);
    }

    public function downloadRates(Request $request) {
        /**
         * GET ALL RATES FOR THE PAST 3 MONTHS
         */
        $rates = Rates::where('ServicePeriod', '>', date('Y-m-d', strtotime('-3 months'))) 
            ->get();

        return response()->json($rates, $this->successStatus);
    }
}