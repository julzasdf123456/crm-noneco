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
                <h4>Generate New KWH Sales Report</h4>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            {!! Form::open(['route' => 'kwhSales.save-sales-report']) !!}
            <div class="card-header border-0">
                <span class="card-title">Sales Report for {{ date('F Y', strtotime($period)) }}</span>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <th>Area</th>
                        <th class="text-right">No. of Consumers</th>
                        <th class="text-right">KWH Consumed</th>
                        <th>Demand KWH</th>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>
                                {{ $item->Town }}
                                <input type="hidden" name="ServicePeriod[]" value="{{ $period }}">
                                <input type="hidden" name="Town[]" value="{{ $item->id }}">
                            </td>
                            <th class="text-right">
                                {{ number_format($item->ConsumerCount) }}
                                <input type="hidden" step="any" class="form-control" name="NoOfConsumers[]" placeholder="No. of Consumers" readonly="true" value="{{ $item->ConsumerCount }}">
                            </th>
                            <th class="text-right">
                                {{ number_format($item->TotalKwhConsumption, 2) }}
                                <input type="hidden" step="any" class="form-control" name="ConsumedKwh[]" placeholder="KWH Consumed" readonly="true" value="{{ $item->TotalKwhConsumption }}">
                            </th>
                            <td>
                                <input type="number" step="any" class="form-control" name="BilledKwh[]" placeholder="Billed KWH">
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection