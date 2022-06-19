
@php
use App\Models\ServiceAccounts;
use App\Models\MemberConsumers;
use App\Models\Bills;
use App\Models\ServiceConnectionAccountTypes;
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
        page-break-after: always;
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
@php
    $acctType = ServiceConnectionAccountTypes::where('AccountType', $bills->ConsumerType)->first();
@endphp
<div id="print-area" class="content" style="padding-top: 25px;">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <span style="margin-left: 95px;">{{ date('F Y', strtotime($bills->ServicePeriod)) }}</span>
    <span style="margin-left: 135px;">{{ date('F d, Y', strtotime($bills->BillingDate)) }}</span><br>
    <br>
    <br>
    <span style="position: fixed; left: 35px;">{{ $account->OldAccountNo }}</span>
    <span  style="position: fixed; left: 140px;">{{ $bills->MeterNumber }}</span>
    <span style="position: fixed; left: 230px;">{{ $acctType != null ? $acctType->Alias : '-' }}</span>
    <span style="position: fixed; left: 260px;">{{ $bills->Multiplier }}</span>
    <span style="position: fixed; left: 315px;">{{ date('m/d/Y', strtotime($bills->DueDate)) }}</span>
    <br>
    <span style="position: fixed; left: 70px; top: 158px;">{{ $account->ServiceAccountName }}</span>
    <span style="position: fixed; left: 400px; top: 158px;">{{ date('M d, Y', strtotime($bills->ServiceDateFrom)) }}</span>
    <span style="position: fixed; left: 470px; top: 158px;">{{ date('M d, Y', strtotime($bills->ServiceDateTo)) }}</span>
    <span style="position: fixed; left: 545px; top: 158px;">{{ $bills->PresentKwh }}</span>
    <span style="position: fixed; left: 595px; top: 158px;">{{ $bills->PreviousKwh }}</span>
    <span style="position: fixed; left: 650px; top: 158px;">{{ $bills->KwhUsed }}</span>

    <span style="position: fixed; left: 80px; top: 174px;">{{ ServiceAccounts::getAddress($account) }}</span>

    <span style="position: fixed; right: 70px; top: 242px;">{{ number_format($bills->LifelineRate, 2) ? number_format($bills->LifelineRate, 2) : $bills->LifelineRate }}</span>
    <span style="position: fixed; right: 135px; top: 242px;">{{ number_format($rate->LifelineRate, 4) ? number_format($rate->LifelineRate, 4) : $rate->LifelineRate }}</span>
    <span style="position: fixed; right: 435px; top: 242px;">{{ number_format($bills->GenerationSystemCharge, 2) ? number_format($bills->GenerationSystemCharge, 2) : $bills->GenerationSystemCharge }}</span>
    <span style="position: fixed; right: 510px; top: 242px;">{{ number_format($rate->GenerationSystemCharge, 4) ? number_format($rate->GenerationSystemCharge, 4) : $rate->GenerationSystemCharge }}</span>

    <span style="position: fixed; right: 70px; top: 258px;">{{ number_format($bills->InterClassCrossSubsidyCharge, 2) ? number_format($bills->InterClassCrossSubsidyCharge, 2) : $bills->InterClassCrossSubsidyCharge }}</span>
    <span style="position: fixed; right: 135px; top: 258px;">{{ number_format($rate->InterClassCrossSubsidyCharge, 4) ? number_format($rate->InterClassCrossSubsidyCharge, 4) : $rate->InterClassCrossSubsidyCharge }}</span>
    <span style="position: fixed; right: 435px; top: 238px;"></span>
    <span style="position: fixed; right: 510px; top: 238px;"></span>

    <span style="position: fixed; right: 70px; top: 274px;">{{ number_format($bills->RealPropertyTax, 2) ? number_format($bills->RealPropertyTax, 2) : $bills->RealPropertyTax }}</span>
    <span style="position: fixed; right: 135px; top: 274px;">{{ number_format($rate->RealPropertyTax, 4) ? number_format($rate->RealPropertyTax, 4) : $rate->RealPropertyTax }}</span>
    <span style="position: fixed; right: 435px; top: 254px;"></span>
    <span style="position: fixed; right: 510px; top: 254px;"></span>

    <span style="position: fixed; right: 70px; top: 290px;">{{ number_format($bills->SeniorCitizenSubsidy, 2) ? number_format($bills->SeniorCitizenSubsidy, 2) : $bills->SeniorCitizenSubsidy }}</span>
    <span style="position: fixed; right: 135px; top: 290px;">{{ number_format($rate->SeniorCitizenSubsidy, 4) ? number_format($rate->SeniorCitizenSubsidy, 4) : $rate->SeniorCitizenSubsidy }}</span>
    <span style="position: fixed; right: 435px; top: 270px;"></span>
    <span style="position: fixed; right: 510px; top: 270px;"></span>

    <span style="position: fixed; right: 70px; top: 325px;">{{ number_format($bills->NPCStrandedDebt, 2) ? number_format($bills->NPCStrandedDebt, 2) : $bills->NPCStrandedDebt }}</span>
    <span style="position: fixed; right: 135px; top: 325px;">{{ number_format($rate->NPCStrandedDebt, 4) ? number_format($rate->NPCStrandedDebt, 4) : $rate->NPCStrandedDebt }}</span>
    <span style="position: fixed; right: 435px; top: 320px;">{{ number_format($bills->TransmissionDeliveryChargeKW, 2) ? number_format($bills->TransmissionDeliveryChargeKW, 2) : $bills->TransmissionDeliveryChargeKW }}</span>
    <span style="position: fixed; right: 510px; top: 320px;">{{ number_format($rate->TransmissionDeliveryChargeKW, 4) ? number_format($rate->TransmissionDeliveryChargeKW, 4) : $rate->TransmissionDeliveryChargeKW }}</span>
    
    <span style="position: fixed; right: 70px; top: 341px;">{{ number_format($bills->StrandedContractCosts, 2) ? number_format($bills->StrandedContractCosts, 2) : $bills->StrandedContractCosts }}</span>
    <span style="position: fixed; right: 135px; top: 341px;">{{ number_format($rate->StrandedContractCosts, 4) ? number_format($rate->StrandedContractCosts, 4) : $rate->StrandedContractCosts }}</span>
    <span style="position: fixed; right: 435px; top: 336px;">{{ number_format($bills->TransmissionDeliveryChargeKWH, 2) ? number_format($bills->TransmissionDeliveryChargeKWH, 2) : $bills->TransmissionDeliveryChargeKWH }}</span>
    <span style="position: fixed; right: 510px; top: 336px;">{{ number_format($rate->TransmissionDeliveryChargeKWH, 4) ? number_format($rate->TransmissionDeliveryChargeKWH, 4) : $rate->TransmissionDeliveryChargeKWH }}</span>

    <span style="position: fixed; right: 70px; top: 337px;"></span>
    <span style="position: fixed; right: 135px; top: 337px;"></span>
    <span style="position: fixed; right: 435px; top: 352px;">{{ number_format($bills->SystemLossCharge, 2) ? number_format($bills->SystemLossCharge, 2) : $bills->SystemLossCharge }}</span>
    <span style="position: fixed; right: 510px; top: 352px;">{{ number_format($rate->SystemLossCharge, 4) ? number_format($rate->SystemLossCharge, 4) : $rate->SystemLossCharge }}</span>

    <span style="position: fixed; right: 70px; top: 373px;">{{ number_format($bills->MissionaryElectrificationCharge, 2) ? number_format($bills->MissionaryElectrificationCharge, 2) : $bills->MissionaryElectrificationCharge }}</span>
    <span style="position: fixed; right: 135px; top: 373px;">{{ number_format($rate->MissionaryElectrificationCharge, 4) ? number_format($rate->MissionaryElectrificationCharge, 4) : $rate->MissionaryElectrificationCharge }}</span>
    <span style="position: fixed; right: 435px; top: 348px;"></span>
    <span style="position: fixed; right: 510px; top: 348px;"></span>

    <span style="position: fixed; right: 70px; top: 369px;"></span>
    <span style="position: fixed; right: 135px; top: 369px;"></span>
    <span style="position: fixed; right: 435px; top: 385px;">{{ number_format($bills->DistributionDemandCharge, 2) ? number_format($bills->DistributionDemandCharge, 2) : $bills->DistributionDemandCharge }}</span>
    <span style="position: fixed; right: 510px; top: 385px;">{{ number_format($rate->DistributionDemandCharge, 4) ? number_format($rate->DistributionDemandCharge, 4) : $rate->DistributionDemandCharge }}</span>

    <span style="position: fixed; right: 70px; top: 401px;">{{ number_format($bills->EnvironmentalCharge, 2) ? number_format($bills->EnvironmentalCharge, 2) : $bills->EnvironmentalCharge }}</span>
    <span style="position: fixed; right: 135px; top: 401px;">{{ number_format($rate->EnvironmentalCharge, 4) ? number_format($rate->EnvironmentalCharge, 4) : $rate->EnvironmentalCharge }}</span>
    <span style="position: fixed; right: 435px; top: 400px;">{{ number_format($bills->DistributionSystemCharge, 2) ? number_format($bills->DistributionSystemCharge, 2) : $bills->DistributionSystemCharge }}</span>
    <span style="position: fixed; right: 510px; top: 400px;">{{ number_format($rate->DistributionSystemCharge, 4) ? number_format($rate->DistributionSystemCharge, 4) : $rate->DistributionSystemCharge }}</span>

    <span style="position: fixed; right: 70px; top: 417px;">{{ number_format($bills->MissionaryElectrificationREDCI, 2) ? number_format($bills->MissionaryElectrificationREDCI, 2) : $bills->MissionaryElectrificationREDCI }}</span>
    <span style="position: fixed; right: 135px; top: 417px;">{{ number_format($rate->MissionaryElectrificationREDCI, 4) ? number_format($rate->MissionaryElectrificationREDCI, 4) : $rate->MissionaryElectrificationREDCI }}</span>
    <span style="position: fixed; right: 435px; top: 416px;">{{ number_format($bills->SupplySystemCharge, 2) ? number_format($bills->SupplySystemCharge, 2) : $bills->SupplySystemCharge }}</span>
    <span style="position: fixed; right: 510px; top: 416px;">{{ number_format($rate->SupplySystemCharge, 4) ? number_format($rate->SupplySystemCharge, 4) : $rate->SupplySystemCharge }}</span>

    <span style="position: fixed; right: 70px; top: 433px;">{{ number_format($bills->FeedInTariffAllowance, 2) ? number_format($bills->FeedInTariffAllowance, 2) : $bills->FeedInTariffAllowance }}</span>
    <span style="position: fixed; right: 135px; top: 433px;">{{ number_format($rate->FeedInTariffAllowance, 4) ? number_format($rate->FeedInTariffAllowance, 4) : $rate->FeedInTariffAllowance }}</span>
    <span style="position: fixed; right: 435px; top: 432px;">{{ number_format($bills->SupplyRetailCustomerCharge, 2) ? number_format($bills->SupplyRetailCustomerCharge, 2) : $bills->SupplyRetailCustomerCharge }}</span>
    <span style="position: fixed; right: 510px; top: 432px;">{{ number_format($rate->SupplyRetailCustomerCharge, 4) ? number_format($rate->SupplyRetailCustomerCharge, 4) : $rate->SupplyRetailCustomerCharge }}</span>

    <span style="position: fixed; right: 70px; top: 449px;">{{ number_format($bills->RFSC, 2) ? number_format($bills->RFSC, 2) : $bills->RFSC }}</span>
    <span style="position: fixed; right: 135px; top: 449px;">{{ number_format($rate->RFSC, 4) ? number_format($rate->RFSC, 4) : $rate->RFSC }}</span>
    <span style="position: fixed; right: 435px; top: 448px;">{{ number_format($bills->MeteringSystemCharge, 2) ? number_format($bills->MeteringSystemCharge, 2) : $bills->MeteringSystemCharge }}</span>
    <span style="position: fixed; right: 510px; top: 448px;">{{ number_format($rate->MeteringSystemCharge, 4) ? number_format($rate->MeteringSystemCharge, 4) : $rate->MeteringSystemCharge }}</span>

    <span style="position: fixed; right: 70px; top: 445px;"></span>
    <span style="position: fixed; right: 135px; top: 445px;"></span>
    <span style="position: fixed; right: 435px; top: 465px;">{{ number_format($bills->MeteringRetailCustomerCharge, 2) ? number_format($bills->MeteringRetailCustomerCharge, 2) : $bills->MeteringRetailCustomerCharge }}</span>
    <span style="position: fixed; right: 510px; top: 465px;">{{ number_format($rate->MeteringRetailCustomerCharge, 4) ? number_format($rate->MeteringRetailCustomerCharge, 4) : $rate->MeteringRetailCustomerCharge }}</span>

    <span style="position: fixed; right: 70px; top: 470px;">{{ number_format($bills->PPARefund, 2) ? number_format($bills->PPARefund, 2) : $bills->PPARefund }}</span>
    <span style="position: fixed; right: 135px; top: 470px;">{{ number_format($rate->PPARefund, 4) ? number_format($rate->PPARefund, 4) : $rate->PPARefund }}</span>
    <span style="position: fixed; right: 435px; top: 482px;"></span>
    <span style="position: fixed; right: 510px; top: 482px;"></span>

    <span style="position: fixed; right: 70px; top: 492px;">{{ number_format($bills->OtherGenerationRateAdjustment, 2) ? number_format($bills->OtherGenerationRateAdjustment, 2) : $bills->OtherGenerationRateAdjustment }}</span>
    <span style="position: fixed; right: 135px; top: 492px;">{{ number_format($rate->OtherGenerationRateAdjustment, 4) ? number_format($rate->OtherGenerationRateAdjustment, 4) : $rate->OtherGenerationRateAdjustment }}</span>
    <span style="position: fixed; left: 395px; top: 492px;">Other Generation Rate Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 435px; top: 455px;"></span>
    <span style="position: fixed; right: 510px; top: 455px;"></span>

    <span style="position: fixed; right: 70px; top: 504px;">{{ number_format($bills->OtherTransmissionCostAdjustmentKW, 2) ? number_format($bills->OtherTransmissionCostAdjustmentKW, 2) : $bills->OtherTransmissionCostAdjustmentKW }}</span>
    <span style="position: fixed; right: 135px; top: 504px;">{{ number_format($rate->OtherTransmissionCostAdjustmentKW, 4) ? number_format($rate->OtherTransmissionCostAdjustmentKW, 4) : $rate->OtherTransmissionCostAdjustmentKW }}</span>
    <span style="position: fixed; left: 395px; top: 504px;">Other Transmission Cost Adjustment (Php/kW)</span>
    <span style="position: fixed; right: 435px; top: 455px;"></span>
    <span style="position: fixed; right: 510px; top: 455px;"></span>

    <span style="position: fixed; right: 70px; top: 516px;">{{ number_format($bills->OtherTransmissionCostAdjustmentKWH, 2) ? number_format($bills->OtherTransmissionCostAdjustmentKWH, 2) : $bills->OtherTransmissionCostAdjustmentKWH }}</span>
    <span style="position: fixed; right: 135px; top: 516px;">{{ number_format($rate->OtherTransmissionCostAdjustmentKWH, 4) ? number_format($rate->OtherTransmissionCostAdjustmentKWH, 4) : $rate->OtherTransmissionCostAdjustmentKWH }}</span>
    <span style="position: fixed; left: 395px; top: 516px;">Other Transmission Cost Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 435px; top: 455px;"></span>
    <span style="position: fixed; right: 510px; top: 455px;"></span>

    <span style="position: fixed; right: 70px; top: 528px;">{{ number_format($bills->OtherSystemLossCostAdjustment, 2) ? number_format($bills->OtherSystemLossCostAdjustment, 2) : $bills->OtherSystemLossCostAdjustment }}</span>
    <span style="position: fixed; right: 135px; top: 528px;">{{ number_format($rate->OtherSystemLossCostAdjustment, 4) ? number_format($rate->OtherSystemLossCostAdjustment, 4) : $rate->OtherSystemLossCostAdjustment }}</span>
    <span style="position: fixed; left: 395px; top: 528px;">Other System Loss Cost Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 435px; top: 482px;"></span>
    <span style="position: fixed; right: 510px; top: 482px;"></span>

    <span style="position: fixed; right: 70px; top: 540px;">{{ number_format($bills->OtherLifelineRateCostAdjustment, 2) ? number_format($bills->OtherLifelineRateCostAdjustment, 2) : $bills->OtherLifelineRateCostAdjustment }}</span>
    <span style="position: fixed; right: 135px; top: 540px;">{{ number_format($rate->OtherLifelineRateCostAdjustment, 4) ? number_format($rate->OtherLifelineRateCostAdjustment, 4) : $rate->OtherLifelineRateCostAdjustment }}</span>
    <span style="position: fixed; left: 395px; top: 540px;">Other Lifeline Rate Cost Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 435px; top: 482px;"></span>
    <span style="position: fixed; right: 510px; top: 482px;"></span>

    <span style="position: fixed; right: 70px; top: 552px;">{{ number_format($bills->SeniorCitizenDiscountAndSubsidyAdjustment, 2) ? number_format($bills->SeniorCitizenDiscountAndSubsidyAdjustment, 2) : $bills->SeniorCitizenDiscountAndSubsidyAdjustment }}</span>
    <span style="position: fixed; right: 135px; top: 552px;">{{ number_format($rate->SeniorCitizenDiscountAndSubsidyAdjustment, 4) ? number_format($rate->SeniorCitizenDiscountAndSubsidyAdjustment, 4) : $rate->SeniorCitizenDiscountAndSubsidyAdjustment }}</span>
    <span style="position: fixed; left: 395px; top: 552px;">Sen. Cit. Discnt. Subsidy Adjustment (Php/kWh)</span>
    <span style="position: fixed; right: 435px; top: 552px;">{{ number_format($bills->GenerationVAT, 2) ? number_format($bills->GenerationVAT, 2) : $bills->GenerationVAT }}</span>
    <span style="position: fixed; right: 510px; top: 552px;">{{ number_format($rate->GenerationVAT, 4) ? number_format($rate->GenerationVAT, 4) : $rate->GenerationVAT }}</span>

    <span style="position: fixed; right: 70px; top: 564px;">{{ number_format($bills->Evat2Percent, 2) ? number_format($bills->Evat2Percent, 2) : $bills->Evat2Percent }}</span>
    <span style="position: fixed; right: 135px; top: 564px;"></span>
    <span style="position: fixed; left: 395px; top: 564px;">2% EWT</span>
    <span style="position: fixed; right: 435px; top: 567px;">{{ number_format($bills->TransmissionVAT, 2) ? number_format($bills->TransmissionVAT, 2) : $bills->TransmissionVAT }}</span>
    <span style="position: fixed; right: 510px; top: 567px;">{{ number_format($rate->TransmissionVAT, 4) ? number_format($rate->TransmissionVAT, 4) : $rate->TransmissionVAT }}</span>

    <span style="position: fixed; right: 70px; top: 577px;">{{ number_format($bills->NetAmount, 2) ? number_format($bills->NetAmount, 2) : $bills->NetAmount }}</span>
    <span style="position: fixed; right: 135px; top: 566px;"></span>
    <span style="position: fixed; left: 580px; top: 577px;">{{ date('d M Y', strtotime($bills->DueDate)) }}</span>
    <span style="position: fixed; right: 435px; top: 582px;">{{ number_format($bills->SystemLossVAT, 2) ? number_format($bills->SystemLossVAT, 2) : $bills->SystemLossVAT }}</span>
    <span style="position: fixed; right: 510px; top: 582px;">{{ number_format($rate->SystemLossVAT, 4) ? number_format($rate->SystemLossVAT, 4) : $rate->SystemLossVAT }}</span>

    <span style="position: fixed; right: 70px; top: 593px;">{{ number_format(Bills::getFinalPenalty($bills), 2) ? number_format(Bills::getFinalPenalty($bills), 2) : Bills::getFinalPenalty($bills) }}</span>
    <span style="position: fixed; right: 135px; top: 595px;"></span>
    <span style="position: fixed; right: 435px; top: 597px;">{{ number_format($bills->DistributionVAT, 2) ? number_format($bills->DistributionVAT, 2) : $bills->DistributionVAT }}</span>
    <span style="position: fixed; right: 510px; top: 597px;">{{ number_format($rate->DistributionVAT, 4) ? number_format($rate->DistributionVAT, 4) : $rate->DistributionVAT }}</span>

    <span style="position: fixed; right: 70px; top: 609px;">{{ number_format(floatval(Bills::getFinalPenalty($bills)) + floatval($bills->NetAmount), 2) ? number_format(floatval(Bills::getFinalPenalty($bills)) + floatval($bills->NetAmount), 2) : floatval(Bills::getFinalPenalty($bills)) + floatval($bills->NetAmount) }}</span>

    
    <span style="position: fixed; left: 110px; top: 642px;">{{ $account->ServiceAccountName }}</span>
    <span style="position: fixed; left: 462px; top: 642px;">{{ $account->OldAccountNo }}</span>
    <span style="position: fixed; right: 70px; top: 642px;">{{ number_format(floatval(Bills::getFinalPenalty($bills)) + floatval($bills->NetAmount), 2) ? number_format(floatval(Bills::getFinalPenalty($bills)) + floatval($bills->NetAmount), 2) : floatval(Bills::getFinalPenalty($bills)) + floatval($bills->NetAmount) }}</span>

    <span style="position: fixed; left: 85px; top: 654px;">{{ $account->AccountType . ' '}} {{ ' ' . ServiceAccounts::getAddress($account) }}</span>
    <span style="position: fixed; left: 462px; top: 654px;">{{ date('F Y', strtotime($bills->ServicePeriod)) }}</span>
    @php
        $totalArr = 0.0;
        if ($arrears != null) {
            foreach($arrears as $item) {
                $totalArr = $totalArr += floatval($item->NetAmount);
            }
        }
    @endphp
    <span style="position: fixed; right: 70px; top: 654px;">({{ $arrears != null ? count($arrears) : '0' }}) {{ number_format($totalArr, 2) }}</span>

    <span style="position: fixed; left: 86px; top: 667px;">{{ $bills->MeterNumber }}</span>
    <span style="position: fixed; left: 462px; top: 667px;">{{ date('F d, Y', strtotime($bills->BillingDate)) }}</span>
    <span style="position: fixed; right: 70px; top: 667px;">{{ date('F d, Y', strtotime($bills->DueDate)) }}</span>
</div>
<script type="text/javascript">
window.print();

window.setTimeout(function(){
    window.history.go(-1)
}, 800);
</script>