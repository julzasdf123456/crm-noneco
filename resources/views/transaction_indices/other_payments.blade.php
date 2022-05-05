@php
    use App\Models\ORAssigning;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Other Payments</h4>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-success float-right" title="Search Consumer"  data-toggle="modal" data-target="#modal-search"><i class="fas fa-search-dollar ico-tab"></i>Search</button>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            {{-- PARTICULARS --}}
            <div class="col-lg-7 col-md-6">
                <div class="card" style="height: 70vh;">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-11">
                                <h4 class="card-title" id="account-name">Account Name</h4><br>
                                <address class="text-muted" id="account-no">Account No</address>
                            </div>

                            <div class="col-lg-1">
                                <button class="btn btn-tool float-right" style="margin: 8px;" id="clear-btn"><i class="fas fa-redo"></i></button>
                            </div>

                            <div class="divider"></div>

                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12">
                                        <input type="text" id="for" class="form-control" placeholder="Payment for...">
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <select id="payables" class="form-control">
                                            @foreach ($payables as $item)
                                                <option id="{{ $item->id }}" def-amount="{{ $item->DefaultAmount }}" vat="{{ $item->VATPercentage==null ? 0 : $item->VATPercentage }}" value="{{ $item->AccountCode }}" particular="{{ $item->AccountTitle }}">{{ $item->AccountTitle }}</option>
                                            @endforeach     
                                            <option value="Other">Others</option>                                   
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="number" step="any" id="amount" class="form-control" placeholder="Amount">
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4">
                                        <button class="btn btn-primary" id="add-to-payable-btn">Add To Payable</button>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive px-0">
                        <table class="table" id="payables-table">
                            <thead>
                                <th>Payables</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Total</th>
                                <th width="40px"></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <table class="table table-borderless">
                            <tr>
                                <th>Total</th>
                                <th style="font-size: 1.5em;" class="text-right" id="total-amnt-text"></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <div class="col-lg-5 col-md-6">
                <div class="card">
                    <div class="card-body">
                        {{-- FORM --}}
                        <table class="table table-borderless">
                            <tr>
                                <td>Total</td>
                                <td class="text-right">
                                    <input type="text" class="form-control text-right" style="font-size: 1.5em;" id="totalAmount" readonly="true">
                                </td>
                            </tr>
                            <tr>
                                <td>OR Number</td>
                                <td class="text-right">
                                    <input type="number" class="form-control text-right" style="font-size: 1.5em;" id="ornumber" value="{{ ORAssigning::getORIncrement(1, $orAssignedLast) }}" autofocus>
                                </td>
                            </tr>
                            <tr>
                                <td>Amount Paid</td>
                                <td class="text-right">
                                    <input type="number" class="form-control text-right" style="font-size: 1.5em;" id="amountPaid" step="any">
                                </td>
                            </tr>
                            <tr>
                                <td>Change</td>
                                <td class="text-right">
                                    <input type="text" class="form-control text-right" style="font-size: 1.5em;" id="change" readonly="true">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button id="cashBtn" class="btn btn-lg btn-primary float-right" disabled><i class="fas fa-dollar-sign"></i> Cash</button>
                        <button id="checkBtn" class="btn btn-sm btn-default float-right ico-tab-mini" disabled><i class="fas fa-money-check-alt"></i> Check</button>
                        <button id="cardBtn" class="btn btn-sm btn-default float-right ico-tab-mini" disabled><i class="fas fa-credit-card"></i> Debit/Credit Card</button>
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
        var accountNo = ""
        var total = 0.0;
        $(document).ready(function() {
            payableDisablers(true)
            setFormAmount()
            $('#search').keyup(function() {
                var letterCount = this.value.length;

                if (letterCount > 4) {
                    performSearch(this.value)
                }
            })

            // PAYABLE SELECT
            $('#payables').on('change', function() {
                setFormAmount()
            })

            // ADD TO PAYABLES BUTTON
            $('#add-to-payable-btn').on('click', function() {
                var amnt = parseFloat($('#amount').val())
                var vat = parseFloat($('#payables option:selected').attr('vat')) * parseFloat($('#amount').val())
                var ttl = amnt + vat
                $.ajax({
                    url : '/cacheOtherPayments',
                    type : 'POST',
                    data : {
                        _token : "{{ csrf_token() }}",
                        AccountNumber : accountNo,
                        Particular : $('#payables option:selected').attr('particular'),
                        Amount : amnt.toFixed(2),
                        VAT : vat.toFixed(2),
                        Total : ttl.toFixed(2),
                        AccountCode : $('#payables').val()
                    },
                    success : function(res) {
                        fetchCachedData(accountNo)
                    }, 
                    error : function(err) {
                        alert("Error adding payable data")
                    }
                })
            })

            // ASSESS VALUE ON TYPE
            var change = 0;
            $('#amountPaid').keyup(function() {
                change = parseFloat(this.value) - parseFloat(total)

                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#ornumber').val())) {
                        $('#cashBtn').removeAttr('disabled')
                        $('#checkBtn').removeAttr('disabled')
                        $('#cardBtn').removeAttr('disabled')                        
                    } else {
                        $('#cashBtn').attr('disabled', 'true')
                        $('#checkBtn').attr('disabled', 'true')
                        $('#cardBtn').attr('disabled', 'true')
                    }
                    $('#change').val(Number(change.toFixed(2)).toLocaleString())
                } else {
                    $('#change').val('')
                    $('#cashBtn').attr('disabled', 'true')
                    $('#checkBtn').attr('disabled', 'true')
                    $('#cardBtn').attr('disabled', 'true')
                }                
            })

            $('#ornumber').keyup(function() { 
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#ornumber').val())) {
                        $('#cashBtn').removeAttr('disabled')
                        $('#checkBtn').removeAttr('disabled')
                        $('#cardBtn').removeAttr('disabled')                        
                    } else {
                        $('#cashBtn').attr('disabled', 'true')
                        $('#checkBtn').attr('disabled', 'true')
                        $('#cardBtn').attr('disabled', 'true')
                    }
                    $('#change').val(Number(change.toFixed(2)).toLocaleString())
                } else {
                    $('#change').val('')
                    $('#cashBtn').attr('disabled', 'true')
                    $('#checkBtn').attr('disabled', 'true')
                    $('#cardBtn').attr('disabled', 'true')
                }   
            })

            // DETECT ON ENTER
            $(document).keypress(function(event){
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#ornumber').val())) {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if(keycode == '13'){
                            saveOtherPayments('Cash')
                        }                      
                    } else {

                    }
                } else {

                }  
            });

            // CASH BUTTON EVENT
            $('#cashBtn').on('click', function() {
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#ornumber').val())) {
                        saveOtherPayments('Cash')                    
                    } else {

                    }
                } else {

                }  
            })

            // CLEAR BUTTON
            $('#clear-btn').on('click', function() {
                clearAll()
            })
        })

        /**
         * SAVE TRANSACTION AND CLEAR CACHE
         */
        function saveOtherPayments(type) {
            $.ajax({
                url : '/cache_other_payments/save-other-payments',
                type : 'GET',
                data : {
                    AccountNumber : accountNo,
                    PaymentTitle : $('#for').val(),
                    ORNumber : $('#ornumber').val(),
                    PaymentUsed : type,
                },
                success : function(res) {
                    if (jQuery.isEmptyObject(res)) {

                    } else {
                        window.location.href = "{{ url('/transaction_indices/print-other-payments') }}" + "/" + res['id'];
                    }
                },
                error : function(err) {

                }
            })
        }

        function addToPayablesTable(particular, amnt, vat, ttl, id) {
            return '<tr id="' + id + '">' +
                    '<td>' + particular + '</td>' +
                    '<td>' + amnt + '</td>' +
                    '<td>' + vat + '</td>' +
                    '<td id="total-' + id + '">' + ttl + '</td>' +
                    '<td class="text-right">' +
                        '<button onclick=deletePayable("' + id + '") class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>'
                    '</td>' +
                '</tr>'
        }

        function performSearch(regex) {
            $.ajax({
                url : '/transaction_indices/search-consumer',
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

        function fetchAccountDetails(id) {
            $('#modal-search').modal('hide')
            payableDisablers(false)
            $.ajax({
                url : '/transaction_indices/fetch-account-details',
                type : 'GET',
                data : {
                    id : id,
                },
                success : function(res) {
                    accountNo = res['id']
                    $('#account-name').text(res['ServiceAccountName'])
                    $('#account-no').text(accountNo)
                    fetchCachedData(accountNo)
                },
                error : function(err) {
                    alert('An error occurred while fetching your request. \n' + err)
                }
            })
        }

        function deletePayable(id) {
            $.ajax({
                url : '/cacheOtherPayments/' + id,
                type : 'DELETE',
                data : {
                    _token : "{{ csrf_token() }}",
                    id : id,
                },
                success : function(res) {
                    fetchCachedData(accountNo)
                },
                error : function(err) {
                    alert("Error deleting data")
                }
            })
        }

        function setFormAmount() {
            $('#amount').val($('#payables option:selected').attr('def-amount'))
        }

        function payableDisablers(bool) {
            if (bool) {
                $('#amount').attr('readonly', true)
                $('#add-to-payable-btn').attr('disabled', true)
            } else {
                $('#amount').removeAttr('readonly')
                $('#add-to-payable-btn').removeAttr('disabled')
            }
        }

        function fetchCachedData(accountNo) {
            total = 0.0;

            $('#amountPaid').val('')
            $('#change').val('')

            $.ajax({
                url : '/cache_other_payments/fetch-cached',
                type : 'GET',
                data : {
                    AccountNumber : accountNo,
                },
                success : function(res) {
                    $('#payables-table tbody tr').remove()
                    if (jQuery.isEmptyObject(res)) {
                        $('#total-amnt-text').text("")
                        $('#totalAmount').val("")
                    } else {
                        $.each(res, function(index, element) {
                            total += parseFloat(res[index]['Total'])
                            $('#payables-table tbody').append(addToPayablesTable(res[index]['Particular'], res[index]['Amount'], res[index]['VAT'], res[index]['Total'], res[index]['id']))
                        })

                        $('#total-amnt-text').text(Number(total.toFixed(2)).toLocaleString())
                        $('#totalAmount').val(total.toFixed(2))
                        focusOrNumber()
                    }
                },
                error : function(error) {
                    alert("Error fetching cached data")
                    $('#total-amnt-text').text("")
                    $('#totalAmount').val("")
                }
            })
        }

        function focusOrNumber() {
            $('#amountPaid').focus()
        }

        function clearAll() {
            total = 0.0;
            accountNo = "";
            $('#payables-table tbody tr').remove()
            $('#total-amnt-text').text("")
            $('#totalAmount').val("")
            $('#amountPaid').val('')
            $('#change').val('')
        }
    </script>
@endpush