@php
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
                <h4>Meter Reader Efficiency Report</h4>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-none">
            <div class="card-body">
                {!! Form::open(['route' => 'readings.efficiency-report', 'method' => 'GET']) !!}
                <div class="row">
                    <div class="form-group col-lg-1">
                        <label for="">Office</label>
                        <input type="text" value="{{ $office!=null ? $office : env('APP_AREA_CODE') }}" class="form-control form-control-sm" id="Office" name="Office" autofocus>
                    </div>
                    <div class="form-group col-lg-2">
                        <label for="ServicePeriod">Select Month</label>
                        <select name="ServicePeriod" id="ServicePeriod" class="form-control form-control-sm">
                            @for ($i = 0; $i < count($months); $i++)
                                <option value="{{ $months[$i] }}" {{ $month!=null && $month==$months[$i] ? 'selected' : '' }}>{{ date('F Y', strtotime($months[$i])) }}</option>
                            @endfor
                        </select>
                    </div>
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
                        <label for="MeterReader">Meter Reader</label>
                        <select name="MeterReader" id="MeterReader" class="form-control form-control-sm">
                            @foreach ($meterReaders as $item)
                                <option value="{{ $item->MeterReader }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">Action</label><br>
                        {!! Form::submit('View', ['class' => 'btn btn-primary btn-sm']) !!}
                        <button class="btn btn-sm btn-warning" id="printBtnReport">Print</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card shadow-none" style="height: 70vh;">
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-bordered table-hover w-auto small">
                    <thead>
                        <tr>
                            <th class="align-middle" rowspan="4">Route<br>Code</th>
                            <th class="text-center" colspan="2">{{ strtoupper(date('F Y', strtotime($period))) }} SALES</th>
                            <th class="text-center" colspan="4">{{ strtoupper(date('F Y', strtotime($period))) }} COLLECTION</th>
                            <th colspan="2"></th>
                        </tr>   
                        <tr>
                            <th></th>
                            <th></th>
                            <th colspan="2" class="text-center">PREV MONTH</th>
                            <th colspan="2" class="text-center">THIS MONTH</th>
                            <th colspan="2" class="text-center">COLLECTED ARREARS</th>
                        </tr> 
                        <tr>
                            <th></th>
                            <th class="text-right">Bill Amount</th>
                            <th></th>
                            <th class="text-right">Bill Amount</th> 
                            <th></th>
                            <th class="text-right">Bill Amount</th> 
                            <th></th>
                            <th class="text-right">Bill Amount</th> 
                        </tr> 
                        <tr>    
                            <th class="text-right"># Bills</th>                    
                            <th class="text-right">Others</th>
                            <th class="text-right"># Bills</th>                  
                            <th class="text-right">Others</th> 
                            <th class="text-right"># Bills</th>                  
                            <th class="text-right">Others</th> 
                            <th class="text-right"># Bills</th>                  
                            <th class="text-right">Others</th> 
                        </tr>                     
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <th rowspan="2">{{ $item->AreaCode }}</th>
                                <td class="text-right">{{ $item->PeriodNoOfBillsSales }}</td>
                                <td class="text-right">{{ number_format($item->PeriodBillAmountSales, 2) }}</td>
                                <td class="text-right">{{ $item->PeriodNoOfBillsPrevMonthCollection }}</td>
                                <td class="text-right">{{ number_format($item->PeriodAmountPrevMonthCollection, 2) }}</td>
                                <td class="text-right">{{ $item->PeriodNoOfBillsCurrentMonthCollection }}</td>
                                <td class="text-right">{{ number_format($item->PeriodAmountCurrentMonthCollection, 2) }}</td>
                                <td class="text-right">{{ $item->PeriodNoOfBillsArrearsCollected }}</td>
                                <td class="text-right">{{ number_format($item->PeriodAmountArrearsCollected, 2) }}</td>
                            </tr>
                            <tr>     
                                <td></td>                           
                                <td class="text-right">{{ number_format($item->PeriodOthersSales, 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection