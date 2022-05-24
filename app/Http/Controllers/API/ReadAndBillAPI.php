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
use App\Models\PrePaymentBalance;
use App\Models\IDGenerator;
use App\Models\PrePaymentTransHistory;
use App\Models\PaidBills;
use App\Models\BAPAReadingSchedules;

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
            ->whereIn('Billing_ServiceAccounts.AccountStatus', ['ACTIVE', 'DISCONNECTED'])
            ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
            ->whereRaw("Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "' AND AccountNumber IS NOT NULL)")
            ->whereNotIn('Billing_ServiceAccounts.AccountType', ['PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE'])
            ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
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
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
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
            // $reading = Readings::update($request->all(), $readings->id);
        } else {
            //create
            $reading = Readings::create($input);
        }

        return response()->json(['res' => 'ok'], $this->successStatus);
    }

    public function receiveBills(Request $request) {
        $input = $request->all();

        $bills = Bills::where('ServicePeriod', $input['ServicePeriod'])
            ->where('AccountNumber', $input['AccountNumber'])
            ->first();

        $prepaymentBalance = PrePaymentBalance::where('AccountNumber', $input['AccountNumber'])->first();
        
        if ($bills != null) {
            // update
            if ($prepaymentBalance != null) {
                if ($input['ExcessDeposit'] != null) {
                    $prepaymentBalance->Balance = $input['ExcessDeposit'];
                    $prepaymentBalance->save();
                    
                    // ADD TRANSACTION HISTORY
                    $transHistory = new PrePaymentTransHistory;
                    $transHistory->id = IDGenerator::generateIDandRandString();
                    $transHistory->AccountNumber = $input['AccountNumber'];
                    $transHistory->Method = 'DEDUCT';
                    $transHistory->Amount = $input['DeductedDeposit'];
                    $transHistory->UserId = $input['UserId']; 
                    $transHistory->save();
                } else {
                    $prepaymentBalance->Balance = "0";
                    $prepaymentBalance->save();

                    // ADD TRANSACTION HISTORY
                    $transHistory = new PrePaymentTransHistory;
                    $transHistory->id = IDGenerator::generateIDandRandString();
                    $transHistory->AccountNumber = $input['AccountNumber'];
                    $transHistory->Method = 'DEDUCT';
                    $transHistory->Amount = $input['DeductedDeposit'];
                    $transHistory->UserId = $input['UserId']; 
                    $transHistory->save();
                }

                // MARK AS PAID
                $netAmnt = intval($input['NetAmount']);
                if ($netAmnt == 0) {
                    // GET LAST OR OF DEPOSIT FIRST
                    $histLast = PrePaymentTransHistory::where('AccountNumber', $input['AccountNumber'])
                        ->where('Method', 'DEPOSIT')
                        ->orderByDesc('created_at')
                        ->first();
                    
                    if ($histLast != null) {
                        // INSERT TO PAID BILLS
                        $paidBills = new PaidBills;
                        $paidBills->id = IDGenerator::generateIDandRandString();
                        $paidBills->BillNumber = $input['BillNumber'];
                        $paidBills->AccountNumber = $input['AccountNumber'];
                        $paidBills->ServicePeriod = $input['ServicePeriod'];
                        $paidBills->ORNumber = $histLast->ORNumber;
                        $paidBills->ORDate = date('Y-m-d');
                        $paidBills->KwhUsed = $input['KwhUsed'];
                        $paidBills->Teller = $histLast->UserId;
                        $paidBills->OfficeTransacted = env('APP_LOCATION');
                        $paidBills->PostingDate = date('Y-m-d');
                        $paidBills->PostingTime = date('H:i:s');
                        $paidBills->Surcharge = 0;
                        $paidBills->Deductions = $input['DeductedDeposit'];
                        $paidBills->NetAmount = "0";
                        $paidBills->Source = 'MONTHLY BILL - Pre-Payments';
                        $paidBills->ObjectSourceId = $input['id'];
                        $paidBills->UserId = $input['UserId'];
                        $paidBills->save();
                    }
                }
            }
            
            // $bill = Bills::update($request->all(), $bills->id);
        } else {
            //create
            if ($prepaymentBalance != null) {
                if ($input['ExcessDeposit'] != null) {
                    $prepaymentBalance->Balance = $input['ExcessDeposit'];
                    $prepaymentBalance->save();
                    
                    // ADD TRANSACTION HISTORY
                    $transHistory = new PrePaymentTransHistory;
                    $transHistory->id = IDGenerator::generateIDandRandString();
                    $transHistory->AccountNumber = $input['AccountNumber'];
                    $transHistory->Method = 'DEDUCT';
                    $transHistory->Amount = $input['DeductedDeposit'];
                    $transHistory->UserId = $input['UserId']; 
                    $transHistory->save();
                } else {
                    $prepaymentBalance->Balance = "0";
                    $prepaymentBalance->save();

                    // ADD TRANSACTION HISTORY
                    $transHistory = new PrePaymentTransHistory;
                    $transHistory->id = IDGenerator::generateIDandRandString();
                    $transHistory->AccountNumber = $input['AccountNumber'];
                    $transHistory->Method = 'DEDUCT';
                    $transHistory->Amount = $input['DeductedDeposit'];
                    $transHistory->UserId = $input['UserId']; 
                    $transHistory->save();
                }

                // MARK AS PAID
                $netAmnt = intval($input['NetAmount']);
                if ($netAmnt == 0) {
                    // GET LAST OR OF DEPOSIT FIRST
                    $histLast = PrePaymentTransHistory::where('AccountNumber', $input['AccountNumber'])
                        ->where('Method', 'DEPOSIT')
                        ->orderByDesc('created_at')
                        ->first();
                    
                    if ($histLast != null) {
                        // INSERT TO PAID BILLS
                        $paidBills = new PaidBills;
                        $paidBills->id = IDGenerator::generateIDandRandString();
                        $paidBills->BillNumber = $input['BillNumber'];
                        $paidBills->AccountNumber = $input['AccountNumber'];
                        $paidBills->ServicePeriod = $input['ServicePeriod'];
                        $paidBills->ORNumber = $histLast->ORNumber;
                        $paidBills->ORDate = date('Y-m-d');
                        $paidBills->KwhUsed = $input['KwhUsed'];
                        $paidBills->Teller = $histLast->UserId;
                        $paidBills->OfficeTransacted = env('APP_LOCATION');
                        $paidBills->PostingDate = date('Y-m-d');
                        $paidBills->PostingTime = date('H:i:s');
                        $paidBills->Surcharge = 0;
                        $paidBills->Deductions = $input['DeductedDeposit'];
                        $paidBills->NetAmount = "0";
                        $paidBills->Source = 'MONTHLY BILL - Pre-Payments';
                        $paidBills->ObjectSourceId = $input['id'];
                        $paidBills->UserId = $input['UserId'];
                        $paidBills->save();
                    }
                }
            }

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

    /**
     * BAPA
     */
    public function getBapaList(Request $request) {
        $townCode = $request['Town'];

        $bapas = DB::table('Billing_ServiceAccounts')
            ->select('OrganizationParentAccount')
            ->where('Town', $townCode)
            ->groupBy('OrganizationParentAccount')
            ->orderBy('OrganizationParentAccount')
            ->get();

        return response()->json($bapas, 200);
    }

    public function getAvailableBapaSchedule(Request $request) {
        $bapaName = $request['BAPAName'];

        $sched = BAPAReadingSchedules::where('BAPAName', $bapaName)
            ->whereNull('Status')
            ->orderByDesc('ServicePeriod')
            ->get();

        if ($sched != null) {
            return response()->json($sched, 200);
        } else {
            return response()->json([], 200);
        }
    }

    public function getBapaAccountList(Request $request) {
        $bapaName = $request['BAPAName'];
        $period = $request['ServicePeriod'];

        $prevMonth = date('Y-m-01', strtotime($period . ' -1 month'));

        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->whereIn('Billing_ServiceAccounts.AccountStatus', ['ACTIVE', 'DISCONNECTED'])
            ->whereRaw("Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND AccountNumber IS NOT NULL)")
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
                'Billing_ServiceAccounts.OrganizationParentAccount',
                'CRM_Towns.Town as TownFull',
                'CRM_Barangays.Barangay as BarangayFull',
                'Billing_ServiceAccounts.Purok',
                'Billing_Collectibles.Balance',
                DB::raw("(SELECT TOP 1 Amount FROM Billing_ArrearsLedgerDistribution WHERE AccountNumber=Billing_ServiceAccounts.id AND IsPaid IS NULL AND ServicePeriod='" . $period . "') AS ArrearsLedger"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                DB::raw("(SELECT TOP 1 CAST(ReadingTimestamp AS DATE) FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS ReadingTimestamp"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                DB::raw("(SELECT SUM(CAST(NetAmount AS DECIMAL(10, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.id)) AS ArrearsTotal"),
                DB::raw("'" . date('Y-m-d', strtotime($period)) . "' AS ServicePeriod"))
            ->get();

            
        /**
         * CHECK IF RATE IS AVAILABLE
         */
        $rates = Rates::where('ServicePeriod', $period) 
            ->get();

        if (count($rates) > 0) {
            return response()->json($accounts, $this->successStatus);
        } else {
            return response()->json([], 404);
        }       
    }

    public function updateBapaSchedule(Request $request) {
        $id = $request['id'];

        $bapaSched = BAPAReadingSchedules::find($id);
        $bapaSched->Status = 'Downloaded';
        $bapaSched->save();

        return response()->json($bapaSched, 200);
    }

    /**
     * HIGH VOLTAGE
     */
    public function downloadHvAccounts(Request $request) {
        $town = $request['Town'];
        $prevMonth = date('Y-m-01', strtotime($request['ServicePeriod'] . ' -1 month'));

        if ($town == '00') { // ALL
            $accounts = DB::table('Billing_ServiceAccounts')
                ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
                ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                ->whereRaw("Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "' AND AccountNumber IS NOT NULL)")
                ->whereIn('Billing_ServiceAccounts.AccountType', ['PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE'])
                ->whereIn('Billing_ServiceAccounts.AccountStatus', ['ACTIVE', 'DISCONNECTED'])
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
                    DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                    DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                    DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                    DB::raw("(SELECT SUM(CAST(NetAmount AS DECIMAL(10, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.id)) AS ArrearsTotal"),
                    DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"))
                ->get();
        } else { // PER TOWN
            $accounts = DB::table('Billing_ServiceAccounts')
                ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
                ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                ->where('Billing_ServiceAccounts.Town', $town)
                ->whereRaw("Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "')")
                ->whereIn('Billing_ServiceAccounts.AccountType', ['PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE'])
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
                    DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                    DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                    DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                    DB::raw("(SELECT SUM(CAST(NetAmount AS DECIMAL(10, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.id)) AS ArrearsTotal"),
                    DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"))
                ->get();
        }

        

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
}