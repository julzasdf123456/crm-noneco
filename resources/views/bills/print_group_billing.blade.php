
@php
    use App\Models\ServiceAccounts;
    use App\Models\MemberConsumers;
    use App\Models\Bills;
@endphp

<style>
    @media print {
        @page {
            size: landscape !important;
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
    <br>
    <br>
    <h4 style="margin: 0px !important; padding: 0px !important;"><strong>{{ env('APP_COMPANY') }}</strong></h4>
    <p style="margin: 0px !important; padding: 0px !important;"><strong>{{ env('APP_ADDRESS') }}</strong></p>
    <br>
    <h4 style="margin: 0px !important; padding: 0px !important;" class="text-center"><strong>STATEMENT OF ACCOUNT</strong></h4>
    <br>
    <p><strong>LINK CODE:</strong> {{ $memberConsumer != null ? (MemberConsumers::serializeMemberName($memberConsumer)) : '' }}</p>
    <p style="margin: 0px !important; padding: 0px !important;"><strong>{{ $memberConsumer != null ? (MemberConsumers::serializeMemberName($memberConsumer)) : '' }}</strong></p>
    <p style="margin: 0px !important; padding: 0px !important;">{{ $memberConsumer != null ? (MemberConsumers::getAddress($memberConsumer)) : '' }}</p>
    <br>
    <p style="margin-top: 0px !important; padding-top: 0px !important; margin-bottom: 0px !important; padding-bottom: 0px !important;">Attention: <strong>THE MANAGER</strong></p>
    <br>
    <p style="margin-top: 0px !important; padding-top: 0px !important; margin-bottom: 0px !important; padding-bottom: 0px !important;">Dear Sir/Madame:</p>
    <p style="margin-top: 0px !important; padding-top: 0px !important; margin-bottom: 0px !important; padding-bottom: 0px !important;" class="left-indent">We would like to inform your office of the unpaid electricity bills of {{ $memberConsumer != null ? (MemberConsumers::serializeMemberName($memberConsumer)) : '' }} for the month of {{ date('F', strtotime($servicePeriod)) }}.</p>
    <br>
    <table class="table table-sm table-borderless">
        <thead style="border-top: 1px solid #878787; border-bottom: 1px solid #878787;">
            <th>Account No:</th>
            <th>Consumer Name</th>
            <th>Bill No</th>
            <th>Consumer Type</th>
            <th>From</th>
            <th>To</th>
            <th>Due Date</th>
            <th>Present</th>
            <th>Previous</th>
            <th class="text-right">Kwh Used</th>
            <th class="text-right">Power Bill</th>
            <th class="text-right">2% VAT</th>
            <th class="text-right">5% VAT</th>
            <th class="text-right">Surcharges</th>
            <th class="text-right">Total</th>
        </thead>
        <tbody>
            @php
                $powerBillTotal = 0;
                $total = 0;
                $has2Percent = false;
                $has5Percent = false;
                $surchargesTotal = 0;
                $evat2Total = 0;
                $evat5Total = 0;
            @endphp
            @foreach ($ledgers as $item)
                <tr>
                    <td>{{ $item->OldAccountNo }}</td>
                    <td>{{ $item->ServiceAccountName }}</td>
                    <td>{{ $item->BillNumber }}</td>
                    <td>{{ $item->ConsumerType }}</td>
                    <td>{{ date('m/d/Y', strtotime($item->ServiceDateFrom)) }}</td>
                    <td>{{ date('m/d/Y', strtotime($item->ServiceDateTo)) }}</td>
                    <td>{{ date('m/d/Y', strtotime($item->DueDate)) }}</td>
                    <td>{{ number_format($item->PresentKwh, 2) }}</td>
                    <td>{{ number_format($item->PreviousKwh, 2)  }}</td>
                    <td class="text-right">{{ number_format($item->KwhUsed, 2) }}</td>                                
                    <td class="text-right">{{ number_format((floatval($item->NetAmount) - (floatval($item->Evat2Percent) + floatval($item->Evat5Percent))), 2) }}</td>
                    <td class="text-right">{{ number_format($item->Evat2Percent, 2) }}</td>
                    <td class="text-right">{{ number_format($item->Evat5Percent, 2) }}</td>
                    <td class="text-right">{{ number_format(Bills::assessDueBillAndGetSurcharge($item), 2) }}</td>
                    <td class="text-right">{{ number_format(floatval($item->NetAmount) + floatval(Bills::assessDueBillAndGetSurcharge($item)), 2) }}</td>
                </tr>
                @php
                    $powerBillTotal += (floatval($item->NetAmount) - (floatval($item->Evat2Percent) + floatval($item->Evat5Percent)));
                    $surchargesTotal += floatval(Bills::assessDueBillAndGetSurcharge($item));
                    $evat2Total += floatval($item->Evat2Percent);
                    $evat5Total += floatval($item->Evat5Percent);
                    $total += (floatval($item->NetAmount) + floatval(Bills::assessDueBillAndGetSurcharge($item)));

                    if ($item->Evat2Percent != null) {
                        $has2Percent = true;
                    }

                    if ($item->Evat5Percent != null) {
                        $has5Percent = true;
                    }
                @endphp
            @endforeach
            {{-- TOTAL --}}
            <tr style="border-top: 1px solid #878787; border-bottom: 1px solid #878787;">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-right">{{ number_format($powerBillTotal, 2) }}</th>
                <th class="text-right">{{ number_format($evat2Total, 2) }}</th>
                <th class="text-right">{{ number_format($evat5Total, 2) }}</th>
                <th class="text-right">{{ number_format($surchargesTotal, 2) }}</th>
                <th class="text-right">{{ number_format($total, 2) }}</th>
            </tr>
        </tbody>
    </table>

    <div class="row">
        {{-- EVAT CONFIG --}}
        <div class="col-lg-9">
            <b></b>
            <p class="left-indent">You prompt action in this matter is highly appreciated.</p>
        </div>

        {{-- TOTAL --}}
        <div class="col-lg-3">
            <table class="table table-sm table-borderless">
                <tr>
                    <th class="text-right">Amount Due:</th>
                    <th class="text-right">{{ number_format($powerBillTotal + $evat2Total + $evat5Total, 2) }}</th>
                </tr>
                <tr style="border-bottom: 1px solid #898989;">
                    <th class="text-right">(Add 5% Surcharges):</th>
                    <th class="text-right">{{ number_format($surchargesTotal, 2) }}</th>
                </tr>
                <tr>
                    <th class="text-right"><h4>Total Amount After Due:</h4></th>
                    <th class="text-right"><h4><strong>{{ number_format($total, 2) }}</strong></h4></th>
                </tr>
            </table>
        </div>  
        
        {{-- SIGNATORIES --}}
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4">
                    <p class="left-indent">Prepared By:</p>
                    <br>
                    <p class="text-center"><strong>TEENA CASANOVA</strong></p>
                    <p class="text-center">Cash Officer</p>
                </div>

                <div class="col-lg-4">
                    <p class="left-indent">Checked By:</p>
                    <br>
                    <p class="text-center"><strong>-</strong></p>
                    <p class="text-center">Collection Supervisor</p>
                </div>

                <div class="col-lg-4">
                    <p class="left-indent">Approved By:</p>
                    <br>
                    <p class="text-center"><strong>ELREEN JANE ZERRUDO</strong></p>
                    <p class="text-center">FSD Manager</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.history.go(-1)
    }, 800);
</script>