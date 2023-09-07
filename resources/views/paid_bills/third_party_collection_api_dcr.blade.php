@extends('layouts.app')

@section('content')
<p style="padding-top: 8px;"><i class="fas fa-chart-line ico-tab"></i>Third Party API DCR</strong></p>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-none p-0">
            <div class="card-body px-4 py-1">
                <form action="{{ route('paidBills.third-party-collection-api-dcr') }}" method="GET">
                    <div class="row">
                        <div class="form-group col-lg-2">
                            <label for="From">From</label>
                            <input type="text" class="form-control form-control-sm" id="From" name="From" placeholder="From" value="{{ isset($_GET['From']) ? $_GET['From'] : '' }}">
                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#From').datetimepicker({
                                        format: 'YYYY-MM-DD',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div>

                        <div class="form-group col-lg-2">
                            <label for="To">To</label>
                            <input type="text" class="form-control form-control-sm" id="To" name="To" placeholder="To" value="{{ isset($_GET['To']) ? $_GET['To'] : '' }}">
                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#To').datetimepicker({
                                        format: 'YYYY-MM-DD',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div>

                        <div class="form-group col-lg-2">
                            <label for="Source">Collector/Source</label>
                            <select name="Source" id="Source" class="form-control form-control-sm">
                                <option value="ALVIO">ALVIO</option>
                                <option value="AMPSS Corp.">AMPSS Corp.</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-2">
                            <label for="Action">Action</label>
                            <br>
                            <button class="btn btn-sm btn-warning" type="submit" id="filter-btn"><i class="fas fa-filter"></i>Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RESULTS --}}
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#dcr-summary" data-toggle="tab">
                        <i class="fas fa-list"></i>
                        DCR Summary</a></li>

                    <li class="nav-item"><a class="nav-link" href="#power-bills" data-toggle="tab">
                        <i class="fas fa-user"></i>
                        Power Bills Payments</a></li>

                    <li class="nav-item"><a class="nav-link" href="#non-power-bills" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Non-Power Bills Payments</a></li>

                    <li class="nav-item"><a class="nav-link" href="#check-payments" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Check Payments</a></li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane active" id="dcr-summary">
                        @include('d_c_r_summary_transactions.dcr_summary_api')
                    </div>

                    <div class="tab-pane" id="power-bills">
                        @include('d_c_r_summary_transactions.power_bills')
                    </div>

                    <div class="tab-pane" id="non-power-bills">
                        @include('d_c_r_summary_transactions.non_power_bills')
                    </div>

                    <div class="tab-pane" id="check-payments">
                        @include('d_c_r_summary_transactions.tab_check_admin_dcr')
                    </div>  
                </div>                    
            </div>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="col-lg-2">
        <div class="card shadow-none">
            <div class="card-header">
                <span class="card-title">Summary</span>
            </div>
            <div class="card-body">
                @php
                    $powerBillsTotal = 0;
                    $nonPowerBillsTotal = 0;
                    $checkTotal = 0;
                    $dcrTotal = 0;
                @endphp
                {{-- POWER BILLS --}}
                @foreach ($powerBills as $item) 
                    @php
                        $powerBillsTotal = $powerBillsTotal + floatval($item->CashPaid);
                    @endphp   
                @endforeach  
                
                {{-- NON POWER BILLS --}}
                @foreach ($nonPowerBills as $item)  
                    @php  
                        $nonPowerBillsTotal = $nonPowerBillsTotal + floatval($item->Total);          
                    @endphp   
                @endforeach  

                {{-- CHECK PAYMENTS --}}
                @if ($checkPayments != null)
                    @foreach ($checkPayments as $item)   
                        @php
                            $checkTotal += floatval($item->Amount);
                        @endphp
                    @endforeach     
                @endif  

                {{-- DCR TOTAL --}}
                @foreach ($data as $item)
                    @php
                        $dcrTotal += floatval($item->Amount);
                    @endphp
                @endforeach
                @php
                    $overAllTotal = $powerBillsTotal + $nonPowerBillsTotal + $checkTotal;

                    // DIFFERENCE
                    $dif = round($overAllTotal - $dcrTotal, 2);
                @endphp
                <p class="text-right text-muted" style="margin: 0; padding: 0;">Check Payments Total</p>
                <h3 class='text-right text-info'>{{ number_format($checkTotal, 2) }}</h3>

                @if ($dif == 0)
                    <p class="text-right text-muted" style="margin: 0; padding: 0;">Cash Payments Total</p>
                    <h3 class='text-right text-info'>{{ number_format($powerBillsTotal + $nonPowerBillsTotal, 2) }}</h3>
                    <div class="divider"></div>
                    <p class="text-right text-muted" style="margin: 0; padding: 0;">Collection Total</p>
                    <h1 class='text-right text-success'>{{ number_format($overAllTotal, 2) }}</h1>                
                @else
                    @php
                        $dif = $dif * (-1);

                        $correctedCash = ($powerBillsTotal + $nonPowerBillsTotal) + $dif;
                        $correctedTtl = ($powerBillsTotal + $nonPowerBillsTotal + $checkTotal) + $dif;
                    @endphp
                    @if ($dif > -20 && $dif < 20)
                        <p class="text-right text-muted" style="margin: 0; padding: 0;">Cash Payments Total</p>
                        <h3 class='text-right text-info'>{{ number_format($correctedCash, 2) }}</h3>
                        <div class="divider"></div>
                        <p class="text-right text-muted" style="margin: 0; padding: 0;">Collection Total</p>
                        <h1 class='text-right text-success'>{{ number_format($correctedTtl, 2) }}</h1>  

                        @push('page_scripts')
                            <script>
                                $(document).ready(function() {
                                    var dif = "{{ $dif }}"
                                    dif = parseFloat(dif)

                                    var powerBills = "{{ $powerBillsTotal }}"
                                    powerBills = parseFloat(powerBills)
                                    $('#total-api-power-dcr').text(Number(dif + powerBills).toLocaleString(2))
                                })
                            </script>
                        @endpush
                    @else
                        <p class="text-right text-muted" style="margin: 0; padding: 0;">Cash Payments Total</p>
                        <h3 class='text-right text-info'>{{ number_format($powerBillsTotal + $nonPowerBillsTotal, 2) }}</h3>
                        <div class="divider"></div>
                        <p class="text-right text-muted" style="margin: 0; padding: 0;">Collection Total</p>
                        <h1 class='text-right text-success'>{{ number_format($overAllTotal, 2) }}</h1> 

                        <div class="divider"></div>
                        <p class="text-right text-muted" style="margin: 0; padding: 0;">DCR Difference Detected</p>
                        <h3 class='text-right text-danger'>{{ number_format($dif, 2) }}</h3>
                        {{-- <button class="btn btn-sm btn-danger float-right" id="fixbtn">Fix</button> --}}
                    @endif   
                @endif
            </div>
        </div>
    </div>
</div>
@endsection