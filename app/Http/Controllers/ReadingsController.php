<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReadingsRequest;
use App\Http\Requests\UpdateReadingsRequest;
use App\Repositories\ReadingsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Bills;
use App\Models\Rates;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Readings;
use App\Models\Towns;
use App\Models\BillingMeters;
use App\Models\ServiceAccounts;
use App\Models\IDGenerator;
use Flash;
use Response;

class ReadingsController extends AppBaseController
{
    /** @var  ReadingsRepository */
    private $readingsRepository;

    public function __construct(ReadingsRepository $readingsRepo)
    {
        $this->middleware('auth');
        $this->readingsRepository = $readingsRepo;
    }

    /**
     * Display a listing of the Readings.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $readings = $this->readingsRepository->all();

        return view('readings.index')
            ->with('readings', $readings);
    }

    /**
     * Show the form for creating a new Readings.
     *
     * @return Response
     */
    public function create()
    {
        return view('readings.create');
    }

    /**
     * Store a newly created Readings in storage.
     *
     * @param CreateReadingsRequest $request
     *
     * @return Response
     */
    public function store(CreateReadingsRequest $request)
    {
        $input = $request->all();

        $readings = $this->readingsRepository->create($input);

        Flash::success('Readings saved successfully.');

        return redirect(route('readings.index'));
    }

