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
        /* font-family: sax-mono, Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif; */
        font-family: sans-serif;
        font-stretch: normal;
        font-size: .85em;
    }

    table tbody th,td,
    table thead th {
        font-family: sans-serif;
        /* font-family: sax-mono, Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif; */
        font-stretch: normal;
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

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .row {
      display: inline;
      width: 100%;
    }

    .col-lg-6 {
      display: inline-table;
      width: 48%;
    }

    .height-pad {
      width: 100%;
      height: 5px;;
    }

</style>

<div class="content">
   <img src="{{ URL::asset('imgs/noneco-official-logo.png'); }}" width="40px;" style="float: left;"> 
   <br>
   <p class="text-center"><strong>{{ strtoupper(env('APP_COMPANY')) }}</strong></p>
   <p class="text-center">{{ env('APP_ADDRESS') }}  |  {{ env('APP_COMPANY_TIN') }}</p>
   <br>
   <h4 class="text-center">DEMAND LETTER</h4>

   <span>Date: <strong>{{ date('F d, Y') }}</strong></span><br>
   <span>Account Name: <strong>{{ $serviceAccounts != null ? $serviceAccounts->ServiceAccountName : '-' }}</strong></span><br>
   <span>Account No.: <strong>{{ $serviceAccounts != null ? $serviceAccounts->OldAccountNo : '-' }}</strong></span><br>
   <span>Address: <strong>{{ $serviceAccounts != null ? ServiceAccounts::getAddress($serviceAccounts) : '-' }}</strong></span><br><br>

   <p>Sir/Madamme,</p>
   <br>
   <p style="text-indent: 30px;">We are writing about your overdue electric account which remain outstanding as follows:</p>
   <br>
   <table style="width: 100%;">
      <thead>
         <th class="text-left">Billing Month</th>
         <th class="text-right">Amount</th>
         <th class="text-right">Surcharge</th>
         <th class="text-right">Interest</th>
         <th class="text-right">Amount Due</th>
      </thead>
      <tbody>
         @if ($serviceAccounts != null)
            @php
               $total = 0;
            @endphp
             @foreach ($bills as $item)
                 <tr id="{{ $item->id }}">
                     <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                     <td class="text-right">{{ is_numeric($item->NetAmount) ? number_format($item->NetAmount, 2) : $item->NetAmount }}</td>
                     <td class="text-right">{{ number_format(Bills::getSurchargeOnly($item), 2) }}</td>
                     <td class="text-right">{{ number_format(Bills::getInterestOnly($item), 2) }}</td>
                     @php
                        $tmpTotal = floatval(Bills::assessDueBillAndGetSurcharge($item)) + (is_numeric($item->NetAmount) ? floatval($item->NetAmount) : 0);
                     @endphp
                     <td class="text-right">{{ number_format($tmpTotal, 2) }}</td>
                 </tr>
                 @php
                     $total += $tmpTotal;
                 @endphp
             @endforeach
             <tr>
               <th colspan="4" class="text-left" style="border-top: 1px dotted #686868;">TOTAL AMOUNT DUE</th>
               <th class="text-right" style="border-top: 1px dotted #686868;">{{ number_format($total, 2) }}</th>
             </tr>
         @endif
      </tbody>
   </table>

   <br>
   <p style="text-indent: 30px;">{{ env('DEMAND_LETTER_BODY_1') }}</p>
   <br>
   <p style="text-indent: 30px;">{{ env('DEMAND_LETTER_BODY_2') }}</p>
   <p>Thank you.</p>
   <br>
   <br>
   <p>Very Truly Yours,</p>
   <br>
   <br>
   <p style="margin: 0px !important; padding: 0px !important;"><strong>{{ env('AREA_MANAGER') }}</strong></p>
   <p style="text-indent: 30px; margin: 0px !important; padding: 0px !important;">AOD Manager</p>
   <br>
   <br>

   <div class="row">
      <div class="col-lg-6">
         <span>CC: <span style="margin-left: 30px;">Legal Counsel</span></span><br><div class="height-pad"></div>
         <span style="margin-left: 55px;">OGM - Audit, ISD</span><br><div class="height-pad"></div>
         <span style="margin-left: 55px;">Area Office</span>
      </div>

      <div class="col-lg-6">
         <span>Received By: _______________________________</span><br><div class="height-pad"></div>
         <span>Date Received: _____________________________</span><br>
      </div>
   </div>
</div>

<script type="text/javascript">   
   window.print();

   window.setTimeout(function(){
      window.history.go(-1)
   }, 1000);
</script>
