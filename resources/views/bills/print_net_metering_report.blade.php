@php
    use App\Models\ServiceAccounts;
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

    table {
      border-collapse: collapse;
    }

    .tbl-border {
      border: 1px solid #454545;
    }

</style>

<div>
    {{-- SUMMARY --}}
    <table style="page-break-before: always; width: 100%;">
        <thead>
            <tr>
                <th colspan="16" class="text-center">{{ strtoupper(env('APP_COMPANY')) }}</th>
            </tr>
            <tr>
                <th colspan="16" class="text-center">{{ strtoupper(env('APP_ADDRESS')) }}</th>
            </tr>
            <tr>
                <th colspan="16" class="text-center">NET METERING REPORT FOR {{ strtoupper(date('F Y', strtotime($period))) }}</th>
            </tr>
            <tr>
               <th rowspan="2" class="text-center tbl-border">#</th>
               <th rowspan="2" class="text-center tbl-border">Account No.</th>
               <th rowspan="2" class="text-center tbl-border">Consumer Name</th>
               <th rowspan="2" class="text-center tbl-border">Address</th>
               <th rowspan="2" class="text-center tbl-border">Imported Energy</th>
               <th rowspan="2" class="text-center tbl-border">Exported Energy</th>
               <th rowspan="2" class="text-center tbl-border">Current Amount<br>DU To Customer</th>
               <th rowspan="2" class="text-center tbl-border">Current Amount<br>Customer To DU</th>
               <th rowspan="2" class="text-center tbl-border">Current Amount Due</th>
               <th colspan="7" class="text-center tbl-border">Net Metering Charges</th>
            </tr> 
            <tr>
               <th class="text-center tbl-border">Generation</th>
               <th class="text-center tbl-border">Demand Charge (kW)</th>
               <th class="text-center tbl-border">Demand Charge (kWh)</th>
               <th class="text-center tbl-border">Supply System</th>
               <th class="text-center tbl-border">Supply Retail</th>
               <th class="text-center tbl-border">Metering System</th>
               <th class="text-center tbl-border">Metering Retail</th>
            </tr>          
        </thead>
        <tbody>
            @php
                  $i = 1;
            @endphp
            @foreach ($data as $item)
               <tr>
                  <td class="tbl-border">{{ $i }}</td>
                  <td class="tbl-border">{{ $item->OldAccountNo }}</td>
                  <td class="tbl-border">{{ $item->ServiceAccountName }}</td>
                  <td class="tbl-border">{{ ServiceAccounts::getAddress($item) }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->KwhUsed) ? round(floatval($item->KwhUsed), 2) : $item->KwhUsed }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarExportKwh) ? round(floatval($item->SolarExportKwh), 2) : $item->SolarExportKwh }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->DUToCustomer) ? round(floatval($item->DUToCustomer), 2) : $item->DUToCustomer }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->CustomerToDU) ? round(floatval($item->CustomerToDU), 2) : $item->CustomerToDU }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->NetAmount) ? round(floatval($item->NetAmount), 2) : $item->NetAmount }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->GenerationChargeSolarExport) ? round(floatval($item->GenerationChargeSolarExport), 2) : $item->GenerationChargeSolarExport }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarDemandChargeKW) ? round(floatval($item->SolarDemandChargeKW), 2) : $item->SolarDemandChargeKW }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarDemandChargeKWH) ? round(floatval($item->SolarDemandChargeKWH), 2) : $item->SolarDemandChargeKWH }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarRetailCustomerCharge) ? round(floatval($item->SolarRetailCustomerCharge), 2) : $item->SolarRetailCustomerCharge }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarSupplySystemCharge) ? round(floatval($item->SolarSupplySystemCharge), 2) : $item->SolarSupplySystemCharge }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarMeteringRetailCharge) ? round(floatval($item->SolarMeteringRetailCharge), 2) : $item->SolarMeteringRetailCharge }}</td>
                  <td class="text-right tbl-border">{{ is_numeric($item->SolarMeteringSystemCharge) ? round(floatval($item->SolarMeteringSystemCharge), 2) : $item->SolarMeteringSystemCharge }}</td>
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
    }, 1600);
</script>