@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
@endphp
<style>
    html, body {
        font-family: sans-serif;
        font-stretch: condensed;
        font-size: .88em;
    }

    table tbody th,td,
    table thead th {
        font-family: sans-serif;
        font-stretch: condensed;
        font-size: .83em;
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
    }  
    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    } 

</style>


{{-- <link rel="stylesheet" href="{{ URL::asset('adminlte.min.css') }}"> --}}

<div id="print-area" class="content">
    <div style="margin-top: 15px;">
        <span style="margin-left: 20px;">Meter #: {{ $meter != null ? $meter->SerialNumber : '-' }}</span>
        <span style="margin-left: 200px;">{{ $paidBillSingle != null ? $paidBillSingle->ORNumber : '-' }}</span><br>
        @if ($account != null)
            <span style="margin-left: 20px;">{{ $account->OldAccountNo }}</span>
            <span style="margin-left: 15px;">{{ $account->ServiceAccountName }}</span><br>
            <span style="margin-left: 20px;">{{ ServiceAccounts::getAddress($account) }}</span>
            <span style="margin-left: 26px;">{{ $account->AccountStatus }}</span>
        @else
            <span style="margin-left: 40px;">Account Details Not Found</span>
        @endif
        
        <br>
        <span style="float: right; margin-right: 10px; margin-top: 10px;">{{ $paidBillSingle != null ? date('h:i:s A m/d/Y', strtotime($paidBillSingle->created_at)) : '-' }}</span><br>
    </div>

    <br>
    @php
        $total = 0.0;
    @endphp
    <div style="width: 100%;">
        <table style="margin-top: 24px; width: 100%;">
            <tbody>     
                @foreach ($paidBill as $item)
                    @php
                        // GET BILLS
                        $bill = Bills::where('ServicePeriod', $item->ServicePeriod)
                            ->where('AccountNumber', $item->AccountNumber)
                            ->first();
                        $total = $total + floatval($item->NetAmount);
                    @endphp
                    <tr style="width: 100%;">
                        <td>{{ $item->BillNumber }}</td>
                        <td style="padding-left: 15px;">{{ date('M Y', strtotime($item->ServicePeriod)) }}</td>
                        <td style="padding-left: 20px;">{{ $bill != null ? date('m/d/Y', strtotime($bill->DueDate)) : 'n/a' }}</td>
                        <td style="padding-left: 15px;">{{ number_format(floatval($item->NetAmount) - floatval($item->Surcharge), 2) }}</td>
                        <td style="padding-left: 15px;">{{ number_format($item->Surcharge, 2) }}</td>
                        <td style="padding-left: 15px;">{{ number_format($item->NetAmount, 2) }}</td>
                    </tr>
                @endforeach      
            </tbody>
        </table>
    </div>
    <div style="position: absolute; bottom: 20px; width: 100%; left: 10px;">
        <span>{{ $user != null ? $user->name : 'Teller: n/a' }}</span>
        <span style="float: right; margin-right: 40px;">{{ number_format($total, 2) }}</span>
    </div>
    
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.location.href = "{{ route('paidBills.index') }}";
    }, 800);
</script>