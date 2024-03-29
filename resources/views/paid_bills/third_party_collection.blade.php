@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>Third-Party Payment Console</h4>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-danger btn-sm float-right" style="margin-left: 10px;"
                    href="{{ route('paidBills.third-party-collection-api-dcr') }}">
                    <i class="fas fa-wifi ico-tab"></i>API Payments
                </a>

                <a class="btn btn-primary btn-sm float-right"
                    href="{{ route('paidBills.upload-third-party-collection') }}">
                    <i class="fas fa-file-upload ico-tab"></i>Upload Collection
                </a>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-none" style="height: 80vh">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-check-circle ico-tab-mini"></i>Recent Third-Party Collection Postings</span>

                <div class="card-tools">
                    <form action="" class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control form-control-sm mb-2" style="margin-right: 3px;" id="Day" name="Day" placeholder="Select Date" value="{{ isset($_GET['Day']) ? $_GET['Day'] : date('Y-m-d') }}">
                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#Day').datetimepicker({
                                        format: 'YYYY-MM-DD',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div>
                        
                        <button type="submit" class="btn btn-sm btn-warning mb-2"><i class="fas fa-filter ico-tab-mini"></i>Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-hover table-bordered">
                    <thead>
                        <th class="text-center">Collection POS</th>
                        <th class="text-center">Transaction Date</th>
                        <th class="text-center">Pendings</th>
                        <th class="text-center">Deposited<br>Double Payments</th>
                        <th class="text-center">Posted</th>
                        <th class="text-center">Total Payments</th>
                    </thead>
                    <tbody>
                        @foreach ($transacted as $item)
                            <tr>
                                <th>
                                    <a href="{{ route('paidBills.tcp-upload-validator', [$item->Notes]) }}">{{ $item->ObjectSourceId }}</a>
                                    <a class="float-right" title="View DCR" href="{{ route('paidBills.third-party-collection-dcr', [$item->ObjectSourceId, $date, $item->Notes, $item->PostingDate!=null ? $item->PostingDate : date('Y-m-d')]) }}"><i class="fas fa-share"></i></a>
                                </th>
                                <td>{{ isset($_GET['Day']) ? date('M d, Y', strtotime($_GET['Day'])) : date('M d, Y') }}</td>
                                <td class='text-right'>{{ $item->Pendings }} <span class="text-danger">({{ $item->PendingsSum != null ? number_format($item->PendingsSum, 2) : '0.0' }})</span>
                                </td>
                                <td class='text-right'>{{ $item->DoublePayments }} <span class="text-danger">({{ $item->DoublePaymentsSum != null ? number_format($item->DoublePaymentsSum, 2) : '0.0' }})</span></td>
                                <td class='text-right'>{{ $item->Posted }} <span class="text-success">({{ $item->PostedSum != null ? number_format($item->PostedSum, 2) : '0.0' }})</span></td>
                                <td class='text-right'>{{ $item->TotalPayments }} <span class="text-primary">({{ $item->TotalPaymentsSum != null ? number_format($item->TotalPaymentsSum, 2) : '0.0' }})</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-none" style="height: 80vh;">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-exclamation-circle ico-tab-mini"></i>Unposted Third-Party Collections</span>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm">
                    <thead>
                        <th>Series No.</th>
                        <th>No. of Payments</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($unposted as $item)
                            <tr>
                                <td>{{ $item->Notes }}</td>
                                <td>{{ $item->NoOfPayments }}</td>
                                <td class="text-right">
                                    <a href="{{ route('paidBills.tcp-upload-validator', [$item->Notes]) }}"><i class="fas fa-eye"></i></a>
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