<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillsRequest;
use App\Http\Requests\UpdateBillsRequest;
use App\Repositories\BillsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Readings;
use App\Models\ReadingImages;
use App\Models\ServiceAccounts;
use App\Models\Rates;
use App\Models\Bills;
use App\Models\BillsOriginal;
use App\Models\BillingMeters;
use App\Models\IDGenerator;
use App\Models\Barangays;
use App\Models\User;
use App\Models\Towns;
use App\Models\MemberConsumerTypes;
use App\Models\MemberConsumers;
use App\Models\PendingBillAdjustments;
use App\Models\ArrearsLedgerDistribution;
use App\Models\ChangeMeterLogs;
use App\Repositories\ReadingsRepository;
use Flash;
use Response;

class BillsController extends AppBaseController
{
    /** @var  BillsRepository */
    private $billsRepository;

    public function __construct(BillsRepository $billsRepo)
    {
        $this->middleware('auth');
        $this->billsRepository = $billsRepo;
    }

    /**
     * Display a listing of the Bills.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $bills = $this->billsRepository->all();

        return view('bills.index')
            ->with('bills', $bills);
    }

    /**
     * Show the form for creating a new Bills.
     *
     * @return Response
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created Bills in storage.
     *
     * @param CreateBillsRequest $request
     *
     * @return Response
     */
    public function store(CreateBillsRequest $request)
    {
        $input = $request->all();

        $bills = $this->billsRepository->create($input);

        Flash::success('Bills saved successfully.');

        return redirect(route('bills.index'));
    }

