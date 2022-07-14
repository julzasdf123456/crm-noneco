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
                <h4>BAPA Collection Monitor | <strong>{{ $bapaName }}</strong></h4>
            </div>

            <div class="col-sm-6">
                <form action="{{ route('bAPAAdjustments.bapa-collection-monitor-console', [urlencode($bapaName)]) }}" method="GET">
                    <div class="form-group row">                        
                        <label for="Period" class="col-sm-3">Select Billing Month</label>
                        <select id="Period" name="Period" class="form-control col-sm-5 mx-sm-3">
                            @for ($i = 0; $i < count($months); $i++)
                                <option value="{{ $months[$i] }}" {{ isset($_GET['Period']) && $_GET['Period']==$months[$i] ? 'selected' : ($rate!=null && date('Y-m-d', strtotime($rate->ServicePeriod))==$months[$i] ? 'selected' : '') }}>{{ date('F Y', strtotime($months[$i])) }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-primary col-sm-2 mb-2"><i class="fas fa-check ico-tab-mini"></i>Go</button>                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="row">
    {{-- LEFT PANEL --}}
    <div class="col-lg-2 col-md-3">
        {{-- ROUTES --}}
        <div class="card shadow-none">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-map-marker-alt ico-tab"></i> Routes in This BAPA</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead>

                    </thead>
                    <tbody>
                        @foreach ($routes as $item)
                            <tr>
                                <th>{{ $item->AreaCode }}</th>
                                <td class="text-right">{{ $item->NoOfConsumers }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>

        {{-- ADJUSTMENTS --}}
        @foreach ($bapaAdjustmentData as $item)
        <div class="card shadow-none">
            <div class="card-header bg-info">
                <span class="card-title"><i class="fas fa-percent ico-tab"></i>{{ number_format(floatval($item->DiscountPercentage) * 100, 2) }} % Dsc.</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead></thead>
                    <tbody>
                        <tr>
                            <td>Date Ajd.</td>
                            <th class="text-right">{{ date('Y/m/d', strtotime($item->DateAdjusted)) }}</th>
                        </tr>
                        <tr>
                            <td># of Cons.</td>
                            <th class="text-right">{{ $item->NoOfConsumers }}</th>
                        </tr>
                        <tr>
                            <td>Total Dsc.</td>
                            <th class="text-right text-danger">{{ number_format($item->DiscountTotal, 2) }}</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button onclick="vouch('{{ $item->DateAdjusted }}', '{{ $item->DiscountPercentage }}', '{{ $bapaName }}', '{{ $_GET['Period'] }}')" class="btn btn-xs btn-warning float-right"><i class="fas fa-print"></i> Print Voucher</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    {{-- DETAILS --}}
    <div class="col-lg-10 col-md-9">
        <div class="card shadow-none" style="height: 80vh">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-chart-line ico-tab"></i>Stats {{ isset($_GET['Period']) ? '(' . date('F Y', strtotime($_GET['Period'])) . ')' : '' }}</span>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm table-bordered table-head-fixed text-nowrap">
                    <thead>
                        <th style="width: 30px;">#</th>
                        <th>Account No</th>
                        <th>Account Name</th>
                        <th title="Account Status"><i class="fas fa-info-circle"></i></th>
                        <th class="text-right">Kwh Used</th>
                        <th class="text-right">Amount Due</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Discount %</th>
                        <th class="text-right">Net Amount Due</th>
                        <th class="text-right">Paid</th>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                            $totalKwh = 0;
                            $totalAmntDue = 0;
                            $totalDiscount = 0;
                            $totalNetAmntDue = 0;
                            $paymentCounter = 0;
                        @endphp
                        @foreach ($bapaData as $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td><a href="{{ route('serviceAccounts.show', [$item->id]) }}">{{ $item->OldAccountNo }}</a></td>
                                <td>{{ $item->ServiceAccountName }}</td>
                                @if ($item->AccountStatus=='ACTIVE')
                                    <td title="{{ $item->AccountStatus }}"><i class="fas fa-check-circle text-success"></i></td>
                                @else
                                    <td title="{{ $item->AccountStatus }}"><i class="fas fa-exclamation-circle text-danger"></i></td>
                                @endif
                                <td class="text-right">{{ $item->KwhUsed!=null ? $item->KwhUsed : '-' }}</td>
                                <td class="text-right text-info">{{ $item->NetAmount!=null ? number_format(floatval($item->NetAmount) + floatval($item->DiscountAmount), 2) : '-' }}</td>
                                <td class="text-right text-danger">{{ $item->DiscountAmount!=null ? number_format($item->DiscountAmount, 2) : '-' }}</td>
                                <th class="text-right text-success">{{ $item->DiscountPercentage!=null ? number_format(floatval($item->DiscountPercentage) * 100, 1) . '%' : '-' }}</th>
                                <th class="text-right text-primary">{{ $item->NetAmount!=null ? number_format($item->NetAmount, 2) : '-' }}</th>
                                <td class="text-right text-success" title="OR Number {{ $item->ORNumber!=null ? $item->ORNumber : '' }}">{{ $item->ORNumber!=null ? 'Yes' : '' }}</td>
                            </tr>
                            @php
                                $i++;
                                $totalKwh += floatval($item->KwhUsed);
                                $totalAmntDue += floatval($item->NetAmount) + floatval($item->DiscountAmount);
                                $totalDiscount += floatval($item->DiscountAmount);
                                $totalNetAmntDue += floatval($item->NetAmount);

                                if ($item->ORNumber!=null) {
                                    $paymentCounter++;
                                }
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot style="position: sticky; bottom:0; inset-block-end: 0; background-color: white;">
                        <th colspan="2">Summary</th>
                        <th></th>
                        <th></th>
                        <th class="text-right">{{ number_format($totalKwh) }}</th>
                        <th class="text-right text-info">{{ number_format($totalAmntDue, 2) }}</th>
                        <th class="text-right text-success">{{ number_format($totalDiscount, 2) }}</th>
                        <th></th>
                        <th class="text-right text-primary">{{ number_format($totalNetAmntDue, 2) }}</th>
                        <th class="text-right text-success">{{ $paymentCounter }}</th>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>11
</div>
@endsection

@push('page_scripts')
    <script>
        function vouch(dateAdjusted, percentage, bapaName, period) {
            Swal.fire({
                title: 'Enter BAPA Representative Name',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Print',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    var val = result.value
                    if (jQuery.isEmptyObject(val)) {
                        Swal.fire({
                            title : 'Representative should not be empty',
                            icon : 'error'
                        })
                    } else {
                        window.location.href = "{{ url('/b_a_p_a_adjustments/print-voucher') }}" + "/" + encodeURI(val) + "/" + encodeURI(bapaName) + "/" + period + "/" + percentage + "/" + dateAdjusted
                    }
                }
            })
        }
    </script>
@endpush