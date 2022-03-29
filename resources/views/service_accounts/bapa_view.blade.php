@php
    use App\Models\ServiceAccounts;
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <span>
                    <h4 style="display: inline; margin-right: 15px;"><?= $bapaName ?></h4>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="row">
    {{-- ROUTES --}}
    <div class="col-lg-4 col-md-5">
        <div class="card" style="height: 80vh;">
            <div class="card-header border-0">
                <span class="card-title">Routes/Area Codes in this BAPA</span>
                <div class="card-tools">
                    <a href="{{ route('serviceAccounts.update-bapa', [urlencode($bapaName)]) }}" class="btn btn-sm btn-primary">Add Routes</a>
                </div>
            </div>
            <div class="card-body table-responsive px-0">
                <table class="table table-hover table-sm">
                    <thead>
                        <th>Route/Area</th>
                        <th>No. of Consumers</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($routes as $item)
                            <tr>
                                <td>{{ $item->AreaCode }}</td>
                                <td>{{ number_format($item->NoOfConsumers) }}</td>
                                <td class="text-right">
                                    <button onclick="removeByRoute('{{ $item->AreaCode }}')" class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ACCOUNTS --}}
    <div class="col-lg-8 col-md-7">
        <div class="card" style="height: 80vh;">
            <div class="card-header border-0">
                <span class="card-title">Accounts in this BAPA <i>(Press <strong>F3</strong> to Search)</i></span>
                <div class="card-tools">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-search">Add Account</button>                   
                </div>
            </div>
            <div class="card-body table-responsive px-0">
                <table class="table table-hover table-sm">
                    <thead>
                        <th>Account ID</th>
                        <th>Account No</th>
                        <th>Service Account Name</th>
                        <th>Address</th>
                        <th>Route</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($serviceAccounts as $item)
                            <tr>
                                <td><a href="{{ route('serviceAccounts.show', [$item->id]) }}">{{ $item->id }}</a></td>
                                <td>{{ $item->OldAccountNo }}</td>
                                <td>{{ $item->ServiceAccountName }}</td>
                                <td>{{ ServiceAccounts::getAddress($item) }}</td>
                                <td>{{ $item->AreaCode }}</td>
                                <td class="text-right">
                                    <button onclick="removeByAccount('{{ $item->id }}')" class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <span>Total Consumers: <strong>{{ count($serviceAccounts) }}</strong></span>
            </div>
        </div>
    </div>
</div>

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
                        <th>Status</th>
                        <th></th>
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
            $('#search').keyup(function() {
                var letterCount = this.value.length;

                if (letterCount > 4) {
                    performSearch(this.value)
                }
            })
        })

        function removeByRoute(route) {
            if (confirm('Are you sure you want to delete this route from this BAPA?')) {
                $.ajax({
                    url : '/service_accounts/remove-bapa-by-route',
                    type : 'GET',
                    data : {
                        BAPAName : "{{ $bapaName }}",
                        Route : route,
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        alert('An error occurred while trying to delete the route from the BAPA')
                    }
                })
            }            
        }

        function removeByAccount(id) {
            if (confirm('Are you sure you want to delete this account from this BAPA?')) {
                $.ajax({
                    url : '/service_accounts/remove-bapa-by-account',
                    type : 'GET',
                    data : {
                        AccountNumber : id,
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        alert('An error occurred while trying to delete the account from the BAPA')
                    }
                })
            }            
        }

        function performSearch(regex) {
            $.ajax({
                url : '/service_accounts/search-accout-bapa',
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

        function addAccountToBapa(id) {
            $.ajax({
                url : '/service_accounts/add-single-account-to-bapa',
                type : 'GET',
                data : {
                    id : id,
                    BAPAName : "{{ $bapaName }}",
                },
                success : function(res) {
                    location.reload()
                },
                error : function(err) {
                    alert('An error occurred while adding the account to this BAPA')
                }
            })
        }
    </script>
@endpush