@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h4>Service Connection Collection</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            {{-- QUEUE --}}
            <div class="col-lg-3 col-md-5">
                <div class="card">
                    <div class="card-header border-0">
                        <span class="card-title">Queue</span>
                    </div>
                    <div class="card-body table-responsive px-0">
                        <table class="table table-hover">
                            <thead>
                                <th>Applicants</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($applications as $item)
                                    <tr>
                                        <td>{{ $item->ServiceAccountName }}</td>
                                        <td class="text-right">
                                            <button onclick="fetchPayables('{{ $item->id }}', '{{ $item->LoadCategory }}')" class="btn btn-sm btn-link {{ $item->LoadCategory == 'above 5kVa' ? 'text-danger' : 'text-primary' }}"><i class="fas fa-forward"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PAYMENT MODULE --}}
            <div class="col-lg-9 col-md-7">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Transaction</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-7 col-md-6">
                                <span>
                                    <h5><strong id="consumerName">...</strong></h5>
                                    <address class="text-muted" id="address">...</address>
                                </span>
    
                                <table class="table table-sm table-hover" id="payable-table">
                                    <thead>
                                        <th>Payables</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">VAT</th>
                                        <th class="text-right">Total</th>
                                    </thead>
                                    <tbody>
    
                                    </tbody>
                                </table>

                                <div id="power-load-area" class="gone">
                                    <p><strong>Power Load Payables</strong></p>
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <th>Particulars</th>
                                            <th>Amount</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        {{-- FORM --}}
                                        <table class="table table-borderless">
                                            <tr id="power-load-row" class="gone">
                                                <td>Power Load</td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control text-right" style="font-size: 1.5em;" id="powerLoadAmount" readonly="true">
                                                </td>
                                            </tr>
                                            <tr id="sa-row" class="gone">
                                                <td>Service Application</td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control text-right" style="font-size: 1.5em;" id="saAmount" readonly="true">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Total</td>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        var activeId = null;
        var total = 0;
        var saTotal = 0;
        var powerLoadTtl = 0;
        var loadCategory = "";

        function fetchPayables(id, category) {
            activeId = id;
            total = 0;
            saTotal = 0;
            powerLoadTtl = 0;
            loadCategory = category;
            $('#amountPaid').val('')
            $('#change').val('')
            $('#ornumber').val('')
            // GET TOTAL
            $.ajax({
                url : '/transaction_indices/get-payable-total',
                type : 'GET',
                data : {
                    svcId : id,
                },
                success : function(res) {
                    if (jQuery.isEmptyObject(res)) {
                        // $('#totalAmount').val('')
                        $('#consumerName').text('')
                        $('#address').text('')
                    } else {
                        total += parseFloat(res['Total'])
                        saTotal = parseFloat(res['Total'])
                        $('#consumerName').text(res['ServiceAccountName'])
                        $('#address').text(res['Barangay'] + ', ' + res['Town'])
                    }

                    // IF POWER LOAD
                    if (category == 'above 5kVa') {
                        $('#power-load-area').removeClass('gone');
                        $('#power-load-row').removeClass('gone');
                        $('#sa-row').removeClass('gone');

                        $.ajax({
                            url : '/transaction_indices/get-power-load-payables',
                            type : 'GET',
                            data : {
                                ServiceConnectionId : id,
                            },
                            success : function(res) {
                                $('#power-load-area tbody tr').remove()
                                if (!jQuery.isEmptyObject(res)) {
                                    $('#power-load-area tbody').append(addRowToPowerLoad('Materials', res['SubTotal']))
                                    $('#power-load-area tbody').append(addRowToPowerLoad('Transformer', res['TransformerTotal']))
                                    $('#power-load-area tbody').append(addRowToPowerLoad('Total Labor Cost', res['LaborCost']))
                                    $('#power-load-area tbody').append(addRowToPowerLoad('Handling Cost', res['HandlingCost']))
                                    $('#power-load-area tbody').append(addRowToPowerLoad('VAT', res['TotalVAT']))
                                    $('#power-load-area tbody').append(addRowToPowerLoad('Total', res['Total']))
                                    total += parseFloat(res['Total'])
                                }

                                $('#totalAmount').val(Number(total.toFixed(2)).toLocaleString())
                                $('#powerLoadAmount').val(Number(parseFloat(res['Total']).toFixed(2)).toLocaleString())
                                $('#saAmount').val(Number(saTotal.toFixed(2)).toLocaleString())
                            },
                            error : function(error) {
                                alert(error)
                            }
                        })
                    } else {
                        $('#power-load-area').addClass('gone');
                        $('#power-load-row').addClass('gone');
                        $('#sa-row').addClass('gone');
                        $('#powerLoadAmount').val('')
                        $('#saAmount').val('')
                        $('#totalAmount').val(Number(total.toFixed(2)).toLocaleString())
                    }
                }, 
                error : function(err) {
                    alert('An error occurred while fetching total payables for this application')
                    console.log(err)
                }
            })

            // GET PAYABLES
            $.ajax({
                url : '/transaction_indices/get-payable-details',
                type : 'GET',
                data : {
                    svcId : id,
                },
                success : function(res) {
                    $('#payable-table tbody tr').remove();
                    if (jQuery.isEmptyObject(res)) {

                    } else {
                        $.each(res, function(index, element) {
                            $('#payable-table tbody').append(addRowToTable(res[index]['Particular'], res[index]['Amount'], res[index]['Vat'], res[index]['Total']))
                        })
                    }
                    $('#ornumber').focus()
                }, 
                error : function(err) {
                    alert('An error occurred while fetching payables for this application')
                    console.log(err)
                }
            })
        }

        function addRowToPowerLoad(particular, amount) {
            return '<tr>' +
                    '<td>' + particular + '</td>' +
                    '<th class="text-right">' + Number(parseFloat(amount).toFixed(2)).toLocaleString() + '</th>' +
                '</tr>';
        }

        function addRowToTable(payables, amount, vat, total) {
            return "<tr>" + 
                        "<td>" + payables + "</td>" +
                        "<td class='text-right'>" + Number(parseFloat(amount).toFixed(2)).toLocaleString() + "</td>" +
                        "<td class='text-right'>" + vat + "</td>" +
                        "<th class='text-right'>" + total + "</th>" +
                    "</tr>"
        }

        function computeTotal(powerLoad, serviceConnection) {
            return powerLoad + serviceConnection;
        }

        $(document).ready(function() {
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

            $(document).keypress(function(event){
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#ornumber').val())) {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if(keycode == '13'){
                            saveAndPrint('Cash')
                        }                      
                    } else {

                    }
                } else {

                }  
            });

            $('#cashBtn').on('click', function() {
                if (parseFloat(change)) {
                    if (change > -1 && !jQuery.isEmptyObject($('#ornumber').val())) {
                        saveAndPrint('Cash')                    
                    } else {

                    }
                } else {

                }  
            })
        })

        function saveAndPrint(paymentUsed) {
            $.ajax({
                url : '/transaction_indices/save-and-print-or-service-connections',
                type : 'GET',
                data : {
                    svcId : activeId,
                    ORNumber : $('#ornumber').val(),
                    PaymentUsed : paymentUsed,
                    Total : total,
                    LoadCategory : loadCategory,
                },
                success : function(res) {
                    window.location.href = "{{ url('/transaction_indices/print-or-service-connections') }}" + "/" + res['id'];
                },
                error : function(err) {
                    alert('An error occurred while performing the transaction')
                }
            })
        }
    </script>
@endpush