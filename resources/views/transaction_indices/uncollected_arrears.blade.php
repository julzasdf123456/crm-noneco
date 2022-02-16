@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h4>Uncollected Arrears Collection</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            {{-- SEARCH BIN --}}
            <div class="col-lg-5">
                <div class="card" style="height: 70vh;">
                    <div class="card-header border-0">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search Account Name or Account Number..." id="search">
                        </div>
                    </div>
                    <div class="card-body table-responsive px-0">
                        <table class="table table-hover" id="search-table">
                            <thead>
                                <th>Account No.</th>
                                <th>Account Name</th>
                                <th>Balance</th>
                                <th></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header border-0">

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        {{-- FORM --}}
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>Current Balance</td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control text-right" style="font-size: 1.5em;" id="totalAmount" readonly="true">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>OR Number</td>
                                                <td class="text-right">
                                                    <input type="number" class="form-control text-right" style="font-size: 1.5em;" id="ornumber" step="any" autofocus>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Amount Paid</td>
                                                <td class="text-right">
                                                    <input type="number" class="form-control text-right" style="font-size: 1.5em;" id="amountPaid" step="any">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Remaining Balance</td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control text-right" style="font-size: 1.5em;" id="remaining-balance" readonly="true">
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        var amountPaid = 0.0;
        var currentBalance = 0.0;
        var remainingBalance = 0.0;
        var accountNo = "";
        $(document).ready(function() {
            // SEARCH EVENT
            $('#search').keyup(function() {
                var searchVal = this.value

                if (searchVal.length > 4) {
                    $.ajax({
                        url : '/transaction_indices/search-arrear-collectibles',
                        type : 'GET',
                        data : {
                            query : searchVal,
                        },
                        success : function(res) {
                            $('#search-table tbody tr').remove()
                            $('#ledger-table tbody tr').remove()

                            if (jQuery.isEmptyObject(res)) {

                            } else {
                                $('#search-table tbody').append(res)
                            }
                        },
                        error : function(err) {
                            console.log(err)
                            alert('An error occurred while performing the search')
                        }
                    })
                }
            })

            // AMOUNT PAID EVENT
            $('#amountPaid').keyup(function() {
                amountPaid = parseFloat(this.value)
                remainingBalance = currentBalance - amountPaid;

                if (parseFloat(remainingBalance) | remainingBalance == 0) {
                    if (remainingBalance < 0) {
                        buttonEnablers(false)
                    } else {
                        buttonEnablers(true)
                    }
                    $('#remaining-balance').val(remainingBalance.toFixed(2))
                } else {
                    $('#remaining-balance').val('')
                    buttonEnablers(false)
                }                
            })

            // CASH BUTTON SAVE EVENT
            $('#cashBtn').on('click', function() {
                saveTransaction()
            })
        })

        function fetchDetails(id) {
            // POPULATE DETAILS
            resetForm()
            $.ajax({
                url : '/transaction_indices/fetch-arrear-details',
                type : 'GET',
                data : {
                    AccountNumber : id,
                },
                success : function(res) {
                    try {
                        if (jQuery.isEmptyObject(res)) {

                        } else {
                            $('#ornumber').focus()
                            $('#totalAmount').val(res['Balance'])
                            currentBalance = parseFloat($('#totalAmount').val())
                            accountNo = res['AccountNumber']
                        }
                    } catch (err) {
                        console.log(err)
                    }
                }
            })
        }

        function buttonEnablers(bool) {
            if (bool) {
                // enable
                $('#cashBtn').removeAttr('disabled')   
                $('#checkBtn').removeAttr('disabled')  
                $('#cardBtn').removeAttr('disabled')               
            } else {
                // disable
                $('#cashBtn').attr('disabled', 'true')
                $('#checkBtn').attr('disabled', 'true')
                $('#cardBtn').attr('disabled', 'true')
            }   
        }

        function resetForm() {
            $('#totalAmount').val('')
            $('#ornumber').val('')
            $('#amountPaid').val('')
            $('#remaining-balance').val('')
        }

        // DETEC IF ENTER KEY IS PRESSED
        $(document).keypress(function(event){
            if (parseFloat(remainingBalance) | remainingBalance == 0) {
                if (remainingBalance < 0 && jQuery.isEmptyObject($('#ornumber').val())) {
                                         
                } else {
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if(keycode == '13'){
                        saveTransaction()
                    } 
                }
            } else {

            }  
        });

        function saveTransaction() {
            $.ajax({
                url : '/transaction_indices/save-arrear-transaction',
                type : 'GET',
                data : {
                    AccountNumber : accountNo,
                    AmountPaid : amountPaid,
                    RemainingBalance : remainingBalance, 
                    PaymentUsed : 'Cash',
                    ORNumber : $('#ornumber').val(),
                }, 
                success : function(res) {
                    alert('Print OR')
                    location.reload()
                },
                error : function(err) {
                    console.log(err)
                    alert('An error occurred while performing the transaction')
                }
            })
        }
    </script>
@endpush