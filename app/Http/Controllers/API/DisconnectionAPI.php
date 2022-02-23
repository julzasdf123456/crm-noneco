<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DisconnectionHistory;
use App\Http\Requests\CreateReadingsRequest;

class DisconnectionAPI extends Controller {
    public $successStatus = 200;

    public function getDisconnectionList(Request $request) {
        $disconnectionList = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereNotIn('Billing_Bills.id', DB::table('Cashier_PaidBills')->where('Cashier_PaidBills.ServicePeriod', $request['ServicePeriod'])->pluck('Cashier_PaidBills.ObjectSourceId'))
            ->where('Billing_Bills.ServicePeriod', $request['ServicePeriod'])
            ->whereRaw('DATEDIFF(dd, Billing_Bills.BillingDate, GETDATE()) > ?', [DisconnectionHistory::noOfDaysTillDisconnection()])
            ->where('Billing_ServiceAccounts.AccountStatus', 'ACTIVE')
            ->select('Billing_Bills.id as BillId',
                'Billing_ServiceAccounts.id AS AccountNumber',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Town',
                'Billing_ServiceAccounts.Barangay',
                'Billing_ServiceAccounts.Purok',
                'Billing_ServiceAccounts.AreaCode',
                'Billing_ServiceAccounts.GroupCode',
                'Billing_Bills.KwhUsed',
                'Billing_Bills.EffectiveRate',
                'Billing_Bills.NetAmount',
                'Billing_Bills.AdditionalCharges',
                'Billing_Bills.Deductions',
                'Billing_Bills.BillingDate',
                'Billing_Bills.ServiceDateFrom',
                'Billing_Bills.ServiceDateTo',
                'Billing_Bills.DueDate',
                'Billing_Bills.ConsumerType',
                'Billing_Bills.MeterNumber')
            ->get();

        return response()->json($disconnectionList, $this->successStatus);
    }
}