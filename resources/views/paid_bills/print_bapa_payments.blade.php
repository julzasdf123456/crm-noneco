@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
    use App\Models\User;
@endphp
<style>
    html, body {
        font-family: sans-serif;
        font-size: .82em;
    }

    table tbody th,td,
    table thead th {
        font-family: sans-serif;
        font-size: .7em;
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

        #print-area {
            page-break-before: always;
        }

        #print-area {
            page-break-after: auto;
        }
    }  
    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    } 

</style>


{{-- <link rel="stylesheet" href="{{ URL::asset('adminlte.min.css') }}"> --}}
@foreach ($paidBills as $item)
<div id="print-area" class="content">
    <br>
    <br>
    <br>
    <br>
    <br>
    <span style="margin-left: 4px;">Meter #: {{ $item->MeterNumber != null ? $meter->MeterNumber : '-' }}</span>
    <span style="margin-left: 170px;">{{ $item != null ? $item->ORNumber : '-' }}</span><br>
    <span style="margin-left: 4px;">{{ $item->OldAccountNo }}</span>
    <span style="margin-left: 20px;">{{ $item->ServiceAccountName }}</span>
    <span style="margin-left: 20px;">{{ $item->AccountType }}</span><br>
    <span style="margin-left: 4px;">{{ ServiceAccounts::getAddress($item) }}</span>
    <span style="margin-left: 20px;">{{ $item->AccountStatus }}</span><br>
    
    <br>
    <span style="float: right; margin-right: 90px; margin-top: 10px;">{{ $item->created_at != null ? date('h:i:s A m/d/Y', strtotime($item->created_at)) : '-' }}</span><br>
    <br>
    @php
        $total = 0.0;
    @endphp
    <table style="margin-top: 38px;">
        <tbody>     
            @php
                // GET BILLS
                $bill = Bills::where('ServicePeriod', $item->ServicePeriod)
                    ->where('AccountNumber', $item->AccountNumber)
                    ->first();
                $total = $total + floatval($item->NetAmount);

                $user = User::find($item->Teller);
            @endphp
            <tr>
                <td>{{ $bill != null ? $bill->BillNumber : $item->ObjectSourceId }}</td>
                <td style="padding-left: 4px;">{{ date('M Y', strtotime($item->ServicePeriod)) }}</td>
                <td style="padding-left: 10px;">{{ $bill != null ? date('m/d/Y', strtotime($bill->DueDate)) : 'n/a' }}</td>
                <td style="padding-left: 10px;">{{ number_format(floatval($item->NetAmount) - floatval($item->Surcharge), 2) }}</td>
                <td style="padding-left: 10px;">{{ number_format($item->Surcharge, 2) }}</td>
                <td style="padding-left: 10px;">{{ number_format($item->NetAmount, 2) }}</td>
            </tr>  
        </tbody>
    </table>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <span>{{ $user != null ? $user->name : 'Teller: n/a' }}</span>
    <span style="float: right; margin-right: 60px;">{{ number_format($total, 2) }}</span>
</div>
@endforeach

<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.location.href = "{{ route('paidBills.bapa-payments') }}";
    }, 800);
</script>