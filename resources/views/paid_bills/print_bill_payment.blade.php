
<style>
    @media print {
        @page {
            margin: 10px;
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

        .map {
            width: 90% auto;
            height: 400px;
        }

        .left-indent {
            margin-left: 30px;
        }
    }  

    .left-indent {
        margin-left: 30px;
    }

    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    } 

    .map {
        width: 90% auto;
        height: 400px;
    }
</style>

<div id="print-area" class="content">
    <br>
    <br>
    <br>
    <br>
    <span style="margin-left: 140px;">{{ $paidBillSingle != null ? $paidBillSingle->ORNumber : '-' }}</span>
    <span style="margin-left: 140px;">{{ $account != null ? $account->ServiceAccountName : '-' }}</span>
    <br>
    <table style="margin-top: 30px;">
        <tbody>     
            @foreach ($paidBill as $item)
                <tr>
                    <td style="padding-left: 20px;">{{ $item->BillNumber }}</td>
                    <td style="padding-left: 150px; float: right;">{{ date('M d, Y', strtotime($item->ServicePeriod)) }}</td>
                    <td style="padding-left: 150px; float: right;">{{ number_format($item->NetAmount, 2) }}</td>
                </tr>
            @endforeach      
        </tbody>
    </table>
    <br>
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.location.href = "{{ route('paidBills.index') }}";
    }, 800);
</script>