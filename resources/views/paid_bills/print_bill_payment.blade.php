
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
    <span style="margin-left: 140px;">{{ $paidBill->BillNumber }}</span>
    <span style="margin-left: 90px;">{{ $paidBill->PostingDate }}</span>
    <br>
    <table style="margin-top: 30px;">
        <tbody>            
            <tr>
                <td style="padding-left: 20px;">Add Particulars Here</td>
                <td style="padding-left: 150px; float: right;">Add Particulars Here</td>
            </tr>
        </tbody>
    </table>
    <br>
    <span style="margin-left: 320px;">{{ number_format($paidBill->NetAmount, 2) }}</span>
    <br>
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.location.href = "{{ route('paidBills.index') }}";
    }, 800);
</script>