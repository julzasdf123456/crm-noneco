@php
    use App\Models\ServiceAccounts;
    use App\Models\PaidBills;
    use App\Models\PaidBillsDetails;
    use App\Models\User;
    use App\Models\TransactionIndex;
    use App\Models\TransactionDetails;
    use App\Models\TransacionPaymentDetails;
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h4>Payment Details - {{ $paymentType }}</h4>
            </div>
        </div>
    </div>
</section>

<div class="content">
    @if ($paymentType == 'BILLS PAYMENT')
        {{-- PAIDBILLS FORM --}}
        @php
            $paidBill = PaidBills::find($id);
            if ($paidBill != null) {
                $account = DB::table('Billing_ServiceAccounts')
                    ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                    ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                    ->select('Billing_ServiceAccounts.id',
                            'Billing_ServiceAccounts.ServiceAccountName',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'Billing_ServiceAccounts.AccountCount',
                            'Billing_ServiceAccounts.Purok',
                            'Billing_ServiceAccounts.AccountType',
                            'Billing_ServiceAccounts.AccountStatus',
                            'Billing_ServiceAccounts.AreaCode',
                            'Billing_ServiceAccounts.Organization',
                            'Billing_ServiceAccounts.GroupCode',
                            'Billing_ServiceAccounts.SeniorCitizen',
                            'Billing_ServiceAccounts.Evat5Percent',
                            'Billing_ServiceAccounts.Ewt2Percent',
                            'Billing_ServiceAccounts.OldAccountNo',
                            'CRM_Towns.Town',
                            'CRM_Barangays.Barangay')
                    ->where('Billing_ServiceAccounts.id', $paidBill->AccountNumber)
                    ->first();

                $paidBillDetails = PaidBillsDetails::where('ORNumber', $paidBill->ORNumber)->orderBy('PaymentUsed')->get();

                $user = User::find($paidBill->UserId);
            } else {
                $account = null;
                $paidBillDetails = null;
                $user = null;
            }
            
        @endphp
        <div class="px-5">
            <div class="row">
                <div class="col-lg-5">
                    <p class="text-muted">Payor</p>
                    <span><strong><a href="{{ $account != null ? (route('serviceAccounts.show', [$account->id])) : '' }}">{{ $account != null ? $account->ServiceAccountName : '-' }}</a></strong></span><br>
                    <span>{{ $account != null ? $account->OldAccountNo : '-' }}</span><br>
                    <span>{{ $account != null ? ServiceAccounts::getAddress($account) : '' }}</span><br>
                    <span>{{ $account != null ? $account->AccountType : '-' }}</span><br>
                </div>

                <div class="col-lg-4">
                    <span class="text-muted">OR Number</span><br>
                    <span><strong>{{ $paidBill->ORNumber }}</strong></span><br>
                    <span class="text-muted">OR Date</span><br>
                    <span><strong>{{ date('F d, Y', strtotime($paidBill->ORDate)) }}</strong></span><br>
                    <span class="text-muted">Teller</span><br>
                    <span><strong>{{ $user != null ? $user->name : '-' }}</strong></span><br>
                </div>

                <div class="col-lg-3">
                    <span class="text-muted float-right">Total Amount Paid</span><br>
                    <h2 class="text-primary text-right"><strong>₱ {{ number_format($paidBill->NetAmount, 2) }}</strong></h2>
                    <a href="{{ $paidBill->ObjectSourceId != null ? (route('bills.show', [$paidBill->ObjectSourceId])) : '' }}" class="btn btn-xs btn-primary float-right">View Bill</a>
                </div>

                {{-- Paidbill Details table --}}
                <div class="col-lg-12">
                    <br>
                    <span class="text-muted">Payment Details</span><br>
                    <table class="table table-hover">
                        <thead>
                            <th>Transaction ID</th>
                            <th class="text-right">Amount Paid</th>
                            <th>Payment Used</th>
                            <th>Check No.</th>
                            <th>Bank</th>
                        </thead>
                        <tbody>
                            @if ($paidBillDetails != null)
                                @foreach ($paidBillDetails as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td class="text-right">₱ {{ number_format($item->Amount, 2) }}</td>
                                        <td class="{{ $item->PaymentUsed == 'Cash' ? 'text-info' : 'text-success' }}"><strong>{{ $item->PaymentUsed }}</strong></td>
                                        <td>{{ $item->CheckNo }}</td>
                                        <td>{{ $item->Bank }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        {{-- OTHER TRANSACTIONS FORM --}}
        @php
            $transactionIndex = TransactionIndex::find($id);

            if ($transactionIndex != null) {
                $user = User::find($transactionIndex->UserId);
                $transactionDetails = TransactionDetails::where('TransactionIndexId', $id)->get();
                $transactionLogs = TransacionPaymentDetails::where('ORNumber', $transactionIndex->ORNumber)->get();
            } else {
                $user = null;
                $transactionDetails = null;
                $transactionLogs = null;
            }
        @endphp
        <div class="px-5">
            <div class="row">
                <div class="col-lg-5">
                    <p class="text-muted">Invoice</p>
                    <span><strong>{{ $transactionIndex->PaymentTitle != null ? $transactionIndex->PaymentTitle : 'No Detail Provided' }}</strong></span><br>
                    <span class="text-muted">Payment Source: </span>{{ $transactionIndex->Source }}<br>
                    <span class="text-muted">Transaction ID: </span>{{ $transactionIndex->TransactionNumber }}<br>
                    <span class="text-muted">Payor: </span>{{ $transactionIndex->PayeeName != null ? $transactionIndex->PayeeName : '-' }}<br>
                </div>

                <div class="col-lg-4">
                    <span class="text-muted">OR Number</span><br>
                    <span><strong>{{ $transactionIndex->ORNumber }}</strong></span><br>
                    <span class="text-muted">OR Date</span><br>
                    <span><strong>{{ date('F d, Y', strtotime($transactionIndex->ORDate)) }}</strong></span><br>
                    <span class="text-muted">Teller</span><br>
                    <span><strong>{{ $user != null ? $user->name : '-' }}</strong></span><br>
                </div>

                <div class="col-lg-3">
                    <span class="text-muted float-right">Total Amount Paid</span><br>
                    <h2 class="text-primary text-right"><strong>₱ {{ number_format($transactionIndex->Total, 2) }}</strong></h2>
                    {{-- <a href="{{ $paidBill->ObjectSourceId != null ? (route('bills.show', [$paidBill->ObjectSourceId])) : '' }}" class="btn btn-xs btn-primary float-right">View Bill</a> --}}
                </div>

                {{-- PAYABLES --}}
                <div class="col-lg-5">
                    <br>
                    <span class="text-muted">Payables</span>
                    <table class="table table-hover table-sm">
                        <thead>
                            <th>Particulars</th>
                            <th>Account Code</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">VAT</th>
                            <th class="text-right">Total</th>
                        </thead>
                        <tbody>
                            @if ($transactionDetails != null)
                                @foreach ($transactionDetails as $item)
                                    <tr>
                                        <td>{{ $item->Particular }}</td>
                                        <td>{{ $item->AccountCode }}</td>
                                        <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->VAT, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->Total, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- PAYMENT DETAILS --}}
                <div class="col-lg-7">
                    <br>
                    <span class="text-muted">Payment Details</span>
                    <table class="table table-hover">
                        <thead>
                            <th>Transaction ID</th>
                            <th>Amount Paid</th>
                            <th>Payment Used</th>
                            <th>Check No.</th>
                            <th>Bank</th>
                        </thead>
                        <tbody>
                            @if ($transactionLogs != null)
                                @foreach ($transactionLogs as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td class="text-right">₱ {{ number_format($item->Amount, 2) }}</td>
                                        <td class="{{ $item->PaymentUsed == 'Cash' ? 'text-info' : 'text-success' }}"><strong>{{ $item->PaymentUsed }}</strong></td>
                                        <td>{{ $item->CheckNo }}</td>
                                        <td>{{ $item->Bank }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection