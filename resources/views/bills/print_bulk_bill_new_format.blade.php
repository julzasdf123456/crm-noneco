
@php
use App\Models\ServiceAccounts;
use App\Models\MemberConsumers;
use App\Models\Bills;
use Illuminate\Support\Facades\DB;
use App\Models\Rates;
use App\Models\BillingMeters;
@endphp

<link rel="stylesheet" href="{{ URL::asset('adminlte.min.css') }}">

@foreach ($bills as $item)
<style>
@media print {
    @page {
        /* size: landscape !important; */
    }

    header {
        display: none;
    }

    .left-indent {
        margin-left: 30px;
    }

    #print-area {        
        page-break-after: always;
    }

    #print-area:last-child {        
        page-break-after: auto;
    }
}  

html {
    margin: 10px !important;
}

.left-indent {
    margin-left: 50px;
}
</style>

    <div id="print-area" class="content">
        {{-- QUERY ALL DEPENDENCIES FIRST --}}
        @php
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
                ->where('ConsumerType', Rates::filterConsumerType($item->ConsumerType))
                ->first();
        @endphp
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
                        <th class="text-right">{{ $account->OldAccountNo }}</th>
                        <td class="left-pad">Prev. Reading</td>
                        <th class="text-right">{{ $item->PreviousKwh }}</th>
                        <td class="left-pad">Date From</td>
                        <th class="text-right">{{ date('F d, Y', strtotime($item->ServiceDateFrom)) }}</th>
                    </tr>
                    <tr>
                        <td>Consumer Name</td>
                        <th class="text-right">{{ $account->ServiceAccountName }}</th>
                        <td class="left-pad">Pres. Reading</td>
                        <th class="text-right">{{ $item->PresentKwh }}</th>
                        <td class="left-pad">Date To</td>
                        <th class="text-right">{{ date('F d, Y', strtotime($item->ServiceDateTo)) }}</th>
                    </tr>
                    <tr>
                        <td>Consumer Address</td>
                        <th class="text-right">{{ ServiceAccounts::getAddress($account) }}</th>
                        <td class="left-pad">Core Loss</td>
                        <th class="text-right">{{ $item->Coreloss }}</th>
                        <td class="left-pad">Due Date</td>
                        <th class="text-right">{{ date('F d, Y', strtotime($item->DueDate)) }}</th>
                    </tr>
                    <tr>
                        <td>Route/Area Code</td>
                        <th class="text-right">{{ $account->AreaCode }}</th>
                        <td class="left-pad">Demand</td>
                        <th class="text-right">{{ $item->DemandPresentKwh }}</th>
                        <td class="left-pad">Billing Month</td>
                        <th class="text-right">{{ date('F Y', strtotime($item->ServicePeriod)) }}</th>
                    </tr>
                    <tr>
                        <td>Meter Number</td>
                        <th class="text-right">{{ $meters != null ? $meters->SerialNumber : '' }}</th>
                        <td class="left-pad">Multiplier</td>
                        <th class="text-right">{{ $item->Multiplier }}</th>
                        <td class="left-pad">Bill Number</td>
                        <th class="text-right">{{ $item->BillNumber }}</th>
                    </tr>
                    <tr>
                        <td>Consumer Type</td>
                        <th class="text-right">{{ $item->ConsumerType }}</th>
                        <td class="left-pad">Form 2307</td>
                        <th class="text-right">{{ $item->Form2307Amount != null ? number_format($item->Form2307Amount, 4) : 'none' }}</th>
                        <td class="left-pad">Kwh Used</td>
                        <th class="text-right">{{ $item->KwhUsed }}</th>
                    </tr>
                </table>

                <div class="divider"></div>

                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <table class="table-borderless table-sm table-hover">
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
                                    <td class="text-right">{{ number_format($item->GenerationSystemCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Transmission Delivery Charge</td>
                                    <td class="indent-td">Per KW</td>
                                    <td class="text-right">{{ $rate->TransmissionDeliveryChargeKW }}</td>
                                    <td class="text-right">{{ number_format($item->TransmissionDeliveryChargeKW, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Transmission Delivery Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->TransmissionDeliveryChargeKWH }}</td>
                                    <td class="text-right">{{ number_format($item->TransmissionDeliveryChargeKWH, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">System Loss Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->SystemLossCharge }}</td>
                                    <td class="text-right">{{ number_format($item->SystemLossCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Other Generation Rate Adj. (OGA)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->OtherGenerationRateAdjustment }}</td>
                                    <td class="text-right">{{ number_format($item->OtherGenerationRateAdjustment, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Other Transmission Cost Adj. (OTCA)</td>
                                    <td class="indent-td">Per KW</td>
                                    <td class="text-right">{{ $rate->OtherTransmissionCostAdjustmentKW }}</td>
                                    <td class="text-right">{{ number_format($item->OtherTransmissionCostAdjustmentKW, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Other Transmission Cost Adj. (OTCA)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->OtherTransmissionCostAdjustmentKWH }}</td>
                                    <td class="text-right">{{ number_format($item->OtherTransmissionCostAdjustmentKWH, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Other System Loss Cost Adj. (OSLA)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->OtherSystemLossCostAdjustment }}</td>
                                    <td class="text-right">{{ number_format($item->OtherSystemLossCostAdjustment, 2) }}</td>
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
                                    <td class="text-right">{{ number_format($item->DistributionDemandCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Distribution System Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->DistributionSystemCharge }}</td>
                                    <td class="text-right">{{ number_format($item->DistributionSystemCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Supply Retail Customer Charge</td>
                                    <td class="indent-td">Per cust/mo</td>
                                    <td class="text-right">{{ $rate->SupplyRetailCustomerCharge }}</td>
                                    <td class="text-right">{{ number_format($item->SupplyRetailCustomerCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Supply System Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->SupplySystemCharge }}</td>
                                    <td class="text-right">{{ number_format($item->SupplySystemCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Metering Retail Customer Charge</td>
                                    <td class="indent-td">Per cust/mo</td>
                                    <td class="text-right">{{ $rate->MeteringRetailCustomerCharge }}</td>
                                    <td class="text-right">{{ number_format($item->MeteringRetailCustomerCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Metering System Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->MeteringSystemCharge }}</td>
                                    <td class="text-right">{{ number_format($item->MeteringSystemCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">RFSC</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->RFSC }}</td>
                                    <td class="text-right">{{ number_format($item->RFSC, 2) }}</td>
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
                                    <td class="text-right">{{ number_format($item->FranchiseTax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Business Tax</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->BusinessTax }}</td>
                                    <td class="text-right">{{ number_format($item->BusinessTax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Real Property Tax (RPT)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->RealPropertyTax }}</td>
                                    <td class="text-right">{{ number_format($item->RealPropertyTax, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <table class="table-borderless table-hover table-sm">
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
                                    <td class="text-right">{{ number_format($item->LifelineRate, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Inter-Class Cross Subsidy Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->InterClassCrossSubsidyCharge }}</td>
                                    <td class="text-right">{{ number_format($item->InterClassCrossSubsidyCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">PPA (Refund)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->PPARefund }}</td>
                                    <td class="text-right">{{ number_format($item->PPARefund, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Senior Citizen Subsidy</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->SeniorCitizenSubsidy }}</td>
                                    <td class="text-right">{{ number_format($item->SeniorCitizenSubsidy, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Other Lifeline Rate Cost Adj. (OLRA)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->OtherLifelineRateCostAdjustment }}</td>
                                    <td class="text-right">{{ number_format($item->OtherLifelineRateCostAdjustment, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">SC Discount & Subsidy Adj.</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->SeniorCitizenDiscountAndSubsidyAdjustment }}</td>
                                    <td class="text-right">{{ number_format($item->SeniorCitizenDiscountAndSubsidyAdjustment, 2) }}</td>
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
                                    <td class="text-right">{{ number_format($item->MissionaryElectrificationCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Environmental Charge</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->EnvironmentalCharge }}</td>
                                    <td class="text-right">{{ number_format($item->EnvironmentalCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Stranded Contract Costs</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->StrandedContractCosts }}</td>
                                    <td class="text-right">{{ number_format($item->StrandedContractCosts, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">NPC Stranded Debt</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->NPCStrandedDebt }}</td>
                                    <td class="text-right">{{ number_format($item->NPCStrandedDebt, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Feed-in Tariff Allowance (FIT-All)</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->FeedInTariffAllowance }}</td>
                                    <td class="text-right">{{ number_format($item->FeedInTariffAllowance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Missionary Electrification - REDCI</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->MissionaryElectrificationREDCI }}</td>
                                    <td class="text-right">{{ number_format($item->MissionaryElectrificationREDCI, 2) }}</td>
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
                                    <td class="text-right">{{ number_format($item->GenerationVAT, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Transmission</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->TransmissionVAT }}</td>
                                    <td class="text-right">{{ number_format($item->TransmissionVAT, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">System Loss</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->SystemLossVAT }}</td>
                                    <td class="text-right">{{ number_format($item->SystemLossVAT, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="indent-td">Distribution</td>
                                    <td class="indent-td">Per KWH</td>
                                    <td class="text-right">{{ $rate->DistributionVAT }}</td>
                                    <td class="text-right">{{ number_format($item->DistributionVAT, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="divider"></div>

                    <div class="col-lg-12">
                        <table class="table table-borderless table-sm">
                            <<tr>
                                <td>Additional Charges - Termed Payments</td>
                                <th class="text-right">+ {{ number_format($item->AdditionalCharges, 2) }}</th>
                                <td style="padding-left: 60px;">EWT 2%</td>
                                <th class="text-right">- {{ $item->Evat2Percent }}</th>
                            </tr>
                            <tr>
                                <td>Deposit/Pre-Payment Deductions</td>
                                <td class="text-right">- {{ number_format($item->DeductedDeposit, 2) }}</td>
                                <td style="padding-left: 60px;">EVAT 5%</td>
                                <th class="text-right">- {{ $item->Evat5Percent }}</th>
                            </tr>
                            <tr>                                                
                                <td>Other Deductions</td>
                                <th class="text-right">- {{ number_format($item->Deductions, 2) }}</th>
                            </tr>
                            <tr>
                                <td>Amount Due</td>
                                <td></td>
                                <td></td>
                                <th class="text-right"><h4><strong>₱ {{ number_format($item->NetAmount, 2) }}</strong></h4></th>
                            </tr>
                            @if (Bills::getAccountTypeByType($item->ConsumerType) != 'RESIDENTIAL')
                            <tr>
                                <td>Surcharge</td>
                                <td></td>
                                <td></td>
                                <th class="text-right">+ {{ number_format(Bills::getFinalPenalty($item), 2) }}</th>                                    
                            </tr>
                            <tr>
                                <td>Amount Due after Due Date</td>
                                <td></td>
                                <td></td>
                                <th class="text-right"><h4><strong>₱ {{ number_format(floatval(Bills::getFinalPenalty($item)) + floatval($item->NetAmount), 2) }}</strong></h4></th>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>            
            </div>
        </div>
    </div>
@endforeach

<script type="text/javascript">
window.print();

window.setTimeout(function(){
    window.history.go(-1)
}, 1000);
</script>