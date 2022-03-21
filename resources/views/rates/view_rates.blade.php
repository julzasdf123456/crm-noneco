@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Rates for {{ date('F Y', strtotime($servicePeriod)) }}</h4>
                </div>
                <div class="col-sm-6">
                    {!! Form::open(['route' => ['rates.delete-rates', $servicePeriod], 'method' => 'post']) !!}
                    {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger float-right', 'onclick' => "return confirm('Are you sure?')", 'title' => 'Delete this rate']) !!}
                    {!! Form::close() !!}

                    <a class="btn btn-primary float-right" style="margin-right: 10px;"
                       href="{{ route('rates.upload-rate') }}" title="Upload New Rate">
                        <i class="fas fa-file-upload"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            @foreach ($categories as $key => $category)
                                <li class="nav-item"><a class="nav-link {{ $key==0 ? 'active' : '' }}" href="#tab{{ $key }}" data-toggle="tab">
                                    {{ $category->RateFor }}</a></li>
                            @endforeach                            
                        </ul>
                    </div>
                    {{-- {{ dd($rates) }} --}}
                    <div class="card-body">
                        <div class="tab-content">
                            @foreach ($categories as $key => $categoryValue)
                                <div class="tab-pane {{ $key==0 ? 'active' : '' }}" id="tab{{ $key }}">                           
                                    <table class="table table-hover table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="30%"></th>
                                                <th class="text-center">RESIDENTIAL</th>
                                                <th class="text-center" colspan="5">LOW VOLTAGE</th>
                                                <th class="text-center" colspan="3">HIGHER VOLTAGE</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <th class="text-center">{{ $item->ConsumerType }}</th>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>GENERATION AND TRANSMISSION CHARGES:</th>
                                            </tr>
                                            <tr>
                                                <td>Generation System</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->GenerationSystemCharge==null ? '' : number_format($item->GenerationSystemCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Transmission Delivery Charge (kW)</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->TransmissionDeliveryChargeKW==null ? '' : number_format($item->TransmissionDeliveryChargeKW, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Transmission Delivery Charge (kWH)</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->TransmissionDeliveryChargeKWH==null ? '' : number_format($item->TransmissionDeliveryChargeKWH, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>System Loss Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->SystemLossCharge==null ? '' : number_format($item->SystemLossCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Other Generation Rate Adjustment (OGA) (KWH)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->OtherGenerationRateAdjustment==null ? '' : number_format($item->OtherGenerationRateAdjustment, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Other Transmission Cost Adjustment (OTCA) (KW)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->OtherTransmissionCostAdjustmentKW==null ? '' : number_format($item->OtherTransmissionCostAdjustmentKW, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Other Transmission Cost Adjustment (OTCA) (KWH)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->OtherTransmissionCostAdjustmentKWH==null ? '' : number_format($item->OtherTransmissionCostAdjustmentKWH, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Other System Loss Cost Adjustment (OSLA) (KWH)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->OtherSystemLossCostAdjustment==null ? '' : number_format($item->OtherSystemLossCostAdjustment, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>DISTRIBUTION/SUPPLY/METERING CHARGES:</th>
                                            </tr>
                                            <tr>
                                                <td>Distribution Demand Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->DistributionDemandCharge==null ? '' : number_format($item->DistributionDemandCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Distribution System Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->DistributionSystemCharge==null ? '' : number_format($item->DistributionSystemCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Supply Retail Customer Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->SupplyRetailCustomerCharge==null ? '' : number_format($item->SupplyRetailCustomerCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Supply System Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->SupplySystemCharge==null ? '' : number_format($item->SupplySystemCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Metering Retail Customer Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->MeteringRetailCustomerCharge==null ? '' : number_format($item->MeteringRetailCustomerCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Metering System Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->MeteringSystemCharge==null ? '' : number_format($item->MeteringSystemCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>RFSC</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->RFSC==null ? '' : number_format($item->RFSC, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>OTHERS:</th>
                                            </tr>
                                            <tr>
                                                <td>Lifeline Rate (Discount/Subsidy)</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->LifelineRate==null ? '' : number_format($item->LifelineRate, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Inter-Class Cross Subsidy Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->InterClassCrossSubsidyCharge==null ? '' : number_format($item->InterClassCrossSubsidyCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>PPA (Refund)</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->PPARefund==null ? '' : number_format($item->PPARefund, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Senior Citizen Subsidy</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->SeniorCitizenSubsidy==null ? '' : number_format($item->SeniorCitizenSubsidy, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Other Lifeline Rate Cost Adjustment (OLRA) (KWH)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->OtherLifelineRateCostAdjustment==null ? '' : number_format($item->OtherLifelineRateCostAdjustment, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Senior Citizen Discount & Subsidy Adjustment (KWH)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->SeniorCitizenDiscountAndSubsidyAdjustment==null ? '' : number_format($item->SeniorCitizenDiscountAndSubsidyAdjustment, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>UNIVERSAL CHARGE:</th>
                                            </tr>
                                            <tr>
                                                <td>Missionary Electrification Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->MissionaryElectrificationCharge==null ? '' : number_format($item->MissionaryElectrificationCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Environmental Charge</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->EnvironmentalCharge==null ? '' : number_format($item->EnvironmentalCharge, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Stranded Contract Costs</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->StrandedContractCosts==null ? '' : number_format($item->StrandedContractCosts, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>NPC Stranded Debt</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->NPCStrandedDebt==null ? '' : number_format($item->NPCStrandedDebt, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Feed-inTariff Allowance</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->FeedInTariffAllowance==null ? '' : number_format($item->FeedInTariffAllowance, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Missionary Electrification - REDCI</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->MissionaryElectrificationREDCI==null ? '' : number_format($item->MissionaryElectrificationREDCI, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>TOTAL RATE PER KWH (VAT NOT INCLUDED)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <th class="text-right">{{ $item->TotalRateVATExcluded==null ? '' : number_format($item->TotalRateVATExcluded, 4) }}</th>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>TOTAL RATE PER KWH (VAT NOT INCLUDED, WITH ADJ.)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <th class="text-right">{{ $item->TotalRateVATExcludedWithAdjustments==null ? '' : number_format($item->TotalRateVATExcludedWithAdjustments, 4) }}</th>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>VAT Rate: Generation</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->GenerationVAT==null ? '' : number_format($item->GenerationVAT, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>VAT Rate: Transmission</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->TransmissionVAT==null ? '' : number_format($item->TransmissionVAT, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>VAT Rate: System Loss</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->SystemLossVAT==null ? '' : number_format($item->SystemLossVAT, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>VAT Rate: Distribution & Others</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->DistributionVAT==null ? '' : number_format($item->DistributionVAT, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Franchise Tax</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->FranchiseTax==null ? '' : number_format($item->FranchiseTax, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Business Tax</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->BusinessTax==null ? '' : number_format($item->BusinessTax, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>Real Property Tax (RPT)</td>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <td class="text-right">{{ $item->RealPropertyTax==null ? '' : number_format($item->RealPropertyTax, 4) }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>TOTAL RATE PER KWH (WITH ALL INCLUSIONS)</th>
                                                @foreach ($rates as $item)
                                                    @if ($item->RateFor == $categoryValue->RateFor)
                                                        <th class="text-right">{{ $item->TotalRateVATIncluded==null ? '' : number_format($item->TotalRateVATIncluded, 4) }}</th>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table> 
                                </div>
                            @endforeach                             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection