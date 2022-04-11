@php
    // GET PREVIOUS MONTHS
    for ($i = -1; $i <= 12; $i++) {
        $months[] = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$i months"));
    }
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h4>Bills Bulk Printing</h4>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-12">
        <div class="card">
            <div class="card-header border-0">
                <span class="card-title">Preferences</span>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- BILLING MONTH --}}
                    <div class="form-group col-lg-4">
                        <label for="Period">Billing Month</label>
                        <select name="Period" id="Period" class="form-control">
                            @for ($i = 0; $i < count($months); $i++)
                                <option value="{{ $months[$i] }}">{{ date('F Y', strtotime($months[$i])) }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- TOWN --}}
                    <div class="form-group col-lg-4">
                        <label for="Town">Town</label>
                        <select name="Town" id="Town" class="form-control">
                            @foreach ($towns as $item)
                                <option value="{{ $item->id }}">{{ $item->Town }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ROUTES --}}
                    <div class="form-group col-lg-4">
                        <label for="Route">Route</label>
                        <select name="Route" id="Route" class="form-control">
                            
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" onclick="printNew()"><i class="fas fa-print ico-tab"></i>Print Using New Form</button>
                <button class="btn btn-warning" onclick="printOld()"><i class="fas fa-print ico-tab"></i>Print Using Pre-Printed Form</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            fetchRoutesFromTown($('#Town').val())

            $('#Town').on('change', function() {
                fetchRoutesFromTown(this.value)
            })
        })

        function fetchRoutesFromTown(id) {
            $('#Route option').remove()
            $.ajax({
                url : "{{ route('bills.get-routes-from-town') }}",
                type : 'GET',
                data : {
                    Town : id,
                },
                success : function(res) {
                    $('#Route').append(res)
                },
                error : function(err) {
                    alert('An error occurred while fetching routes')
                }
            })
        }

        function printNew() {
            if (jQuery.isEmptyObject($('#Route').val())) {
                alert('Select route first')
            } else {
                window.location.href = "{{ url('/bills/print-bulk-bill-new-format') }}" + "/" + $('#Period').val() + "/" + $('#Town').val() + "/" + $('#Route').val();
            }            
        }
    </script>
@endpush