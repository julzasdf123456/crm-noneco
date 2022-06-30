@php
    // GET PREVIOUS MONTHS
    for ($i = 0; $i <= 12; $i++) {
        $months[] = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$i months"));
    }
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Generate Notice of Disconnection List</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            {{-- Config --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Setup</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="service-period">Service Period</label>
                            <select id="service-period" class="form-control">
                                @for ($i = 0; $i < count($months); $i++)
                                    <option value="{{ $months[$i] }}">{{ date('F Y', strtotime($months[$i])) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="towns">Area</label>
                            <select id="towns" class="form-control">
                                <option value="All">All</option>
                                @foreach ($towns as $item)
                                    <option value="{{ $item->id }}" {{ env('APP_AREA_CODE')==$item->id ? 'selected' : '' }}>{{ $item->Town }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="MeterReader">Select Meter Reader</label>
                            <select name="MeterReader" id="MeterReader" class="form-control">
                                @if (count($meterReaders) > 0)
                                    @foreach ($meterReaders as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                @else
                                    <option value="">No Meter Reader Found</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="Day">Select Day</label>
                            <select name="Day" id="Day" class="form-control">
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" id="get-list-btn">Generate List</button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="col-lg-8">
                <div class="card" style="height: 75vh;">
                    <div class="card-header border-0">
                        <span class="card-title">List (<i>press <strong>F3</strong> to search</i>)</span>
                    </div>
                    <div class="card-body table-responsive px-0">
                        <table class="table table-sm table-hover table-bordered" id="results-table">
                            <thead>
                                <th>Bill Number</th>
                                <th>Account Number</th>
                                <th>Account Name</th>
                                <th>Address</th>
                                <th class="text-right">Amount Due</th>
                                <th></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button id="print-list" class="btn btn-success"><i class="fas fa-print ico-tab"></i>Print List</button>
                    </div>
                </div>                
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        var period = ""
        var town = ""
        $(document).ready(function() {
            $('#get-list-btn').on('click', function() {
                period = $('#service-period').val()
                town = $('#towns').val()

                getList()
            })

            $('#print-list').on('click', function() {
                // if (jQuery.isEmptyObject(period) || jQuery.isEmptyObject(town)) {
                //     alert('Generate Preview First')
                // } else {
                //     $.ajax({
                //         url : '{{ route("discoNoticeHistories.print-reroute") }}',
                //         type : 'GET',
                //         data : {
                //             ServicePeriod : period,
                //             Town : town,
                //             MeterReader : $('#MeterReader').val(),
                //             Day : $('#Day').val()
                //         },
                //         success : function(res) {
                //             window.location.href = "{{ url('/disco_notice_histories/print-disconnection-list') }}" + "/" + res['period'] + "/" + res['area']
                //         },
                //         error : function(err) {
                //             alert('An error occurred while trying to print the disconnection list')
                //         }
                //     })
                // }      
                window.location.href = "{{ url('/disco_notice_histories/print-disconnection-list') }}" + "/" + $('#service-period').val() + "/" + $('#towns').val() + "/" + $('#MeterReader').val() + "/" + $('#Day').val()          
            })
        })

        function getList() {
            $('#results-table tbody tr').remove()
            $.ajax({
                url : '{{ route("discoNoticeHistories.get-disco-list-preview") }}',
                type : 'GET',
                data : {
                    ServicePeriod : period,
                    Town : town,
                    MeterReader : $('#MeterReader').val(),
                    Day : $('#Day').val()
                },
                success : function(res) {
                    $('#results-table tbody').append(res)
                },
                error : function(err) {
                    alert('An error occurred while fetching the disconnection list')
                }
            })
        }

        function deleteNotice(id) {
            if (confirm('Are you sure you want to remove this entry from the disconnection list?')) {
                $.ajax({
                    url : '/discoNoticeHistories/' + id,
                    type : 'DELETE',
                    data : {
                        _token : "{{ csrf_token() }}",
                        id : id,
                    },
                    success : function(res) {
                        $('#' + id).remove()
                    },
                    error : function(err) {
                        alert('An error occurred while deleting the item from the disconnection list')
                    }
                })
            } 
        }
    </script>
@endpush