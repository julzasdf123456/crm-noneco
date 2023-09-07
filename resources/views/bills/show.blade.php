@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
    use Illuminate\Support\Facades\Auth;
@endphp

@extends('layouts.app')

@push('page_css')
    <style>
        p {
            margin: 0px !important;
            padding: 0px !important;
        }

        .table-sm tr td,
        .table-sm tr th {
            margin-top: 0px;
            margin-bottom: 0px;
            padding-top: 0px;
            padding-bottom: 0px;
        }

        .left-pad {
            padding-left: 60px !important;
        }

    </style>
@endpush

@section('content')
    <br>
    <div class="content px-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-none">
                    <div class="card-header border-0">
                        <div class="card-tools">
                            @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Data Administrator'])) 
                                @if ($paidBill != null)
                                    <span class="badge bg-success">Paid - OR No: {{ $paidBill->ORNumber }}</span>
                                @else
                                    @if ($bills->IsUnlockedForPayment == 'CLOSED')
                                        <span class="badge bg-success">CLOSED</span>
                                    @else
                                        
                                    @endif  
                                    @if ($account->NetMetered=='Yes')
                                        <a href="{{ route('bills.adjust-bill-net-metering', [$bills->id]) }}" class="btn btn-link" title="Adjust Reading"><i class="fas fa-pen"></i></a>                                  
                                    @else
                                        <a href="{{ route('bills.adjust-bill', [$bills->id]) }}" class="btn btn-link" title="Adjust Reading"><i class="fas fa-pen"></i></a>                                  
                                    @endif  
                                    
                                @endif                                
                            @endif
                            @if ($account->NetMetered=='Yes')
                                <a href="{{ route('bills.print-single-net-metering', [$bills->id]) }}" class="btn btn-link" title="Print Net Metering Bill"><i class="fas fa-print"></i></a>
                            @else
                                <a href="{{ route('bills.print-single-bill-new-format', [$bills->id]) }}" class="btn btn-link" title="Print New Formatted Bill"><i class="fas fa-print"></i></a>
                            @endif                            
                            <a href="{{ route('bills.print-single-bill-old', [$bills->id]) }}" class="btn btn-link text-warning" title="Print Pre-Formatted Bill (Old)"><i class="fas fa-print"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="text-center"><strong>{{ env('APP_COMPANY') }}</strong></p>
                                <p class="text-center">{{ env('APP_ADDRESS') }}</p>

                                <br>

                                <h4 class="text-center">STATEMENT OF ACCOUNT</h4>

                                <br>

                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td>Account Number</td>
                                        <th class="text-right"><a href="{{ route('serviceAccounts.show', [$account->id]) }}">{{ $account->OldAccountNo != null ? $account->OldAccountNo : '-' }} ({{ $bills->AccountNumber }})</a></th>
                                        <td class="left-pad">{{ $account->NetMetered=='Yes' ? 'Prev. Import' : 'Prev. Reading' }}</td>
                                        <th class="text-right">{{ $bills->PreviousKwh }}</th>
                                        <td class="left-pad">Date From</td>
                                        <th class="text-right">{{ date('F d, Y', strtotime($bills->ServiceDateFrom)) }}</th>
                                    </tr>
                                    <tr>
                                        <td>Consumer Name</td>
                                        <th class="text-right">{{ $account->ServiceAccountName }}</th>
                                        <td class="left-pad">{{ $account->NetMetered=='Yes' ? 'Pres. Import' : 'Pres. Reading' }}</td>
                                        <th class="text-right">{{ $bills->PresentKwh }}</th>
                                        <td class="left-pad">Date To</td>
                                        <th class="text-right">{{ date('F d, Y', strtotime($bills->ServiceDateTo)) }}</th>
                                    </tr>
                                    <tr>
                                        <td>Consumer Address</td>
                                        <th class="text-right">{{ ServiceAccounts::getAddress($account) }}</th>
                                        <td class="left-pad">Prev. Export</td>
                                        <th class="text-right">{{ is_numeric($bills->SolarExportPrevious) ? round(floatval($bills->SolarExportPrevious), 2) : $bills->SolarExportPrevious }}</th>
                                        <td class="left-pad text-danger">Due Date</td>
                                        <th class="text-right text-danger">{{ date('F d, Y', strtotime($bills->DueDate)) }}</th>
                                    </tr>
                                    <tr>
                                        <td>Route/Area Code</td>
                                        <th class="text-right">{{ $account->AreaCode }}</th>
                                        <td class="left-pad">Pres. Export</td>
                                        <th class="text-right">{{ is_numeric($bills->SolarExportPresent) ? round(floatval($bills->SolarExportPresent), 2) : $bills->SolarExportPresent }}</th>
                                        <td class="left-pad">Billing Month</td>
                                        <th class="text-right">{{ date('F Y', strtotime($bills->ServicePeriod)) }}</th>
                                    </tr>
                                    <tr>
                                        <td>Meter Number</td>
                                        <th class="text-right">{{ $meters != null ? $meters->SerialNumber : '' }}</th>
                                        <td class="left-pad">Rate</td>
                                        <th class="text-right">{{ $rate != null ? number_format($rate->TotalRateVATIncluded, 4) : 'none' }}</th>
                                        <td class="left-pad">Bill Number</td>
                                        <th class="text-right">{{ $bills->BillNumber }}</th>
                                    </tr>
                                    <tr>
                                        <td>Consumer Type</td>
                                        <th class="text-right">{{ $bills->ConsumerType }} {{ $account->NetMetered=='Yes' ? '(Net Metered)' : '' }}</th>
                                        <td class="left-pad">Demand</td>
                                        <th class="text-right">{{ $bills->DemandPresentKwh }}</th>
                                        <td class="left-pad">Kwh Used</td>
                                        <th class="text-right">{{ is_numeric($bills->KwhUsed) ? round(floatval($bills->KwhUsed), 2) : $bills->KwhUsed }}</th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <th class="text-right"></th>
                                        <td class="left-pad">Multiplier</td>
                                        <th class="text-right">{{ $bills->Multiplier }}</th>
                                        <td class="left-pad">Export Kwh Used</td>
                                        <th class="text-right">{{ is_numeric($bills->SolarExportKwh) ? round(floatval($bills->SolarExportKwh), 2) : $bills->SolarExportKwh }}</th>
                                    </tr>
                                </table>

                                <div class="divider"></div>

                                <div class="row">
                                    {{-- COLUMN 1 --}}
                                    <div class="col-lg-6 col-md-12">
                                        <table class="table-borderless table-sm table-hover" style="width: 100%;">
                                            <thead>
                                                <th>CHARGES</th>
                                                <th></th>
                                                <th class="left-pad">RATE</th>
                                                <th class="left-pad">AMOUNT</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>Generation and Transmission Charges</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>                                                    
                                                    <td class="indent-td">Generation System</td>
                                                    <td class="indent-td">Per KW</td>
                                                    <td class="text-right">{{ $rate->GenerationSystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->GenerationSystemCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Transmission Delivery Charge</td>
                                                    <td class="indent-td">Per KW</td>
                                                    <td class="text-right">{{ $rate->TransmissionDeliveryChargeKW }}</td>
                                                    <td class="text-right">{{ number_format($bills->TransmissionDeliveryChargeKW, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Transmission Delivery Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->TransmissionDeliveryChargeKWH }}</td>
                                                    <td class="text-right">{{ number_format($bills->TransmissionDeliveryChargeKWH, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">System Loss Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->SystemLossCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SystemLossCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Other Generation Rate Adj. (OGA)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->OtherGenerationRateAdjustment }}</td>
                                                    <td class="text-right">{{ number_format($bills->OtherGenerationRateAdjustment, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Other Transmission Cost Adj. (OTCA)</td>
                                                    <td class="indent-td">Per KW</td>
                                                    <td class="text-right">{{ $rate->OtherTransmissionCostAdjustmentKW }}</td>
                                                    <td class="text-right">{{ number_format($bills->OtherTransmissionCostAdjustmentKW, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Other Transmission Cost Adj. (OTCA)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->OtherTransmissionCostAdjustmentKWH }}</td>
                                                    <td class="text-right">{{ number_format($bills->OtherTransmissionCostAdjustmentKWH, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Other System Loss Cost Adj. (OSLA)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->OtherSystemLossCostAdjustment }}</td>
                                                    <td class="text-right">{{ number_format($bills->OtherSystemLossCostAdjustment, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Distribution, Metering, & Supply Charges</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Distribution Demand  Charge</td>
                                                    <td class="indent-td">Per KW</td>
                                                    <td class="text-right">{{ $rate->DistributionDemandCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->DistributionDemandCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Distribution System Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->DistributionSystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->DistributionSystemCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Supply Retail Customer Charge</td>
                                                    <td class="indent-td">Per cust/mo</td>
                                                    <td class="text-right">{{ $rate->SupplyRetailCustomerCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SupplyRetailCustomerCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Supply System Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->SupplySystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SupplySystemCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Metering Retail Customer Charge</td>
                                                    <td class="indent-td">Per cust/mo</td>
                                                    <td class="text-right">{{ $rate->MeteringRetailCustomerCharge }}</td>
                                                    <td class="text-right">{{ $bills->MeteringRetailCustomerCharge }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Metering System Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->MeteringSystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->MeteringSystemCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">RFSC</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->RFSC }}</td>
                                                    <td class="text-right">{{ number_format($bills->RFSC, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Pass Through Taxes</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Franchise Tax</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->FranchiseTax }}</td>
                                                    <td class="text-right">{{ number_format($bills->FranchiseTax, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Business Tax</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->BusinessTax }}</td>
                                                    <td class="text-right">{{ number_format($bills->BusinessTax, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Real Property Tax (RPT)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->RealPropertyTax }}</td>
                                                    <td class="text-right">{{ number_format($bills->RealPropertyTax, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- COLUMN 2 --}}
                                    <div class="col-lg-6 col-md-12">
                                        <table class="table-borderless table-hover table-sm" style="width: 100%;">
                                            <thead>
                                                <th>CHARGES</th>
                                                <th></th>
                                                <th class="left-pad">RATE</th>
                                                <th class="left-pad">AMOUNT</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>Other Charges</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Lifeline Rate (Discount/Subsidy)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->LifelineRate }}</td>
                                                    <td class="text-right">{{ number_format($bills->LifelineRate, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Inter-Class Cross Subsidy Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->InterClassCrossSubsidyCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->InterClassCrossSubsidyCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">PPA (Refund)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->PPARefund }}</td>
                                                    <td class="text-right">{{ number_format($bills->PPARefund, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Senior Citizen Subsidy</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->SeniorCitizenSubsidy }}</td>
                                                    <td class="text-right">{{ number_format($bills->SeniorCitizenSubsidy, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Other Lifeline Rate Cost Adj. (OLRA)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->OtherLifelineRateCostAdjustment }}</td>
                                                    <td class="text-right">{{ number_format($bills->OtherLifelineRateCostAdjustment, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">SC Discount & Subsidy Adj.</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->SeniorCitizenDiscountAndSubsidyAdjustment }}</td>
                                                    <td class="text-right">{{ number_format($bills->SeniorCitizenDiscountAndSubsidyAdjustment, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Universal Charges</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Missionary Electrification Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->MissionaryElectrificationCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->MissionaryElectrificationCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Environmental Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->EnvironmentalCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->EnvironmentalCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Stranded Contract Costs</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->StrandedContractCosts }}</td>
                                                    <td class="text-right">{{ number_format($bills->StrandedContractCosts, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">NPC Stranded Debt</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->NPCStrandedDebt }}</td>
                                                    <td class="text-right">{{ number_format($bills->NPCStrandedDebt, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Feed-in Tariff Allowance (FIT-All)</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->FeedInTariffAllowance }}</td>
                                                    <td class="text-right">{{ number_format($bills->FeedInTariffAllowance, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Missionary Electrification - REDCI</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->MissionaryElectrificationREDCI }}</td>
                                                    <td class="text-right">{{ number_format($bills->MissionaryElectrificationREDCI, 2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th>VAT Charges</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Generation</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->GenerationVAT }}</td>
                                                    <td class="text-right">{{ number_format($bills->GenerationVAT, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Transmission</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->TransmissionVAT }}</td>
                                                    <td class="text-right">{{ number_format($bills->TransmissionVAT, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">System Loss</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->SystemLossVAT }}</td>
                                                    <td class="text-right">{{ number_format($bills->SystemLossVAT, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Distribution</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->DistributionVAT }}</td>
                                                    <td class="text-right">{{ number_format($bills->DistributionVAT, 2) }}</td>
                                                </tr>

                                                @if ($account->NetMetered=='Yes')
                                                <tr>
                                                    <th>Net Metering Charges</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Distribution Demand  Charge</td>
                                                    <td class="indent-td">Per KW</td>
                                                    <td class="text-right">{{ $rate->DistributionDemandCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SolarDemandChargeKW, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Distribution System Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->DistributionSystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SolarDemandChargeKWH, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Supply Retail Customer Charge</td>
                                                    <td class="indent-td">Per cust/mo</td>
                                                    <td class="text-right">{{ $rate->SupplyRetailCustomerCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SolarRetailCustomerCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Supply System Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->SupplySystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SolarSupplySystemCharge, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Metering Retail Customer Charge</td>
                                                    <td class="indent-td">Per cust/mo</td>
                                                    <td class="text-right">{{ $rate->MeteringRetailCustomerCharge }}</td>
                                                    <td class="text-right">{{ $bills->SolarMeteringRetailCharge }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="indent-td">Metering System Charge</td>
                                                    <td class="indent-td">Per KWH</td>
                                                    <td class="text-right">{{ $rate->MeteringSystemCharge }}</td>
                                                    <td class="text-right">{{ number_format($bills->SolarMeteringSystemCharge, 2) }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    @if ($account->NetMetered=='Yes')
                                        <div class="divider"></div>

                                        <div class="col-lg-6 col-md-12">
                                            <table class="table-borderless table-hover table-sm" style="width: 100%;">    
                                                <thead>
                                                    <th colspan="4">CUSTOMER CHARGES TO DISTRIBUTION UTILITY</th>
                                                </thead>                                           
                                                <tbody>
                                                    <tr>                                                    
                                                        <td class="indent-td">Generation System</td>
                                                        <td class="indent-td">Per KWH</td>
                                                        <td class="text-right">{{ $rate->GenerationSystemCharge }}</td>
                                                        <td class="text-right">{{ number_format($bills->GenerationChargeSolarExport, 2) }}</td>
                                                    </tr>
                                                    <tr>                                                    
                                                        <td class="indent-td">Residual Credit Earned in Prior Months</td>
                                                        <td class="indent-td"></td>
                                                        <td class="text-right">
                                                            @if (Auth::id()=='1')
                                                                <a href="{{ route('bills.remove-residual-credit', [$bills->id]) }}" class="btn btn-xs btn-danger">Remove Res. Credit</a>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">{{ number_format(floatval($bills->Item4) - floatval($bills->GenerationChargeSolarExport), 2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-lg-6 col-md-12">
                                            <table class="table-borderless table-hover table-sm" style="width: 100%;">                                         
                                                <tbody>
                                                    <tr>                                                    
                                                        <th class="indent-td">CURRENT AMOUNT DU TO CUSTOMER</th>
                                                        <td class="indent-td"></td>
                                                        <td class="text-right"></td>
                                                        <td class="text-right">{{ number_format($bills->Item1, 2) }}</td>
                                                    </tr>
                                                </tbody>
                                                <tbody>
                                                    <tr>                                                    
                                                        <th class="indent-td">CURRENT AMOUNT CUSTOMER TO DU</th>
                                                        <td class="indent-td"></td>
                                                        <td class="text-right"></td>
                                                        <td class="text-right">{{ number_format($bills->Item4, 2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <div class="divider"></div>

                                    <div class="col-lg-12">
                                        <table class="table table-borderless table-sm table-hover" style="width: 100%;">
                                            <tr>
                                                <td>Additional Charges - Termed Payments</td>
                                                <th class="text-right">+ {{ number_format($bills->AdditionalCharges, 2) }}</th>
                                                <td style="padding-left: 60px;">EWT 2%</td>
                                                <th class="text-right">- {{ $bills->Evat2Percent }}</th>
                                            </tr>
                                            <tr>
                                                <td>Deposit/Pre-Payment Deductions</td>
                                                <td class="text-right">- {{ number_format($bills->DeductedDeposit, 2) }}</td>
                                                <td style="padding-left: 60px;">EVAT 5%</td>
                                                <th class="text-right">- {{ $bills->Evat5Percent }}</th>
                                            </tr>
                                            <tr>                                                
                                                <td>Other Deductions</td>
                                                <th class="text-right">- {{ number_format($bills->Deductions, 2) }}</th>
                                                <td style="padding-left: 60px;">Katas Ng VAT</td>
                                                <th class="text-right">- {{ number_format($bills->KatasNgVat, 2) }}</th>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td style="padding-left: 60px;">Penalty Charges after Due Date</td>
                                                <th class="text-right">₱ {{ number_format(Bills::assessDueBillAndGetSurcharge($bills), 2) }}</th>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td style="padding-left: 60px;" class="text-primary">Amount Due Before Due Date</td>
                                                <th class="text-right text-primary"><h4><strong>₱ {{ number_format($bills->NetAmount, 2) }}</strong></h4></th>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td style="padding-left: 60px;" class="text-danger">Amount Due After Due Date</td>
                                                <th class="text-right text-danger"><h4><strong>₱ {{ number_format(floatval(Bills::assessDueBillAndGetSurcharge($bills)) + floatval($bills->NetAmount), 2) }}</strong></h4></th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="divider"></div>
                                <p class="text-muted"><strong>Remarks</strong></p>
                                <p>{{ $bills->Notes }}</p>
                                
                                <p class="text-muted"><strong>Billing Date</strong></p>
                                <p>{{ date('F d, Y', strtotime($bills->BillingDate)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