    /**
     * Display the specified Bills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bills = $this->billsRepository->find($id);
        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
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
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
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
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $bills->AccountNumber)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $bills->AccountNumber)
            ->orderByDesc('created_at')
            ->first();

        $rate = Rates::where('ServicePeriod', $bills->ServicePeriod)
            ->where('ConsumerType', $bills->ConsumerType)
            ->first();

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        return view('bills.show', [
            'bills' => $bills,
            'account' => $account,
            'meters' => $meters,
            'rate' => $rate,
        ]);
    }

    /**
     * Show the form for editing the specified Bills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        return view('bills.edit')->with('bills', $bills);
    }

    /**
     * Update the specified Bills in storage.
     *
     * @param int $id
     * @param UpdateBillsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBillsRequest $request)
    {
        $bills = $this->billsRepository->find($id);
        $bills->AdjustedBy = Auth::id();
        $bills->DateAdjusted = date('Y-m-d');

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        // BEFORE UPDATE
        // INSERT TO BILLS ORIGINAL
        $billsArr = $bills->toArray();
        $billsArr['id'] = IDGenerator::generateIDandRandString();
        $billsOriginal = BillsOriginal::create($billsArr);

        // UPDATE BILL
        $bills = $this->billsRepository->update($request->all(), $id);

        // UPDATE READINGS
        $reading = Readings::where('AccountNumber', $bills->AccountNumber)
            ->where('ServicePeriod', $bills->ServicePeriod)
            ->first();
        if ($reading != null) {
            $reading->KwhUsed = $bills->PresentKwh;
            $reading->save();
        }

        // Flash::success('Bills updated successfully.');

        return redirect(route('serviceAccounts.show', [$bills->AccountNumber]));
    }

    /**
     * Remove the specified Bills from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        $this->billsRepository->delete($id);

        Flash::success('Bills deleted successfully.');

        return redirect(route('bills.index'));
    }

    public function unbilledReadings() {
        $stats = DB::table('Billing_Bills')
            ->select('ServicePeriod',
                DB::raw("(SELECT COUNT(id) FROM Billing_Bills WHERE ServicePeriod=Billing_Bills.ServicePeriod) as 'Bills'"),
                DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod=Billing_Bills.ServicePeriod) as 'Readings'"))
            ->orderByDesc('ServicePeriod')
            ->groupBy('ServicePeriod')
            ->get();
        return view('/bills/unbilled_readings', [
            'stats' => $stats
        ]);
    }

    public function unbilledReadingsConsole($servicePeriod, Request $request) {
        $area = $request['Area'];
        $meterReader = $request['MeterReader'];
        $groupCode = $request['GroupCode'];

        if ($meterReader == null | $groupCode == null | $area == null) {
            $zeroReadings = null;
            $disconnectedReadings = null;
            $changeMeters = null;
        } else {
            $zeroReadings = DB::table('Billing_Readings')
                ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $servicePeriod . "')")
                // ->whereNotIn('Billing_Readings.AccountNumber', DB::table('Billing_Bills')->where('Billing_Bills.ServicePeriod', $servicePeriod)->pluck('Billing_Bills.AccountNumber'))
                ->where('Billing_Readings.ServicePeriod', $servicePeriod)
                ->where('Billing_ServiceAccounts.AccountStatus', 'ACTIVE')
                ->where('Billing_ServiceAccounts.MeterReader', $meterReader)
                ->where('Billing_ServiceAccounts.GroupCode', $groupCode)
                ->where('Billing_ServiceAccounts.Town', $area)
                ->whereRaw("Billing_Readings.id NOT IN (SELECT ReadingId FROM Billing_PendingBillAdjustments WHERE Confirmed IS NULL AND ServicePeriod='" . $servicePeriod . "')")
                // ->whereNotIn('Billing_Readings.id', DB::table('Billing_PendingBillAdjustments')->where('ServicePeriod', $servicePeriod)->whereNull('Confirmed')->pluck('ReadingId'))
                ->select('Billing_Readings.AccountNumber',
                    'Billing_Readings.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_Readings.FieldStatus')
                ->get();
        
            $disconnectedReadings = DB::table('Billing_Readings')
                ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                // ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $servicePeriod . "')")
                // ->whereNotIn('Billing_Readings.AccountNumber', DB::table('Billing_Bills')->where('Billing_Bills.ServicePeriod', $servicePeriod)->pluck('Billing_Bills.AccountNumber'))
                ->where('Billing_Readings.ServicePeriod', $servicePeriod)
                ->where('Billing_ServiceAccounts.AccountStatus', 'DISCONNECTED')
                ->where('Billing_ServiceAccounts.MeterReader', $meterReader)
                ->where('Billing_ServiceAccounts.GroupCode', $groupCode)
                ->where('Billing_ServiceAccounts.Town', $area)
                // ->whereRaw("Billing_Readings.id NOT IN (SELECT ReadingId FROM Billing_PendingBillAdjustments WHERE Confirmed IS NULL AND ServicePeriod='" . $servicePeriod . "')")
                // ->whereNotIn('Billing_Readings.id', DB::table('Billing_PendingBillAdjustments')->where('ServicePeriod', $servicePeriod)->whereNull('Confirmed')->pluck('ReadingId'))
                ->select('Billing_Readings.AccountNumber',
                    'Billing_Readings.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_Readings.KwhUsed',
                    'Billing_Readings.FieldStatus')
                ->get();

            $changeMeters = DB::table('Billing_Readings')
                ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                ->whereRaw("Billing_Readings.AccountNumber NOT IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod='" . $servicePeriod . "')")
                // ->whereNotIn('Billing_Readings.AccountNumber', DB::table('Billing_Bills')->where('Billing_Bills.ServicePeriod', $servicePeriod)->pluck('Billing_Bills.AccountNumber'))
                ->where('Billing_Readings.ServicePeriod', $servicePeriod)
                ->where('Billing_ServiceAccounts.AccountStatus', 'ACTIVE')
                ->where('Billing_ServiceAccounts.MeterReader', $meterReader)
                ->where('Billing_ServiceAccounts.GroupCode', $groupCode)
                ->where('Billing_ServiceAccounts.Town', $area)
                ->whereRaw("Billing_Readings.id NOT IN (SELECT ReadingId FROM Billing_PendingBillAdjustments WHERE Confirmed IS NULL AND ServicePeriod='" . $servicePeriod . "')")
                ->whereRaw("Billing_ServiceAccounts.id IN (SELECT AccountNumber FROM Billing_ChangeMeterLogs WHERE ServicePeriod='" . $servicePeriod . "')")
                // ->whereNotIn('Billing_Readings.id', DB::table('Billing_PendingBillAdjustments')->where('ServicePeriod', $servicePeriod)->whereNull('Confirmed')->pluck('ReadingId'))
                ->select('Billing_Readings.AccountNumber',
                    'Billing_Readings.id',
                    'Billing_Readings.KwhUsed',
                    'Billing_Readings.ServicePeriod',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_Readings.FieldStatus')
                ->get();
        }        

        $meterReaders = User::role('Meter Reader')->get();
        
        return view('/bills/unbilled_readings_console', [
            'servicePeriod' => $servicePeriod,
            'zeroReadings' => $zeroReadings,
            'disconnectedReadings' => $disconnectedReadings,
            'meterReaders' => $meterReaders,
            'changeMeters' => $changeMeters,
        ]);
    }

    public function zeroReadingsView($readingId) {
        $reading = Readings::find($readingId);
        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $reading->AccountNumber)
            ->first();
        $images = ReadingImages::where('ServicePeriod', $reading->ServicePeriod)
            ->where('AccountNumber', $account->id)
            ->get();
        
        $previousBills = DB::table('Billing_Bills')
            ->where('AccountNumber', $reading->AccountNumber)
            ->orderByDesc('ServicePeriod')
            ->limit(3)
            ->get();

        $meterInfo = DB::table('Billing_Meters')
            ->where('ServiceAccountId', $reading->AccountNumber)
            ->orderByDesc('created_at')
            ->first();

        $pendingAdjustments = PendingBillAdjustments::where('ReadingId', $readingId)->first();

        return view('/bills/zero_readings_view', [
            'reading' => $reading,
            'account' => $account,
            'images' => $images,
            'previousBills' => $previousBills,
            'meterInfo' => $meterInfo,
            'pendingAdjustments' => $pendingAdjustments,
        ]);
    }

    /**
     * AVERAGE BILL
     */
    public function averageBill($readingId) {
        $reading = Readings::find($readingId);
        $account = ServiceAccounts::find($reading->AccountNumber);
        $previousBills = DB::table('Billing_Bills')
            ->where('AccountNumber', $reading->AccountNumber)
            ->orderByDesc('ServicePeriod')
            ->limit(3)
            ->get();
        $previousBill = DB::table('Billing_Bills')
            ->where('AccountNumber', $reading->AccountNumber)
            ->orderByDesc('ServicePeriod')
            ->first();
        $rate = Rates::where('ServicePeriod', $reading->ServicePeriod)
            ->where('AreaCode', $account->Town)
            ->where('ConsumerType', $account->AccountType)
            ->first();
        $meterInfo = DB::table('Billing_Meters')
            ->where('ServiceAccountId', $reading->AccountNumber)
            ->orderByDesc('created_at')
            ->first();
        $arrearLedgers = ArrearsLedgerDistribution::where('AccountNumber', $reading->AccountNumber)
            ->where('ServicePeriod', $reading->ServicePeriod)
            ->first();

        if ($rate != null) {
            if (count($previousBills) > 2) {
                // GET AVERAGE KWH
                $average = 0.0;
                foreach($previousBills as $item) {
                    $average += floatval($item->KwhUsed);
                }
                $average = $average/count($previousBills);

                $presKwh = floatval($previousBill->PresentKwh) + intval($average);

                $bill = Bills::computeRegularBill($account, null, intval($average), $previousBill->PresentKwh, $presKwh, $reading->ServicePeriod, date('Y-m-d'), 0, 0, null);

                Flash::success('Bill to account ' . $account->ServiceAccountName . ' for period ' . date('F Y', strtotime($bill->ServicePeriod)) . ' averaged successfully!');

                return redirect(route('bills.unbilled-readings-console', [$reading->ServicePeriod]));
            } else {
                return abort(403, "NO ENOUGH PREVIOUS BILLS FOR AVERAGING");
            }
        } else {
            return abort(403, "No Rate for this service period yet.");
        }        
    }

    public function rebillReadingAdjustment($readingId) {
        $reading = Readings::find($readingId);
        $previousBill = DB::table('Billing_Bills')
            ->where('AccountNumber', $reading->AccountNumber)
            ->orderByDesc('ServicePeriod')
            ->first();

        return view('/bills/rebill_reading_adjustment', [
            'reading' => $reading,
            'previousBill' => $previousBill,
        ]);
    }

