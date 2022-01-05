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
                                </lord-icon>Billing History</a></li>
                            <li class="nav-item"><a class="nav-link" href="#disco-hist" data-toggle="tab">
                                <i class="fas fa-unlink"></i>
                                </lord-icon>Disconnection History</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="technical">
                               @include('service_accounts.tab_technical')
                            </div>

                            <div class="tab-pane" id="billing-hist">
                                
                            </div>

                            <div class="tab-pane" id="disco-hist">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
