<?php

use App\Models\ServiceConnections;
use Illuminate\Support\Facades\Auth;

?>

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if (empty($timeFrame) | $timeFrame == null)
                        <span><i>Timeframe not recorded</i></span>
                    @else
                        <span class="badge-lg bg-warning"><strong>{{ $timeFrame->first()==null ? 'Timeframe not recorded' : $timeFrame->first()->Status; }}</strong></span>
                    @endif
                    
                </div> 
                <div class="col-sm-6">
                    <a class="btn btn-default float-right"
                       href="{{ route('serviceConnections.index') }}">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="row">
            <div class="col-md-4 col-lg-4">
                {{-- APPLICATON DETAILS --}}
                <div class="card {{ $serviceConnections->LoadCategory == 'above 5kVa' ? 'card-danger' : 'card-primary' }} card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img id="prof-img" class="profile-user-img img-fluid img-circle" src="" alt="User profile picture">
                        </div>

                        <h3 title="Go to Membership Profile" class="profile-username text-center"><a href="{{ route('memberConsumers.show', [$serviceConnections->MemberConsumerId]) }}">{{ $serviceConnections->ServiceAccountName }}</a></h3>
                        <p class="text-muted text-center">{{ $serviceConnections->id }} ({{ $serviceConnections->AccountApplicationType }})</p>

                        <hr>

                        <strong><i class="far fa-calendar mr-1"></i> Date of Application</strong>
                        <p class="text-muted">{{ date('F d, Y', strtotime($serviceConnections->DateOfApplication)) }}</p>

                        <hr>                        

                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                        <p class="text-muted">{{ ServiceConnections::getAddress($serviceConnections) }}</p>

                        <hr>

                        <strong><i class="fas fa-phone mr-1"></i> Contact Info</strong>
                        <p class="text-muted">{{ ServiceConnections::getContactInfo($serviceConnections) }}</p>

                        <hr>

                        <strong><i class="fas fa-search-plus mr-1"></i> Account Count</strong>
                        <p class="text-muted">{{ $serviceConnections->AccountCount }}</p>

                        <hr>

                        <strong><i class="fas fa-code-branch mr-1"></i> Account Type</strong>
                        <p class="text-muted">{{ $serviceConnections->AccountType }}</p>

                        <hr>

                        <strong><i class="fas fa-code-branch mr-1"></i> Application Type</strong>
                        <p class="text-muted">{{ $serviceConnections->ConnectionApplicationType }}</p>

                        <hr>

                        <strong><i class="fas fa-file-alt mr-1"></i> Notes</strong>
                        <p class="text-muted">{{ $serviceConnections->Notes}}</p>

                        <hr>

                        <strong><i class="fas fa-warehouse mr-1"></i> Office Registered</strong>
                        <p class="text-muted">{{ $serviceConnections->Office}}</p>

                        @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Service Connection Assessor'])) 
                            <a href="{{ route('serviceConnections.edit', [$serviceConnections->id]) }}" class="text-warning" title="Edit service connection details">
                                <i class="fas fa-pen"></i>
                            </a>

                            <a href="{{ route('serviceConnections.move-to-trash', [$serviceConnections->id]) }}" class="text-danger float-right" title="Move to trash">
                                <i class="fas fa-trash"></i>
                            </a>                            
                        @endif
                        
                    </div>
                </div> 
            </div>

            <div class="col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#logs" data-toggle="tab">
                                <i class="fas fa-info-circle"></i>
                                Details and Logs</a></li>
                            <li class="nav-item"><a class="nav-link" href="#verification" data-toggle="tab">
                                <i class="fas fa-clipboard-check"></i>
                                </lord-icon>Verification</a></li>
                            <li class="nav-item"><a class="nav-link" href="#metering" data-toggle="tab">
                                <i class="fas fa-tachometer-alt"></i>
                                Metering and Transformer</a></li>
                            <li class="nav-item"><a class="nav-link" href="#invoice" data-toggle="tab">
                                <i class="fas fa-file-invoice-dollar"></i>
                                Payment Invoice</a></li>
                            @if ($serviceConnections->LoadCategory == 'above 5kVa')
                            <li class="nav-item"><a class="nav-link" href="#bom" data-toggle="tab">
                                <i class="fas fa-toolbox"></i>
                                Bill of Materials</a></li>
                            @endif
                            <li class="nav-item"><a class="nav-link" href="#photos" data-toggle="tab">
                                <i class="fas fa-file-image"></i>
                                Photos</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="logs">
                                @include('service_connections.details')
                            </div>

                            <div class="tab-pane" id="verification">
                                @include('service_connections.verification')
                            </div>

                            <div class="tab-pane" id="metering">
                                @include('service_connections.metering')
                            </div>

                            <div class="tab-pane" id="invoice">
                                @include('service_connections.invoice')
                            </div>
                            
                            <div class="tab-pane" id="bom">
                                @include('service_connections.bom_details')
                            </div>

                            <div class="tab-pane" id="photos">
                                @include("service_connections.photos")
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // LOAD IMAGE
            $.ajax({
                url : '/member_consumer_images/get-image/' + "{{ $serviceConnections->MemberConsumerId }}",
                type : 'GET',
                success : function(result) {
                    var data = JSON.parse(result)
                    $('#prof-img').attr('src', data['img'])
                },
                error : function(error) {
                    console.log(error);
                }
            })
        });
    </script>
@endpush
