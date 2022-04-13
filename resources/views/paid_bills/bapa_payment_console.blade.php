@php
    use Illuminate\Support\Facades\Auth; 
    use App\Models\IDGenerator;
    use App\Models\ORAssigning;

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
                <h4>BAPA Bills Payment Console - <strong>{{ $bapaName }}</strong></h4>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-8">
        <div class="card" style="height: 80vh;">
            <div class="card-header">
                <div class="card-tools">
                    <div class="spinner-border text-primary" role="status" id="spinner">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <span class="card-title">Select Billing Month</span>
                    </div>
                    <div class="col-lg-4">
                        <select name="ServicePeriod" id="ServicePeriod" class="form-control">
                            @for ($i = 0; $i < count($months); $i++)
                                <option value="{{ $months[$i] }}">{{ date('F Y', strtotime($months[$i])) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="divider"></div>
                    <div class="col-lg-12">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-right">Total # of Accts:</td>
                                <th id="total-acts"></th>
                                <td class="text-right">Active Accts:</td>
                                <th id="active-total"></th>
                                <td class="text-right">Total # of Bills:</td>
                                <th id="bills-total"></th>
                                <td class="text-right">Individually Paid:</td>
                                <th id="indiv-paid"></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <p><i>Press <strong>F3</strong> to search</i></p>
                <table class="table table-sm table-hover" id="accounts-table">
                    <thead>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Account Status</th>
                        <th>kWh Used</th>
                        <th>Billing Mo.</th>
                        <th>Bill Number</th>
                        <th>Amount Due</th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td>Total Amount</td>
                        <td class="text-right">
                            <h4 class="text-right" id="totalAmount">-</h4>
                        </td>
                    </tr>
                    <tr>                        
                        <td>
                            <div class="input-group">
                                <input type="hidden" value="" name="Discount3">
                                <input type="checkbox" value="" name="Discount3" id="Discount3" class="custom-checkbox">
                                <label for="Discount3">Discount (3%)</label>
                            </div>
                        </td>
                        <td class="text-right">
                            <h4 class="text-right" id="discount3">-</h4>
                        </td>
                    </tr>
                    <tr>                        
                        <td>
                            <div class="input-group">
                                <input type="hidden" value="" name="Discount5">
                                <input type="checkbox" value="" name="Discount5" id="Discount5" class="custom-checkbox">
                                <label for="Discount5">Discount (5%)</label>
                            </div>
                        </td>
                        <td class="text-right">
                            <h4 class="text-right" id="discount5">-</h4>
                        </td>
                    </tr>
                    <tr>                        
                        <td>Amount Due</td>
                        <td class="text-right">
                            <h4 class="text-right" id="amountDue">-</h4>
                        </td>
                    </tr>
                    <tr>
                        <td>OR Number</td>
                        <td class="text-right">
                            <input type="number" class="form-control text-right" style="font-size: 1.5em;" id="orNumber" value="{{ ORAssigning::getORIncrement(1, $orAssignedLast) }}">
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
@endsection

@push('page_scripts')
    <script>
        var accountsAdded = []
        var totalAmount = 0
        var discount3 = 0
        var discount5 = 0
        var change = 0
        var amountDue = 0
        var discount3Checked = false
        var discount5Checked = false

        $(document).ready(function() {
            $('#spinner').hide()

            $('#ServicePeriod').on('change', function() {                
                $('#spinner').show()

                totalAmount = 0
                discount3 = 0
                discount5 = 0
                change = 0
                amountDue = 0
                $('#amountPaid').val('')
                updatePaymentDisplays()

                fetchBills(this.value)
            })

            // AMOUNT PAID ON KEY PRESSED
            $('#amountPaid').keyup(function() {
                change = (parseFloat(this.value) - amountDue).toFixed(2).toLocaleString()

                if (parseFloat(change)) {
                    $('#change').val(change)
                    if (change > -1 && !jQuery.isEmptyObject($('#orNumber').val()) && !jQuery.isEmptyObject(accountsAdded)) {
                        buttonEnablers(true)                        
                    } else {
                        buttonEnablers(false)
                    }
                } else {
                    $('#change').val('')
                    buttonEnablers(false)
                }                
            })

            // CASH BUTTON EVENT
            $('#cashBtn').on('click', function() {
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#orNumber').val()) && !jQuery.isEmptyObject(accountsAdded)) {  
                        transact()                     
                    } else {

                    }
                }
            })

            // OR NO ON KEY PRESSED
            $('#orNumber').keyup(function() {
                change = (parseFloat($('#amountPaid').val()) - amountDue).toFixed(2).toLocaleString()

                if (parseFloat(change)) {
                    $('#change').val(change)
                    if (change > -1 && !jQuery.isEmptyObject(this.value) && !jQuery.isEmptyObject(accountsAdded)) {
                        buttonEnablers(true)                        
                    } else {
                        buttonEnablers(false)
                    }
                } else {
                    $('#change').val('')
                    buttonEnablers(false)
                }                
            })

            // DISCOUNT 3% CHANGE
            $('#Discount3').change(function() {
                if($('#Discount3').prop('checked')) {
                    discount3Checked = true                           
                } else {
                    discount3Checked = false                    
                }
                computeAmounts()
                updatePaymentDisplays()
            })

            $('#Discount5').change(function() {
                if($('#Discount5').prop('checked')) {
                    discount5Checked = true                            
                } else {
                    discount5Checked = false                  
                }
                computeAmounts()
                updatePaymentDisplays()
            })
        })

        function fetchBills(period) {
            $('#accounts-table tbody tr').remove()
            $.ajax({
                url : "{{ route('paidBills.get-bills-from-bapa') }}",
                type : 'GET',
                data : {
                    BAPAName : "{{ $bapaName }}",
                    Period : period,
                },
                success : function(res) {
                    getDetails(res)
                    $.each(res, function(index, element) {
                        $('#accounts-table tbody').append(addToRow(res[index]['AccountNumber'],
                            res[index]['ServiceAccountName'],
                            res[index]['AccountStatus'],
                            res[index]['KwhUsed'],
                            res[index]['ServicePeriod'],
                            res[index]['BillNumber'],
                            res[index]['NetAmount'],
                            res[index]['ORNumber']))
                    })
                    $('#spinner').hide()
                },
                error : function(err) {
                    alert('An error occurred while fetching accounts')
                }
            })
        }

        function addToRow(acctNo, acctName, status, kwhused, billmo, billno, amtdue, or) {
            if (billno != null) {
                if (or != null) { // PAID AREA
                    return '<tr title="OR Number: ' + or + '" class="bg-teal disabled">' +
                                '<td><i class="ico-tab fas fa-check text-success"></i>' + acctNo + '</td>' +
                                '<td>' + acctName + '</td>' +
                                '<td>' + status + '</td>' +
                                '<td class="text-right">' + Number(parseFloat(kwhused).toFixed(2)).toLocaleString() + '</td>' +
                                '<td>' + billmo + '</td>' +
                                '<td class="text-right">' + (billno != null ? billno : '') + '</td>' +
                                '<td class="text-right">' + (parseFloat(amtdue) ? Number(parseFloat(amtdue).toFixed(2)).toLocaleString() : '') + '</td>' +
                                '<td></td>' +
                            '</tr>';
                } else { // PAYABLE AREA
                    return '<tr onclick=addToPayables("' + acctNo + '")>' +
                                '<td><i class="ico-tab fas fa-exclamation text-primary"></i>' + acctNo + '</td>' +
                                '<td>' + acctName + '</td>' +
                                '<td>' + status + '</td>' +
                                '<td class="text-right">' + Number(parseFloat(kwhused).toFixed(2)).toLocaleString() + '</td>' +
                                '<td>' + billmo + '</td>' +
                                '<td class="text-right">' + (billno != null ? billno : '') + '</td>' +
                                '<td class="text-right">' + (parseFloat(amtdue) ? Number(parseFloat(amtdue).toFixed(2)).toLocaleString() : '') + '</td>' +
                                '<td><button class="btn btn-link btn-sm"><i ischecked="true" amount="' + amtdue + '" id="btn-' + acctNo + '" class="fas fa-check-circle text-success"></i></button></td>' +
                            '</tr>';
                }
            } else { // UNBILLED AREA
                return '<tr class="bg-secondary disabled">' +
                                '<td><i class="ico-tab fas fa-exclamation text-danger"></i>' + acctNo + '</td>' +
                                '<td>' + acctName + '</td>' +
                                '<td>' + status + '</td>' +
                                '<td class="text-right">' + Number(parseFloat(kwhused).toFixed(2)).toLocaleString() + '</td>' +
                                '<td>' + billmo + '</td>' +
                                '<td class="text-right">' + (billno != null ? billno : '') + '</td>' +
                                '<td class="text-right">' + (parseFloat(amtdue) ? Number(parseFloat(amtdue).toFixed(2)).toLocaleString() : '') + '</td>' +
                                '<td></td>' +
                            '</tr>';
            }
        }

        function addToPayables(acctNo) {
            if ($('#btn-' + acctNo).attr('ischecked') == 'false') {
                $('#btn-' + acctNo).removeClass('text-muted').addClass('text-primary')

                // ADD TO ARRAY
                accountsAdded.push(acctNo)
                // ADD PAYABLE
                totalAmount += parseFloat($('#btn-' + acctNo).attr('amount'))
                amountDue = totalAmount

                $('#btn-' + acctNo).attr('ischecked', 'true')
            } else {
                $('#btn-' + acctNo).removeClass('text-primary').addClass('text-muted')

                // REMOVE ITEM FROM ARRAY
                removeItemFromArray(acctNo)
                totalAmount -= parseFloat($('#btn-' + acctNo).attr('amount'))
                amountDue = totalAmount

                $('#btn-' + acctNo).attr('ischecked', 'false')
            }

            // VALIDATE FORM
            discount3 = 0
            discount5 = 0
            computeAmounts()
            updatePaymentDisplays()
        }

        function computeAmounts(update = false) {
            // COMPUTE 3%
            if(discount3Checked == true) {
                if (discount3 == 0) {
                    discount3 = parseFloat(totalAmount) * .03
                    amountDue = amountDue - discount3
                }                
            } else {
                amountDue = amountDue + discount3
                discount3 = 0;
            }

            // COMPUTE 5%
            if(discount5Checked == true) {
                if (discount5 == 0) {
                    discount5 = parseFloat(totalAmount) * .05
                    amountDue = amountDue - discount5
                }                
            } else {
                amountDue = amountDue + discount5
                discount5 = 0;
            }
        }

        function updatePaymentDisplays() {
            $('#totalAmount').text(Number(totalAmount.toFixed(2)).toLocaleString())
            $('#discount3').text(Number(discount3.toFixed(2)).toLocaleString())
            $('#discount5').text(Number(discount5.toFixed(2)).toLocaleString())
            $('#amountDue').text(Number(amountDue.toFixed(2)).toLocaleString())
            $('#amountPaid').focus()

            change = (parseFloat($('#amountPaid').val()) - amountDue).toFixed(2).toLocaleString()

            if (parseFloat(change)) {
                $('#change').val(change)
                if (change > -1 && !jQuery.isEmptyObject($('#orNumber').val()) && !jQuery.isEmptyObject(accountsAdded)) {
                    buttonEnablers(true)                        
                } else {
                    buttonEnablers(false)
                }
            } else {
                $('#change').val('')
                buttonEnablers(false)
            } 
        }

        function removeItemFromArray(id) {
            var index = accountsAdded.indexOf(id)
            if (index > -1) {
                accountsAdded.splice(index, 1)
            }
        }

        function getDetails(res) {
            if (!jQuery.isEmptyObject(res)) {
                var ttlAccts = 0;
                var ttlActive = 0;
                var ttlBills = 0;
                var ttlPaid = 0;
                $.each(res, function(index, element) {
                    if (res[index]['AccountStatus'] != 'DISCONNECTED') {
                        ttlActive += 1;
                    }

                    if (!jQuery.isEmptyObject(res[index]['ORNumber'])) {
                        ttlPaid += 1;
                    }

                    if (!jQuery.isEmptyObject(res[index]['BillNumber'])) {
                        ttlBills += 1;
                        // COMPUTE TOTAL ON INITALIZE
                        if (jQuery.isEmptyObject(res[index]['ORNumber'])){
                            // ADD ACCOUNTS ON INITIALIZE
                            accountsAdded.push(res[index]['AccountNumber'])
                            totalAmount += parseFloat(res[index]['NetAmount'])
                        }                        
                    }

                    ttlAccts += 1;

                    // SHOW AMOUNTS
                    amountDue = totalAmount
                    $('#totalAmount').text(Number(totalAmount.toFixed(2)).toLocaleString())
                    $('#amountDue').text(Number(amountDue.toFixed(2)).toLocaleString())
                })

                $('#total-acts').text(ttlAccts)
                $('#active-total').text(ttlActive)
                $('#bills-total').text(ttlBills)
                $('#indiv-paid').text(ttlPaid)
            }
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

        // DETECT ENTER
        $(document).keydown(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);  
            if(keycode == '13'){
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#orNumber').val()) && !jQuery.isEmptyObject(accountsAdded)) {  
                        transact()                     
                    } else {

                    }
                } else {

                }
            } 
        });

        function transact() {
            $.ajax({
                url : "{{ route('paidBills.save-bapa-payments') }}",
                type : 'GET',
                data : {
                    Period : $('#ServicePeriod').val(),
                    AccountNumbers : accountsAdded,
                    IsDiscount3 : discount3Checked,
                    Discount3 : discount3,
                    IsDiscount5 : discount5Checked,
                    Discount5 : discount5,
                    TotalAmountPaid : amountDue,
                    ORNumber : $('#orNumber').val(),
                    BAPAName : "{{ urlencode($bapaName) }}",
                    SubTotal : totalAmount
                },
                success : function(res) {
                    alert('PRINT OR BAPA')
                    // location.reload()
                    console.log(res)
                },
                error : function(err) {
                    alert('An error occurred during the transaction')
                }
            })
        }
    </script>
@endpush