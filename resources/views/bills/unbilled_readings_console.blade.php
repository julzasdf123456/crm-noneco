@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Unbilled Readings Console - {{ date('F Y', strtotime($servicePeriod)) }}</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        @include('flash::message')

        <div class="clearfix"></div>
        
        <div class="row">
            {{-- ZERO READINGS --}}
            <div class="col-lg-6">
                <div class="card" style="height: 80vh;">
                    <div class="card-header border-0">
                        <span class="card-title">Zero Readings ({{ count($zeroReadings) }})</span>

                        <div class="card-tools">
                            <a href="#" class="btn btn-sm btn-primary">Average All</a>
                        </div>
                    </div>

                    <div class="card-body table-responsive px-0">
                        <table class="table table-sm table-hover">
                            <thead>
                                <th>Account No</th>
                                <th>Consumer Name</th>
                                <th>Status</th>
                                <th width="8%"></th>
                            </thead>
                            <tbody>
                                @if (count($zeroReadings) > 0)
                                    @foreach ($zeroReadings as $item)
                                        <tr>
                                            <td><a href="{{ route('serviceAccounts.show', [$item->AccountNumber]) }}">{{ $item->AccountNumber }}</a></td>
                                            <td>{{ $item->ServiceAccountName }}</td>
                                            <td>{{ $item->FieldStatus }}</td>
                                            <td>
                                                <a href="{{ route('bills.zero-readings-view', [$item->id]) }}" class="btn btn-link btn-sm"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- DISCONNECTED ACCOUNTS --}}
            <div class="col-lg-6">
                <div class="card" style="height: 80vh;">
                    <div class="card-header border-0">
                        <span class="card-title">Disconnected Account Readings</span>
                    </div>

                    <div class="card-body table-responsive px-0">
                        <table class="table table-sm table-hover">
                            <thead>
                                <th>Account No</th>
                                <th>Consumer Name</th>
                                <th>Status</th>
                                <th>Kwh Used</th>
                            </thead>
                            <tbody>
                                @if (count($disconnectedReadings) > 0)
                                    @foreach ($disconnectedReadings as $item)
                                        <tr>
                                            <td><a href="{{ route('serviceAccounts.show', [$item->AccountNumber]) }}">{{ $item->AccountNumber }}</a></td>
                                            <td>{{ $item->ServiceAccountName }}</td>
                                            <td>{{ $item->FieldStatus }}</td>
                                            <td>{{ $item->KwhUsed }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection