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
                    <h4>Disconnection Report Per BAPA</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        {{-- FORM --}}
        <div class="col-lg-12">
            <div class="card shadow-none">
                <div class="card-body">
                    {!! Form::open(['route' => 'readings.disco-per-bapa', 'method' => 'GET']) !!}
                    <div class="row">
                        <div class="form-group col-lg-2">
                            <label for="ServicePeriod">Billing Month</label>
                            <select name="ServicePeriod" id="ServicePeriod" class="form-control form-control-sm">
                                @for ($i = 0; $i < count($months); $i++)
                                    <option value="{{ $months[$i] }}" {{ isset($_GET['ServicePeriod']) && $_GET['ServicePeriod']==$months[$i] ? 'selected' : '' }}>{{ date('F Y', strtotime($months[$i])) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group col-lg-2">
                            <label for="Bapa">BAPA</label>
                            <select name="Bapa" id="Bapa" class=" select2 form-control form-control-sm">
                                <option value="All">All</option>
                                @foreach ($bapas as $item)
                                    <option value="{{ $item->OrganizationParentAccount }}" {{ isset($_GET['Bapa']) && $_GET['Bapa']==$item->OrganizationParentAccount ? 'selected' : '' }}>{{ $item->OrganizationParentAccount }}</option>
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

        {{-- RESULTS --}}
        <div class="col-lg-12">
            <div class="card shadow-none">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <th>Account No</th>
                            <th>Account Name</th>
                            <th>Account Status</th>
                            <th>BAPA</th>
                            <th>Bill Amnt.</th>
                            <th>Last Reading</th>
                            <th>Date Disconnected</th>
                            <th>Date Reconnected</th>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td><a href="{{ route('serviceAccounts.show', [$item->AccountNumber]) }}">{{ $item->OldAccountNo }}</a></td>
                                    <td>{{ $item->ServiceAccountName }}</td>
                                    <td>{{ $item->AccountStatus }}</td>
                                    <td>{{ $item->OrganizationParentAccount }}</td>
                                    <td class="text-right">{{ is_numeric($item->NetAmount) ? number_format($item->NetAmount, 2) : $item->NetAmount }}</td>
                                    <td class="text-danger text-right"><strong>{{ $item->BillId }}</strong></td>
                                    <td>{{ $item->DateDisconnected != null ? date('M d, Y', strtotime($item->DateDisconnected)) : '' }}</td>
                                    <td>{{ $item->DateReconnected != null ? date('M d, Y', strtotime($item->DateReconnected)) : '' }}</td>
                                </tr>
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
            $('#printBtnReport').on('click', function(e) {
                e.preventDefault()
                window.location.href = "{{ url('/readings/print-disco-per-mreader') }}" + "/"  + $('#ServicePeriod').val() + "/" +  $('#MeterReader').val()
            })
        })
    </script>
@endpush