    public function rebill($readingId, Request $request) {
        $reading = Readings::find($readingId);
        // UPDATE READING FIRST
        $reading->update($request->all());

        $account = ServiceAccounts::find($reading->AccountNumber);
        $rate = Rates::where('ServicePeriod', $reading->ServicePeriod)
            ->where('AreaCode', $account->AreaCode)
            ->where('ConsumerType', $account->AccountType)
            ->first();
        $meterInfo = DB::table('Billing_Meters')
            ->where('ServiceAccountId', $reading->AccountNumber)
            ->orderByDesc('created_at')
            ->first();
        $arrearLedgers = ArrearsLedgerDistribution::where('AccountNumber', $reading->AccountNumber)
            ->where('ServicePeriod', $reading->ServicePeriod)
            ->first();
        $previousBill = DB::table('Billing_Bills')
            ->where('AccountNumber', $reading->AccountNumber)
            ->where('ServicePeriod', date('Y-m-d', strtotime($reading->ServicePeriod . ' -1 month')))
            ->orderByDesc('ServicePeriod')
            ->first();

        /**
         * CHECK IF PREVIOUS BILL IS NULL
         */
        $previousKwhUsed = 0.0;
        if ($previousBill != null) {
            $previousKwhUsed = floatval($previousBill->PresentKwh);
        } else {
            $previousKwhUsed = 0.0;
        }

        /**
         * CHECK IF RATE IS NOT YET AVAILABLE
         */
        if ($rate != null) {
            // SET IMPORTANT PARAMETERS
            $kwhUsed = floatval($reading->KwhUsed) - $previousKwhUsed;
            $multiplier = $account->Multiplier != null ? floatval($account->Multiplier) : 1;
            $coreloss = $account->Coreloss != null ? floatval($account->Coreloss) : 0;
            $effectiveRate = Rates::floatRate($rate->TotalRateVATIncluded);

            $totalKwh = ($kwhUsed * $multiplier) + $coreloss;

            $netAmount = $totalKwh * $effectiveRate;

            // DEDUCTIONS
            $seniorCitizen = Rates::floatRate($rate->SeniorCitizenSubsidy) * $netAmount;
            $deductions = $seniorCitizen;

            // ADDITIONAL CHARGES
            $arrears = $arrearLedgers != null ? floatval($arrearLedgers->Amount) : 0;
            $addOns = $arrears;

            // CHECK IF Bill for this period
            $bill = Bills::where('ServicePeriod', $reading->ServicePeriod)
                ->where('AccountNumber', $account->id)
                ->first();
            
            if ($bill != null) {
                if ($account->SeniorCitizen != null && $account->SeniorCitizen=="Yes" && $kwhUsed < 101) {
                    $bill->Deductions = $deductions;
                    $netAmount = $netAmount - $deductions;
                }
                $netAmount = $netAmount + $addOns;
                // SET BILLING VALUES
                $bill->KwhUsed = $kwhUsed;
                $bill->PreviousKwh = $previousBill != null ? $previousBill->PresentKwh : 0;
                $bill->PresentKwh = $reading->KwhUsed;
                $bill->KwhAmount = $effectiveRate * $kwhUsed;
                $bill->EffectiveRate = $effectiveRate;
                $bill->AdditionalCharges = $addOns;
                $bill->NetAmount = $netAmount;
                $bill->BillingDate = date('Y-m-d');
                $bill->ServiceDateFrom = $previousBill != null ? date('Y-m-d', strtotime($previousBill->ServiceDateTo . ' +1 day')) : date('Y-m-d');
                $bill->ServiceDateTo = date('Y-m-d', strtotime($reading->ReadingTimestamp));
                $bill->DueDate = Bills::createDueDate($bill->ServiceDateTo);
                $bill->MeterNumber = $meterInfo != null ? $meterInfo->SerialNumber : null;
                $bill->ConsumerType = $account->AccountType;
                $bill->BillType = $bill->ConsumerType;              

                $bill->GenerationSystemCharge = $kwhUsed * Rates::floatRate($rate->GenerationSystemCharge);
                $bill->TransmissionDeliveryChargeKW = $kwhUsed * Rates::floatRate($rate->TransmissionDeliveryChargeKW);
                $bill->TransmissionDeliveryChargeKWH = $kwhUsed * Rates::floatRate($rate->TransmissionDeliveryChargeKWH);
                $bill->SystemLossCharge = $kwhUsed * Rates::floatRate($rate->SystemLossCharge);
                $bill->DistributionDemandCharge = $kwhUsed * Rates::floatRate($rate->DistributionDemandCharge);
                $bill->DistributionSystemCharge = $kwhUsed * Rates::floatRate($rate->DistributionSystemCharge);
                $bill->SupplyRetailCustomerCharge = $kwhUsed * Rates::floatRate($rate->SupplyRetailCustomerCharge);
                $bill->SupplySystemCharge = $kwhUsed * Rates::floatRate($rate->SupplySystemCharge);
                $bill->MeteringRetailCustomerCharge = $kwhUsed * Rates::floatRate($rate->MeteringRetailCustomerCharge);
                $bill->MeteringSystemCharge = $kwhUsed * Rates::floatRate($rate->MeteringSystemCharge);
                $bill->RFSC = $kwhUsed * Rates::floatRate($rate->RFSC);
                $bill->LifelineRate = $kwhUsed * Rates::floatRate($rate->LifelineRate);
                $bill->InterClassCrossSubsidyCharge = $kwhUsed * Rates::floatRate($rate->InterClassCrossSubsidyCharge);
                $bill->PPARefund = $kwhUsed * Rates::floatRate($rate->PPARefund);
                $bill->SeniorCitizenSubsidy = $kwhUsed * Rates::floatRate($rate->SeniorCitizenSubsidy);
                $bill->MissionaryElectrificationCharge = $kwhUsed * Rates::floatRate($rate->MissionaryElectrificationCharge);
                $bill->EnvironmentalCharge = $kwhUsed * Rates::floatRate($rate->EnvironmentalCharge);
                $bill->StrandedContractCosts = $kwhUsed * Rates::floatRate($rate->StrandedContractCosts);
                $bill->NPCStrandedDebt = $kwhUsed * Rates::floatRate($rate->NPCStrandedDebt);
                $bill->FeedInTariffAllowance = $kwhUsed * Rates::floatRate($rate->FeedInTariffAllowance);
                $bill->MissionaryElectrificationREDCI = $kwhUsed * Rates::floatRate($rate->MissionaryElectrificationREDCI);
                $bill->GenerationVAT = $kwhUsed * Rates::floatRate($rate->GenerationVAT);
                $bill->TransmissionVAT = $kwhUsed * Rates::floatRate($rate->TransmissionVAT);
                $bill->SystemLossVAT = $kwhUsed * Rates::floatRate($rate->SystemLossVAT);
                $bill->DistributionVAT = $kwhUsed * Rates::floatRate($rate->DistributionVAT);
                $bill->RealPropertyTax = $kwhUsed * Rates::floatRate($rate->RealPropertyTax);
                $bill->BilledFrom = 'WEB';
                $bill->UserId = Auth::id();

                // SAVE BILL
                $bill->save();
            } else {
                // SET BILLING VALUES
                $bill = new Bills;
                $bill->id = IDGenerator::generateIDandRandString();
                $bill->BillNumber = IDGenerator::generateBillNumber($account->AreaCode);
                $bill->AccountNumber = $account->id;
                $bill->ServicePeriod = $reading->ServicePeriod;
                
                if ($account->SeniorCitizen != null && $account->SeniorCitizen=="Yes" && $kwhUsed < 101) {
                    $bill->Deductions = $deductions;
                    $netAmount = $netAmount - $deductions;
                }
                $netAmount = $netAmount + $addOns;
                $bill->Multiplier = $multiplier;
                $bill->Coreloss = $coreloss;
                $bill->KwhUsed = $kwhUsed;
                $bill->PreviousKwh = $previousBill != null ? $previousBill->PresentKwh : 0;
                $bill->PresentKwh = $reading->KwhUsed;
                $bill->KwhAmount = $effectiveRate * $kwhUsed;
                $bill->EffectiveRate = $effectiveRate;
                $bill->AdditionalCharges = $addOns;
                $bill->NetAmount = $netAmount;
                $bill->BillingDate = date('Y-m-d');
                $bill->ServiceDateFrom = $previousBill != null ? date('Y-m-d', strtotime($previousBill->ServiceDateTo . ' +1 day')) : date('Y-m-d');
                $bill->ServiceDateTo = date('Y-m-d', strtotime($reading->ReadingTimestamp));
                $bill->DueDate = Bills::createDueDate($bill->ServiceDateTo);
                $bill->MeterNumber = $meterInfo != null ? $meterInfo->SerialNumber : null;
                $bill->ConsumerType = $account->AccountType;
                $bill->BillType = $bill->ConsumerType;

                $bill->GenerationSystemCharge = $kwhUsed * Rates::floatRate($rate->GenerationSystemCharge);
                $bill->TransmissionDeliveryChargeKW = $kwhUsed * Rates::floatRate($rate->TransmissionDeliveryChargeKW);
                $bill->TransmissionDeliveryChargeKWH = $kwhUsed * Rates::floatRate($rate->TransmissionDeliveryChargeKWH);
                $bill->SystemLossCharge = $kwhUsed * Rates::floatRate($rate->SystemLossCharge);
                $bill->DistributionDemandCharge = $kwhUsed * Rates::floatRate($rate->DistributionDemandCharge);
                $bill->DistributionSystemCharge = $kwhUsed * Rates::floatRate($rate->DistributionSystemCharge);
                $bill->SupplyRetailCustomerCharge = $kwhUsed * Rates::floatRate($rate->SupplyRetailCustomerCharge);
                $bill->SupplySystemCharge = $kwhUsed * Rates::floatRate($rate->SupplySystemCharge);
                $bill->MeteringRetailCustomerCharge = $kwhUsed * Rates::floatRate($rate->MeteringRetailCustomerCharge);
                $bill->MeteringSystemCharge = $kwhUsed * Rates::floatRate($rate->MeteringSystemCharge);
                $bill->RFSC = $kwhUsed * Rates::floatRate($rate->RFSC);
                $bill->LifelineRate = $kwhUsed * Rates::floatRate($rate->LifelineRate);
                $bill->InterClassCrossSubsidyCharge = $kwhUsed * Rates::floatRate($rate->InterClassCrossSubsidyCharge);
                $bill->PPARefund = $kwhUsed * Rates::floatRate($rate->PPARefund);
                $bill->SeniorCitizenSubsidy = $kwhUsed * Rates::floatRate($rate->SeniorCitizenSubsidy);
                $bill->MissionaryElectrificationCharge = $kwhUsed * Rates::floatRate($rate->MissionaryElectrificationCharge);
                $bill->EnvironmentalCharge = $kwhUsed * Rates::floatRate($rate->EnvironmentalCharge);
                $bill->StrandedContractCosts = $kwhUsed * Rates::floatRate($rate->StrandedContractCosts);
                $bill->NPCStrandedDebt = $kwhUsed * Rates::floatRate($rate->NPCStrandedDebt);
                $bill->FeedInTariffAllowance = $kwhUsed * Rates::floatRate($rate->FeedInTariffAllowance);
                $bill->MissionaryElectrificationREDCI = $kwhUsed * Rates::floatRate($rate->MissionaryElectrificationREDCI);
                $bill->GenerationVAT = $kwhUsed * Rates::floatRate($rate->GenerationVAT);
                $bill->TransmissionVAT = $kwhUsed * Rates::floatRate($rate->TransmissionVAT);
                $bill->SystemLossVAT = $kwhUsed * Rates::floatRate($rate->SystemLossVAT);
                $bill->DistributionVAT = $kwhUsed * Rates::floatRate($rate->DistributionVAT);
                $bill->RealPropertyTax = $kwhUsed * Rates::floatRate($rate->RealPropertyTax);
                $bill->BilledFrom = 'WEB';
                $bill->UserId = Auth::id();

                // SAVE BILL
                $bill->save();
            }            

            Flash::success('Bill to account ' . $account->ServiceAccountName . ' for period ' . date('F Y', strtotime($bill->ServicePeriod)) . ' averaged successfully!');

            return redirect(route('bills.unbilled-readings-console', [$reading->ServicePeriod]));
        } else {
            return abort(404, "No Rate for this service period yet.");
        }
    }

