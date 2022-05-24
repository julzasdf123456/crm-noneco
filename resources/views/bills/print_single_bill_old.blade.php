
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

#print-area, td {
    font-size: .8em !important;
}

span, p {
    font-size: 1.3em !important;
}

</style>

<link rel="stylesheet" href="{{ URL::asset('adminlte.min.css') }}">

<div id="print-area" class="content">
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-sm table-borderless" style="margin-top: 124px; margin-left: 70px;">
                <tr>
                    <td style="padding-left: 50px">{{ date('F d, Y', strtotime($bills->ServicePeriod)) }}</td>
                    <td style="padding-left: 50px">{{ date('F d, Y', strtotime($bills->BillingDate)) }}</td>
                </tr>
            </table>

            @php
                $acctType = ServiceConnectionAccountTypes::where('AccountType', $bills->ConsumerType)->first();
            @endphp
            
            <table class="table table-sm table-borderless" style="margin-top: 8px; margin-left: 2px;">
                <tr>
                    <td style="padding-left: 30px" width="25%">{{ $account->OldAccountNo }}</td>
                    <td style="padding-left: 30px" width="25%">{{ $bills->MeterNumber }}</td>
                    <td width="10%">{{ $acctType != null ? $acctType->Alias : '-' }}</td>
                    <td width="20%">{{ $bills->Multiplier }}</td>
                    <td width="20%">{{ $bills->DueDate }}</td>
                </tr>                
            </table>

            <table class="table table-sm table-borderless" style="margin-top: -16px; margin-left: 2px;">
                <tr>
                    <td style="padding-left: 70px">{{ $account->ServiceAccountName }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 70px">{{ ServiceAccounts::getAddress($account) }}</td>
                </tr>
            </table>

            <div style="margin-left: 65%; margin-top: 53px;">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>{{ $rate->GenerationSystemCharge }}</td>
                        <td>{{ $bills->GenerationSystemCharge }}</td>
                    </tr>
                    <tr style="opacity: 0;">
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr style="opacity: 0;">
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr style="opacity: 0;">
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr style="opacity: 0;">
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>{{ $rate->TransmissionDeliveryChargeKW }}</td>
                        <td>{{ $bills->TransmissionDeliveryChargeKW }}</td>
                    </tr>
                    <tr>
                        <td>{{ $rate->TransmissionDeliveryChargeKW }}</td>
                        <td>{{ $bills->TransmissionDeliveryChargeKW }}</td>
                    </tr>
                </table>
            </div>
        </div>
        {{-- <div class="col-lg-12">
            
            <table class="table table-borderless table-sm">
                <tr>
                    <td>Account Number</td>
                    <th class="text-right">{{ $bills->AccountNumber }}</th>
                    <td class="left-pad">Prev. Reading</td>
                    <th class="text-right">{{ $bills->PreviousKwh }}</th>
                    <td class="left-pad">Date From</td>
                    <th class="text-right">{{ date('F d, Y', strtotime($bills->ServiceDateFrom)) }}</th>
                </tr>
                <tr>
                    <td>Consumer Name</td>
                    <th class="text-right">{{ $account->ServiceAccountName }}</th>
                    <td class="left-pad">Pres. Reading</td>
                    <th class="text-right">{{ $bills->PresentKwh }}</th>
                    <td class="left-pad">Date To</td>
                    <th class="text-right">{{ date('F d, Y', strtotime($bills->ServiceDateTo)) }}</th>
                </tr>
                <tr>
                    <td>Consumer Address</td>
                    <th class="text-right">{{ ServiceAccounts::getAddress($account) }}</th>
                    <td class="left-pad">Core Loss</td>
                    <th class="text-right">{{ $bills->Coreloss }}</th>
                    <td class="left-pad">Due Date</td>
                    <th class="text-right">{{ date('F d, Y', strtotime($bills->DueDate)) }}</th>
                </tr>
                <tr>
                    <td>Route/Area Code</td>
                    <th class="text-right">{{ $account->AreaCode }}</th>
                    <td class="left-pad">Demand</td>
                    <th class="text-right">{{ $bills->DemandPresentKwh }}</th>
                    <td class="left-pad">Billing Month</td>
                    <th class="text-right">{{ date('F Y', strtotime($bills->ServicePeriod)) }}</th>
                </tr>
                <tr>
                    <td>Meter Number</td>
                    <th class="text-right">{{ $meters != null ? $meters->SerialNumber : '' }}</th>
                    <td class="left-pad">Multiplier</td>
                    <th class="text-right">{{ $bills->Multiplier }}</th>
                    <td class="left-pad">Bill Number</td>
                    <th class="text-right">{{ $bills->BillNumber }}</th>
                </tr>
                <tr>
                    <td>Consumer Type</td>
                    <th class="text-right">{{ $bills->ConsumerType }}</th>
                    <td class="left-pad">Form 2307</td>
                    <th class="text-right">{{ $bills->Form2307Amount != null ? number_format($bills->Form2307Amount, 4) : 'none' }}</th>
                    <td class="left-pad">Kwh Used</td>
                    <th class="text-right">{{ $bills->KwhUsed }}</th>
                </tr>
            </table>

            <div class="divider"></div>

                       
        </div> --}}
    </div>
</div>
{{-- <script type="text/javascript">
window.print();

window.setTimeout(function(){
    window.history.go(-1)
}, 800);
</script> --}}