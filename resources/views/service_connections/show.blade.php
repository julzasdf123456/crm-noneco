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
                        <span class="badge-lg bg-warning">{{ $timeFrame->last()==null ? 'Timeframe not recorded' : $timeFrame->last()->Status; }}</span>
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

                        <a href="{{ route('serviceConnections.edit', [$serviceConnections->id]) }}" class="text-warning" title="Edit service connection details"><i class="fas fa-user-edit"></i></a>
                    </div>
                </div>

                {{-- TIMELINE --}}
                <div class="card card-primary card-outline">
                    <div class="card-header border-0">
                        <h3 class="card-title">Timeframe</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>           
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="timeline timeline-inverse">
                            @if ($timeFrame == null)
                                <p><i>No timeframe recorded</i></p>
                            @else
                                @php
                                    $timeframeCount = count($timeFrame);
                                    $i = 0;
                                @endphp
                                @foreach ($timeFrame as $item)
                                    <div class="time-label">
                                        <span class="{{ $i+1==$timeframeCount ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->Status }}
                                        </span>
                                    </div>
                                    <div>
                                    <i class="fas fa-info-circle bg-primary"></i>
            
                                    <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> {{ date('H:i A', strtotime($item->created_at)) }}</span>
                
                                            <h3 class="timeline-header"><a href="">{{ date('F d, Y', strtotime($item->created_at)) }}</a> by {{ $item->name }}</h3>
                                        </div>
                                    </div>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#verification" data-toggle="tab"><i class="fas fa-clipboard-check ico-tab"></i>Verification</a></li>
                            <li class="nav-item"><a class="nav-link" href="#metering" data-toggle="tab"><i class="fas fa-charging-station ico-tab"></i>Metering and Transformer</a></li>
                            <li class="nav-item"><a class="nav-link" href="#invoice" data-toggle="tab"><i class="fas fa-shopping-cart ico-tab"></i>Payment Invoice</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="verification">
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
