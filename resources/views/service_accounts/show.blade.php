@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Service Account Management Console</h4>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right"
                       href="{{ route('serviceAccounts.index') }}">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="row">
            <div class="col-lg-4 col-md-5">
                @include('service_accounts.show_account_info')
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#technical" data-toggle="tab">
                                <i class="fas fa-car-battery"></i>
                                Technical Info</a></li>
                            <li class="nav-item"><a class="nav-link" href="#billing-hist" data-toggle="tab">
                                <i class="fas fa-file-invoice"></i>
                                Billing History</a></li>
                            <li class="nav-item"><a class="nav-link" href="#disco-hist" data-toggle="tab">
                                <i class="fas fa-unlink"></i>
                                Disconnection History</a></li>
                            <li class="nav-item"><a class="nav-link" href="#arrears" data-toggle="tab">
                                <i class="fas fa-receipt"></i>
                                Arrears</a></li>
                            <li class="nav-item"><a class="nav-link" href="#prepayments" data-toggle="tab">
                                <i class="fas fa-piggy-bank"></i>
                                Pre-Payments/Deposits</a></li>
                            <li class="nav-item"><a class="nav-link" href="#ticket-hist" data-toggle="tab">
                                <i class="fas fa-exclamation-triangle"></i>
                                Ticket History</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="technical">
                               @include('service_accounts.tab_technical')
                            </div>

                            <div class="tab-pane" id="billing-hist">
                                @include('service_accounts.tab_billing_history')
                            </div>

                            <div class="tab-pane" id="disco-hist">
                                @include('service_accounts.tab_disconnection_history')
                            </div>

                            <div class="tab-pane" id="arrears">
                                @include('service_accounts.tab_arrears')
                            </div>
                            <div class="tab-pane" id="prepayments">
                                @include('service_accounts.tab_prepayments')
                            </div>
                            <div class="tab-pane" id="ticket-hist">
                                @include('service_accounts.tab_ticket_history')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
