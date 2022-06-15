@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
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
    <br>
    <br>
    <br>
    <br>
    <br>
    <span style="margin-left: 4px;">Meter #: {{ $meter != null ? $meter->SerialNumber : '-' }}</span>
    <span style="margin-left: 170px;">{{ $paidBillSingle != null ? $paidBillSingle->ORNumber : '-' }}</span><br>
    @if ($account != null)
        <span style="margin-left: 4px;">{{ $account->OldAccountNo }}</span>
        <span style="margin-left: 20px;">{{ $account->ServiceAccountName }}</span>
        <span style="margin-left: 20px;">{{ $account->AccountType }}</span><br>
        <span style="margin-left: 4px;">{{ ServiceAccounts::getAddress($account) }}</span>
        <span style="margin-left: 20px;">{{ $account->AccountStatus }}</span><br>
    @else
        <span style="margin-left: 40px;">Account Details Not Found</span>
    @endif
    
    <br>
    <span style="float: right; margin-right: 90px; margin-top: 10px;">{{ $paidBillSingle != null ? date('h:i:s A m/d/Y', strtotime($paidBillSingle->created_at)) : '-' }}</span><br>
    <br>
    @php
        $total = 0.0;
    @endphp
    <table style="margin-top: 38px;">
        <tbody>     
            @foreach ($paidBill as $item)
                @php
                    // GET BILLS
                    $bill = Bills::where('ServicePeriod', $item->ServicePeriod)
                        ->where('AccountNumber', $item->AccountNumber)
                        ->first();
                    $total = $total + floatval($item->NetAmount);
                @endphp
                <tr>
                    <td>{{ $item->BillNumber }}</td>
                    <td style="padding-left: 4px;">{{ date('M Y', strtotime($item->ServicePeriod)) }}</td>
                    <td style="padding-left: 10px;">{{ $bill != null ? date('m/d/Y', strtotime($bill->DueDate)) : 'n/a' }}</td>
                    <td style="padding-left: 10px;">{{ number_format(floatval($item->NetAmount) - floatval($item->Surcharge), 2) }}</td>
                    <td style="padding-left: 10px;">{{ number_format($item->Surcharge, 2) }}</td>
                    <td style="padding-left: 10px;">{{ number_format($item->NetAmount, 2) }}</td>
                </tr>
            @endforeach      
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
    <div style="position: fixed; bottom: 50px; width: 100%; left: 10px;">
        <span>{{ $user != null ? $user->name : 'Teller: n/a' }}</span>
        <span style="float: right; margin-right: 60px;">{{ number_format($total, 2) }}</span>
    </div>
    
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.location.href = "{{ route('paidBills.index') }}";
    }, 800);
</script>