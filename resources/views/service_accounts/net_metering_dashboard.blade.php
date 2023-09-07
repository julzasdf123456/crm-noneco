@php
    $years = [];
    for($i=0; $i<12; $i++) {
        $years[$i] = date('Y', strtotime('this year -' . $i . ' years'));
    }
@endphp

@extends('layouts.app')

@section('content')
<div class="row">
    {{-- CONFIG --}}
    <div class="col-lg-5">
        <p style="padding-top: 8px;"><i class="fas fa-chart-line ico-tab"></i>Net Metering Dashboard and Monitoring</p>
    </div>
    <div class="col-lg-7" style="padding-top: 5px;"></div>

    {{-- EXPORTED --}}
    <div class="col-lg-12">
        <div class="card shadow-none" style="height: 70vh">
            <div class="card-header">
                <span class="card-title">Exported</span>
                <div class="card-tools">
                    <select name="ExportedYear" id="ExportedYear" class="form-control form-control-sm float-right" style="width: 150px;">
                        @foreach ($years as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="loader-exported" class="spinner-border text-info gone float-right" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table id="exported-table" class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <td rowspan="3" style="width: 30px;">#</td>
                            <td rowspan="2" colspan="3" class="text-center">Qualified End-User</td>
                            <td colspan="24" class="text-center">Monthly Monitoring (Past 12 months)</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">January</td>
                            <td colspan="2" class="text-center">February</td>
                            <td colspan="2" class="text-center">March</td>
                            <td colspan="2" class="text-center">April</td>
                            <td colspan="2" class="text-center">May</td>
                            <td colspan="2" class="text-center">June</td>
                            <td colspan="2" class="text-center">July</td>
                            <td colspan="2" class="text-center">August</td>
                            <td colspan="2" class="text-center">September</td>
                            <td colspan="2" class="text-center">October</td>
                            <td colspan="2" class="text-center">November</td>
                            <td colspan="2" class="text-center">December</td>
                        </tr>
                        <tr>
                            <td rowspan="2" class="text-center">Consumer Name</td>
                            <td rowspan="2" class="text-center">Account Number</td>
                            <td rowspan="2" class="text-center">Address</td>
                            {{-- JAN --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- FEB --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- MARCH --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- APR --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- MAY --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- JUN --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- JUL --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- AUG --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- SEP --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- OCT --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- NOV --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                            {{-- DEC --}}
                            <td class="text-center">Exported<br>Energy (kWh)</td>
                            <td class="text-center">Blended<br>Generation (Php/kWh)</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- IMPORTED --}}
    <div class="col-lg-12">
        <div class="card shadow-none" style="height: 70vh">
            <div class="card-header">
                <span class="card-title">Imported</span>
                <div class="card-tools">
                    <select name="ImportedYear" id="ImportedYear" class="form-control form-control-sm float-right" style="width: 150px;">
                        @foreach ($years as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="loader-imported" class="spinner-border text-info gone float-right" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table id="imported-table" class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <td rowspan="3" style="width: 30px;">#</td>
                            <td rowspan="2" colspan="3" class="text-center">Qualified End-User</td>
                            <td colspan="24" class="text-center">Monthly Monitoring (Past 12 months)</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">January</td>
                            <td colspan="2" class="text-center">February</td>
                            <td colspan="2" class="text-center">March</td>
                            <td colspan="2" class="text-center">April</td>
                            <td colspan="2" class="text-center">May</td>
                            <td colspan="2" class="text-center">June</td>
                            <td colspan="2" class="text-center">July</td>
                            <td colspan="2" class="text-center">August</td>
                            <td colspan="2" class="text-center">September</td>
                            <td colspan="2" class="text-center">October</td>
                            <td colspan="2" class="text-center">November</td>
                            <td colspan="2" class="text-center">December</td>
                        </tr>
                        <tr>
                            <td rowspan="2" class="text-center">Consumer Name</td>
                            <td rowspan="2" class="text-center">Account Number</td>
                            <td rowspan="2" class="text-center">Address</td>
                            {{-- JAN --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- FEB --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- MARCH --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- APR --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- MAY --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- JUN --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- JUL --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- AUG --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- SEP --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- OCT --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- NOV --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                            {{-- DEC --}}
                            <td class="text-center">Imported<br>Energy (kWh)</td>
                            <td class="text-center">Total Billed<br>Amount (Php/kWh)</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            getExportData($('#ExportedYear').val())
            getImportData($('#ImportedYear').val())

            $('#ExportedYear').change(function() {
                getExportData(this.value)
            })

            $('#ImportedYear').change(function() {
                getImportData(this.value)
            })
        })

        function getExportData(year) {
            $('#loader-exported').removeClass('gone')
            $('#exported-table tbody tr').remove()
            $.ajax({
                url : "{{ route('bills.get-netmetering-eported-energy-report') }}",
                type : "GET",
                data : {
                    Year : year,
                },
                success : function(res) {
                    $('#exported-table tbody').append(res)
                    $('#loader-exported').addClass('gone')
                },
                error : function(err) {
                    Toast.fire({
                        icon : 'error',
                        text : 'Error getting exported data' 
                    })
                    $('#loader-exported').addClass('gone')
                }
            })
        }

        function getImportData(year) {
            $('#loader-imported').removeClass('gone')
            $('#imported-table tbody tr').remove()
            $.ajax({
                url : "{{ route('bills.get-netmetering-imported-energy-report') }}",
                type : "GET",
                data : {
                    Year : year,
                },
                success : function(res) {
                    $('#imported-table tbody').append(res)
                    $('#loader-imported').addClass('gone')
                },
                error : function(err) {
                    Toast.fire({
                        icon : 'error',
                        text : 'Error getting imported data' 
                    })
                    $('#loader-imported').addClass('gone')
                }
            })
        }
    </script>
@endpush