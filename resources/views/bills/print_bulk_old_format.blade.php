
@php
use App\Models\ServiceAccounts;
use App\Models\MemberConsumers;
use App\Models\Bills;
use App\Models\Rates;
use App\Models\BillingMeters;
use App\Models\ServiceConnectionAccountTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
@endphp

<style>
@media print {
    @page {
        /* size: 8.5in 6.5in; */
        font-size: .8em !important;
    }

    html, body {
        font-family: sans-serif;
        font-size: .8em !important;
    }

    header {
        display: none;
    }

    .print-area {        
        page-break-before: always;
    }

    .print-area:last-child {        
        page-break-after: auto;
    }
}  

html, body {
    font-family: sans-serif;
    font-size: .8em !important;
}

.left-indent {
    margin-left: 50px;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.divider {
    width: 100%;
    margin: 10px auto;
    height: 1px;
    background-color: #dedede;
} 


</style>

{{-- <link rel="stylesheet" href="{{ URL::asset('adminlte.min.css') }}"> --}}

@foreach ($bills as $item)
@php
    $acctType = ServiceConnectionAccountTypes::where('AccountType', $item->ConsumerType)->first();

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
        ->where('Billing_ServiceAccounts.id', $item->AccountNumber)
        ->first();

    $meters = BillingMeters::where('ServiceAccountId', $item->AccountNumber)
        ->orderByDesc('created_at')
        ->first();

    $rate = Rates::where('ServicePeriod', $item->ServicePeriod)
        ->where('ConsumerType', $item->ConsumerType)
        ->first();

    if ($account != null) {
        $arrears = DB::table('Billing_Bills')
            ->whereRaw("Billing_Bills.id NOT IN (SELECT ObjectSourceId FROM Cashier_PaidBills WHERE AccountNumber='" . $account->id . "')")
            ->where('Billing_Bills.AccountNumber', $account->id)
            ->whereNotIN('Billing_Bills.id', [$item->id])
            ->select('Billing_Bills.*')
            ->get();
    } else {
        $arrears = null;
    }
@endphp
<div class="print-area" style="padding-top: 5px;">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <span style="margin-left: 55px;">{{ date('F Y', strtotime($item->ServicePeriod)) }}</span>
    <span style="margin-left: 135px;">{{ date('F d, Y', strtotime($item->BillingDate)) }}</span><br>
    <br>
    <br>
    <span>{{ $account->OldAccountNo }}</span>
    <span style="margin-left: 15px;">{{ $item->MeterNumber }}</span>
    <span style="position: fixed; left: 190px;">{{ $acctType != null ? $acctType->Alias : '-' }}</span>
    <span style="position: fixed; left: 220px;">{{ $item->Multiplier }}</span>
    <span style="position: fixed; left: 270px;">{{ date('m/d/Y', strtotime($item->DueDate)) }}</span>
    <br>
    <span style="position: fixed; left: 40px; top: 138px;">{{ $account->ServiceAccountName }}</span>
    <span style="position: fixed; left: 350px; top: 138px;">{{ date('M d, Y', strtotime($item->ServiceDateFrom)) }}</span>
    <span style="position: fixed; left: 420px; top: 138px;">{{ date('M d, Y', strtotime($item->ServiceDateTo)) }}</span>
    <span style="position: fixed; left: 495px; top: 138px;">{{ $item->PresentKwh }}</span>
    <span style="position: fixed; left: 545px; top: 138px;">{{ $item->PreviousKwh }}</span>
    <span style="position: fixed; left: 600px; top: 138px;">{{ $item->KwhUsed }}</span>

    <span style="position: fixed; left: 40px; top: 154px;">{{ ServiceAccounts::getAddress($account) }}</span>

    <span style="position: fixed; right: 100px; top: 222px;">{{ number_format($item->LifelineRate, 2) ? number_format($item->LifelineRate, 2) : $item->LifelineRate }}</span>
    <span style="position: fixed; right: 185px; top: 222px;">{{ number_format($rate->LifelineRate, 4) ? number_format($rate->LifelineRate, 4) : $rate->LifelineRate }}</span>
    <span style="position: fixed; right: 485px; top: 222px;">{{ number_format($item->GenerationSystemCharge, 2) ? number_format($item->GenerationSystemCharge, 2) : $item->GenerationSystemCharge }}</span>
    <span style="position: fixed; right: 560px; top: 222px;">{{ number_format($rate->GenerationSystemCharge, 4) ? number_format($rate->GenerationSystemCharge, 4) : $rate->GenerationSystemCharge }}</span>

    <span style="position: fixed; right: 100px; top: 238px;">{{ number_format($item->InterClassCrossSubsidyCharge, 2) ? number_format($item->InterClassCrossSubsidyCharge, 2) : $item->InterClassCrossSubsidyCharge }}</span>
    <span style="position: fixed; right: 185px; top: 238px;">{{ number_format($rate->InterClassCrossSubsidyCharge, 4) ? number_format($rate->InterClassCrossSubsidyCharge, 4) : $rate->InterClassCrossSubsidyCharge }}</span>
    <span style="position: fixed; right: 485px; top: 238px;"></span>
    <span style="position: fixed; right: 560px; top: 238px;"></span>

    <span style="position: fixed; right: 100px; top: 254px;">{{ number_format($item->RealPropertyTax, 2) ? number_format($item->RealPropertyTax, 2) : $item->RealPropertyTax }}</span>
    <span style="position: fixed; right: 185px; top: 254px;">{{ number_format($rate->RealPropertyTax, 4) ? number_format($rate->RealPropertyTax, 4) : $rate->RealPropertyTax }}</span>
    <span style="position: fixed; right: 485px; top: 254px;"></span>
    <span style="position: fixed; right: 560px; top: 254px;"></span>

    <span style="position: fixed; right: 100px; top: 270px;">{{ number_format($item->SeniorCitizenSubsidy, 2) ? number_format($item->SeniorCitizenSubsidy, 2) : $item->SeniorCitizenSubsidy }}</span>
    <span style="position: fixed; right: 185px; top: 270px;">{{ number_format($rate->SeniorCitizenSubsidy, 4) ? number_format($rate->SeniorCitizenSubsidy, 4) : $rate->SeniorCitizenSubsidy }}</span>
    <span style="position: fixed; right: 485px; top: 270px;"></span>
    <span style="position: fixed; right: 560px; top: 270px;"></span>

    <span style="position: fixed; right: 100px; top: 305px;">{{ number_format($item->NPCStrandedDebt, 2) ? number_format($item->NPCStrandedDebt, 2) : $item->NPCStrandedDebt }}</span>
    <span style="position: fixed; right: 185px; top: 305px;">{{ number_format($rate->NPCStrandedDebt, 4) ? number_format($rate->NPCStrandedDebt, 4) : $rate->NPCStrandedDebt }}</span>
    <span style="position: fixed; right: 485px; top: 300px;">{{ number_format($item->TransmissionDeliveryChargeKW, 2) ? number_format($item->TransmissionDeliveryChargeKW, 2) : $item->TransmissionDeliveryChargeKW }}</span>
    <span style="position: fixed; right: 560px; top: 300px;">{{ number_format($rate->TransmissionDeliveryChargeKW, 4) ? number_format($rate->TransmissionDeliveryChargeKW, 4) : $rate->TransmissionDeliveryChargeKW }}</span>
    
    <span style="position: fixed; right: 100px; top: 321px;">{{ number_format($item->StrandedContractCosts, 2) ? number_format($item->StrandedContractCosts, 2) : $item->StrandedContractCosts }}</span>
    <span style="position: fixed; right: 185px; top: 321px;">{{ number_format($rate->StrandedContractCosts, 4) ? number_format($rate->StrandedContractCosts, 4) : $rate->StrandedContractCosts }}</span>
    <span style="position: fixed; right: 485px; top: 316px;">{{ number_format($item->TransmissionDeliveryChargeKWH, 2) ? number_format($item->TransmissionDeliveryChargeKWH, 2) : $item->TransmissionDeliveryChargeKWH }}</span>
    <span style="position: fixed; right: 560px; top: 316px;">{{ number_format($rate->TransmissionDeliveryChargeKWH, 4) ? number_format($rate->TransmissionDeliveryChargeKWH, 4) : $rate->TransmissionDeliveryChargeKWH }}</span>

    <span style="position: fixed; right: 100px; top: 337px;"></span>
    <span style="position: fixed; right: 185px; top: 337px;"></span>
    <span style="position: fixed; right: 485px; top: 332px;">{{ number_format($item->SystemLossCharge, 2) ? number_format($item->SystemLossCharge, 2) : $item->SystemLossCharge }}</span>
    <span style="position: fixed; right: 560px; top: 332px;">{{ number_format($rate->SystemLossCharge, 4) ? number_format($rate->SystemLossCharge, 4) : $rate->SystemLossCharge }}</span>

    <span style="position: fixed; right: 100px; top: 353px;">{{ number_format($item->MissionaryElectrificationCharge, 2) ? number_format($item->MissionaryElectrificationCharge, 2) : $item->MissionaryElectrificationCharge }}</span>
    <span style="position: fixed; right: 185px; top: 353px;">{{ number_format($rate->MissionaryElectrificationCharge, 4) ? number_format($rate->MissionaryElectrificationCharge, 4) : $rate->MissionaryElectrificationCharge }}</span>
    <span style="position: fixed; right: 485px; top: 348px;"></span>
    <span style="position: fixed; right: 560px; top: 348px;"></span>

    <span style="position: fixed; right: 100px; top: 369px;"></span>
    <span style="position: fixed; right: 185px; top: 369px;"></span>
    <span style="position: fixed; right: 485px; top: 365px;">{{ number_format($item->DistributionDemandCharge, 2) ? number_format($item->DistributionDemandCharge, 2) : $item->DistributionDemandCharge }}</span>
    <span style="position: fixed; right: 560px; top: 365px;">{{ number_format($rate->DistributionDemandCharge, 4) ? number_format($rate->DistributionDemandCharge, 4) : $rate->DistributionDemandCharge }}</span>

    <span style="position: fixed; right: 100px; top: 381px;">{{ number_format($item->EnvironmentalCharge, 2) ? number_format($item->EnvironmentalCharge, 2) : $item->EnvironmentalCharge }}</span>
    <span style="position: fixed; right: 185px; top: 381px;">{{ number_format($rate->EnvironmentalCharge, 4) ? number_format($rate->EnvironmentalCharge, 4) : $rate->EnvironmentalCharge }}</span>
    <span style="position: fixed; right: 485px; top: 380px;">{{ number_format($item->DistributionSystemCharge, 2) ? number_format($item->DistributionSystemCharge, 2) : $item->DistributionSystemCharge }}</span>
    <span style="position: fixed; right: 560px; top: 380px;">{{ number_format($rate->DistributionSystemCharge, 4) ? number_format($rate->DistributionSystemCharge, 4) : $rate->DistributionSystemCharge }}</span>

    <span style="position: fixed; right: 100px; top: 397px;">{{ number_format($item->MissionaryElectrificationREDCI, 2) ? number_format($item->MissionaryElectrificationREDCI, 2) : $item->MissionaryElectrificationREDCI }}</span>
    <span style="position: fixed; right: 185px; top: 397px;">{{ number_format($rate->MissionaryElectrificationREDCI, 4) ? number_format($rate->MissionaryElectrificationREDCI, 4) : $rate->MissionaryElectrificationREDCI }}</span>
    <span style="position: fixed; right: 485px; top: 396px;">{{ number_format($item->SupplySystemCharge, 2) ? number_format($item->SupplySystemCharge, 2) : $item->SupplySystemCharge }}</span>
    <span style="position: fixed; right: 560px; top: 396px;">{{ number_format($rate->SupplySystemCharge, 4) ? number_format($rate->SupplySystemCharge, 4) : $rate->SupplySystemCharge }}</span>

    <span style="position: fixed; right: 100px; top: 413px;">{{ number_format($item->FeedInTariffAllowance, 2) ? number_format($item->FeedInTariffAllowance, 2) : $item->FeedInTariffAllowance }}</span>
    <span style="position: fixed; right: 185px; top: 413px;">{{ number_format($rate->FeedInTariffAllowance, 4) ? number_format($rate->FeedInTariffAllowance, 4) : $rate->FeedInTariffAllowance }}</span>
    <span style="position: fixed; right: 485px; top: 412px;">{{ number_format($item->SupplyRetailCustomerCharge, 2) ? number_format($item->SupplyRetailCustomerCharge, 2) : $item->SupplyRetailCustomerCharge }}</span>
    <span style="position: fixed; right: 560px; top: 412px;">{{ number_format($rate->SupplyRetailCustomerCharge, 4) ? number_format($rate->SupplyRetailCustomerCharge, 4) : $rate->SupplyRetailCustomerCharge }}</span>

    <span style="position: fixed; right: 100px; top: 429px;">{{ number_format($item->RFSC, 2) ? number_format($item->RFSC, 2) : $item->RFSC }}</span>
    <span style="position: fixed; right: 185px; top: 429px;">{{ number_format($rate->RFSC, 4) ? number_format($rate->RFSC, 4) : $rate->RFSC }}</span>
    <span style="position: fixed; right: 485px; top: 428px;">{{ number_format($item->MeteringSystemCharge, 2) ? number_format($item->MeteringSystemCharge, 2) : $item->MeteringSystemCharge }}</span>
    <span style="position: fixed; right: 560px; top: 428px;">{{ number_format($rate->MeteringSystemCharge, 4) ? number_format($rate->MeteringSystemCharge, 4) : $rate->MeteringSystemCharge }}</span>

    <span style="position: fixed; right: 100px; top: 445px;"></span>
    <span style="position: fixed; right: 185px; top: 445px;"></span>
    <span style="position: fixed; right: 485px; top: 444px;">{{ number_format($item->MeteringRetailCustomerCharge, 2) ? number_format($item->MeteringRetailCustomerCharge, 2) : $item->MeteringRetailCustomerCharge }}</span>
    <span style="position: fixed; right: 560px; top: 444px;">{{ number_format($rate->MeteringRetailCustomerCharge, 4) ? number_format($rate->MeteringRetailCustomerCharge, 4) : $rate->MeteringRetailCustomerCharge }}</span>

    <span style="position: fixed; right: 100px; top: 455px;">{{ number_format($item->PPARefund, 2) ? number_format($item->PPARefund, 2) : $item->PPARefund }}</span>
    <span style="position: fixed; right: 185px; top: 455px;">{{ number_format($rate->PPARefund, 4) ? number_format($rate->PPARefund, 4) : $rate->PPARefund }}</span>
    <span style="position: fixed; right: 485px; top: 455px;"></span>
    <span style="position: fixed; right: 560px; top: 455px;"></span>

    <span style="position: fixed; right: 100px; top: 467px;">{{ number_format($item->OtherGenerationRateAdjustment, 2) ? number_format($item->OtherGenerationRateAdjustment, 2) : $item->OtherGenerationRateAdjustment }}</span>
    <span style="position: fixed; right: 185px; top: 467px;">{{ number_format($rate->OtherGenerationRateAdjustment, 4) ? number_format($rate->OtherGenerationRateAdjustment, 4) : $rate->OtherGenerationRateAdjustment }}</span>
    <span style="position: fixed; left: 355px; top: 467px;">Other Generation Rate Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 485px; top: 455px;"></span>
    <span style="position: fixed; right: 560px; top: 455px;"></span>

    <span style="position: fixed; right: 100px; top: 479px;">{{ number_format($item->OtherTransmissionCostAdjustmentKW, 2) ? number_format($item->OtherTransmissionCostAdjustmentKW, 2) : $item->OtherTransmissionCostAdjustmentKW }}</span>
    <span style="position: fixed; right: 185px; top: 479px;">{{ number_format($rate->OtherTransmissionCostAdjustmentKW, 4) ? number_format($rate->OtherTransmissionCostAdjustmentKW, 4) : $rate->OtherTransmissionCostAdjustmentKW }}</span>
    <span style="position: fixed; left: 355px; top: 479px;">Other Transmission Cost Adjustment (Php/kW)</span>
    <span style="position: fixed; right: 485px; top: 455px;"></span>
    <span style="position: fixed; right: 560px; top: 455px;"></span>

    <span style="position: fixed; right: 100px; top: 491px;">{{ number_format($item->OtherTransmissionCostAdjustmentKWH, 2) ? number_format($item->OtherTransmissionCostAdjustmentKWH, 2) : $item->OtherTransmissionCostAdjustmentKWH }}</span>
    <span style="position: fixed; right: 185px; top: 491px;">{{ number_format($rate->OtherTransmissionCostAdjustmentKWH, 4) ? number_format($rate->OtherTransmissionCostAdjustmentKWH, 4) : $rate->OtherTransmissionCostAdjustmentKWH }}</span>
    <span style="position: fixed; left: 355px; top: 491px;">Other Transmission Cost Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 485px; top: 455px;"></span>
    <span style="position: fixed; right: 560px; top: 455px;"></span>

    <span style="position: fixed; right: 100px; top: 503px;">{{ number_format($item->OtherSystemLossCostAdjustment, 2) ? number_format($item->OtherSystemLossCostAdjustment, 2) : $item->OtherSystemLossCostAdjustment }}</span>
    <span style="position: fixed; right: 185px; top: 503px;">{{ number_format($rate->OtherSystemLossCostAdjustment, 4) ? number_format($rate->OtherSystemLossCostAdjustment, 4) : $rate->OtherSystemLossCostAdjustment }}</span>
    <span style="position: fixed; left: 355px; top: 503px;">Other System Loss Cost Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 485px; top: 455px;"></span>
    <span style="position: fixed; right: 560px; top: 455px;"></span>

    <span style="position: fixed; right: 100px; top: 515px;">{{ number_format($item->OtherLifelineRateCostAdjustment, 2) ? number_format($item->OtherLifelineRateCostAdjustment, 2) : $item->OtherLifelineRateCostAdjustment }}</span>
    <span style="position: fixed; right: 185px; top: 515px;">{{ number_format($rate->OtherLifelineRateCostAdjustment, 4) ? number_format($rate->OtherLifelineRateCostAdjustment, 4) : $rate->OtherLifelineRateCostAdjustment }}</span>
    <span style="position: fixed; left: 355px; top: 515px;">Other Lifeline Rate Cost Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 485px; top: 455px;"></span>
    <span style="position: fixed; right: 560px; top: 455px;"></span>

    <span style="position: fixed; right: 100px; top: 527px;">{{ number_format($item->SeniorCitizenDiscountAndSubsidyAdjustment, 2) ? number_format($item->SeniorCitizenDiscountAndSubsidyAdjustment, 2) : $item->SeniorCitizenDiscountAndSubsidyAdjustment }}</span>
    <span style="position: fixed; right: 185px; top: 527px;">{{ number_format($rate->SeniorCitizenDiscountAndSubsidyAdjustment, 4) ? number_format($rate->SeniorCitizenDiscountAndSubsidyAdjustment, 4) : $rate->SeniorCitizenDiscountAndSubsidyAdjustment }}</span>
    <span style="position: fixed; left: 355px; top: 527px;">Sen. Cit. Discnt. Subsidy Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 485px; top: 527px;">{{ number_format($item->GenerationVAT, 2) ? number_format($item->GenerationVAT, 2) : $item->GenerationVAT }}</span>
    <span style="position: fixed; right: 560px; top: 527px;">{{ number_format($rate->GenerationVAT, 4) ? number_format($rate->GenerationVAT, 4) : $rate->GenerationVAT }}</span>

    <span style="position: fixed; right: 100px; top: 539px;">{{ number_format($item->Evat2Percent, 2) ? number_format($item->Evat2Percent, 2) : $item->Evat2Percent }}</span>
    <span style="position: fixed; right: 185px; top: 539px;"></span>
    <span style="position: fixed; left: 355px; top: 539px;">2% EWT</span>
    <span style="position: fixed; right: 485px; top: 542px;">{{ number_format($item->TransmissionVAT, 2) ? number_format($item->TransmissionVAT, 2) : $item->TransmissionVAT }}</span>
    <span style="position: fixed; right: 560px; top: 542px;">{{ number_format($rate->TransmissionVAT, 4) ? number_format($rate->TransmissionVAT, 4) : $rate->TransmissionVAT }}</span>

    <span style="position: fixed; right: 100px; top: 552px;">{{ number_format($item->NetAmount, 2) ? number_format($item->NetAmount, 2) : $item->NetAmount }}</span>
    <span style="position: fixed; right: 185px; top: 539px;"></span>
    <span style="position: fixed; left: 520px; top: 552px;">{{ date('d M Y', strtotime($item->DueDate)) }}</span>
    <span style="position: fixed; right: 485px; top: 557px;">{{ number_format($item->SystemLossVAT, 2) ? number_format($item->SystemLossVAT, 2) : $item->SystemLossVAT }}</span>
    <span style="position: fixed; right: 560px; top: 557px;">{{ number_format($rate->SystemLossVAT, 4) ? number_format($rate->SystemLossVAT, 4) : $rate->SystemLossVAT }}</span>

    <span style="position: fixed; right: 100px; top: 568px;">{{ number_format(Bills::getFinalPenalty($item), 2) ? number_format(Bills::getFinalPenalty($item), 2) : Bills::getFinalPenalty($item) }}</span>
    <span style="position: fixed; right: 185px; top: 568px;"></span>
    <span style="position: fixed; right: 485px; top: 572px;">{{ number_format($item->DistributionVAT, 2) ? number_format($item->DistributionVAT, 2) : $item->DistributionVAT }}</span>
    <span style="position: fixed; right: 560px; top: 572px;">{{ number_format($rate->DistributionVAT, 4) ? number_format($rate->DistributionVAT, 4) : $rate->DistributionVAT }}</span>

    <span style="position: fixed; right: 100px; top: 584px;">{{ number_format(floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount), 2) ? number_format(floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount), 2) : floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount) }}</span>

    
    <span style="position: fixed; left: 65px; top: 618px;">{{ $account->ServiceAccountName }}</span>
    <span style="position: fixed; left: 412px; top: 618px;">{{ $account->OldAccountNo }}</span>
    <span style="position: fixed; right: 100px; top: 618px;">{{ number_format(floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount), 2) ? number_format(floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount), 2) : floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount) }}</span>

    <span style="position: fixed; left: 50px; top: 634px;">{{ $account->AccountType . ' '}} {{ ' ' . ServiceAccounts::getAddress($account) }}</span>
    <span style="position: fixed; left: 412px; top: 634px;">{{ date('F Y', strtotime($item->ServicePeriod)) }}</span>
    @php
        $totalArr = 0.0;
        if ($arrears != null) {
            foreach($arrears as $item) {
                $totalArr = $totalArr += floatval($item->NetAmount);
            }
        }
    @endphp
    <span style="position: fixed; right: 100px; top: 634px;">({{ $arrears != null ? count($arrears) : '0' }}) {{ number_format($totalArr, 2) }}</span>

    <span style="position: fixed; left: 56px; top: 650px;">{{ $item->MeterNumber }}</span>
    <span style="position: fixed; left: 412px; top: 650px;">{{ date('F d, Y', strtotime($item->BillingDate)) }}</span>
    <span style="position: fixed; right: 100px; top: 650px;">{{ date('F d, Y', strtotime($item->DueDate)) }}</span>
</div>
@endforeach

<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.history.go(-1)
    }, 800);
</script>