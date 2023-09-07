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
use App\Models\KatasNgVat;
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
        // $accounts = ServiceAccounts::where('Town', $request['AreaCode'])
        //     ->where('GroupCode', $request['GroupCode'])
        //     ->get();

        $prevMonth = date('Y-m-01', strtotime($request['ServicePeriod'] . ' -1 month'));

        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('Billing_KatasNgVat', 'Billing_ServiceAccounts.id', '=', 'Billing_KatasNgVat.AccountNumber')
            ->whereRaw("Billing_ServiceAccounts.MeterReader='" . $request['MeterReader'] . "' AND Billing_ServiceAccounts.Item1 IS NULL AND Billing_ServiceAccounts.Town='" . $request['AreaCode'] . "' AND Billing_ServiceAccounts.GroupCode='" . $request['GroupCode'] . "' AND Billing_ServiceAccounts.AccountStatus IN ('ACTIVE', 'DISCONNECTED') AND Billing_ServiceAccounts.OrganizationParentAccount IS NULL")
            ->whereRaw("(Billing_ServiceAccounts.NetMetered IS NULL OR Billing_ServiceAccounts.NetMetered='No') AND (Billing_ServiceAccounts.Main IS NULL OR Billing_ServiceAccounts.Main='No') 
                AND Billing_ServiceAccounts.AccountType NOT IN ('PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE')
                AND Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "' AND AccountNumber IS NOT NULL)")
            ->whereRaw("((Billing_ServiceAccounts.AccountExpiration > '" . date('Y-m-d') . "' AND AccountRetention='Temporary') OR AccountRetention='Permanent' OR AccountExpiration IS NULL)")
            ->select('Billing_ServiceAccounts.id', 
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.Multiplier',
                // 'Billing_ServiceAccounts.Coreloss',
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
                'Billing_KatasNgVat.Balance as KatasNgVat',
                DB::raw("(SELECT TOP 1 Amount FROM Billing_ArrearsLedgerDistribution WHERE AccountNumber=Billing_ServiceAccounts.id AND IsPaid IS NULL AND ServicePeriod='" . $request['ServicePeriod'] . "') AS ArrearsLedger"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE ServicePeriod=(SELECT TOP 1 ServicePeriod FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY ServicePeriod DESC) AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE ServicePeriod=(SELECT TOP 1 ServicePeriod FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY ServicePeriod DESC) AND AccountNumber=Billing_ServiceAccounts.id) AS PrevKwhUsed"),
                DB::raw("(SELECT TOP 1 CAST(ReadingTimestamp AS DATE) FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS ReadingTimestamp"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                DB::raw("(SELECT SUM(TRY_CAST(NetAmount AS DECIMAL(8, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND AccountNumber IS NOT NULL AND (Status IS NULL OR Status='Application') AND ServicePeriod=Billing_Bills.ServicePeriod)) AS ArrearsTotal"),
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

        $readingsOg = Readings::find($request['id']);

        if ($readingsOg != null) {
            $input['id'] = IDGenerator::generateIDandRandString();
            $reading = Readings::create($input);
        } else {
            if ($request['AccountNumber'] != null) {
                $readings = Readings::where('ServicePeriod', $input['ServicePeriod'])
                    ->where('AccountNumber', $request['AccountNumber'])
                    ->first();
                
                if ($readings != null) {
                    // update
                    // $reading = Readings::update($request->all(), $readings->id);
                } else {
                    //create
                    $reading = Readings::create($input);
                }
            } else {
                $reading = Readings::create($input);
            } 
        }              

        return response()->json(['res' => 'ok'], $this->successStatus);
    }

    public function receiveBills(Request $request) {
        $input = $request->all();

        if ($input['AccountNumber'] != null) {
            $bills = Bills::where('ServicePeriod', $input['ServicePeriod'])
                ->where('AccountNumber', $input['AccountNumber'])
                ->first();

            $prepaymentBalance = PrePaymentBalance::where('AccountNumber', $input['AccountNumber'])->first();
            
            if ($bills != null) {
                // update
                // if ($prepaymentBalance != null) {
                //     if ($input['ExcessDeposit'] != null) {
                //         $prepaymentBalance->Balance = $input['ExcessDeposit'];
                //         $prepaymentBalance->save();
                        
                //         // ADD TRANSACTION HISTORY
                //         $transHistory = new PrePaymentTransHistory;
                //         $transHistory->id = IDGenerator::generateIDandRandString();
                //         $transHistory->AccountNumber = $input['AccountNumber'];
                //         $transHistory->Method = 'DEDUCT';
                //         $transHistory->Amount = $input['DeductedDeposit'];
                //         $transHistory->UserId = $input['UserId']; 
                //         $transHistory->save();
                //     } else {
                //         $prepaymentBalance->Balance = "0";
                //         $prepaymentBalance->save();

                //         // ADD TRANSACTION HISTORY
                //         $transHistory = new PrePaymentTransHistory;
                //         $transHistory->id = IDGenerator::generateIDandRandString();
                //         $transHistory->AccountNumber = $input['AccountNumber'];
                //         $transHistory->Method = 'DEDUCT';
                //         $transHistory->Amount = $input['DeductedDeposit'];
                //         $transHistory->UserId = $input['UserId']; 
                //         $transHistory->save();
                //     }

                //     // MARK AS PAID
                //     $netAmnt = intval($input['NetAmount']);
                //     if ($netAmnt == 0) {
                //         // GET LAST OR OF DEPOSIT FIRST
                //         $histLast = PrePaymentTransHistory::where('AccountNumber', $input['AccountNumber'])
                //             ->where('Method', 'DEPOSIT')
                //             ->orderByDesc('created_at')
                //             ->first();
                        
                //         if ($histLast != null) {
                //             // INSERT TO PAID BILLS
                //             $paidBills = new PaidBills;
                //             $paidBills->id = IDGenerator::generateIDandRandString();
                //             $paidBills->BillNumber = $input['BillNumber'];
                //             $paidBills->AccountNumber = $input['AccountNumber'];
                //             $paidBills->ServicePeriod = $input['ServicePeriod'];
                //             $paidBills->ORNumber = $histLast->ORNumber;
                //             $paidBills->ORDate = date('Y-m-d');
                //             $paidBills->KwhUsed = $input['KwhUsed'];
                //             $paidBills->Teller = $histLast->UserId;
                //             $paidBills->OfficeTransacted = env('APP_LOCATION');
                //             $paidBills->PostingDate = date('Y-m-d');
                //             $paidBills->PostingTime = date('H:i:s');
                //             $paidBills->Surcharge = 0;
                //             $paidBills->Deductions = $input['DeductedDeposit'];
                //             $paidBills->NetAmount = "0";
                //             $paidBills->Source = 'MONTHLY BILL - Pre-Payments';
                //             $paidBills->ObjectSourceId = $input['id'];
                //             $paidBills->UserId = $input['UserId'];
                //             $paidBills->save();
                //         }
                //     }
                // }
                
                // $bill = Bills::update($request->all(), $bills->id);
            } else {
                //create
                /**
                 * ASSESS PREPAYMENTS
                 */
                if ($prepaymentBalance != null) {
                    if (isset($input['ExcessDeposit']) && $input['ExcessDeposit'] != null) {
                        if (floatval($input['ExcessDeposit']) <= 0) {
                            $prepaymentBalance->Balance = "0";
                            $prepaymentBalance->save();

                            // ADD TRANSACTION HISTORY
                            $transHistory = new PrePaymentTransHistory;
                            $transHistory->id = IDGenerator::generateIDandRandString();
                            $transHistory->AccountNumber = $input['AccountNumber'];
                            $transHistory->Method = 'DEDUCT';
                            $transHistory->Amount = isset($input['DeductedDeposit']) ? $input['DeductedDeposit'] : 0;
                            $transHistory->UserId = $input['UserId']; 
                            $transHistory->save();
                        } else {
                            $prepaymentBalance->Balance = $input['ExcessDeposit'];
                            $prepaymentBalance->save();
                            
                            // ADD TRANSACTION HISTORY
                            $transHistory = new PrePaymentTransHistory;
                            $transHistory->id = IDGenerator::generateIDandRandString();
                            $transHistory->AccountNumber = $input['AccountNumber'];
                            $transHistory->Method = 'DEDUCT';
                            $transHistory->Amount = isset($input['DeductedDeposit']) ? $input['DeductedDeposit'] : 0;
                            $transHistory->UserId = $input['UserId']; 
                            $transHistory->save();
                        } 
                    } else {
                        $prepaymentBalance->Balance = "0";
                        $prepaymentBalance->save();

                        // ADD TRANSACTION HISTORY
                        $transHistory = new PrePaymentTransHistory;
                        $transHistory->id = IDGenerator::generateIDandRandString();
                        $transHistory->AccountNumber = $input['AccountNumber'];
                        $transHistory->Method = 'DEDUCT';
                        $transHistory->Amount = isset($input['DeductedDeposit']) ? $input['DeductedDeposit'] : 0;
                        $transHistory->UserId = $input['UserId']; 
                        $transHistory->save();
                    }

                    // MARK AS PAID
                    $netAmnt = $input['NetAmount'] != null ? intval($input['NetAmount']) : 0;
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
                            $paidBills->ORNumber = $histLast !=null ? $histLast->ORNumber : IDGenerator::generateID();
                            $paidBills->ORDate = date('Y-m-d');
                            $paidBills->KwhUsed = $input['KwhUsed'];
                            $paidBills->Teller = $histLast !=null ? $histLast->UserId : $input['UserId'];
                            $paidBills->OfficeTransacted = env('APP_LOCATION');
                            $paidBills->PostingDate = date('Y-m-d');
                            $paidBills->PostingTime = date('H:i:s');
                            $paidBills->Surcharge = 0;
                            $paidBills->Deductions = isset($input['DeductedDeposit']) ? $input['DeductedDeposit'] : 0;
                            $paidBills->NetAmount = "0";
                            $paidBills->Source = 'MONTHLY BILL - Pre-Payments';
                            $paidBills->ObjectSourceId = $input['id'];
                            $paidBills->UserId = $input['UserId'];
                            $paidBills->save();
                        }
                    }
                }

                /**
                 * ASSESS KATAS
                 */
                if (isset($input['KatasNgVat']) && $input['KatasNgVat'] != null) {
                    $katas = KatasNgVat::where('AccountNumber', $request['AccountNumber'])->first();

                    if ($katas != null) {
                        $katasBal = floatval($katas->Balance);
                        $katasDeducted = floatval($request['KatasNgVat']);
                        $katasRemain = $katasBal - $katasDeducted;

                        $katas->Balance = round($katasRemain, 2);
                        $katas->save();
                    }
                }

                $bill = Bills::create($input);
            }
        } else {
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
        set_time_limit(500);
        $bapaName = $request['BAPAName'];
        $period = $request['ServicePeriod'];

        $prevMonth = date('Y-m-01', strtotime($period . ' -1 month'));

        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('Billing_KatasNgVat', 'Billing_ServiceAccounts.id', '=', 'Billing_KatasNgVat.AccountNumber')
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->whereIn('Billing_ServiceAccounts.AccountStatus', ['ACTIVE', 'DISCONNECTED'])
            ->whereRaw("(Billing_ServiceAccounts.NetMetered IS NULL OR Billing_ServiceAccounts.NetMetered='No') AND (Billing_ServiceAccounts.Main IS NULL OR Billing_ServiceAccounts.Main='No') 
                AND Billing_ServiceAccounts.AccountType NOT IN ('PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE')
                AND Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "' AND AccountNumber IS NOT NULL)")
            // ->where(function ($query) {
            //     $query->where(function($queryX) {
            //             $queryX->where('Billing_ServiceAccounts.AccountExpiration', '>', date('Y-m-d'))
            //                 ->where('Billing_ServiceAccounts.AccountRetention', 'Temporary');
            //         })
            //         ->orWhere('Billing_ServiceAccounts.AccountRetention', 'Permanent')
            //         ->orWhereNull('Billing_ServiceAccounts.AccountExpiration');
            // })
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
                'Billing_KatasNgVat.Balance as KatasNgVat',
                DB::raw("(SELECT TOP 1 Amount FROM Billing_ArrearsLedgerDistribution WHERE AccountNumber=Billing_ServiceAccounts.id AND IsPaid IS NULL AND ServicePeriod='" . $request['ServicePeriod'] . "') AS ArrearsLedger"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE ServicePeriod=(SELECT TOP 1 ServicePeriod FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY ServicePeriod DESC) AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE ServicePeriod=(SELECT TOP 1 ServicePeriod FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY ServicePeriod DESC) AND AccountNumber=Billing_ServiceAccounts.id) AS PrevKwhUsed"),
                DB::raw("(SELECT TOP 1 CAST(ReadingTimestamp AS DATE) FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS ReadingTimestamp"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                DB::raw("(SELECT SUM(TRY_CAST(NetAmount AS DECIMAL(8, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND AccountNumber IS NOT NULL AND (Status IS NULL OR Status='Application') AND ServicePeriod=Billing_Bills.ServicePeriod)) AS ArrearsTotal"),
                DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"))
            ->orderBy('Billing_ServiceAccounts.AreaCode')
            ->orderBy('Billing_ServiceAccounts.OldAccountNo')
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
        set_time_limit(1024);
        $town = $request['Town'];
        $prevMonth = date('Y-m-01', strtotime($request['ServicePeriod'] . ' -1 month'));

        if ($town == '00') { // ALL
            $accounts = DB::table('Billing_ServiceAccounts')
                    ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
                    ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                    ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                    ->leftJoin('Billing_KatasNgVat', 'Billing_ServiceAccounts.id', '=', 'Billing_KatasNgVat.AccountNumber')
                    ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
                    ->whereRaw("Billing_ServiceAccounts.AccountStatus IN ('ACTIVE', 'DISCONNECTED') AND Billing_ServiceAccounts.MeterReader='" . $request['MeterReader'] . "' AND (Billing_ServiceAccounts.AccountType IN ('PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE')
                            OR (Billing_ServiceAccounts.NetMetered='Yes' OR Billing_ServiceAccounts.Main='Yes'))
                        AND Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "' AND AccountNumber IS NOT NULL) ")
                    ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
                    // ->where(function ($query) {
                    //     $query->where(function($queryX) {
                    //             $queryX->where('Billing_ServiceAccounts.AccountExpiration', '>', date('Y-m-d'))
                    //                 ->where('Billing_ServiceAccounts.AccountRetention', 'Temporary');
                    //         })
                    //         ->orWhere('Billing_ServiceAccounts.AccountRetention', 'Permanent')
                    //         ->orWhereNull('Billing_ServiceAccounts.AccountExpiration');
                    // })
                    ->select(DB::raw("Billing_ServiceAccounts.id AS 'AccountId'"), 
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
                        'Billing_ServiceAccounts.NetMetered',
                        'CRM_Towns.Town as TownFull',
                        'CRM_Barangays.Barangay as BarangayFull',
                        'Billing_ServiceAccounts.Purok',
                        'Billing_Collectibles.Balance',
                        'Billing_KatasNgVat.Balance as KatasNgVat',
                        DB::raw("(SELECT TOP 1 Amount FROM Billing_ArrearsLedgerDistribution WHERE AccountNumber=Billing_ServiceAccounts.id AND IsPaid IS NULL AND ServicePeriod='" . $request['ServicePeriod'] . "') AS ArrearsLedger"),
                        DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                        DB::raw("(SELECT TOP 1 CAST(ReadingTimestamp AS DATE) FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS ReadingTimestamp"),
                        DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                        DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                        DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                        DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                        DB::raw("(SELECT SUM(TRY_CAST(NetAmount AS DECIMAL(8, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND AccountNumber IS NOT NULL AND (Status IS NULL OR Status='Application') AND ServicePeriod=Billing_Bills.ServicePeriod)) AS ArrearsTotal"),
                        DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"),                        
                        DB::raw("(SELECT TOP 1 DemandKwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS DemandKwhUsed"),
                        DB::raw("(SELECT TOP 1 SolarExportPresent FROM Billing_Bills WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS SolarKwhUsed"),
                        DB::raw("(SELECT TOP 1 SolarResidualCredit FROM Billing_Bills WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS SolarResidualCredit"),
                    )
                    ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                    ->get();
        } else { // PER TOWN
                $accounts = DB::table('Billing_ServiceAccounts')
                    ->leftJoin('Billing_Collectibles', 'Billing_ServiceAccounts.id', '=', 'Billing_Collectibles.AccountNumber')
                    ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                    ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                    ->leftJoin('Billing_KatasNgVat', 'Billing_ServiceAccounts.id', '=', 'Billing_KatasNgVat.AccountNumber')
                    ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
                    ->whereRaw("Billing_ServiceAccounts.AccountStatus IN ('ACTIVE', 'DISCONNECTED') AND Billing_ServiceAccounts.MeterReader='" . $request['MeterReader'] . "' AND Billing_ServiceAccounts.Town='" . $town . "' AND (Billing_ServiceAccounts.AccountType IN ('PUBLIC BUILDING HIGH VOLTAGE', 'COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE')
                            OR (Billing_ServiceAccounts.NetMetered='Yes' OR Billing_ServiceAccounts.Main='Yes'))
                        AND Billing_ServiceAccounts.id NOT IN (SELECT AccountNumber FROM Billing_Readings WHERE ServicePeriod='" . $request['ServicePeriod'] . "' AND AccountNumber IS NOT NULL)")
                    ->whereNull('Billing_ServiceAccounts.OrganizationParentAccount')
                    // ->where(function ($query) {
                    //     $query->where(function($queryX) {
                    //             $queryX->where('Billing_ServiceAccounts.AccountExpiration', '>', date('Y-m-d'))
                    //                 ->where('Billing_ServiceAccounts.AccountRetention', 'Temporary');
                    //         })
                    //         ->orWhere('Billing_ServiceAccounts.AccountRetention', 'Permanent')
                    //         ->orWhereNull('Billing_ServiceAccounts.AccountExpiration');
                    // })
                    ->select(DB::raw("Billing_ServiceAccounts.id AS 'AccountId'"), 
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
                        'Billing_ServiceAccounts.NetMetered',
                        'CRM_Towns.Town as TownFull',
                        'CRM_Barangays.Barangay as BarangayFull',
                        'Billing_ServiceAccounts.Purok',
                        'Billing_Collectibles.Balance',
                        'Billing_KatasNgVat.Balance as KatasNgVat',
                        DB::raw("(SELECT TOP 1 Amount FROM Billing_ArrearsLedgerDistribution WHERE AccountNumber=Billing_ServiceAccounts.id AND IsPaid IS NULL AND ServicePeriod='" . $request['ServicePeriod'] . "') AS ArrearsLedger"),
                        DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS KwhUsed"),
                        DB::raw("(SELECT TOP 1 CAST(ReadingTimestamp AS DATE) FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS ReadingTimestamp"),
                        DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterSerial"),
                        DB::raw("(SELECT TOP 1 Balance FROM Billing_PrePaymentBalance WHERE AccountNumber=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS Deposit"),
                        DB::raw("(SELECT TOP 1 AdditionalKwhForNextBilling FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterAdditionalKwh"),
                        DB::raw("(SELECT TOP 1 NewMeterStartKwh FROM Billing_ChangeMeterLogs WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "' ORDER BY created_at DESC) AS ChangeMeterStartKwh"),
                        DB::raw("(SELECT SUM(TRY_CAST(NetAmount AS DECIMAL(8, 2))) FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND MergedToCollectible IS NULL AND AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND AccountNumber IS NOT NULL AND (Status IS NULL OR Status='Application') AND ServicePeriod=Billing_Bills.ServicePeriod)) AS ArrearsTotal"),
                        DB::raw("'" . date('Y-m-d', strtotime($request['ServicePeriod'])) . "' AS ServicePeriod"),                        
                        DB::raw("(SELECT TOP 1 DemandKwhUsed FROM Billing_Readings WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS DemandKwhUsed"),
                        DB::raw("(SELECT TOP 1 SolarExportPresent FROM Billing_Bills WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS SolarKwhUsed"),
                        DB::raw("(SELECT TOP 1 SolarResidualCredit FROM Billing_Bills WHERE ServicePeriod='" . $prevMonth . "' AND AccountNumber=Billing_ServiceAccounts.id) AS SolarResidualCredit"),
                    )
                    ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
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