    public function adjustBill($billNumber) {
        $bill = Bills::find($billNumber);

        $account = ServiceAccounts::find($bill->AccountNumber);

        return view('/bills/adjust_bill', [
            'bill' => $bill,
            'account' => $account,
        ]);
    }

    public function fetchBillAdjustmentData(Request $request) {
        $bill = Bills::find($request['BillId']);

        $account = ServiceAccounts::find($request['AccountNumber']);

        $additionalCharges = $request['AdditionalCharges'] != null ? floatval($request['AdditionalCharges']) : 0;
        $deductions = $request['Deductions'] != null ? floatval($request['Deductions']) : 0;

        return response()->json(Bills::computeRegularBillAndDontSave($account, $bill->id, $request['KwhUsed'], $bill->PreviousKwh, $bill->PresentKwh, $bill->ServicePeriod, $bill->BillingDate, $additionalCharges, $deductions, $request['Is2307']), 200);
    }

    public function allBills(Request $request) {
        if ($request['params'] == null) {
            $bills = DB::table('Billing_Bills')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->leftJoin('Cashier_PaidBills', 'Billing_Bills.id', '=', 'Cashier_PaidBills.ObjectSourceId')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 
                            'Billing_ServiceAccounts.id as AccountNumber', 
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay', 
                            'Billing_ServiceAccounts.AccountCount',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_Bills.BillNumber',
                            'Billing_Bills.id',
                            'Cashier_PaidBills.ORNumber',
                            'Billing_Bills.ServicePeriod')
                        ->orderByDesc('Billing_Bills.created_at')
                        ->limit(60)
                        ->paginate(15);
        } else {
            $bills = DB::table('Billing_Bills')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('Cashier_PaidBills', 'Billing_Bills.id', '=', 'Cashier_PaidBills.ObjectSourceId')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 
                            'Billing_ServiceAccounts.id as AccountNumber', 
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay', 
                            'Billing_ServiceAccounts.AccountCount',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_Bills.BillNumber',
                            'Billing_Bills.id',
                            'Cashier_PaidBills.ORNumber',
                            'Billing_Bills.ServicePeriod')
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_Bills.BillNumber', 'LIKE', '%' . $request['params'] . '%')
                        ->orderByDesc('Billing_Bills.created_at')
                        ->paginate(15);
        }    

        return view('/bills/all_bills', [
            'bills' => $bills,
        ]);
    }

    public function billArrearsUnlocking() {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('users', 'Billing_Bills.UnlockedBy', '=', 'users.id')
            ->where('Billing_Bills.IsUnlockedForPayment', 'Requested')
            ->select('Billing_ServiceAccounts.ServiceAccountName',
                'users.name',
                'Billing_Bills.*')
            ->get();

        return view('/bills/bill_arrears_unlocking', [
            'bills' => $bills
        ]);
    }

    public function unlockBillArrear($id) {
        $bill = Bills::find($id);

        $bill->IsUnlockedForPayment = 'Yes';
        $bill->UnlockedBy = Auth::id();
        $bill->save();

        return redirect(route('bills.bill-arrears-unlocking'));
    }

    public function rejectUnlockBillArrear($id) {
        $bill = Bills::find($id);

        $bill->IsUnlockedForPayment = null;
        $bill->UnlockedBy = Auth::id();
        $bill->save();

        return redirect(route('bills.bill-arrears-unlocking'));
    }

    public function groupedBilling() {
        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_MemberConsumers', 'Billing_ServiceAccounts.MemberConsumerId', '=', 'CRM_MemberConsumers.id')
            ->whereNotNull('Billing_ServiceAccounts.MemberConsumerId')
            ->select('CRM_MemberConsumers.FirstName',
                'CRM_MemberConsumers.MiddleName',
                'CRM_MemberConsumers.LastName',
                'CRM_MemberConsumers.Suffix',
                'CRM_MemberConsumers.OrganizationName',
                'CRM_MemberConsumers.MembershipType',
                'Billing_ServiceAccounts.MemberConsumerId',
                DB::raw("COUNT (Billing_ServiceAccounts.id) AS NoOfAccounts"))
            ->groupBy('CRM_MemberConsumers.FirstName',
                'CRM_MemberConsumers.MiddleName',
                'CRM_MemberConsumers.LastName',
                'CRM_MemberConsumers.Suffix',
                'CRM_MemberConsumers.OrganizationName',
                'CRM_MemberConsumers.MembershipType',
                'Billing_ServiceAccounts.MemberConsumerId')
            ->get();

        return view('/bills/grouped_billing', [
            'accounts' => $accounts
        ]);
    }

    public function createGroupBillingStepOne() {
        $types = MemberConsumerTypes::orderByDesc('Id')->pluck('Type', 'Id');

        $barangays = Barangays::orderBy('Barangay')->pluck('Barangay', 'id');

        $towns = Towns::orderBy('Town')->pluck('Town', 'id');
        return view('/bills/create_group_billing_step_one', [
            'types' => $types, 
            'barangays' => $barangays, 
            'towns' => $towns
        ]);
    }

    public function storeGroupBillingStepOne(Request $request) {
        $input = $request->all();

        $memberConsumers = MemberConsumers::create($input);

        return redirect(route('bills.create-group-billing-step-two', [$input['Id']]));
    }

    public function createGroupBillingStepTwo($memberConsumerId) {
        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->select('Billing_ServiceAccounts.ServiceAccountName', 
                'Billing_ServiceAccounts.id', 
                'Billing_ServiceAccounts.Purok', 
                'Billing_ServiceAccounts.OldAccountNo', 
                'CRM_Towns.Town', 
                'CRM_Barangays.Barangay')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        return view('/bills/create_group_billing_step_two', [
            'memberConsumerId' => $memberConsumerId,
            'accounts' => $accounts,
        ]);
    }

    public function createGroupBillingStepOnePreSelect() {
        return view('/bills/create_group_billing_step_one_pre_select', [
        ]);
    }

    public function fetchMemberConsumers(Request $request) {
        $query = $request['query'];
            
        if ($query != '') {
            $data = DB::table('CRM_MemberConsumers')
                ->leftJoin('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
                ->leftJoin('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
                ->leftJoin('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
                ->select('CRM_MemberConsumers.Id as ConsumerId',
                                'CRM_MemberConsumers.MembershipType as MembershipType', 
                                'CRM_MemberConsumers.FirstName as FirstName', 
                                'CRM_MemberConsumers.MiddleName as MiddleName', 
                                'CRM_MemberConsumers.LastName as LastName', 
                                'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                                'CRM_MemberConsumers.Suffix as Suffix', 
                                'CRM_MemberConsumers.Birthdate as Birthdate', 
                                'CRM_MemberConsumers.Barangay as Barangay', 
                                'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                                'CRM_MemberConsumers.DateApplied as DateApplied', 
                                'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                                'CRM_MemberConsumers.DateApproved as DateApproved', 
                                'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                                'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                                'CRM_MemberConsumers.Notes as Notes', 
                                'CRM_MemberConsumers.Gender as Gender', 
                                'CRM_MemberConsumers.Sitio as Sitio', 
                                'CRM_MemberConsumerTypes.*',
                                'CRM_Towns.Town as Town',
                                'CRM_Barangays.Barangay as Barangay')
                ->where('CRM_MemberConsumers.LastName', 'LIKE', '%' . $query . '%')
                ->orWhere('CRM_MemberConsumers.Id', 'LIKE', '%' . $query . '%')
                ->orWhere('CRM_MemberConsumers.OrganizationName', 'LIKE', '%' . $query . '%')
                ->orWhere('CRM_MemberConsumers.MiddleName', 'LIKE', '%' . $query . '%')
                ->orWhere('CRM_MemberConsumers.FirstName', 'LIKE', '%' . $query . '%')
                ->orderBy('CRM_MemberConsumers.FirstName')
                ->get();
        } else {
            $data = DB::table('CRM_MemberConsumers')
                ->leftJoin('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
                ->leftJoin('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
                ->leftJoin('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
                ->select('CRM_MemberConsumers.Id as ConsumerId',
                                'CRM_MemberConsumers.MembershipType as MembershipType', 
                                'CRM_MemberConsumers.FirstName as FirstName', 
                                'CRM_MemberConsumers.MiddleName as MiddleName', 
                                'CRM_MemberConsumers.LastName as LastName', 
                                'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                                'CRM_MemberConsumers.Suffix as Suffix', 
                                'CRM_MemberConsumers.Birthdate as Birthdate', 
                                'CRM_MemberConsumers.Barangay as Barangay', 
                                'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                                'CRM_MemberConsumers.DateApplied as DateApplied', 
                                'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                                'CRM_MemberConsumers.DateApproved as DateApproved', 
                                'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                                'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                                'CRM_MemberConsumers.Notes as Notes', 
                                'CRM_MemberConsumers.Gender as Gender', 
                                'CRM_MemberConsumers.Sitio as Sitio', 
                                'CRM_MemberConsumerTypes.*',
                                'CRM_Towns.Town as Town',
                                'CRM_Barangays.Barangay as Barangay')
                ->orderByDesc('CRM_MemberConsumers.created_at')
                ->take(30)
                ->get();
        }

        $output = "";
        foreach($data as $item) {
            $output .= '<tr>
                            <td>' . $item->ConsumerId . '</td>
                            <td>' . MemberConsumers::serializeMemberName($item) . '</td>
                            <td>' . $item->Barangay . ', ' . $item->Town . '</td>
                            <td>
                                <a href="' . route('bills.create-group-billing-step-two', [$item->ConsumerId]) . '" class="btn btn-xs btn-primary">Proceed</a>
                            <td>
                        </tr>';
        }
        
        return response()->json($output, 200);
    }

    public function searchAccount(Request $request) {
        $results = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->whereNull('Billing_ServiceAccounts.MemberConsumerId')
            ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['query'] . '%')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $output = "";

        if (count($results) > 0) {
            foreach($results as $item) {
                $output .= '
                        <tr>
                            <td>' . $item->id . '</td>
                            <td>' . $item->OldAccountNo . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . ServiceAccounts::getAddress($item) . '</td>
                            <td>
                                <button class="btn btn-link text-primary" onclick=addToGroup("' . $item->id . '")><i class="fas fa-plus"></i></button>
                            </td>
                        </tr>
                    '; 
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }        
    }

    public function addToGroup(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $account->MemberConsumerId = $request['MemberConsumerId'];
            $account->save();
        }

        return response()->json($account, 200);
    }

    public function removeFromGroup(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account != null) {
            $account->MemberConsumerId = null;
            $account->save();
        }

        return response()->json($account, 200);
    }

    public function groupedBillingView($memberConsumerId) {
        $accounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.ServiceAccountName', 
                'Billing_ServiceAccounts.id', 
                'Billing_ServiceAccounts.Purok', 
                'Billing_ServiceAccounts.OldAccountNo', 
                'CRM_Towns.Town', 
                'CRM_Barangays.Barangay')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->get();
        $memberConsumer = DB::table('CRM_MemberConsumers')
            ->leftJoin('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
            ->leftJoin('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
            ->select('CRM_MemberConsumers.Id as ConsumerId',
                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                    'CRM_MemberConsumers.FirstName as FirstName', 
                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                    'CRM_MemberConsumers.LastName as LastName', 
                    'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                    'CRM_MemberConsumers.Suffix as Suffix', 
                    'CRM_MemberConsumers.Birthdate as Birthdate', 
                    'CRM_MemberConsumers.Barangay as Barangay', 
                    'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                    'CRM_MemberConsumers.DateApplied as DateApplied', 
                    'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                    'CRM_MemberConsumers.DateApproved as DateApproved', 
                    'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                    'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                    'CRM_MemberConsumers.Notes as Notes', 
                    'CRM_MemberConsumers.Gender as Gender', 
                    'CRM_MemberConsumers.Sitio as Sitio', 
                    'CRM_MemberConsumerTypes.*',
                    'CRM_Towns.Town as Town',
                    'CRM_Barangays.Barangay as Barangay')
            ->where('CRM_MemberConsumers.Id', $memberConsumerId)
            ->first();
        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->select('Billing_ServiceAccounts.MemberConsumerId',
                'Billing_Bills.ServicePeriod',
                DB::raw("COUNT(Billing_Bills.id) AS BillCount"))
            ->groupBy('Billing_ServiceAccounts.MemberConsumerId',
                'Billing_Bills.ServicePeriod')
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();

        return view('/bills/grouped_billing_view', [
            'accounts' => $accounts,
            'memberConsumer' => $memberConsumer,
            'ledgers' => $ledgers,
        ]);
    }

    public function groupedBillingBillView($memberConsumerId, $period) {
        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->where('Billing_Bills.ServicePeriod', $period)
            ->whereRaw("Billing_Bills.AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE ServicePeriod='" . $period . "' AND Status IS NULL)")
            // ->whereNotIn('Billing_Bills.AccountNumber', DB::table('Cashier_PaidBills')->where('ServicePeriod', $period)->whereNull('Status')->pluck('AccountNumber'))
            ->select('Billing_ServiceAccounts.MemberConsumerId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_Bills.*',
                'Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $memberConsumer = DB::table('CRM_MemberConsumers')
            ->leftJoin('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
            ->leftJoin('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
            ->select('CRM_MemberConsumers.Id as ConsumerId',
                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                    'CRM_MemberConsumers.FirstName as FirstName', 
                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                    'CRM_MemberConsumers.LastName as LastName', 
                    'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                    'CRM_MemberConsumers.Suffix as Suffix', 
                    'CRM_MemberConsumers.Birthdate as Birthdate', 
                    'CRM_MemberConsumers.Barangay as Barangay', 
                    'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                    'CRM_MemberConsumers.DateApplied as DateApplied', 
                    'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                    'CRM_MemberConsumers.DateApproved as DateApproved', 
                    'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                    'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                    'CRM_MemberConsumers.Notes as Notes', 
                    'CRM_MemberConsumers.Gender as Gender', 
                    'CRM_MemberConsumers.Sitio as Sitio', 
                    'CRM_MemberConsumerTypes.*',
                    'CRM_Towns.Town as Town',
                    'CRM_Barangays.Barangay as Barangay')
            ->where('CRM_MemberConsumers.Id', $memberConsumerId)
            ->first();

        return view('/bills/group_billing_bill_view', [
            'ledgers' => $ledgers,
            'memberConsumer' => $memberConsumer,
            'servicePeriod' => $period
        ]);
    }

    public function add2Percent(Request $request) {
        $memberConsumerId = $request['MemberConsumerId'];
        $period = $request['Period'];

        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->where('Billing_Bills.ServicePeriod', $period)
            ->select('Billing_Bills.*')
            ->get();

        foreach($ledgers as $item) {
            Bills::add2Percent($item->id);
        }

        return response()->json('ok', 200);
    }

    public function remove2Percent(Request $request) {
        $memberConsumerId = $request['MemberConsumerId'];
        $period = $request['Period'];

        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->where('Billing_Bills.ServicePeriod', $period)
            ->select('Billing_Bills.*')
            ->get();

        foreach($ledgers as $item) {
            Bills::remove2Percent($item->id);
        }

        return response()->json('ok', 200);
    }

    public function add5Percent(Request $request) {
        $memberConsumerId = $request['MemberConsumerId'];
        $period = $request['Period'];

        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->where('Billing_Bills.ServicePeriod', $period)
            ->select('Billing_Bills.*')
            ->get();

        foreach($ledgers as $item) {
            Bills::add5Percent($item->id);
        }

        return response()->json('ok', 200);
    }

    public function remove5Percent(Request $request) {
        $memberConsumerId = $request['MemberConsumerId'];
        $period = $request['Period'];

        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->where('Billing_Bills.ServicePeriod', $period)
            ->select('Billing_Bills.*')
            ->get();

        foreach($ledgers as $item) {
            Bills::remove5Percent($item->id);
        }

        return response()->json('ok', 200);
    }

    public function printGroupBilling($memberConsumerId, $period) {
        $ledgers = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.MemberConsumerId', $memberConsumerId)
            ->where('Billing_Bills.ServicePeriod', $period)
            ->whereNotIn('Billing_Bills.AccountNumber', DB::table('Cashier_PaidBills')->where('ServicePeriod', $period)->whereNull('Status')->pluck('AccountNumber'))
            ->select('Billing_ServiceAccounts.MemberConsumerId',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_Bills.*',
                'Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $memberConsumer = DB::table('CRM_MemberConsumers')
            ->leftJoin('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
            ->leftJoin('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
            ->select('CRM_MemberConsumers.Id as ConsumerId',
                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                    'CRM_MemberConsumers.FirstName as FirstName', 
                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                    'CRM_MemberConsumers.LastName as LastName', 
                    'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                    'CRM_MemberConsumers.Suffix as Suffix', 
                    'CRM_MemberConsumers.Birthdate as Birthdate', 
                    'CRM_MemberConsumers.Barangay as Barangay', 
                    'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                    'CRM_MemberConsumers.DateApplied as DateApplied', 
                    'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                    'CRM_MemberConsumers.DateApproved as DateApproved', 
                    'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                    'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                    'CRM_MemberConsumers.Notes as Notes', 
                    'CRM_MemberConsumers.Gender as Gender', 
                    'CRM_MemberConsumers.Sitio as Sitio', 
                    'CRM_MemberConsumerTypes.*',
                    'CRM_Towns.Town as Town',
                    'CRM_Barangays.Barangay as Barangay')
            ->where('CRM_MemberConsumers.Id', $memberConsumerId)
            ->first();

        return view('/bills/print_group_billing', [
            'ledgers' => $ledgers,
            'memberConsumer' => $memberConsumer,
            'servicePeriod' => $period
        ]);
    }

    public function printSingleBillNewFormat($billId) {
        $bills = $this->billsRepository->find($billId);
        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
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
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
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
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $bills->AccountNumber)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $bills->AccountNumber)
            ->orderByDesc('created_at')
            ->first();

        $rate = Rates::where('ServicePeriod', $bills->ServicePeriod)
            ->where('ConsumerType', $bills->ConsumerType)
            ->first();

        return view('/bills/print_single_bill_new_format', [
            'bills' => $bills,
            'account' => $account,
            'meters' => $meters,
            'rate' => $rate,
        ]);
    }

    public function printSingleBillOld($billId) {
        $bills = $this->billsRepository->find($billId);
        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
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
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
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
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $bills->AccountNumber)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $bills->AccountNumber)
            ->orderByDesc('created_at')
            ->first();

        $rate = Rates::where('ServicePeriod', $bills->ServicePeriod)
            ->where('ConsumerType', $bills->ConsumerType)
            ->first();

        if ($account != null) {
            $arrears = DB::table('Billing_Bills')
                ->whereRaw("Billing_Bills.id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber='" . $account->id . "')")
                ->where('Billing_Bills.AccountNumber', $account->id)
                ->whereNotIN('Billing_Bills.id', [$billId])
                ->select('Billing_Bills.*')
                ->get();
        } else {
            $arrears = null;
        }
        

        return view('/bills/print_single_bill_old', [
            'bills' => $bills,
            'account' => $account,
            'meters' => $meters,
            'rate' => $rate,
            'arrears' => $arrears
        ]);
    }

    public function printBulkBillOldFormat($servicePeriod, $town, $route) {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ServicePeriod', $servicePeriod)
            ->where('Billing_ServiceAccounts.Town', $town)
            ->where('Billing_ServiceAccounts.AreaCode', $route)
            ->select('Billing_Bills.*')
            ->get();

        return view('/bills/print_bulk_old_format', [
            'bills' => $bills
        ]);
    }

    public function bulkPrintBill() {
        $towns = Towns::orderBy('id')->get();
        return view('/bills/bulk_print_bill', [
            'towns' => $towns,
        ]);
    }

    public function getRoutesFromTown(Request $request) {
        $routes = DB::table('Billing_ServiceAccounts')
            ->where('Town', $request['Town'])
            ->select('AreaCode')
            ->groupBy('AreaCode')
            ->orderBy('AreaCode')
            ->get();

        $output = "";
        foreach($routes as $item) {
            $output .= '<option value="' . $item->AreaCode . '">' . $item->AreaCode . '</option>';
        }

        return response()->json($output, 200);
    }

    public function printBulkBillNewFormat($period, $town, $route) {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ServicePeriod', $period)
            ->where('Billing_ServiceAccounts.Town', $town)
            ->where('Billing_ServiceAccounts.AreaCode', $route)
            ->select('Billing_Bills.*')
            ->get();

        return view('/bills/print_bulk_bill_new_format', [
            'bills' => $bills
        ]);
    }

    public function bapaManualBilling() {
        $towns = Towns::all();
        return view('/bills/bapa_manual_billing', [
            'towns' => $towns,
        ]);
    }

    public function searchBapaForBilling(Request $request) {
        $param = $request['BAPA'];
        $town = $request['Town'];

        if ($town == 'All') {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        } else {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->where('sa.Town', $town)
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        }

        $output = "";
        foreach($bapas as $item) {
            if (strlen($item->OrganizationParentAccount) > 1) {
                $output .= '<tr>
                                <td><a href="' . route('bills.bapa-manual-billing-console', [urlencode($item->OrganizationParentAccount)]) . '">' . $item->OrganizationParentAccount . '</a></td>
                                <td>' . $item->Town . '</td>
                                <td>' . number_format($item->NoOfAccounts) . '</td>
                                <td>' . $item->Result . '</td>
                            </tr>';
            }
            
        }

        return response()->json($output, 200);
    }

    public function bapaManualBillingConsole($bapaName) {
        return view('/bills/bapa_manual_billing_console', [
            'bapaName' => $bapaName,
        ]);
    }

    public function getBillComputation(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        $bill = Bills::computeRegularBillAndDontSave(
            $account, 
            null, 
            $request['KwhUsed'], 
            $request['PreviousKwh'], 
            $request['PresentKwh'], 
            $request['ServicePeriod'],
            date('Y-m-d'),
            0,
            0,
            false
        );

        return response()->json($bill, 200);
    }

    public function billManually(Request $request) {
        $account = ServiceAccounts::find($request['id']);

        if ($account->AccountStatus == 'ACTIVE') {
            $bill = Bills::computeRegularBill(
                $account, 
                null, 
                $request['KwhUsed'], 
                $request['PreviousKwh'], 
                $request['PresentKwh'], 
                $request['ServicePeriod'],
                date('Y-m-d'),
                0,
                0,
                false
            );
        } else {
            $bill = null;
        }    

        // save reading
        $reading = new Readings;
        $reading->id = IDGenerator::generateIDandRandString();
        $reading->AccountNumber = $account->id;
        $reading->ServicePeriod = $request['ServicePeriod'];
        $reading->ReadingTimestamp = date('Y-m-d H:i:s');
        $reading->KwhUsed = $request['PresentKwh'];
        $reading->Notes = $request['Remarks'];
        $reading->MeterReader = Auth::id();
        $reading->save();

        if ($bill != null) {
            $response = [];

            array_push($response, [
                'ServiceAccountName' => $account->ServiceAccountName,
                'id' => $account->id,
                'OldAccountNo' => $account->OldAccountNo,
                'PreviousKwh' => $bill->PreviousKwh,
                'PresentKwh' => $bill->PresentKwh,
                'KwhUsed' => $bill->KwhUsed,
                'NetAmount' => $bill->NetAmount,
                'BillId' => $bill->id,
                'ReadingId' => $reading->id,
            ]);

            return response()->json($response, 200);
        } else {
            $response = [];

            array_push($response, [
                'ServiceAccountName' => $account->ServiceAccountName,
                'id' => $account->id,
                'OldAccountNo' => $account->OldAccountNo,
                'PreviousKwh' => $request['PreviousKwh'],
                'PresentKwh' => $request['PresentKwh'],
                'KwhUsed' => $request['KwhUsed'],
                'NetAmount' => null,
                'BillId' => null,
                'ReadingId' => $reading->id,
            ]);

            return response()->json($response, 200);
        }
        
    }

    public function fetchBilledConsumersFromReading(Request $request) {
        $bills = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $request['BAPAName'])
            ->where('Billing_Readings.ServicePeriod', $request['ServicePeriod'])
            ->select('Billing_Readings.*',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    DB::raw("(SELECT TOP 1 NetAmount FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "') as NetAmount"),
                    DB::raw("(SELECT TOP 1 id FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod='" . $request['ServicePeriod'] . "') as BillId"))
            ->get();

        return response()->json($bills, 200);
    }

    public function requestCancelBill(Request $request) {
        $bill = Bills::find($request['id']);

        if ($bill != null) {
            $bill->ForCancellation = 'Yes';
            $bill->CancelRequestedBy = Auth::id();
            $bill->CancelApprovedBy = env('APP_BILLING_ANALYST_ID');
            $bill->Notes = $request['Remarks'];
            $bill->save();
        }

        return response()->json($bill, 200);
    }

    public function billsCancellationApproval() {
        $bills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Bills.ForCancellation', 'Yes')
            ->where('Billing_Bills.CancelApprovedBy', env('APP_BILLING_ANALYST_ID'))
            ->select('Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_Bills.*')
            ->get();

        return view('/bills/bills_cancellation_approval', [
            'bills' => $bills,
        ]);
    }

    public function approveBillCancellationRequest($id) {
        $bill = Bills::find($id);

        if ($bill != null) {
            // INSERT TO BILLS ORIGINAL
            $billsArr = $bill->toArray();
            $billsArr['id'] = IDGenerator::generateIDandRandString();
            $billsArr['ForCancellation'] = 'Cancelled';
            $billsArr['CancelApprovedBy'] = Auth::id();
            $billsOriginal = BillsOriginal::create($billsArr);

            $bill->delete();
        }

        return redirect(route('bills.bills-cancellation-approval'));
    }

    public function rejectBillCancellationRequest($id) {
        $bill = Bills::find($id);

        if ($bill != null) {
            $bill->ForCancellation = null;
            $bill->CancelRequestedBy = null;
            $bill->CancelApprovedBy = null;
            $bill->Notes = null;
            $bill->save();
        }

        return redirect(route('bills.bills-cancellation-approval'));
    }

    public function changeMeterReadings($accountId, $period) {
        $account = ServiceAccounts::find($accountId);
        $reading = Readings::where('AccountNumber', $accountId)
            ->where('ServicePeriod', $period)
            ->first();
        $changeMeterLogs = ChangeMeterLogs::where('AccountNumber', $accountId)
            ->where('ServicePeriod', $period)
            ->first();

        $prevReadings = Readings::where('AccountNumber', $accountId)
            ->whereNotIn('ServicePeriod', [$period])
            ->orderByDesc('ServicePeriod')
            ->limit(15)
            ->get();

        return view('/bills/change_meter_readings', [
            'account' => $account,
            'reading' => $reading,
            'changeMeterLogs' => $changeMeterLogs,
            'prevReadings' => $prevReadings,
        ]);
    }

    public function billChangeMeters(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);
        $reading = Readings::where('AccountNumber', $request['AccountNumber'])
            ->where('ServicePeriod', $request['ServicePeriod'])
            ->first();
        $prevReading = Readings::where('AccountNumber', $request['AccountNumber'])
            ->where('ServicePeriod', date('Y-m-01', strtotime($request['ServicePeriod'] . ' -1 month')))
            ->first();

        $bill = Bills::computeRegularBill(
                $account, 
                null, 
                $request['KwhUsed'], 
                ($prevReading != null ? $prevReading->KwhUsed : '0'), 
                ($reading != null ? $reading->KwhUsed : '0'), 
                $request['ServicePeriod'],
                ($reading != null ? date('Y-m-d', strtotime($reading->ReadingTimestamp)) : date('Y-m-d')),
                0,
                0,
                false
            );
        
        return redirect(route('bills.unbilled-readings-console', $request['ServicePeriod']));
    }
}
