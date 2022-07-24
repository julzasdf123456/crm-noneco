@php
    use App\Models\ServiceAccounts;
    use App\Models\MemberConsumers;
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <span>
                    <h4 style="display: inline; margin-right: 15px;">Grouped Billing {{ $memberConsumer != null ? (' - ' . MemberConsumers::serializeMemberName($memberConsumer)) : '' }}</h4>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="row">
    {{-- ACCOUNTS --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header border-0">
                <span class="card-title">Accounts in this Group</span>
                <div class="card-tools">
                    <a href="{{ route('bills.create-group-billing-step-two', [$memberConsumer->ConsumerId]) }}" class="btn btn-primary btn-sm">Edit This Group</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-borderd table-sm">
                    <thead>
                        <th>Account ID</th>
                        <th>Account No</th>
                        <th>Consumer Name</th>
                        <th>Consumer Address</th>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $item)
                            <tr>
                                <td><a href="{{ route('serviceAccounts.show', [$item->id]) }}">{{ $item->id }}</a></td>
                                <td>{{ $item->OldAccountNo }}</td>
                                <td>{{ $item->ServiceAccountName }}</td>
                                <td>{{ ServiceAccounts::getAddress($item) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <p>No. of Consumers: <strong>{{ count($accounts) }}</strong></p>
            </div>
        </div>
    </div>

    {{-- LEDGERS --}}
    <div class="col-lg-5">
        <div class="card" style="height: 80vh;">
            <div class="card-header border-0">
                <span class="card-title">Ledgers</span>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <th>Billing Month</th>
                        <th>No. of Bills</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($ledgers as $item)
                            <tr>
                                <td>{{ date('F d, Y', strtotime($item->ServicePeriod)) }}</td>
                                <td>{{ $item->BillCount }}</td>
                                <td class="text-right">
                                    <a title="Print Bills in List Form" href="{{ route('serviceAccounts.print-group-bills-list', [$item->ServicePeriod, $memberConsumer->ConsumerId]) }}" class="text-success ico-tab"><i class="fas fa-clipboard-list"></i></a>
                                    <a title="Print Bills in New Format" href="{{ route('bills.print-bulk-bill-new-format-group', [$item->ServicePeriod, $memberConsumer->ConsumerId]) }}" class="text-primary ico-tab"><i class="fas fa-print"></i></a>
                                    <a title="Print Bills in Old Format" href="{{ route('bills.print-bulk-bill-old-format-group', [$item->ServicePeriod, $memberConsumer->ConsumerId]) }}" class="text-warning ico-tab"><i class="fas fa-print"></i></a>
                                    <a title="Print Statement Summary" href="{{ route('bills.print-group-billing', [$memberConsumer->ConsumerId, $item->ServicePeriod]) }}" class="text-info ico-tab"><i class="fas fa-print"></i></a>
                                    <a title="View Details" href="{{ route('bills.grouped-billing-bill-view', [$memberConsumer->ConsumerId, $item->ServicePeriod]) }}"><i class="fas fa-eye"></i></a>
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