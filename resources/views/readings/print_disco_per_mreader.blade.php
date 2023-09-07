@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
    use App\Models\Readings;
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

<div id="print-area" class="content">
    <p>Generated On: {{ date('F d, Y h:i:s A') }}</p>
    <br>
    <p class="text-center">{{ strtoupper(env('APP_COMPANY')) }}</p>
    <p class="text-center">{{ strtoupper(env('APP_ADDRESS')) }}</p>
    <p class="text-center">DISCONNECTION REPORT FOR {{ date('F Y', strtotime($period)) }}</p>
    <br> 
    <p>Meter Reader: {{ $meterReader }}</p>  
    <table style="width: 100%;">
        <thead>
            <th style="width: 25px; border-bottom: 1px solid #454455">#</th>
            <th style="border-bottom: 1px solid #454455">Account No</th>
            <th style="border-bottom: 1px solid #454455">Consumer Name</th>
            <th style="border-bottom: 1px solid #454455">Account Status</th>
            <th style="border-bottom: 1px solid #454455" class="text-right">Bill Amnt.</th>
            <th style="border-bottom: 1px solid #454455" class="text-right">Last Reading</th>
            <th style="border-bottom: 1px solid #454455">Meter Reader/Disconnector</th>
            <th style="border-bottom: 1px solid #454455">Date Disconnected</th>
            <th style="border-bottom: 1px solid #454455">Date Reconnected</th>
        </thead>
        <tbody>
            @php
                $i=0;
            @endphp
            @foreach ($data as $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->OldAccountNo }}</td>
                    <td>{{ $item->ServiceAccountName }}</td>
                    <td>{{ $item->AccountStatus }}</td>
                    <td class="text-right">{{ is_numeric($item->NetAmount) ? number_format($item->NetAmount, 2) : $item->NetAmount }}</td>
                    <td class="text-right">{{ $item->BillId }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->DateDisconnected != null ? date('M d, Y', strtotime($item->DateDisconnected)) : '' }}</td>
                    <td>{{ $item->DateReconnected != null ? date('M d, Y', strtotime($item->DateReconnected)) : '' }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        </tbody>
    </table>
</div>
<script type="text/javascript">
    window.print();

    window.setTimeout(function(){
        window.history.go(-1)
    }, 800);
</script>