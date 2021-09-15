@extends('layouts.app')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Dashboard</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard v1</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    {{-- DASHBOARD COUNTER --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="for-inspection-count">...</h3>

                    <p>Received Applicants For Inspection</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="" id="show-received" data-toggle="modal" data-target="#approved-modal" class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- APPROVED --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success" title="Applicants that are approved during the inspection and are yet to pay the fees.">
                <div class="inner">
                    <h3 id="approved-count">...</h3>

                    <p>Approved Applicants</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="" id="show-approved" data-toggle="modal" data-target="#approved-modal"  class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- METERING DASH --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger" title="Already paid applications without metering data yet.">
                <div class="inner">
                    <h3 id="metering-unassigned">...</h3>

                    <p>Unassigned Meters</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.assigning') }}" class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3 id="energization-count">...</h3>

              <p>Applications For Energization</p>
            </div>
            <div class="icon">
              <i class="fas fa-charging-station"></i>
            </div>
            <a href="{{ route('serviceConnections.energization') }}" class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>     
        
    </div>

    {{-- OTHERS --}}
    <div class="row">
        <div class="col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Inspection Report</h3>
                        <a href="javascript:void(0);">View Report</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        <p class="d-flex flex-column">
                            <span>Number of Inspections</span>
                        </p>
                        <p class="ml-auto d-flex flex-column text-right">
                            <span class="text-muted">Current Uninspected Applications</span>
                        </p>
                    </div>
                    <!-- /.d-flex -->
  
                    <div class="position-relative mb-4"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas id="sales-chart" height="200" style="display: block; width: 764px; height: 200px;" width="764" class="chartjs-render-monitor"></canvas>
                    </div>
  
                    {{-- <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                            <i class="fas fa-square text-primary"></i> This year
                        </span>
    
                        <span>
                            <i class="fas fa-square text-gray"></i> Last year
                        </span>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- MODALS SECTION --}}
{{-- MODAL FOR APPROVED AND FOR PAYMENT --}}
<div class="modal fade" id="approved-modal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Approved Applicants</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="approved-table">
                    <thead>
                        <th>ID</th>
                        <th>Service Account Name</th>
                        <th>Address</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@push('page_scripts')
    <script type="text/javascript">
        // NEW CONNECTIONS DASH
        $.ajax({
            url : '/home/get-new-service-connections',
            type: "GET",
            dataType : "json",
            success : function(response) {
                // $.each(response, function(index, element) {
                //     console.log(response[index]['id']);
                // });
                console.log(response.length);
                $('#for-inspection-count').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });

        // APPROVED
        $.ajax({
            url : '/home/get-approved-service-connections',
            type: "GET",
            dataType : "json",
            success : function(response) {
                console.log(response.length);
                $('#approved-count').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });
        
        // METERING DASH
        // METERING DASH IS MOVED TO app.blade.php

        // FOR ENERGIZATION
        $.ajax({
            url : '/home/get-for-engergization',
            type: "GET",
            dataType : "json",
            success : function(response) {
                // $.each(response, function(index, element) {
                //     console.log(response[index]['id']);
                // });
                console.log(response.length);
                $('#energization-count').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });

        // LOAD CONTENT FOR APPROVED
        $('#show-approved').on('click', function() {
            $('#modal-title').text('Approved Applications');
            $.ajax({
                url : '/home/get-approved-service-connections',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    $('#approved-table tbody tr').remove();
                    $.each(response, function(index, element) {
                        console.log(response[index]['id']);
                        $('#approved-table tbody').append('<tr><td><a href="/serviceConnections/' + response[index]["id"] + '">' + response[index]['id'] + '</a></td><td>' + response[index]['ServiceAccountName'] + '</td><td>' + response[index]['Barangay'] + ', ' + response[index]['Town'] + '</td></tr>');
                    });
                },
                error : function(error) {
                    alert(error);
                }
            });
        });

        $('#show-received').on('click', function() {
            $('#modal-title').text('New Applications');
            $.ajax({
                url : '/home/get-new-service-connections',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    $('#approved-table tbody tr').remove();
                    $.each(response, function(index, element) {
                        console.log(response[index]['id']);
                        $('#approved-table tbody').append('<tr><td><a href="/serviceConnections/' + response[index]["id"] + '">' + response[index]['id'] + '</a></td><td>' + response[index]['ServiceAccountName'] + '</td><td>' + response[index]['Barangay'] + ', ' + response[index]['Town'] + '</td></tr>');
                    });
                },
                error : function(error) {
                    alert(error);
                }
            });
        });

        // CHART
        $.ajax({
            url : '/home/get-inspection-report',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    var labels = [];
                    var data = [];
                    $.each(response, function(index, element) {
                        labels.push(response[index]['name']);
                        data.push(response[index]['Total']);
                    });

                    // DISPLAY QUERY TO CHART
                    'use strict'

                    var ticksStyle = {
                        fontColor: '#495057',
                        fontStyle: 'bold'
                    }

                    var mode = 'index'
                    var intersect = true
                    var $salesChart = $('#sales-chart')
                    // eslint-disable-next-line no-unused-vars
                    var salesChart = new Chart($salesChart, {
                        type: 'bar',
                        data: {
                        labels: labels,
                        datasets: [
                                {
                                    backgroundColor: '#007bff',
                                    borderColor: '#007bff',
                                    data: data
                                },
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            tooltips: {
                                mode: mode,
                                intersect: intersect
                            },
                            hover: {
                                mode: mode,
                                intersect: intersect
                            },
                            legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    // display: false,
                                    gridLines: {
                                        display: true,
                                        lineWidth: '4px',
                                        color: 'rgba(0, 0, 0, .2)',
                                        zeroLineColor: 'transparent'
                                    },
                                    ticks: $.extend({
                                        beginAtZero: true,
                                    }, ticksStyle)
                                }],
                                xAxes: [{
                                    display: true,
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: ticksStyle
                                }]
                            }
                        }
                    })
                },
                error : function(error) {
                    alert(error);
                }
        })

        
    </script>
@endpush
