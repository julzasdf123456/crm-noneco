@php
    use App\Models\ServiceAccounts;
    // GET PREVIOUS MONTHS
    for ($i = 0; $i <= 12; $i++) {
        $months[] = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$i months"));
    }
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Net Metering Monthly Summary Report</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-none">
                <div class="card-body">
                    {!! Form::open(['route' => 'bills.net-metering-report', 'method' => 'GET']) !!}
                    <div class="row">
                        {{-- <div class="form-group col-lg-1">
                            <label for="Town">Town</label>
                            <select name="Town" id="Town" class="form-control form-control-sm">
                                <option value="All">All</option>
                                @foreach ($towns as $item)
                                    <option value="{{ $item->id }}" {{ isset($_GET['Town']) && $_GET['Town']==$item->id ? 'selected' : '' }}>{{ $item->Town }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="form-group col-lg-2">
                            <label for="ServicePeriod">Billing Month</label>
                            <select name="ServicePeriod" id="ServicePeriod" class="form-control form-control-sm">
                                @for ($i = 0; $i < count($months); $i++)
                                    <option value="{{ $months[$i] }}" {{ isset($_GET['ServicePeriod']) && $_GET['ServicePeriod']==$months[$i] ? 'selected' : '' }}>{{ date('F Y', strtotime($months[$i])) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">Action</label><br>
                            {!! Form::submit('View', ['class' => 'btn btn-primary btn-sm']) !!}
                            <button class="btn btn-sm btn-warning" id="print">Print</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        {{-- RESULTS --}}
        <div class="col-lg-12">
            <div class="card shadow-none" style="height: 70vh">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-sm table-bordered">
                        <thead>
                           <tr>
                              <th rowspan="2" class="text-center">#</th>
                              <th rowspan="2" class="text-center">Account No.</th>
                              <th rowspan="2" class="text-center">Consumer Name</th>
                              <th rowspan="2" class="text-center">Address</th>
                              <th rowspan="2" class="text-center">Imported Energy</th>
                              <th rowspan="2" class="text-center">Exported Energy</th>
                              <th rowspan="2" class="text-center">Current Amount<br>DU To Customer</th>
                              <th rowspan="2" class="text-center">Current Amount<br>Customer To DU</th>
                              <th rowspan="2" class="text-center">Current Amount Due</th>
                              <th colspan="7" class="text-center">Net Metering Charges</th>
                           </tr> 
                           <tr>
                              <th class="text-center">Generation</th>
                              <th class="text-center">Demand Charge (kW)</th>
                              <th class="text-center">Demand Charge (kWh)</th>
                              <th class="text-center">Supply System</th>
                              <th class="text-center">Supply Retail</th>
                              <th class="text-center">Metering System</th>
                              <th class="text-center">Metering Retail</th>
                           </tr>
                        </thead>
                        <tbody>
                           @php
                               $i = 1;
                           @endphp
                           @foreach ($data as $item)
                              <tr>
                                 <td>{{ $i }}</td>
                                 <td><a href="{{ route('serviceAccounts.show', [$item->id]) }}">{{ $item->OldAccountNo }}</a></td>
                                 <td>{{ $item->ServiceAccountName }}</td>
                                 <td>{{ ServiceAccounts::getAddress($item) }}</td>
                                 <td class="text-right">{{ is_numeric($item->KwhUsed) ? round(floatval($item->KwhUsed), 2) : $item->KwhUsed }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarExportKwh) ? round(floatval($item->SolarExportKwh), 2) : $item->SolarExportKwh }}</td>
                                 <td class="text-right">{{ is_numeric($item->DUToCustomer) ? round(floatval($item->DUToCustomer), 2) : $item->DUToCustomer }}</td>
                                 <td class="text-right">{{ is_numeric($item->CustomerToDU) ? round(floatval($item->CustomerToDU), 2) : $item->CustomerToDU }}</td>
                                 <td class="text-right">{{ is_numeric($item->NetAmount) ? round(floatval($item->NetAmount), 2) : $item->NetAmount }}</td>
                                 <td class="text-right">{{ is_numeric($item->GenerationChargeSolarExport) ? round(floatval($item->GenerationChargeSolarExport), 2) : $item->GenerationChargeSolarExport }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarDemandChargeKW) ? round(floatval($item->SolarDemandChargeKW), 2) : $item->SolarDemandChargeKW }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarDemandChargeKWH) ? round(floatval($item->SolarDemandChargeKWH), 2) : $item->SolarDemandChargeKWH }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarRetailCustomerCharge) ? round(floatval($item->SolarRetailCustomerCharge), 2) : $item->SolarRetailCustomerCharge }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarSupplySystemCharge) ? round(floatval($item->SolarSupplySystemCharge), 2) : $item->SolarSupplySystemCharge }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarMeteringRetailCharge) ? round(floatval($item->SolarMeteringRetailCharge), 2) : $item->SolarMeteringRetailCharge }}</td>
                                 <td class="text-right">{{ is_numeric($item->SolarMeteringSystemCharge) ? round(floatval($item->SolarMeteringSystemCharge), 2) : $item->SolarMeteringSystemCharge }}</td>
                              </tr>
                              @php
                                  $i++;
                              @endphp
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#print').on('click', function(e) {
                e.preventDefault()
                window.location.href = "{{ url('/bills/print-net-metering-report') }}/" + $('#ServicePeriod').val()
            })
        })
    </script>
@endpush