@php
    use Illuminate\Support\Facades\Auth; 
    use App\Models\IDGenerator;
    use App\Models\ORAssigning;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Bills Payment OR Cancellation Console</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        {{-- SEARCH --}}
        <div class="col-lg-5 col-md-6">
            <div class="card" style="height: 70vh;">
                <div class="card-header border-0">
                    <div class="row">
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="search-field" placeholder="Search OR Number or Consumer" autofocus>
                        </div>
                        <div class="col-lg-4">
                            <div class="card-tools">
                                <button class="btn btn-primary" id="search-btn">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive px-0">
                    <table class="table table-hover table-sm" id="result-table">
                        <thead>
                            <th>OR Number</th>
                            <th>Account No</th>
                            <th>Consumer Name</th>
                            <th>Amount</th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <div class="col-lg-7 col-md-6">
            <div class="card" style="height: 70vh;">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10 col-md-8">
                            <input type="text" class="form-control" placeholder="Why do you wanna cancel this OR? Provide remarks here." id="notes">
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <button class="btn btn-danger" id="cancel-or-btn">Cancel OR</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12">
                                <p>
                                    <small class="float-right">Transaction Date: <strong id="posting-date"></strong></small>
                                </p>
                            </div>
                            <!-- /.col -->
                        </div>
    
                        <div class="row invoice-info">
                            <div class="col-lg-6 invoice-col">
                                <address>
                                    <strong id="account-name"></strong><br>
                                    <span id="account-no"></span><br>
                                    <span id="account-address"></span><br>
                                </address>
                            </div>
                            <!-- /.col -->
                            <div class="col-lg-6 invoice-col">
                                <address>
                                    <strong id="or-number">OR No: </strong><br>
                                    <span id="or-date">OR Date: </span><br>
                                    <span id="bill-number">Bill No: </span><br>
                                </address>
                            </div>
                            <!-- /.col -->                            
                        </div>    
                    </div>

                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>Teller/Cashier:</td>
                            <th id="teller"></th>
                        </tr>
                        <tr>
                            <td>Billing Month:</td>
                            <th id="period"></th>
                        </tr>
                        <tr>
                            <td>Kwh Used:</td>
                            <th id="kwh-used"></th>
                        </tr>
                        <tr>
                            <td>Additional Charges:</td>
                            <th id="additional-charges"></th>
                        </tr>
                        <tr>
                            <td>Deductions:</td>
                            <th id="deductions"></th>
                        </tr>
                        <tr>
                            <td>Amount Due:</td>
                            <th><h4 id="amount-due"></h4></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        var paidBillId = ""

        $(document).ready(function() {
            $('#search-field').keyup(function() {
                var letterCount = this.value.length;

                if (letterCount > 4) {
                    performSearchOR(this.value)
                }                
            })

            $('#search-btn').on('click', function() {
                performSearchOR($('#search-field').val())
            })

            $('#cancel-or-btn').on('click', function() {
                if (jQuery.isEmptyObject(paidBillId)) {
                    alert('Select payment first!')
                } else {
                    if (confirm('Are you sure you want to cancel this OR?')) {
                        $.ajax({
                            url : '/paid_bills/request-cancel-or',
                            type : 'GET',
                            data : {
                                id : paidBillId,
                                Notes : $('#notes').val(),
                            },
                            success : function(res) {
                                window.location.reload()
                            },
                            error : function(err) {
                                alert('An error occurred while cancelling the OR. \n' . err)
                            }
                        })
                    }
                }
            })
        })

        function performSearchOR(value) {
            $('#result-table tbody tr').remove()
            $.ajax({
                url : '/paid_bills/search-or',
                type : 'GET',
                data : {
                    query : value
                },
                success : function(res) {
                    $('#result-table tbody').append(res)
                },
                error : function(err) {
                    $('#result-table tbody tr').remove()
                    alert('An error occurred during the search')
                }
            })
        }

        function fetchDetails(id) {
            paidBillId = id
            $.ajax({
                url : '/paid_bills/fetch-or-details',
                type : 'GET',
                data : {
                    id : id,
                },
                success : function(res) {
                    if (jQuery.isEmptyObject(res)) {

                    } else {
                        $('#posting-date').text(res['PostingDate'])
                        $('#account-name').text(res['ServiceAccountName'])
                        $('#account-no').text(res['AccountNumber'])
                        $('#account-address').text(res['Address'])
                        $('#or-number').text('OR No: ' + res['ORNumber'])
                        $('#or-date').text('OR Date: ' + res['ORDate'])
                        $('#bill-number').text('Bill No: ' + res['BillNumber'])
                        $('#teller').text(res['name'])
                        $('#kwh-used').text(res['KwhUsed'])
                        $('#additional-charges').text(res['AdditionalCharges'])
                        $('#deductions').text(res['Deductions'])
                        $('#amount-due').text(res['NetAmount'])
                    }
                },
                error : function(err) {
                    alert('An error occurred while fetching the data')
                }
            })
        }
    </script>
@endpush