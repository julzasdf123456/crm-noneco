@php
    use App\Models\Readings;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="text-muted">Reading Report </i> | <i class="text-muted">Billing Month: </i> <strong>{{ date('F Y', strtotime($period)) }}</strong>
                        | <i class="text-muted">Meter Reader: </i> <strong>{{ $meterReader != null ? $meterReader->name : '-' }} </strong>
                        | <i class="text-muted">Day: </i><strong>{{ $day }}</strong></h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        {{-- SUMMARY --}}
        <div class="col-lg-12">
            <div class="card shadow-none">
                <div class="card-header border-0">
                    <span class="card-title"><i class="fas fa-check-circle ico-tab"></i>Summary</span>
                </div>
                <div class="card-body table-responsive px-0">
                    <table class="table table-sm">
                        <thead>
                            <th class="text-muted">Captured Readings</th>
                            <th class="text-muted">Stuck Up</th>
                            <th class="text-muted">Change Meters</th>
                            <th class="text-muted">No Display</th>
                            <th class="text-muted">Not in Use</th>
                            <th class="text-muted">Other Unbilled</th>
                            <th class="text-muted">Total Billed</th>
                            <th class="text-muted">Total Readings</th>
                        </thead>
                        <tbody>
                            <tr>
                                <th>{{ $summary != null ? $summary->Captured : '0' }}</th>
                                <th>{{ $summary != null ? $summary->StuckUp : '0' }}</th>
                                <th>{{ $summary != null ? $summary->ChangeMeter : '0' }}</th>
                                <th>{{ $summary != null ? $summary->NoDisplay : '0' }}</th>
                                <th>{{ $summary != null ? $summary->NotInUse : '0' }}</th>
                                <th>{{ $summary != null ? $summary->OtherUnbilled : '0' }}</th>
                                <th>{{ $summary != null ? $summary->TotalBilled : '0' }}</th>
                                <th>{{ $summary != null ? number_format(intval($summary->Total) + intval($summary->Captured)) : '0' }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INDIVIDUALIZED --}}
        <div class="col-lg-12">
            <div class="card shadow-none" style="height: 60vh;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-list ico-tab"></i>Reading Full Report</span>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-hover table-head-fixed text-nowrap">
                        <thead>
                            <th>Account #</th>
                            <th>Sequence #</th>
                            <th>Name</th>
                            <th>Timestamp</th>
                            <th class="text-right">Prev</th>
                            <th class="text-right">Pres</th>
                            <th class="text-right">Current <br>Kwh Used</th>
                            <th class="text-right">Daily <br>Average</th>
                            <th class="text-right">Previous <br>Kwh Used</th>
                            <th class="text-right">% Inc/Dec</th>
                            <th class="text-right"># of Days</th>
                            <th class="text-right">Meter #</th>
                            <th>Field <br>Findings</th>
                            <th>Remarks</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($readingReport as $item)
                                @php
                                    // NUMBER OF DAYS
                                    $noOfDays = Readings::getDaysBetweenDates($item->PrevReadingTimestamp, $item->ReadingTimestamp);

                                    // COMPUTE PERCENTAGE
                                    $currentKwh = $item->CurrentKwh != null ? $item->CurrentKwh : (round(floatval($item->KwhUsed) - floatval($item->PrevReading), 2));
                                    $currentKwh = floatval($currentKwh);
                                    $prevKwh = $item->PrevKwh != null ? $item->PrevKwh : 0;
                                    $diffKwh = $currentKwh - $prevKwh;
                                    if ($currentKwh != 0) {
                                        $percentage = $diffKwh/$currentKwh;
                                    } else {
                                        $percentage = 0;
                                    }
                                    $percentage = $item->CurrentKwh != null ? round($percentage, 4) : 0;                                    

                                @endphp
                                <tr title="{{ $item->CurrentKwh != null ? '' : 'No Bill' }}">
                                    <td><i class="fas {{ $item->CurrentKwh != null ? 'fa-check-circle ico-tab text-success' : 'fa-exclamation-circle ico-tab text-danger' }}"></i><a href="{{ route('serviceAccounts.show', [$item->AccountId]) }}">{{ $item->OldAccountNo }}</a></td>
                                    <td>{{ $item->SequenceCode }}</td>
                                    <td>{{ $item->ServiceAccountName }}</td>
                                    <td>{{ date('Y-m-d h:i:s A', strtotime($item->ReadingTimestamp )) }}</td>
                                    <td class="text-right">{{ $item->PrevReading }}</td>
                                    <td class="text-right">{{ $item->KwhUsed }}</td>
                                    @if ($item->CurrentKwh != null)
                                        <td class="{{ $item->CurrentKwh != null ? 'text-success' : 'text-danger' }} text-right">{{ $item->CurrentKwh != null ? $item->CurrentKwh : (round(floatval($item->KwhUsed) - floatval($item->PrevReading), 2)) }}</td>
                                    @else
                                        <th class="{{ $item->CurrentKwh != null ? 'text-success' : 'text-danger' }} text-right">{{ $item->CurrentKwh != null ? $item->CurrentKwh : (round(floatval($item->KwhUsed) - floatval($item->PrevReading), 2)) }}</th>
                                    @endif
                                    <td class="text-right">{{ $item->CurrentKwh != null ? round(floatval($item->CurrentKwh) / floatval($noOfDays), 2) : '-' }}</td>
                                    <td class="text-right">{{ $item->PrevKwh != null ? $item->PrevKwh : '0' }}</td>
                                    @if ($item->CurrentKwh != null)
                                        <td class="text-right {{ floatval($percentage) < 0 ? 'text-success' : 'text-danger' }}"><i class="fas {{ floatval($percentage) < 0 ? 'fa-caret-down' : 'fa-caret-up' }} ico-tab"></i>{{ $item->CurrentKwh != null ? ($percentage * 100) . '%' : '-' }}</td>
                                    @else
                                        <td class="text-right">-</td>
                                    @endif                                    
                                    <td class="text-right">{{ $noOfDays }}</td>
                                    <td class="text-right">{{ $item->MeterNumber }}</td>
                                    <td>{{ $item->FieldStatus }}</td>
                                    <td>{{ $item->Notes }}</td>
                                    <td class="text-right">
                                        @if ($item->CurrentKwh == null)
                                            <a href="{{ route('bills.zero-readings-view', [$item->id]) }}"><i class="fas fa-pen"></i></a>
                                        @endif                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection