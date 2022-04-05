@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>OR Cancellation Confirmation</h4>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-default float-right"
                    href="{{ route('oRCancellations.index') }}">
                    Back
                </a>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-10 offset-lg-1 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <small class="float-right">Transaction Date: {{ date('F d, Y', strtotime($paidBill->PostingDate)) }}</small>
                        </div>
                        <!-- /.col -->
                    </div>
                    <br>
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>{{ $account->ServiceAccountName }}</strong><br>
                                Account ID: {{ $account->id }}<br>
                                Account No: {{ $account->OldAccountNo }}<br>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>OR No: {{ $paidBill->ORNumber }}</strong><br>
                                OR Date: {{ date('F d, Y', strtotime($paidBill->ORDate)) }}<br>
                                Bill No: {{ $paidBill->BillNumber }}<br>
                            </address>
                        </div>

                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong>Teller: {{ $user != null ? $user->name : '-' }}</strong><br>
                                Amount: <h1 class="text-success">P {{ number_format($paidBill->NetAmount, 2) }}</h1>
                            </address>
                        </div>

                        <div class="col-sm-12">
                            <div class="divider"></div>
                            Reason of Cancellation: <strong><i>{{ $paidBill->Notes }}</i></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('oRCancellations.approve-bills-or-cancellation', [$orCancellations->id]) }}" class="btn btn-warning">Approve Cancellation</a>
                <a href="" class="btn btn-default float-right">Decline</a>
            </div>
        </div>
    </div>
</div>
@endsection
