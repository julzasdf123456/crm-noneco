@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
    use App\Models\Readings;
    use App\Models\MemberConsumers;
@endphp
<style>
    @font-face {
        font-family: 'sax-mono';
        src: url('/fonts/saxmono.ttf');
    }
    html, body {
        font-family: sax-mono, Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
        /* font-family: sans-serif; */
        /* font-stretch: condensed; */
        font-size: .85em;
    }

    table tbody th,td,
    table thead th {
        /* font-family: sans-serif; */
        font-family: sax-mono, Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
        /* font-stretch: condensed; */
        /* , Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif; */
        font-size: .72em;
    }
    @media print {
        @page {
            /* margin: 10px; */
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

        p {
            padding: 0px !important;
            margin: 0px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }
    }  
    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    } 

    p {
        padding: 0px !important;
        margin: 0px;
    }

    .text-center {
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

</style>

<div>
    {{-- SUMMARY --}}
    <table style="page-break-before: always; width: 100%;">
        <thead>
            <tr>
                <th colspan="10" class="text-center">{{ strtoupper(env('APP_COMPANY')) }}</th>
            </tr>
            <tr>
                <th colspan="10" class="text-center">{{ strtoupper(env('APP_ADDRESS')) }}</th>
            </tr>
            <tr>
                <th colspan="10" class="text-center">SENIOR CITIZEN SUMMARY FOR BILLING MONTH {{ date('F Y', strtotime($period)) }}</th>
            </tr>
            <tr>
                <th colspan="10" class="text-left">TOWN/CITY: {{ $town }}</th>
            </tr>
            <tr>
                <!-- <th style="width: 25px;"></th> -->
                <th style="border-bottom: 1px solid #454455">Kwh Category</th>
                <th style="border-bottom: 1px solid #454455" class="text-left">Number of Consumers</th>
                <th style="border-bottom: 1px solid #454455" class="text-left">Total Kwh Consumed</th>
                <th style="border-bottom: 1px solid #454455" class="text-left">Total Discount</th>
                <th style="border-bottom: 1px solid #454455; padding-left: 5px !important;" class="text-left">Total Amount</th>
            </tr>            
        </thead>
        <tbody>
            @php
                $totalKwhUsed = 0;
                $totalCount = 0;
                $totalAmount = 0;
                $totalDsc = 0;
            @endphp
            @foreach ($summary as $item)
                <tr>
                    <td>{{ number_format($item->KwhUsed) }} kWh</td>
                    <td class="text-right">{{ $item->NoOfConsumers }}</td>
                    <td class="text-right">{{ number_format($item->TotalKwhUsed) }}</td>
                    <td class="text-right">{{ number_format($item->TotalDiscount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->TotalAmount, 2) }}</td>
                </tr>
                @php
                    $totalKwhUsed += floatval($item->TotalKwhUsed);
                    $totalCount += floatval($item->NoOfConsumers);
                    $totalAmount += floatval($item->TotalAmount);
                    $totalDsc += floatval($item->TotalDiscount);
                @endphp
            @endforeach
            <tr>
                <th  style="border-top: 1px solid #454455">TOTAL</th>
                <th  style="border-top: 1px solid #454455" class="text-right">{{ $totalCount }}</th>
                <th  style="border-top: 1px solid #454455" class="text-right">{{ number_format($totalKwhUsed) }}</th>
                <th  style="border-top: 1px solid #454455" class="text-right">{{ number_format($totalDsc, 2) }}</th>
                <th  style="border-top: 1px solid #454455" class="text-right">{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tbody>
    </table>

    {{-- DETAILS --}}
    <table style="page-break-before: always; width: 100%;">
        <thead>
            <tr>
                <th colspan="10" class="text-center">{{ strtoupper(env('APP_COMPANY')) }}</th>
            </tr>
            <tr>
                <th colspan="10" class="text-center">{{ strtoupper(env('APP_ADDRESS')) }}</th>
            </tr>
            <tr>
                <th colspan="10" class="text-center">SENIOR CIIZEN REPORT FOR BILLING MONTH {{ date('F Y', strtotime($period)) }}</th>
            </tr>
            <tr>
                <th colspan="10" class="text-left">TOWN/CITY: {{ $town }}</th>
            </tr>
            <tr>
                <!-- <th style="width: 25px;"></th> -->
                <th style="border-bottom: 1px solid #454455">Acct. #</th>
                <th style="border-bottom: 1px solid #454455">Consumer Name</th>
                {{-- <th style="border-bottom: 1px solid #454455" class="text-left">Acct.<br>Status</th> --}}
                <th style="border-bottom: 1px solid #454455; padding-left: 5px !important;" class="text-center">Kwh <br> Used</th>
                <th style="border-bottom: 1px solid #454455; padding-left: 5px !important;" class="text-center">Discount</th>
                <th style="border-bottom: 1px solid #454455; padding-left: 5px !important;" class="text-center">Net Amount</th>
            </tr>            
        </thead>
        <tbody>
            @php
                $totalKwhUsed = 0;
                $totalDiscount = 0;
                $totalAmount = 0;
            @endphp
            @foreach ($bills as $itemx)
                <tr>
                    <td>{{ $itemx->OldAccountNo }}</td>
                    <td>{{ $itemx->ServiceAccountName }}</td>
                    {{-- <td>{{ $itemx->AccountStatus != null ? $itemx->AccountStatus : '' }}</td> --}}
                    <td class="text-right">{{ number_format($itemx->KwhUsed) }}</td>
                    <td class="text-right">{{ number_format($itemx->SeniorCitizenSubsidy, 2) }}</td>
                    <td class="text-right">{{ number_format($itemx->NetAmount, 2) }}</td>
                </tr>
                @php
                    $totalKwhUsed += floatval($itemx->KwhUsed);
                    $totalDiscount += floatval($itemx->SeniorCitizenSubsidy);
                    $totalAmount += floatval($itemx->NetAmount);
                @endphp
            @endforeach
            <tr>
                <th style="border-top: 1px solid #454455">TOTAL</th>
                <th style="border-top: 1px solid #454455">{{ count($bills) }} CONSUMERS</th>
                {{-- <th style="border-top: 1px solid #454455"></th> --}}
                <th style="border-top: 1px solid #454455" class="text-right">{{ number_format($totalKwhUsed) }}</th>
                <th style="border-top: 1px solid #454455" class="text-right">{{ number_format($totalDiscount, 2) }}</th>
                <th style="border-top: 1px solid #454455" class="text-right">{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tbody>
    </table>
    
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.history.go(-1)
    }, 1600);
</script>