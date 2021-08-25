<?php

use App\Models\ServiceConnections;

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
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center">{{ $serviceConnections->ServiceAccountName }}</h3>
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

                        <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
                        <p class="text-muted">{{ $serviceConnections->Notes}}</p>

                        <a href="{{ route('serviceConnections.edit', [$serviceConnections->id]) }}" class="text-warning" title="Edit service connection details">
                            <lord-icon
                                src="https://cdn.lordicon.com/puvaffet.json"
                                trigger="loop"
                                delay="1500"
                                colors="primary:#121331,secondary:#08a88a"
                                stroke="100"
                                style="width:28px;height:28px">
                            </lord-icon>
                        </a>

                        <a href="{{ route('serviceConnections.move-to-trash', [$serviceConnections->id]) }}" class="text-danger float-right" title="Move to trash">
                            <lord-icon
                                src="https://cdn.lordicon.com/gsqxdxog.json"
                                trigger="loop"
                                delay="1500"
                                colors="primary:#c71f16,secondary:#c71f16"
                                stroke="100"
                                style="width:28px;height:28px">
                            </lord-icon>
                        </a>
                    </div>
                </div> 
            </div>

            <div class="col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#logs" data-toggle="tab">
                                <lord-icon
                                    src="https://cdn.lordicon.com/tdrtiskw.json"
                                    trigger="loop"
                                    delay="800"
                                    colors="primary:#ffffff,secondary:#ffffff"
                                    stroke="100"
                                    style="width:28px;height:28px">
                                </lord-icon>
                                Details and Logs</a></li>
                            <li class="nav-item"><a class="nav-link" href="#verification" data-toggle="tab">
                                <lord-icon
                                    src="https://cdn.lordicon.com/nocovwne.json"
                                    trigger="loop"
                                    delay="800"
                                    colors="primary:#ffffff,secondary:#ffffff"
                                    stroke="100"
                                    style="width:28px;height:28px">
                                </lord-icon>Verification</a></li>
                            <li class="nav-item"><a class="nav-link" href="#metering" data-toggle="tab">
                                <lord-icon
                                    src="https://cdn.lordicon.com/dbsklakl.json"
                                    trigger="loop"
                                    colors="primary:#ffffff,secondary:#ffffff"
                                    stroke="100"
                                    delay="800"
                                    style="width:28px;height:28px">
                                </lord-icon>
                                Metering and Transformer</a></li>
                            <li class="nav-item"><a class="nav-link" href="#invoice" data-toggle="tab">
                                <lord-icon
                                    src="https://cdn.lordicon.com/huwchbks.json"
                                    trigger="loop"
                                    stroke="100"
                                    delay="800"
                                    colors="primary:#ffffff,secondary:#ffffff"
                                    style="width:28px;height:28px">
                                </lord-icon>
                                Payment Invoice</a></li>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
