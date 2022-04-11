
@php
use App\Models\ServiceAccounts;
use App\Models\MemberConsumers;
use App\Models\Bills;
@endphp

<style>
@media print {
    @page {
        /* size: landscape !important; */
    }

    header {
        display: none;
    }

    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    }

    .left-indent {
        margin-left: 30px;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .print-area {        
        page-break-after: always;
    }

    .print-area:last-child {        
        page-break-after: auto;
    }
}  

html {
    margin: 10px !important;
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

<link rel="stylesheet" href="{{ URL::asset('adminlte.min.css') }}">

<div id="print-area" class="content">
    <div class="row">
        <div class="col-lg-12">
            
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

                       
        </div>
    </div>
</div>
<script type="text/javascript">
window.print();

window.setTimeout(function(){
    window.history.go(-1)
}, 800);
</script>