    /**
     * Display the specified Readings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        return view('readings.show')->with('readings', $readings);
    }

    /**
     * Show the form for editing the specified Readings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        return view('readings.edit')->with('readings', $readings);
    }

    /**
     * Update the specified Readings in storage.
     *
     * @param int $id
     * @param UpdateReadingsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReadingsRequest $request)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        $readings = $this->readingsRepository->update($request->all(), $id);

        Flash::success('Readings updated successfully.');

        return redirect(route('readings.index'));
    }

    /**
     * Remove the specified Readings from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        $this->readingsRepository->delete($id);

        Flash::success('Readings deleted successfully.');

        return redirect(route('readings.index'));
    }

    public function readingMonitor() {
        $servicePeriods = DB::table('Billing_Readings')
            ->select('ServicePeriod')
            ->groupBy('ServicePeriod')
            ->orderByDesc('ServicePeriod')
            ->limit(30)
            ->get();

        return view('/readings/reading_monitor', [
            'servicePeriods' => $servicePeriods,
        ]);
    }

    public function readingMonitorView($servicePeriod) {
        $meterReaders = User::role('Meter Reader')->orderBy('name')->get();
        $towns = Towns::orderBy('id')->get();

        return view('/readings/reading_monitor_view', [
            'meterReaders' => $meterReaders,
            'servicePeriod' => $servicePeriod,
            'towns' => $towns,
        ]);
    }

    public function getReadingsFromMeterReader(Request $request) {
        $readings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.MeterReader', $request['MeterReader'])
            ->where('Billing_Readings.ServicePeriod', $request['ServicePeriod'])
            ->where('Billing_ServiceAccounts.GroupCode', $request['Day'])
            ->where('Billing_ServiceAccounts.Town', $request['Town'])
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode')
            ->orderBy('Billing_Readings.ReadingTimestamp')
            ->get();

        return response()->json($readings, 200);
    }

    public function manualReading(Request $request) {
        if ($request['params'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'Billing_ServiceAccounts.Purok', 'Billing_ServiceAccounts.OldAccountNo', 'CRM_Towns.Town', 'CRM_Barangays.Barangay', 'Billing_ServiceAccounts.AccountCount')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        } else {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'Billing_ServiceAccounts.Purok', 'Billing_ServiceAccounts.OldAccountNo', 'CRM_Towns.Town', 'CRM_Barangays.Barangay', 'Billing_ServiceAccounts.AccountCount')
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['params'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(18);
        }    

        return view('/readings/manual_reading', [
            'serviceAccounts' => $serviceAccounts
        ]);
    }

    public function manualReadingConsole($id) {
        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.OrganizationParentAccount',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Town as TownId',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'users.name as MeterReader')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $bills = DB::table('Billing_Bills')
            ->where('Billing_Bills.AccountNumber', $id)
            ->select('Billing_Bills.*',
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND Status IS NULL) AS ORNumber"),
                DB::raw("(SELECT TOP 1 ORDate FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND Status IS NULL) AS ORDate"),
                DB::raw("(SELECT TOP 1 id FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND Status IS NULL) AS PaidBillId"))
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();

        if ($serviceAccounts != null) {
            $rate = Rates::where('ConsumerType', Bills::getAccountType($serviceAccounts))
                ->where('AreaCode', $serviceAccounts->TownId)
                ->orderByDesc('ServicePeriod')
                ->first();

            $presentReading = Readings::where('AccountNumber', $id)
                ->orderByDesc('ServicePeriod')
                ->first();

            $latestBill = Bills::where('AccountNumber', $id)
                ->orderByDesc('ServicePeriod')
                ->first();

            return view('/readings/manual_reading_console', [
                'account' => $serviceAccounts,
                'meter' => $meters,
                'bills' => $bills,
                'rate' => $rate,
                'presentReading' => $presentReading,
                'latestBill' => $latestBill
            ]);
        } else {
            return abort(404, 'Account not found!');
        }
    }

    public function getComputedBill(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);

        if (Bills::isHighVoltage(Bills::getAccountType($account))) {
            $bills = Bills::computeHighVoltageBillAndDontSave($account, 
                null, 
                $request['KwhUsed'], 
                $request['PreviousKwh'], 
                $request['PresentKwh'], 
                $request['ServicePeriod'], 
                $request['BillingDate'], 
                0, 
                0, 
                $request['Is2307'],
                $request['Demand']);
        } else {
            $bills = Bills::computeRegularBillAndDontSave($account, 
                null, 
                $request['KwhUsed'], 
                $request['PreviousKwh'], 
                $request['PresentKwh'], 
                $request['ServicePeriod'], 
                $request['BillingDate'], 
                0, 
                0, 
                $request['Is2307']);
        }
        
        return response()->json($bills, 200);
    }

    public function createManualBilling(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);

        if (floatval($request['KwhUsed']) > -1) {
            if (Bills::isHighVoltage(Bills::getAccountType($account))) {
                // CREATE READING
                $readings = Readings::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();

                if ($readings != null) {
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->DemandKwhUsed = $request['Demand'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                } else {
                    $readings = new Readings;
                    $readings->id = IDGenerator::generateIDandRandString();
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->DemandKwhUsed = $request['Demand'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                }
                
                $bills = Bills::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();
                
                if ($bills != null) {
                    $bills = Bills::computeHighVoltageBill($account, 
                        $bills->id, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307'],
                        $request['Demand']);
                } else {
                    $bills = Bills::computeHighVoltageBill($account, 
                        null, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307'],
                        $request['Demand']);
                }            
            } else {
                // CREATE READING
                $readings = Readings::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();

                if ($readings != null) {
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                } else {
                    $readings = new Readings;
                    $readings->id = IDGenerator::generateIDandRandString();
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                }

                $bills = Bills::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();
                
                if ($bills != null) {
                    $bills = Bills::computeRegularBill($account, 
                        $bills->id, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307']);
                } else {
                    $bills = Bills::computeRegularBill($account, 
                        null, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307']);
                }            
            }

            Flash::success('Billing and reading saved!.');

            return redirect(route('readings.manual-reading'));
        } else {
            return abort(403, 'Invalid Reading. Your inputted reading is less than the previous one.');
        }
    }

    public function createManualBillingAjax(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);

        if (floatval($request['KwhUsed']) > -1) {
            if (Bills::isHighVoltage(Bills::getAccountType($account))) {
                // CREATE READING
                $readings = Readings::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();

                if ($readings != null) {
                    $readings->AccountNumber = $account->id;
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->DemandKwhUsed = $request['Demand'];
                    // $readings->MeterReader = Auth::id();
                    $readings->save();
                } else {
                    $readings = new Readings;
                    $readings->id = IDGenerator::generateIDandRandString();
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->DemandKwhUsed = $request['Demand'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                }
                
                $bills = Bills::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();
                
                if ($bills != null) {
                    $bills = Bills::computeHighVoltageBill($account, 
                        $bills->id, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        date('Y-m-d', strtotime($readings->ReadingTimestamp)), 
                        0, 
                        0, 
                        '',
                        $request['Demand']);
                } else {
                    $bills = Bills::computeHighVoltageBill($account, 
                        null, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        date('Y-m-d', strtotime($readings->ReadingTimestamp)), 
                        0, 
                        0, 
                        '',
                        $request['Demand']);
                }            
            } else {
                // CREATE READING
                $readings = Readings::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();

                if ($readings != null) {
                    $readings->AccountNumber = $account->id;
                    $readings->KwhUsed = $request['PresentKwh'];
                    // $readings->MeterReader = Auth::id();
                    $readings->save();
                } else {
                    $readings = new Readings;
                    $readings->id = IDGenerator::generateIDandRandString();
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                }

                $bills = Bills::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();
                
                if ($bills != null) {
                    $bills = Bills::computeRegularBill($account, 
                        $bills->id, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        date('Y-m-d', strtotime($readings->ReadingTimestamp)), 
                        0, 
                        0, 
                        '');
                } else {
                    $bills = Bills::computeRegularBill($account, 
                        null, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        date('Y-m-d', strtotime($readings->ReadingTimestamp)), 
                        0, 
                        0, 
                        '');
                }            
            }

            return response()->json('ok', 200);
        } else {
            return response()->json('amount negative', 200);
        }
    }

    public function capturedReadings(Request $request) {
        // $area = $request['Area'];
        // $groupCode = $request['GroupCode'];
        $meterReader = $request['MeterReader'];
        $period = $request['ServicePeriod'];

        if ($meterReader==null | $period==null) {
            $readings = null;
        } else {
            $readings = Readings::whereNull('AccountNumber')
                ->where('ServicePeriod', $period)
                ->where('MeterReader', $meterReader)
                ->get();
        }

        $meterReaders = User::role('Meter Reader')->get();

        return view('/readings/captured_readings', [
            'meterReaders' => $meterReaders,
            'readings' => $readings,
        ]);
    }

    public function markAsDone(Request $request) {
        $readings = Readings::find($request['id']);

        if ($readings != null) {
            $readings->AccountNumber = 'ERRONEOUS';
            $readings->save();
        }

        return response()->json($readings, 200);
    }

    public function fetchAccount(Request $request) {
        $account = ServiceAccounts::where('OldAccountNo', $request['OldAccountNo'])
            ->first();

        return response()->json($account, 200);
    }

    public function viewFullReportBapa($period, $bapaName) {
        $bapaName = urldecode($bapaName);

        // GET READING DAY FROM TIMESTAMP
        $reading = DB::table('Billing_Readings')
            ->whereNotNull('Billing_Readings.AccountNumber')
            ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->first();

        if ($reading != null) {
            $summary = DB::table('Billing_Readings')
                ->select(
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND AccountNumber IS NULL AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')) AS Captured"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')) AS Total"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE AccountStatus='DISCONNECTED' AND OrganizationParentAccount='" . $bapaName . "')) AS Disconnected"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND FieldStatus='STUCK-UP' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')) AS StuckUp"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND FieldStatus='CHANGE METER' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')) AS ChangeMeter"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND FieldStatus='NOT IN USE' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')) AS NotInUse"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND FieldStatus='NO DISPLAY' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')) AS NoDisplay"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND AccountNumber IS NOT NULL AND FieldStatus NOT IN ('STUCK-UP', 'CHANGE METER', 'NOT IN USE', 'NO DISPLAY') AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "') AND AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')) AS OtherUnbilled"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod='" . $period . "' AND AccountNumber IS NOT NULL AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "') AND AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')) AS TotalBilled")
                )
                ->first();
        } else {
            $summary = null;
        } 

        $readingReport = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.ServicePeriod', $period)
            // ->where(function ($query) use ($reading, $bapaName) {
            //     $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')")
            //             ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
            // })
            ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE OrganizationParentAccount='" . $bapaName . "')")
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.id AS AccountId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Multiplier',
                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "') AS PrevKwh"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                )
            ->orderBy('AccountStatus')
            ->orderBy('CurrentKwh')
            ->orderBy('FieldStatus')
            ->get();

        return view('/readings/view_full_report', [
            'period' => $period,
            'day' => '-',
            'meterReader' => null,
            'summary' => $summary,
            'reading' => $reading,
            'readingReport' => $readingReport,
            'town' => '-',
            'bapaName' => $bapaName,
        ]);
    }

    public function viewFullReport($period, $meterReader, $day, $town) {
        $meterReader = User::find($meterReader);

        // GET READING DAY FROM TIMESTAMP
        $reading = DB::table('Billing_Readings')
            ->whereNotNull('Billing_Readings.AccountNumber')
            ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
            ->first();

        if ($reading != null) {
            $summary = DB::table('Billing_Readings')
                ->select(
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')) AS Captured"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')) AS Total"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE AccountStatus='DISCONNECTED' AND Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')) AS Disconnected"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND FieldStatus='STUCK-UP' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')) AS StuckUp"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND FieldStatus='CHANGE METER' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')) AS ChangeMeter"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND FieldStatus='NOT IN USE' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')) AS NotInUse"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND FieldStatus='NO DISPLAY' AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')) AS NoDisplay"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND AccountNumber IS NOT NULL AND FieldStatus NOT IN ('STUCK-UP', 'CHANGE METER', 'NOT IN USE', 'NO DISPLAY') AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "') AND AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')) AS OtherUnbilled"),
                    DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE MeterReader='" . $meterReader->id . "' AND ServicePeriod='" . $period . "' AND AccountNumber IS NOT NULL AND AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "') AND AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')) AS TotalBilled")
                )
                ->first();
        } else {
            $summary = null;
        } 

        $readingReport = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->where(function ($query) use ($town, $day, $reading, $meterReader) {
                $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                        ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
            })
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.id AS AccountId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Multiplier',
                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "') AS PrevKwh"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                )
            ->orderBy('AccountStatus')
            ->orderBy('CurrentKwh')
            ->orderBy('FieldStatus')
            ->get();
        
        return view('/readings/view_full_report', [
            'period' => $period,
            'day' => $day,
            'meterReader' => $meterReader,
            'summary' => $summary,
            'reading' => $reading,
            'readingReport' => $readingReport,
            'town' => $town,
            'bapaName' => null,
        ]);
    }

    public function getPreviousReadings(Request $request) {
        $readings = DB::table('Billing_Readings')
            ->leftJoin('users', 'Billing_Readings.MeterReader', '=', 'users.id')
            ->where('AccountNumber', $request['AccountNumber'])
            ->select('Billing_Readings.*', 'users.name')
            ->orderByDesc('ServicePeriod')
            ->get();

        return response()->json($readings, 200);
    }

    public function checkIfAccountHasBill(Request $request) {
        $bills = Bills::where('ServicePeriod', $request['ServicePeriod'])
            ->where('AccountNumber', $request['AccountNumber'])
            ->first();
        
        if ($bills != null) {
            return response()->json('has bill', 200);
        } else {
            return response()->json('ok', 200);
        }
    }

    public function capturedReadingsConsole($id, $readId, $day, $bapaName) {
        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.OrganizationParentAccount',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Town as TownId',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'users.name as MeterReader')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $bills = DB::table('Billing_Bills')
            ->where('Billing_Bills.AccountNumber', $id)
            ->select('Billing_Bills.*',
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND Status IS NULL) AS ORNumber"),
                DB::raw("(SELECT TOP 1 ORDate FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND Status IS NULL) AS ORDate"),
                DB::raw("(SELECT TOP 1 id FROM Cashier_PaidBills WHERE ObjectSourceId=Billing_Bills.id AND Status IS NULL) AS PaidBillId"))
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();

        $reading = Readings::find($readId);

        if ($serviceAccounts != null) {
            $rate = Rates::where('ConsumerType', Bills::getAccountType($serviceAccounts))
                ->where('AreaCode', $serviceAccounts->TownId)
                ->orderByDesc('ServicePeriod')
                ->first();

            $prevReading = Readings::where('AccountNumber', $id)
                ->where('ServicePeriod', date('Y-m-01', strtotime($reading->ServicePeriod . ' -1 month')))
                ->orderByDesc('ServicePeriod')
                ->first();

            $latestBill = Bills::where('AccountNumber', $id)
                ->orderByDesc('ServicePeriod')
                ->first();

            return view('/readings/captured_readings_console', [
                'account' => $serviceAccounts,
                'meter' => $meters,
                'bills' => $bills,
                'rate' => $rate,
                'prevReading' => $prevReading,
                'reading' => $reading,
                'latestBill' => $latestBill,
                'day' => $day,
                'bapaName' => $bapaName
            ]);
        } else {
            return abort(404, 'Account not found!');
        }
    }

    public function createBillForCapturedReading(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);

        $readings = Readings::find($request['ReadingId']);

        if (floatval($request['KwhUsed']) > -1) {
            if (Bills::isHighVoltage(Bills::getAccountType($account))) {

                if ($readings != null) {
                    $readings->AccountNumber = $account->id;
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->DemandKwhUsed = $request['Demand'];
                    $readings->save();
                } else {
                    $readings = new Readings;
                    $readings->id = IDGenerator::generateIDandRandString();
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->DemandKwhUsed = $request['Demand'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                }
                
                $bills = Bills::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();
                
                if ($bills != null) {
                    $bills = Bills::computeHighVoltageBill($account, 
                        $bills->id, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307'],
                        $request['Demand']);
                } else {
                    $bills = Bills::computeHighVoltageBill($account, 
                        null, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307'],
                        $request['Demand']);
                }   
                $bills->Notes = 'CAPTURED';
                $bills->save();             
            } else {
                if ($readings != null) {
                    $readings->AccountNumber = $account->id;
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->save();
                } else {
                    $readings = new Readings;
                    $readings->id = IDGenerator::generateIDandRandString();
                    $readings->AccountNumber = $account->id;
                    $readings->ServicePeriod = $request['ServicePeriod'];
                    $readings->ReadingTimestamp = date('Y-m-d H:i:s');
                    $readings->KwhUsed = $request['PresentKwh'];
                    $readings->MeterReader = Auth::id();
                    $readings->save();
                }

                $bills = Bills::where('AccountNumber', $account->id)
                    ->where('ServicePeriod', $request['ServicePeriod'])
                    ->first();
                
                if ($bills != null) {
                    $bills = Bills::computeRegularBill($account, 
                        $bills->id, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307']);
                } else {
                    $bills = Bills::computeRegularBill($account, 
                        null, 
                        $request['KwhUsed'], 
                        $request['PreviousKwh'], 
                        $request['PresentKwh'], 
                        $request['ServicePeriod'], 
                        $request['ReadingDate'], 
                        0, 
                        0, 
                        $request['Is2307']);                    
                }  
                $bills->Notes = 'CAPTURED';
                $bills->save();          
            }

            Flash::success('Billing and reading saved!.');

            if ($request['BapaName'] == 'mreader') {
                return redirect(route('readings.view-full-report', [$request['ServicePeriod'], $readings->MeterReader, $request['Day'], $account->Town]));
            } else {
                return redirect(route('readings.view-full-report-bapa', [$request['ServicePeriod'], $request['BapaName']]));
            }            
        } else {
            return abort(403, 'Invalid Reading. Your inputted reading is less than the previous one.');
        }
    }

    public function printOldFormatAdjusted($period, $day, $town, $meterReader) {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ServicePeriod', $period)
            ->where('Billing_ServiceAccounts.Town', $town)
            ->where('Billing_ServiceAccounts.GroupCode', $day)
            ->whereRaw("Billing_ServiceAccounts.MeterReader='" . $meterReader . "'")
            ->whereRaw("Billing_Bills.UserId ='". Auth::id() . "'")
            ->whereRaw("Billing_Bills.AccountNumber IN (SELECT AccountNumber FROM Billing_Readings WHERE FieldStatus IS NOT NULL AND ServicePeriod='" . $period . "' AND MeterReader='" . $meterReader . "' AND AccountNumber IS NOT NULL)")
            ->select('Billing_Bills.*')
            ->orderBy('Billing_Bills.BillNumber')
            ->get();

        return view('/bills/print_bulk_old_format', [
            'bills' => $bills
        ]);
    }

    public function printNewFormatAdjusted($period, $day, $town, $meterReader) {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ServicePeriod', $period)
            ->where('Billing_ServiceAccounts.Town', $town)
            ->where('Billing_ServiceAccounts.GroupCode', $day)
            ->whereRaw("Billing_ServiceAccounts.MeterReader ='" . $meterReader . "'")
            ->whereRaw("Billing_Bills.UserId ='". Auth::id() . "'")
            ->whereRaw("Billing_Bills.AccountNumber IN (SELECT AccountNumber FROM Billing_Readings WHERE FieldStatus IS NOT NULL AND ServicePeriod='" . $period . "' AND MeterReader='" . $meterReader . "' AND AccountNumber IS NOT NULL)")
            ->select('Billing_Bills.*')
            ->orderBy('Billing_Bills.BillNumber')
            ->get();

        return view('/bills/print_bulk_bill_new_format', [
            'bills' => $bills
        ]);
    }

    public function printOldFormatAdjustedBapa($period, $bapaName) {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ServicePeriod', $period)
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->whereRaw("Billing_Bills.UserId ='". Auth::id() . "'")
            ->select('Billing_Bills.*')
            ->orderBy('Billing_Bills.BillNumber')
            ->get();

        return view('/bills/print_bulk_old_format', [
            'bills' => $bills
        ]);
    }

    public function printNewFormatAdjustedBapa($period, $bapaName) {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ServicePeriod', $period)
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->whereRaw("Billing_Bills.UserId ='". Auth::id() . "'")
            ->select('Billing_Bills.*')
            ->orderBy('Billing_Bills.BillNumber')
            ->get();

        return view('/bills/print_bulk_bill_new_format', [
            'bills' => $bills
        ]);
    }

    public function printUnbilledList($period, $day, $town, $meterReader, $status) {
        $meterReader = User::find($meterReader);

        $reading = DB::table('Billing_Readings')
            ->whereNotNull('Billing_Readings.AccountNumber')
            ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
            ->first();

        $readingReport = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->where('Billing_Readings.FieldStatus', $status)
            ->where(function ($query) use ($town, $day, $reading, $meterReader) {
                $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                        ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
            })
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.id AS AccountId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Multiplier',
                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                )
            ->orderBy('AccountStatus')
            ->orderBy('FieldStatus')
            ->get();

        return view('/readings/print_unbilled_by_status', [
            'period' => $period,
            'day' => $day,
            'town' => $town,
            'status' => $status,
            'meterReader' => $meterReader,
            'readingReport' => $readingReport,
        ]);
    }

    public function printOtherUnbilledList($period, $day, $town, $meterReader) {
        $meterReader = User::find($meterReader);

        $reading = DB::table('Billing_Readings')
            ->whereNotNull('Billing_Readings.AccountNumber')
            ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
            ->first();

        $readingReport = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
            ->where('Billing_Readings.ServicePeriod', $period)
            ->whereNotIn('Billing_Readings.FieldStatus', ['STUCK-UP', 'NO DISPLAY', 'NOT IN USE', 'CHANGE METER'])
            ->where(function ($query) use ($town, $day, $reading, $meterReader) {
                $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                        ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
            })
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.id AS AccountId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.Multiplier',
                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                )
            ->orderBy('AccountStatus')
            ->orderBy('FieldStatus')
            ->get();

        return view('/readings/print_unbilled_by_status', [
            'period' => $period,
            'day' => $day,
            'town' => $town,
            'status' => 'OTHER UNBILLED',
            'meterReader' => $meterReader,
            'readingReport' => $readingReport,
        ]);
    }

    public function billAndUnbilledReport(Request $request) {
        $type = $request['Type']; // billed, unbilled
        $meterReader = $request['MeterReader'];
        $day = $request['Day'];
        $period = $request['ServicePeriod'];
        $town = $request['Office'];

        $meterReaders = User::role('Meter Reader')->orderBy('name')->get();

        if ($type == null && $meterReader == null && $day == null && $period == null && $town == null) {
            $readingReport = [];
        } else {
            $meterReader = User::find($meterReader);
            if ($type == 'Billed') {
                if ($day == 'All') {
                    $readingReport = DB::table('Billing_Readings')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
                        ->where('Billing_Readings.ServicePeriod', $period)    
                        ->where('Billing_ServiceAccounts.Town', $town)  
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                        ->select('Billing_Readings.*',
                            'Billing_ServiceAccounts.id AS AccountId',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.AreaCode',
                            DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                            )
                        ->orderBy('Billing_ServiceAccounts.AreaCode')
                        ->orderBy('Billing_ServiceAccounts.SequenceCode')
                        ->orderBy('AccountStatus')
                        ->orderBy('FieldStatus')
                    ->get();
                } else {
                    $readingReport = DB::table('Billing_Readings')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
                        ->where('Billing_Readings.ServicePeriod', $period)    
                        ->where('Billing_ServiceAccounts.Town', $town)  
                        ->where('Billing_ServiceAccounts.GroupCode', $day)
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                        ->select('Billing_Readings.*',
                            'Billing_ServiceAccounts.id AS AccountId',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.AreaCode',
                            DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                            )
                        ->orderBy('Billing_ServiceAccounts.AreaCode')
                        ->orderBy('Billing_ServiceAccounts.SequenceCode')
                        ->orderBy('AccountStatus')
                        ->orderBy('FieldStatus')
                    ->get();
                }
            } else {
                // UNBILLED
                if ($day == 'All') {
                    $reading = DB::table('Billing_Readings')
                        ->whereNotNull('Billing_Readings.AccountNumber')
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE MeterReader='" . $meterReader->id . "')")
                        ->where('Billing_Readings.ServicePeriod', $period)
                        ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
                        ->orderByDesc('ReadingTimestamp')
                        ->first();

                    $readingReport = DB::table('Billing_Readings')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
                        ->where('Billing_Readings.ServicePeriod', $period)      
                        ->where(function ($query) use ($reading, $period, $town, $meterReader) {
                            $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND MeterReader='" . $meterReader->id . "')")
                                ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                                ->orWhereRaw("Billing_Readings.AccountNumber IS NULL");
                        })
                        ->select('Billing_Readings.*',
                            'Billing_ServiceAccounts.id AS AccountId',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.AreaCode',
                            DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                            )
                        ->orderBy('Billing_ServiceAccounts.AreaCode')
                        ->orderBy('AccountStatus')
                        ->orderBy('FieldStatus')
                    ->get();
                } else {
                    $reading = DB::table('Billing_Readings')
                        ->whereNotNull('Billing_Readings.AccountNumber')
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                        ->where('Billing_Readings.ServicePeriod', $period)
                        ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
                        ->orderByDesc('ReadingTimestamp')
                        ->first();

                    if ($reading != null) {
                        $readingReport = DB::table('Billing_Readings')
                            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                            ->whereRaw("Billing_Readings.MeterReader='" . $meterReader->id . "'")
                            ->where('Billing_Readings.ServicePeriod', $period)    
                            ->where(function ($query) use ($reading, $period, $town, $day, $meterReader) {
                                $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                                    ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                                    ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
                            })
                            ->select('Billing_Readings.*',
                                'Billing_ServiceAccounts.id AS AccountId',
                                'Billing_ServiceAccounts.OldAccountNo',
                                'Billing_ServiceAccounts.ServiceAccountName',
                                'Billing_ServiceAccounts.SequenceCode',
                                'Billing_ServiceAccounts.AccountStatus',
                                'Billing_ServiceAccounts.Multiplier',
                                'Billing_ServiceAccounts.AreaCode',
                                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                                )
                            ->orderBy('Billing_ServiceAccounts.AreaCode')
                            ->orderBy('AccountStatus')
                            ->orderBy('FieldStatus')
                        ->get();
                    } else {
                        $readingReport = [];
                    }                    
                }            
            }
        }        

        return view('/readings/reports_billed_unbilled', [
            'meterReaders' => $meterReaders,
            'readingReport' => $readingReport,
        ]);
    }

    public function printBilledUnbilled($type, $meterReader, $day, $period, $town) {     
        if ($type == null && $meterReader == null && $day == null && $period == null && $town == null) {
            return abort(404, 'MISSIG PARAMETERS');
        } else {
            $meterReader = User::find($meterReader);
            if ($type == 'Billed') {
                if ($day == 'All') {
                    $readingReport = DB::table('Billing_Readings')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
                        ->where('Billing_Readings.ServicePeriod', $period)    
                        ->where('Billing_ServiceAccounts.Town', $town)  
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                        ->select('Billing_Readings.*',
                            'Billing_ServiceAccounts.id AS AccountId',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.AreaCode',
                            DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                            )
                        ->orderBy('Billing_ServiceAccounts.AreaCode')
                        ->orderBy('Billing_ServiceAccounts.SequenceCode')
                        ->orderBy('AccountStatus')
                        ->orderBy('FieldStatus')
                    ->get();
                } else {
                    $readingReport = DB::table('Billing_Readings')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
                        ->where('Billing_Readings.ServicePeriod', $period)    
                        ->where('Billing_ServiceAccounts.Town', $town)  
                        ->where('Billing_ServiceAccounts.GroupCode', $day)
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                        ->select('Billing_Readings.*',
                            'Billing_ServiceAccounts.id AS AccountId',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.AreaCode',
                            DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                            )
                        ->orderBy('Billing_ServiceAccounts.AreaCode')
                        ->orderBy('Billing_ServiceAccounts.SequenceCode')
                        ->orderBy('AccountStatus')
                        ->orderBy('FieldStatus')
                    ->get();
                }
            } else {
                // UNBILLED
                if ($day == 'All') {
                    $reading = DB::table('Billing_Readings')
                        ->whereNotNull('Billing_Readings.AccountNumber')
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE MeterReader='" . $meterReader->id . "')")
                        ->where('Billing_Readings.ServicePeriod', $period)
                        ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
                        ->orderByDesc('ReadingTimestamp')
                        ->first();

                    $readingReport = DB::table('Billing_Readings')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
                        ->where('Billing_Readings.ServicePeriod', $period)      
                        ->where(function ($query) use ($reading, $period, $town, $meterReader) {
                            $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND MeterReader='" . $meterReader->id . "')")
                                ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                                ->orWhereRaw("Billing_Readings.AccountNumber IS NULL");
                        })
                        ->select('Billing_Readings.*',
                            'Billing_ServiceAccounts.id AS AccountId',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.SequenceCode',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.Multiplier',
                            'Billing_ServiceAccounts.AreaCode',
                            DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                            DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                            DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                            )
                        ->orderBy('Billing_ServiceAccounts.AreaCode')
                        ->orderBy('AccountStatus')
                        ->orderBy('FieldStatus')
                    ->get();
                } else {
                    $reading = DB::table('Billing_Readings')
                        ->whereNotNull('Billing_Readings.AccountNumber')
                        ->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                        ->where('Billing_Readings.ServicePeriod', $period)
                        ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
                        ->orderByDesc('ReadingTimestamp')
                        ->first();

                    if ($reading != null) {
                        $readingReport = DB::table('Billing_Readings')
                            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                            ->whereRaw("Billing_Readings.MeterReader = '" . $meterReader->id . "'")
                            ->where('Billing_Readings.ServicePeriod', $period)    
                            ->where(function ($query) use ($reading, $period, $town, $day, $meterReader) {
                                $query->whereRaw("Billing_Readings.AccountNumber IN (SELECT id FROM Billing_ServiceAccounts WHERE Town='" . $town . "' AND GroupCode='" . $day . "' AND MeterReader='" . $meterReader->id . "')")
                                    ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $period . "')")
                                    ->orWhereRaw("Billing_Readings.AccountNumber IS NULL AND (ReadingTimestamp BETWEEN '" . date('Y-m-d', strtotime($reading->ReadingTimestamp)) . "' AND '" . date('Y-m-d', strtotime($reading->ReadingTimestamp . ' +1 day')) . "')");
                            })
                            ->select('Billing_Readings.*',
                                'Billing_ServiceAccounts.id AS AccountId',
                                'Billing_ServiceAccounts.OldAccountNo',
                                'Billing_ServiceAccounts.ServiceAccountName',
                                'Billing_ServiceAccounts.SequenceCode',
                                'Billing_ServiceAccounts.AccountStatus',
                                'Billing_ServiceAccounts.Multiplier',
                                'Billing_ServiceAccounts.AreaCode',
                                DB::raw("(SELECT TOP 1 ReadingTimestamp FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReadingTimestamp"),
                                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Readings WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . date('Y-m-01', strtotime($period . ' -1 month')) . "' ORDER BY ServicePeriod DESC) AS PrevReading"),
                                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id ORDER BY created_at DESC) AS MeterNumber"),
                                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $period . "') AS CurrentKwh"),
                                )
                            ->orderBy('Billing_ServiceAccounts.AreaCode')
                            ->orderBy('AccountStatus')
                            ->orderBy('FieldStatus')
                        ->get();
                    } else {
                        $readingReport = [];
                    }                    
                }            
            }

            return view('/readings/print_billed_unbilled_report', [
                'meterReader' => $meterReader,
                'readingReport' => $readingReport,
                'day' => $day,
                'town' => $town,
                'period' => $period,
                'type' => $type,
            ]);
        }  
    }
}
