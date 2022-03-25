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
use App\Models\BillingMeters;
use App\Models\IDGenerator;
use App\Models\PendingBillAdjustments;
use App\Models\ArrearsLedgerDistribution;
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

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        $bills = $this->billsRepository->update($request->all(), $id);

        Flash::success('Bills updated successfully.');

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

    public function unbilledReadingsConsole($servicePeriod) {
        $zeroReadings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereNotIn('Billing_Readings.AccountNumber', DB::table('Billing_Bills')->where('Billing_Bills.ServicePeriod', $servicePeriod)->pluck('Billing_Bills.AccountNumber'))
            ->where('Billing_Readings.ServicePeriod', $servicePeriod)
            ->where('Billing_ServiceAccounts.AccountStatus', 'ACTIVE')
            ->whereNotIn('Billing_Readings.id', DB::table('Billing_PendingBillAdjustments')->where('ServicePeriod', $servicePeriod)->whereNull('Confirmed')->pluck('ReadingId'))
            ->select('Billing_Readings.AccountNumber',
                'Billing_Readings.id',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_Readings.FieldStatus')
            ->get();
        
        return view('/bills/unbilled_readings_console', [
            'servicePeriod' => $servicePeriod,
            'zeroReadings' => $zeroReadings,
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

        if ($rate != null) {
            if (count($previousBills) > 0) {
                // GET AVERAGE BILL
                $average = 0.0;
                foreach($previousBills as $item) {
                    $average += floatval($item->KwhUsed);
                }
                $average = $average/count($previousBills);

                $multiplier = $account->Multiplier != null ? floatval($account->Multiplier) : 1;
                $coreloss = $account->Coreloss != null ? floatval($account->Coreloss) : 0;
                $effectiveRate = Rates::floatRate($rate->TotalRateVATIncluded);

                $totalKwh = ($average * $multiplier) + $coreloss;

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
                    $bill->KwhUsed = $average;
                    $bill->PreviousKwh = $previousBill != null ? $previousBill->PresentKwh : 0;
                    $bill->PresentKwh = 0;
                    $bill->KwhAmount = $effectiveRate * $average;
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

                    $bill->GenerationSystemCharge = $average * Rates::floatRate($rate->GenerationSystemCharge);
                    $bill->TransmissionDeliveryChargeKW = $average * Rates::floatRate($rate->TransmissionDeliveryChargeKW);
                    $bill->TransmissionDeliveryChargeKWH = $average * Rates::floatRate($rate->TransmissionDeliveryChargeKWH);
                    $bill->SystemLossCharge = $average * Rates::floatRate($rate->SystemLossCharge);
                    $bill->DistributionDemandCharge = $average * Rates::floatRate($rate->DistributionDemandCharge);
                    $bill->DistributionSystemCharge = $average * Rates::floatRate($rate->DistributionSystemCharge);
                    $bill->SupplyRetailCustomerCharge = $average * Rates::floatRate($rate->SupplyRetailCustomerCharge);
                    $bill->SupplySystemCharge = $average * Rates::floatRate($rate->SupplySystemCharge);
                    $bill->MeteringRetailCustomerCharge = $average * Rates::floatRate($rate->MeteringRetailCustomerCharge);
                    $bill->MeteringSystemCharge = $average * Rates::floatRate($rate->MeteringSystemCharge);
                    $bill->RFSC = $average * Rates::floatRate($rate->RFSC);
                    $bill->LifelineRate = $average * Rates::floatRate($rate->LifelineRate);
                    $bill->InterClassCrossSubsidyCharge = $average * Rates::floatRate($rate->InterClassCrossSubsidyCharge);
                    $bill->PPARefund = $average * Rates::floatRate($rate->PPARefund);
                    $bill->SeniorCitizenSubsidy = $average * Rates::floatRate($rate->SeniorCitizenSubsidy);
                    $bill->MissionaryElectrificationCharge = $average * Rates::floatRate($rate->MissionaryElectrificationCharge);
                    $bill->EnvironmentalCharge = $average * Rates::floatRate($rate->EnvironmentalCharge);
                    $bill->StrandedContractCosts = $average * Rates::floatRate($rate->StrandedContractCosts);
                    $bill->NPCStrandedDebt = $average * Rates::floatRate($rate->NPCStrandedDebt);
                    $bill->FeedInTariffAllowance = $average * Rates::floatRate($rate->FeedInTariffAllowance);
                    $bill->MissionaryElectrificationREDCI = $average * Rates::floatRate($rate->MissionaryElectrificationREDCI);
                    $bill->GenerationVAT = $average * Rates::floatRate($rate->GenerationVAT);
                    $bill->TransmissionVAT = $average * Rates::floatRate($rate->TransmissionVAT);
                    $bill->SystemLossVAT = $average * Rates::floatRate($rate->SystemLossVAT);
                    $bill->DistributionVAT = $average * Rates::floatRate($rate->DistributionVAT);
                    $bill->RealPropertyTax = $average * Rates::floatRate($rate->RealPropertyTax);
                    $bill->AveragedCount = 'YES';
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    // SAVE BILL
                    $bill->save();
                } else {
                    // SET BILLING VALUES
                    $bill = new Bills;
                    $bill->id = IDGenerator::generateIDandRandString();
                    $bill->BillNumber = IDGenerator::generateBillNumber($account->AreaCode);

                    if ($account->SeniorCitizen != null && $account->SeniorCitizen=="Yes" && $kwhUsed < 101) {
                        $bill->Deductions = $deductions;
                        $netAmount = $netAmount - $deductions;
                    }
                    $netAmount = $netAmount + $addOns;
                    $bill->AccountNumber = $account->id;
                    $bill->ServicePeriod = $reading->ServicePeriod;
                    $bill->Multiplier = $multiplier;
                    $bill->Coreloss = $coreloss;
                    $bill->KwhUsed = $average;
                    $bill->PreviousKwh = $previousBill != null ? $previousBill->PresentKwh : 0;
                    $bill->PresentKwh = 0;
                    $bill->KwhAmount = $effectiveRate * $average;
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

                    $bill->GenerationSystemCharge = $average * Rates::floatRate($rate->GenerationSystemCharge);
                    $bill->TransmissionDeliveryChargeKW = $average * Rates::floatRate($rate->TransmissionDeliveryChargeKW);
                    $bill->TransmissionDeliveryChargeKWH = $average * Rates::floatRate($rate->TransmissionDeliveryChargeKWH);
                    $bill->SystemLossCharge = $average * Rates::floatRate($rate->SystemLossCharge);
                    $bill->DistributionDemandCharge = $average * Rates::floatRate($rate->DistributionDemandCharge);
                    $bill->DistributionSystemCharge = $average * Rates::floatRate($rate->DistributionSystemCharge);
                    $bill->SupplyRetailCustomerCharge = $average * Rates::floatRate($rate->SupplyRetailCustomerCharge);
                    $bill->SupplySystemCharge = $average * Rates::floatRate($rate->SupplySystemCharge);
                    $bill->MeteringRetailCustomerCharge = $average * Rates::floatRate($rate->MeteringRetailCustomerCharge);
                    $bill->MeteringSystemCharge = $average * Rates::floatRate($rate->MeteringSystemCharge);
                    $bill->RFSC = $average * Rates::floatRate($rate->RFSC);
                    $bill->LifelineRate = $average * Rates::floatRate($rate->LifelineRate);
                    $bill->InterClassCrossSubsidyCharge = $average * Rates::floatRate($rate->InterClassCrossSubsidyCharge);
                    $bill->PPARefund = $average * Rates::floatRate($rate->PPARefund);
                    $bill->SeniorCitizenSubsidy = $average * Rates::floatRate($rate->SeniorCitizenSubsidy);
                    $bill->MissionaryElectrificationCharge = $average * Rates::floatRate($rate->MissionaryElectrificationCharge);
                    $bill->EnvironmentalCharge = $average * Rates::floatRate($rate->EnvironmentalCharge);
                    $bill->StrandedContractCosts = $average * Rates::floatRate($rate->StrandedContractCosts);
                    $bill->NPCStrandedDebt = $average * Rates::floatRate($rate->NPCStrandedDebt);
                    $bill->FeedInTariffAllowance = $average * Rates::floatRate($rate->FeedInTariffAllowance);
                    $bill->MissionaryElectrificationREDCI = $average * Rates::floatRate($rate->MissionaryElectrificationREDCI);
                    $bill->GenerationVAT = $average * Rates::floatRate($rate->GenerationVAT);
                    $bill->TransmissionVAT = $average * Rates::floatRate($rate->TransmissionVAT);
                    $bill->SystemLossVAT = $average * Rates::floatRate($rate->SystemLossVAT);
                    $bill->DistributionVAT = $average * Rates::floatRate($rate->DistributionVAT);
                    $bill->RealPropertyTax = $average * Rates::floatRate($rate->RealPropertyTax);
                    $bill->AveragedCount = 'YES';
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    // SAVE BILL
                    $bill->save();
                }
                

                Flash::success('Bill to account ' . $account->ServiceAccountName . ' for period ' . date('F Y', strtotime($bill->ServicePeriod)) . ' averaged successfully!');

                return redirect(route('bills.unbilled-readings-console', [$reading->ServicePeriod]));
            } else {
                return abort(403, "NO PREVIOUS BILLS RECORDED FOR AVERAGING");
            }
        } else {
            return abort(404, "No Rate for this service period yet.");
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

        return response()->json(Bills::computeRegularBill($account, $bill->id, $request['KwhUsed'], $bill->PreviousKwh, $bill->PresentKwh, $bill->ServicePeriod, $bill->BillingDate, $additionalCharges, $deductions, $request['Is2307']), 200);
    }

    public function allBills(Request $request) {
        if ($request['params'] == null) {
            $bills = DB::table('Billing_Bills')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 
                            'Billing_ServiceAccounts.id as AccountNumber', 
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay', 
                            'Billing_ServiceAccounts.AccountCount',
                            'Billing_Bills.BillNumber',
                            'Billing_Bills.id',
                            'Billing_Bills.ServicePeriod')
                        ->orderByDesc('Billing_Bills.created_at')
                        ->limit(60)
                        ->paginate(15);
        } else {
            $bills = DB::table('Billing_Bills')
                        ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 
                            'Billing_ServiceAccounts.id as AccountNumber', 
                            'CRM_Towns.Town', 
                            'CRM_Barangays.Barangay', 
                            'Billing_ServiceAccounts.AccountCount',
                            'Billing_Bills.BillNumber',
                            'Billing_Bills.id',
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
}
