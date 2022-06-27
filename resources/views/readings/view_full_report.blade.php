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
                        | <i class="text-muted">Meter Reader: </i> <strong>{{ $meterReader != null ? $meterReader->name : ($bapaName != null ? $bapaName : '') }} </strong>
                        | <i class="text-muted">Day: </i><strong>{{ $day }}</strong></h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        {{-- SUMMARY --}}
        <div class="col-lg-12">
            <div class="card shadow-none">
                {{-- <div class="card-header">
                    <span class="card-title"><i class="fas fa-check-circle ico-tab"></i>Summary</span>
                </div> --}}
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm">
                        <thead>
                            <td class="text-muted text-center">Disconnected<br>Readings</td>
                            <td class="text-muted text-center">Captured <br> Readings</td>
                            <td class="text-muted text-center">Stuck Up</td>
                            <td class="text-muted text-center">Change <br> Meters</td>
                            <td class="text-muted text-center">No Display</td>
                            <td class="text-muted text-center">Not in <br> Use</td>
                            <td class="text-muted text-center">Otder <br> Unbilled</td>
                            <td class="text-muted text-center">Total <br> Billed</td>
                            <td class="text-muted text-center">Total <br> Readings</td>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="text-center">{{ $summary != null ? $summary->Disconnected : '0' }}</th>
                                <th class="text-center">{{ $summary != null ? $summary->Captured : '0' }}</th>
                                <th class="text-center">{{ $summary != null ? $summary->StuckUp : '0' }}</th>
                                <th class="text-center">{{ $summary != null ? $summary->ChangeMeter : '0' }}</th>
                                <th class="text-center">{{ $summary != null ? $summary->NoDisplay : '0' }}</th>
                                <th class="text-center">{{ $summary != null ? $summary->NotInUse : '0' }}</th>
                                <th class="text-center">{{ $summary != null ? $summary->OtherUnbilled : '0' }}</th>
                                <th class="text-center text-primary">{{ $summary != null ? $summary->TotalBilled : '0' }}</th>
                                <th class="text-center text-success">{{ $summary != null ? number_format(intval($summary->Total) + intval($summary->Captured)) : '0' }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INDIVIDUALIZED --}}
        <div class="col-lg-12">
            <div class="card shadow-none" style="height: 70vh;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-list ico-tab"></i>Reading Full Report</span>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-hover table-head-fixed text-nowrap table-bordered">
                        <thead>
                            <th class="text-center">#</th>
                            <th class="text-center text-primary">Account #</th>
                            <th class="text-center">Sequence #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Acct. Status</th>
                            <th class="text-center">Timestamp</th>
                            <th class="text-center">Pres</th>
                            <th class="text-center">Prev</th>
                            <th class="text-center text-success">Current <br>Kwh Used</th>
                            <th class="text-center text-info">Previous <br>Kwh Used</th>
                            <th class="text-center">Daily <br>Average</th>
                            <th class="text-center">% <span class="text-danger">Inc</span>/<span class="text-success">Dec</span></th>
                            <th class="text-center"># of Days</th>
                            <th class="text-center">Meter #</th>
                            <th class="text-center">Field <br>Findings</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center"></th>
                        </thead>
                        <tbody>
                            @php
                                $i=1;
                            @endphp
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
                                    <td>{{ $i }}</td>
                                    @if ($item->AccountStatus == 'ACTIVE')
                                        <td><i class="fas {{ $item->CurrentKwh != null ? 'fa-check-circle ico-tab text-success' : 'fa-exclamation-circle ico-tab text-danger' }}"></i><a href="{{ route('serviceAccounts.show', [$item->AccountId]) }}">{{ $item->OldAccountNo }}</a></td>
                                    @else
                                        <td><i class="fas fa-info-circle ico-tab text-muted"></i><a href="{{ route('serviceAccounts.show', [$item->AccountId]) }}">{{ $item->OldAccountNo }}</a></td>
                                    @endif
                                    
                                    <td>{{ $item->SequenceCode }}</td>
                                    <td>{{ $item->ServiceAccountName }}</td>
                                    <td>{{ $item->AccountStatus }}</td>
                                    <td>{{ date('Y-m-d h:i:s A', strtotime($item->ReadingTimestamp )) }}</td>
                                    <td class="text-right">{{ $item->KwhUsed }}</td>
                                    <td class="text-right">{{ $item->PrevReading }}</td>
                                    @if ($item->CurrentKwh != null)
                                        <td class="{{ $item->CurrentKwh != null ? 'text-success' : 'text-danger' }} text-right">{{ $item->CurrentKwh != null ? $item->CurrentKwh : (round(floatval($item->KwhUsed) - floatval($item->PrevReading), 2)) }}</td>
                                    @else
                                        <th class="{{ $item->CurrentKwh != null ? 'text-success' : 'text-danger' }} text-right">{{ $item->CurrentKwh != null ? $item->CurrentKwh : (round(floatval($item->KwhUsed) - floatval($item->PrevReading), 2)) }}</th>
                                    @endif
                                    <td class="text-right text-info">{{ $item->PrevKwh != null ? $item->PrevKwh : '0' }}</td>
                                    <td class="text-right">{{ $item->CurrentKwh != null ? round(floatval($item->CurrentKwh) / floatval($noOfDays), 2) : '-' }}</td>
                                    @if ($item->CurrentKwh != null)
                                        <td class="text-right {{ floatval($percentage) < 0 ? 'text-success' : 'text-danger' }}"><i class="float-left fas {{ floatval($percentage) < 0 ? 'fa-caret-down' : 'fa-caret-up' }}"></i>{{ $item->CurrentKwh != null ? ($percentage * 100) . '%' : '-' }}</td>
                                    @else
                                        <td class="text-right">-</td>
                                    @endif                                    
                                    <td class="text-right">{{ $noOfDays }}</td>
                                    <td class="text-right">{{ $item->MeterNumber }}</td>
                                    <td>{{ $item->FieldStatus }}</td>
                                    <td>{{ $item->Notes }}</td>
                                    <td class="text-right">
                                        @if ($item->CurrentKwh == null && $item->AccountStatus == 'ACTIVE')
                                            <a href="{{ route('bills.zero-readings-view', [$item->id]) }}"><i class="fas fa-pen"></i></a>
                                        @endif                                        
                                    </td>
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