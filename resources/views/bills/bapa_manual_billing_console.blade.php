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
            <div class="col-sm-6">
                <h4><strong>{{ urldecode($bapaName) }}</strong> Manual Read and Bill</h4>
            </div>
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="Period" class="col-sm-3">Set Billing Month</label>
                    <select id="Period" class="form-control col-sm-5 mx-sm-3">
                        @for ($i = 0; $i < count($months); $i++)
                            <option value="{{ $months[$i] }}">{{ date('F Y', strtotime($months[$i])) }}</option>
                        @endfor
                    </select>
                    <button id="setBtn" class="btn btn-primary col-sm-3 mb-2"><i class="fas fa-check ico-tab-mini"></i>Set</button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .select {
        background-color: #bbdefb;
    }
</style>

<div class="row">
    {{-- TO BE READ --}}
    <div class="col-lg-3">
        <div class="card" style="height: 80vh">
            <div class="card-header border-0">
                <span class="card-title"><i class="fas fa-exclamation-circle text-danger ico-tab"></i>Accounts to be Billed</span>
            </div>
            <div class="card-body table-responsive px-0">
                <table class="table table-sm table-hover" id="previous-table">
                    <thead>
                        <th>Acct. Name</th>
                        <th>Previous Reading</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- READING FORM --}}
    <div class="col-lg-6">
        <div class="card" style="height: 80vh">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle text-primary ico-tab"></i>Read and Bill Form</span>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Account Number</td>
                        <th id="acctNo"></th>
                    </tr>
                    <tr>
                        <td>Account Name</td>
                        <th id="acctName"></th>
                    </tr>
                    <tr>
                        <td>Account ID</td>
                        <th id="acctId"></th>
                    </tr>
                    <tr>
                        <td>Account Type</td>
                        <th id="acctType"></th>
                    </tr>
                    <tr>
                        <td>Previous Reading</td>
                        <th>
                            <input id="prevReading" type="number" step="any" class="form-control" readonly>
                        </th>
                    </tr>
                    <tr>
                        <td>Present Reading</td>
                        <th>
                            <input id="presReading" type="number" step="any" class="form-control">
                        </th>
                    </tr>
                    <tr>
                        <td>Kwh Used</td>
                        <th>
                            <input id="kwhUsed" type="number" step="any" class="form-control">
                        </th>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <th>
                            <input id="remarks" type="text" class="form-control">
                        </th>
                    </tr>
                </table>
                <div class="form-group row">
                    <button class="btn btn-default col-sm-3 mx-sm-3" id="prevBtn" onclick="moveCursor('backward')"><i class="fas fa-backward ico-tab"></i>Previous</button>
                    <button class="btn btn-primary col-sm-4 mx-sm-3" id="readAndBillBtn"><i class="fas fa-check-circle ico-tab"></i>Read and Bill</button>
                    <button class="btn btn-default col-sm-3 mx-sm-3" id="presBtn" onclick="moveCursor('forward')"><i class="fas fa-forward ico-tab"></i>Next</button>
                </div>
                
                <div class="divider"></div>

                <div class="row">
                    <div class="col-lg-12">
                        <p><i>Bill Preview</i></p>
                        <table class="table table-sm">
                            <tr>
                                <td>Amount Due</td>
                                <th id="amnt-due"></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- READ AND BILLED --}}
    <div class="col-lg-3">
        <div class="card" style="height: 80vh">
            <div class="card-header border-0">
                <span class="card-title"><i class="fas fa-check-circle text-success ico-tab"></i>Accounts Already Billed</span>
            </div>
            <div class="card-body table-responsive px-0">

            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        var readingList = {}
        var activeObject = {}
        var activeObjectIndex = 0
        var period = ''

        $(document).ready(function() {
            $('#setBtn').on('click', function() {
                fetchReadables()
            })

            $('#presReading').keyup(function() {
                $('#kwhUsed').val(getKwhUsed())
                previewBill()
            })
        })

        function fetchReadables() {
            readingList = {}
            activeObject = {}
            activeObjectIndex = 0

            period = $('#Period').val()

            $('#previous-table tbody tr').remove()
            $.ajax({
                url : "{{ route('api.readAndBillApi.get-bapa-account-list') }}",
                type : 'GET',
                data : {
                    BAPAName : "{{ urldecode($bapaName) }}",
                    ServicePeriod : period,
                },
                success : function(res) {
                    if (jQuery.isEmptyObject(res)) {

                    } else {
                        readingList = res
                        
                        $.each(res, function(index, element) {
                            $('#previous-table tbody').append(addRowToPrevTable(res[index]['id'], res[index]['ServiceAccountName'], res[index]['KwhUsed']))
                        })

                        activeObject = readingList[activeObjectIndex]
                        addActiveObjectToQueue(activeObject)
                    }                    
                },
                error : function() {
                    Swal.fire({
                        title: 'Oops...',
                        text: 'An error occurred while fetching data. Contact support immediately!',
                        icon: 'error',
                    })
                }
            })
        }

        function addRowToPrevTable(accountNo, accountName, kwhUsed) {
            return '<tr id="' + accountNo + '" onclick=selectConsumer("' + accountNo + '")>' +
                        '<td>' + accountName + '</td>' +
                        '<td>' + kwhUsed + '</td>' +
                    '</tr>'
        }

        function addActiveObjectToQueue(activeObject) {
            if (jQuery.isEmptyObject(activeObject)) {

            } else {
                // CLEAR READING FIELDS
                $('#presReading').val('')
                $('#kwhUsed').val('')

                $('#acctNo').text(activeObject['OldAccountNo'])
                $('#acctId').text(activeObject['id'])
                $('#acctName').text(activeObject['ServiceAccountName'])
                $('#acctType').text(activeObject['AccountType'])
                if (jQuery.isEmptyObject(activeObject['KwhUsed']) | activeObject['KwhUsed']==null) {
                    $('#prevReading').val('0')
                } else {
                    $('#prevReading').val(activeObject['KwhUsed'])
                }

                // add color to active queue
                $('#' + activeObject['id']).addClass('select')

                $('#presReading').focus()
            }
        }

        function moveCursor(direction) {
            if (direction == 'forward') {
                $('#' + activeObject['id']).removeClass('select')

                if (activeObjectIndex == readingList.length-1) {
                    activeObjectIndex = 0
                } else {
                    activeObjectIndex++
                }
                
                activeObject = readingList[activeObjectIndex]
                addActiveObjectToQueue(activeObject)
            } else { // backward
                $('#' + activeObject['id']).removeClass('select')

                if (activeObjectIndex == 0) {
                    activeObjectIndex = readingList.length-1
                } else {
                    activeObjectIndex--
                }
                
                activeObject = readingList[activeObjectIndex]
                addActiveObjectToQueue(activeObject)
            }
        }

        function getIndexOfSelected(id) {
            var index = 0
            index = readingList.findIndex(x => x.id === id)

            return index
        }

        function selectConsumer(id) {
            $('#' + activeObject['id']).removeClass('select')

            activeObjectIndex = getIndexOfSelected(id)
            activeObject = readingList[activeObjectIndex]
            addActiveObjectToQueue(activeObject)
        }

        function getKwhUsed() {
            var prev = parseFloat($('#prevReading').val())
            var pres = parseFloat($('#presReading').val())
            var usedkwh = pres - prev

            return usedkwh.toFixed(2)
        }

        // DETEC IF ENTER KEY IS PRESSED
        $(document).keypress(function(event){
            // var keycode = (event.keyCode ? event.keyCode : event.which);
            var keycode = event.keyCode
            if(keycode == '13'){
                readAndBill()
            }
        });

        $(document).keydown(function(event){
            // var keycode = (event.keyCode ? event.keyCode : event.which);
            var keycode = event.keyCode
            if (keycode == '39') { // right arrow
                moveCursor('forward')
            } else if (keycode == '37') { // left arrow
                moveCursor('backward')
            }
        });

        function readAndBill() {
            if (jQuery.isEmptyObject($('#presReading').val()) | jQuery.isEmptyObject($('#kwhUsed').val())) {
                Swal.fire({
                        title: 'Missing Reading',
                        text: 'Kindly fill in the Present Reading or the Kwh Used Fields',
                        icon: 'error',
                    })
            } else {
                if (getKwhUsed() < 0) {
                    Swal.fire({
                            title: 'Invalid Reading',
                            text: 'Kwh used should not be less than Zero',
                            icon: 'error',
                        })
                } else {
                    // REMOVE FROM PREVIOUS QUEUE
                    $('#' + activeObject['id']).remove()

                    // ADD TO CURRENT EDIT
                    moveCursor('forward')
                }
                
            }            
        }

        function previewBill() {
            $.ajax({
                url : "{{ route('bills.get-bill-computation') }}",
                type : 'GET',
                data : {
                    id : activeObject['id'],
                    KwhUsed : $('#kwhUsed').val(),
                    PreviousKwh : $('#prevReading').val(),
                    PresentKwh : $('#presReading').val(),
                    ServicePeriod : period
                },
                success : function(res) {
                    if (!jQuery.isEmptyObject(res)) {
                        $('#amnt-due').text(Number(parseFloat(res['NetAmount']).toFixed(2)).toLocaleString())
                    }
                },
                error : function(err) {
                    Swal.fire({
                        title: 'Oops...',
                        text: 'An error occurred while fetching data. Contact support immediately!',
                        icon: 'error',
                    })
                }
            })
        }
    </script>
@endpush