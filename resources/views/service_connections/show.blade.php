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
                    {{-- @if (empty($timeFrame) | $timeFrame == null)
                        <span><i>Timeframe not recorded</i></span>
                    @else
                        <span class="badge-lg bg-warning"><strong>{{ $timeFrame->first()==null ? 'Timeframe not recorded' : $timeFrame->first()->Status; }}</strong></span>
                    @endif --}}
                    
                    <span class="badge-lg bg-warning"><strong>{{ $serviceConnections->Status }}</strong></span>
                </div> 
                <div class="col-sm-6">
                    @if (Auth::user()->hasAnyRole(['Administrator'])) 
                        <button id="override" class="btn btn-danger btn-sm float-right" style="margin-left: 10px;">Override Status</button>
                        <select name="Status" id="Status" class="form-control form-control-sm float-right" style="width: 200px;">
                            <option {{ $serviceConnections->Status=="Approved" ? 'selected' : '' }} value="Approved">Approved</option>
                            <option {{ $serviceConnections->Status=="Approved For Change Name" ? 'selected' : '' }} value="Approved For Change Name">Approved For Change Name</option>
                            <option {{ $serviceConnections->Status=="Closed" ? 'selected' : '' }} value="Closed">Closed</option>
                            <option {{ $serviceConnections->Status=="Downloaded by Crew" ? 'selected' : '' }} value="Downloaded by Crew">Downloaded by Crew</option>
                            <option {{ $serviceConnections->Status=="Energized" ? 'selected' : '' }} value="Energized">Energized</option>
                            <option {{ $serviceConnections->Status=="For Inspection" ? 'selected' : '' }} value="For Inspection">For Inspection</option>
                            <option {{ $serviceConnections->Status=="Forwarded To Planning" ? 'selected' : '' }} value="Forwarded To Planning">Forwarded To Planning</option>
                        </select>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="row">
            <div class="col-md-4 col-lg-4">
                {{-- APPLICATON DETAILS --}}
                <div class="card {{ $serviceConnections->LoadCategory == 'above 5kVa' ? 'card-danger' : 'card-primary' }} card-outline shadow-none">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img id="prof-img" class="profile-user-img img-fluid img-circle" src="" alt="User profile picture">
                        </div>

                        <h3 title="Go to Membership Profile" class="profile-username text-center"><a href="{{ $serviceConnections->MemberConsumerId != null ? route('memberConsumers.show', [$serviceConnections->MemberConsumerId]) : '' }}">{{ $serviceConnections->ServiceAccountName }}</a></h3>
                        <p class="text-muted text-center">
                            {{ $serviceConnections->id }} ({{ $serviceConnections->AccountApplicationType }}) 
                            @if ($serviceConnections->ORNumber != null)
                                <span class="badge badge-success">Paid</span>
                            @endif
                        </p>

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

                        <strong><i class="fas fa-receipt mr-1"></i> OR Number</strong>
                        <p class="text-muted">{{ $serviceConnections->ORNumber }} ({{ $serviceConnections->ORDate != null ? date('M d, Y', strtotime($serviceConnections->ORDate)) : '' }})</p>

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

                            @if ($serviceConnections->ConnectionApplicationType == 'Change Name' && $serviceConnections->ORNumber != null)
                                @if ($serviceConnections->Status == 'Approved For Change Name')
                                    
                                @else
                                    <a href="{{ route('serviceConnections.approve-change-name', [$serviceConnections->id]) }}" class="text-success" title="Approve Change Name and Forward to Billing Analyst" style="margin-left: 20px;">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                @endif                                
                            @endif

                            <a href="{{ route('serviceConnections.move-to-trash', [$serviceConnections->id]) }}" class="text-danger float-right" title="Move to trash">
                                <i class="fas fa-trash"></i>
                            </a>                            
                        @endif
                        
                    </div>
                    <div class="card-footer">
                        @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Service Connection Assessor']))
                            @if ($serviceConnections->MemberConsumerId != null)
                                <a class="btn btn-success btn-xs" href="{{ route('memberConsumers.print-membership-application', [$serviceConnections->MemberConsumerId]) }}" title="Print Application Form">
                                    <i class="fas fa-print"> </i> Membership Form
                                </a>
                                <a href="{{ route('memberConsumers.print-certificate', [$serviceConnections->MemberConsumerId]) }}" class="btn btn-xs btn-warning" title="Print Certificate"><i class="fas fa-print"></i>
                                    Certificate
                                </a>
                                <a class="btn btn-danger btn-xs" href="{{ route('serviceConnections.print-service-connection-contract', [$serviceConnections->id]) }}" class="text-danger" title="Print Service Connection Contract">
                                    <i class="fas fa-print"> </i> Contract
                                </a>  

                                <a class="btn btn-primary btn-xs" href="{{ route('serviceConnections.print-service-connection-application', [$serviceConnections->id]) }}" title="Print Service Connection Application">
                                    <i class="fas fa-print"> </i> Application Form
                                </a>

                                @if ($serviceConnections->ORNumber != null && ($serviceConnections->Status=='Approved' || $serviceConnections->Status=='Not Energized' || $serviceConnections->Status=='Energized'))
                                    <a class="btn btn-success btn-xs" href="{{ route('serviceConnections.print-order', [$serviceConnections->id]) }}" title="Print Turn On Order">
                                        <i class="fas fa-print"> </i> Turn On Order
                                    </a>
                                @endif                                
                            @else
                                <a class="btn btn-danger btn-xs" href="{{ route('serviceConnections.print-contract-without-membership', [$serviceConnections->id]) }}" class="text-danger" title="Print Service Connection Contract">
                                    <i class="fas fa-print"> </i> Contract
                                </a>  

                                <a class="btn btn-primary btn-xs" href="{{ route('serviceConnections.print-application-form-without-membership', [$serviceConnections->id]) }}" title="Print Service Connection Application">
                                    <i class="fas fa-print"> </i> Application Form
                                </a>
                                @if ($serviceConnections->ORNumber != null && ($serviceConnections->Status=='Approved' || $serviceConnections->Status=='Not Energized' || $serviceConnections->Status=='Energized'))
                                    <a class="btn btn-success btn-xs" href="{{ route('serviceConnections.print-order', [$serviceConnections->id]) }}" title="Print Turn On Order">
                                        <i class="fas fa-print"> </i> Turn On Order
                                    </a>
                                @endif 
                            @endif                                           
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
                            @if ($serviceConnections->LoadCategory == 'above 5kVa' | $serviceConnections->LongSpan == 'Yes')
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

            $('#override').on('click', function(e) {
                e.preventDefault()
                var status = $('#Status').val()

                $.ajax({
                    url : "{{ route('serviceConnections.update-status') }}",
                    type : 'GET',
                    data : {
                        id : "{{ $serviceConnections->id }}",
                        Status : status
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        Swal.fire({
                            icon : 'error',
                            text : 'Error updating status'
                        })
                    }
                })
            })
        });
    </script>
@endpush
