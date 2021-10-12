@php
    use App\Models\ServiceConnections;
@endphp
@extends('layouts.app')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <span>
                    <h4 style="display: inline; margin-right: 15px;">Pending Accounts</h4>
                    <i class="text-muted">Energized service connection accounts for activation</i>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-hover table-sm">
                <thead>
                    <th width="3%"></th>
                    <th>Account No.</th>
                    <th>Account Name</th>
                    <th>Account Address</th>
                    <th width="8%"></th>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($serviceConnections as $item)
                        <tr>
                            <th>{{ $i }}</th>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->ServiceAccountName }} ({{ $item->AccountCount }})<i class="fas fa-check-circle text-primary" style="font-size: .75em;"></i></td>
                            <td>{{ ServiceConnections::getAddress($item) }}</td>
                            <td class="text-right" >
                                <a href="{{ route('serviceAccounts.account-migration', [$item->id]) }}" title="Proceed activating {{ $item->ServiceAccountName }}" ><i class="fas fa-arrow-circle-right text-success"></i></a>
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


@endsection