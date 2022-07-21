@php
    // GET PREVIOUS MONTHS
    for ($i = 0; $i <= 12; $i++) {
        $months[] = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$i months"));
    }
@endphp

@extends('layouts.app')

@section('content')
<p style="padding-top: 8px;"><i class="fas fa-chart-line ico-tab"></i>Billing Dashboard</p>
<div class="row">
    <div class="col-lg-6">
        <div class="card" style="height: 50vh;">
            <div class="card-header">
                <span class="card-title">Reading Monitor</span>

                <div class="card-tools">
                    <div class="row">
                        <div class="form-group col-6">
                            <select id="service-period" class="form-control form-control-sm">
                                @for ($i = 0; $i < count($months); $i++)
                                    <option value="{{ $months[$i] }}">{{ date('F Y', strtotime($months[$i])) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <select id="day-reading-monitor" class="form-control form-control-sm">
                                <option value="All">All</option>
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
                            </select>
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered table-hover" id="reading-monitor-table">
                    <thead>
                        <th>Meter Reader</th>
                        <th>Unbilled Based <br> From Readings</th>
                        <th>All Unbilled</th>
                        <th>Total Reading</th>
                        <th>Total Billed</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer">
                <p><strong class="text-danger">NOTE: </strong>These figures doesn't include the office billings performed by Data Ads.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            fetchReadingMonitor()

            $('#service-period').on('change', function() {
                fetchReadingMonitor(this.value, $('#day-reading-monitor').val())
            })

            $('#day-reading-monitor').on('change', function() {
                fetchReadingMonitor($('#service-period').val(), this.value)
            })
        })

        function fetchReadingMonitor(period, day) {
            $('#reading-monitor-table tbody tr').remove()
            $.ajax({
                url : "{{ route('bills.dashboard-reading-monitor') }}",
                type : 'GET',
                data : {
                    Period : period,
                    Day : day,
                },
                success : function(res) {
                    $('#reading-monitor-table tbody').append(res)
                },
                error : function(err) {
                    Swal.fire({
                        title : 'Error fetching reading monitor',
                        icon : 'error'
                    })
                }
            })
        }
    </script>
@endpush