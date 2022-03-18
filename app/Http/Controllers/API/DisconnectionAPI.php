<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DisconnectionHistory;
use App\Models\ServiceAccounts;
use App\Models\IDGenerator;
use App\Models\Tickets;
use App\Http\Requests\CreateReadingsRequest;

class DisconnectionAPI extends Controller {
    public $successStatus = 200;

    public function getDisconnectionList(Request $request) {
        $disconnectionList = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereNotIn('Billing_Bills.id', DB::table('Cashier_PaidBills')->where('Cashier_PaidBills.ServicePeriod', $request['ServicePeriod'])->pluck('Cashier_PaidBills.ObjectSourceId'))
            ->where('Billing_Bills.ServicePeriod', $request['ServicePeriod'])
            ->where('Billing_ServiceAccounts.AreaCode', $request['Area'])
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
                'Billing_ServiceAccounts.Latitude',
                'Billing_ServiceAccounts.Longitude',
                'Billing_ServiceAccounts.SequenceCode',
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
                'Billing_Bills.MeterNumber',
                'Billing_Bills.ServicePeriod',
                'Billing_Bills.BillNumber')
            ->get();

        return response()->json($disconnectionList, $this->successStatus);
    }

    public function receiveDisconnectionUploads(Request $request) {
        // UPDATE ACCOUNT
        $account = ServiceAccounts::find($request['AccountNumber']);

        if ($account != null) {
            $account->AccountStatus = $request['AccountStatus'];
            $account->DateDisconnected = $request['DateDisconnected'];
            $account->save();
        }

        // CREATE DISCONNECTION HISTORY
        $discoHist = new DisconnectionHistory;
        $discoHist->id = IDGenerator::generateIDandRandString();
        $discoHist->AccountNumber = $request['AccountNumber'];
        $discoHist->ServicePeriod = $request['ServicePeriod'];
        $discoHist->Latitude = $request['LatitudeCaptured'];
        $discoHist->Longitude = $request['LongitudeCaptured'];
        $discoHist->BillId = $request['BillId'];
        $discoHist->Status = $request['AccountStatus'];
        $discoHist->UserId = $request['UserId'];
        $discoHist->DateDisconnected = $request['DateDisconnected'];
        $discoHist->TimeDisconnected = $request['TimeDisconnected'];
        $discoHist->save();

        // CREATE DISCONNECTION TICKET
        // $ticket = new Tickets;
        // $ticket->id = IDGenerator::generateIDandRandString();
        // $ticket->AccountNumber = $request['AccountNumber'];
        // $ticket->ConsumerName = $request['ServiceAccountName'];
        // $ticket->Town = $request['Town'];
        // $ticket->Barangay = $request['Barangay'];
        // $ticket->Sitio = $request['Purok'];
        // $ticket->Ticket = Tickets::getDisconnectionDelinquencyId();
        // $ticket->Reason = 'Delinquency';
        // $ticket->GeoLocation = $request['LatitudeCaptured'] . ',' . $request['LongitudeCaptured'];
        // $ticket->Status = 'Executed';
        // $ticket->DateTimeDownloaded = $request['DateDisconnected'] . ' ' . $request['TimeDisconnected'];
        // $ticket->DateTimeLinemanArrived = $request['DateDisconnected'] . ' ' . $request['TimeDisconnected'];
        // $ticket->DateTimeLinemanExecuted = $request['DateDisconnected'] . ' ' . $request['TimeDisconnected'];
        // $ticket->UserId = $request['UserId'];
        // $ticket->Office = env('APP_LOCATION');
        // $ticket->save();

        return response()->json($discoHist, $this->successStatus);
    }
}