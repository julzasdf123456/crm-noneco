@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Bills Payment Console</h4>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-success float-right" title="Search Consumer"  data-toggle="modal" data-target="#modal-search"><i class="fas fa-search-dollar"></i></button>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            <div class="col-lg-4 col-md-5">
                <div class="card" style="height: 60vh;">
                    <div class="card-header border-0">
                        <span class="card-title">
                            <h4 id="account-name">...</h4>
                            <address class="text-muted" id="account-number">...</address>
                        </span>
                    </div>
                    <div class="card-body table-responsive px-0">
                        <table class="table table-hover" id="payables-table">
                            <thead>
                                <th>Billing Month</th>
                                <th>Amount Due</th>
                                <th width="50px"></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>                  
            </div>
        </div>
    </div>
@endsection

{{-- MODAL FOR SEARCHING OF CONSUMERS --}}
<div class="modal fade" id="modal-search" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search Consumer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- SEARCH --}}
                <div class="row">                    
                    <div class="form-group col-lg-8 offset-lg-1">
                        <input type="text" id="search" placeholder="Account Number, Account Name, or Meter Number" class="form-control" autofocus="true">
                    </div>
                    <div class="form-group col-lg-1">
                        <button id="search-consumer" class="btn btn-primary btn-lg"><i class="fas fa-search-dollar"></i></button>
                    </div>
                </div>

                {{-- RESULTS --}}
                <p class="text-muted"><i id="count">Results</i></p>
                <table class="table table-sm table-hover" id="res-table">
                    <thead>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Address</th>
                        <th>Meter Number</th>
                        <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            {{-- <div class="modal-footer justify-content-between">--}}
                {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
                {{-- <button type="button" class="btn btn-primary" id="submit">Save changes</button> --}}
                {{-- <input type="submit" value="Add" id="submit" class="btn btn-primary"> --}}
            {{-- </div>  --}}
        </div>
    </div>
</div>

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#search').keyup(function() {
                var letterCount = this.value.length;

                if (letterCount > 4) {
                    performSearch(this.value)
                }
            })

            $('#search-consumer').on('click', function() {
                var letterCount = $('#search').val().length;
                if (letterCount > 6) {
                    performSearch(this.value)
                }
            })
        })

        function performSearch(regex) {
            $.ajax({
                url : '/paid_bills/search',
                type : 'GET',
                data : {
                    query : regex,
                },
                success : function(res) {
                    try {
                        if (jQuery.isEmptyObject(res)) {
                            $('#res-table tbody tr').remove()
                        } else {
                            $('#res-table tbody tr').remove()
                            $('#res-table tbody').append(res)
                        }   
                    } catch (err) {
                        $('#res-table tbody tr').remove()
                    }                                     
                },
                error : function(error) {
                    $('#res-table tbody tr').remove()
                    alert('Error fetching data')
                    console.log(error)
                }
            })
        }

        function fetchDetails(id) {
            $('#account-number').text(id)
            // FETCH ACCOUNT DETAILS
            $.ajax({
                url : '/paid_bills/fetch-account',
                type : 'GET',
                data : {
                    AccountNumber : id,
                },
                success : function(res) {
                    $('#payables-table tbody tr').remove();
                    if (jQuery.isEmptyObject(res)) {

                    } else {
                        $('#account-name').text(res['ServiceAccountName'])
                    }
                    $('#modal-search').modal('hide')
                },
                error : function(err) {
                    $('#modal-search').modal('hide')
                    console.log(err)
                    alert('Error fetching account details. Contact support for more.')
                } 
            })

            // FETCH UNPAID BILLS
            $.ajax({
                url : '/paid_bills/fetch-details',
                type : 'GET',
                data : {
                    AccountNumber : id,
                }, 
                success : function(res) {
                    $('#payables-table tbody tr').remove();
                    if (jQuery.isEmptyObject(res)) {

                    } else {
                        $('#payables-table tbody').append(res)
                    }
                },
                error : function(err) {
                    console.log(err)
                    alert('Error fetching account details. Contact support for more.')
                }
            })            
        }
    </script>
@endpush
