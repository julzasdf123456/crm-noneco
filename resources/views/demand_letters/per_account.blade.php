@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
@endphp
@extends('layouts.app')

@section('content')
<section class="content-header">
   <div class="container-fluid">
       <div class="row mb-2">
           <div class="col-sm-6">
               <h4>Create Demand Letter | Per Account</h4>
           </div>
       </div>
   </div>
</section>

<div class="row">
   {{-- DAYS --}}
   <div class="col-lg-4">
      <div class="card shadow-none">
         <div class="card-header">
            <div class="form-group row">
               <label for="assOf" class="col-lg-3">As Of</label>
               <input type="text" id="assOf" placeholder="As Of" class="form-control col-lg-9" value="{{ date('Y-m-d') }}">

               @push('page_scripts')
                  <script type="text/javascript">
                     $('#assOf').datetimepicker({
                           format: 'YYYY-MM-DD',
                           useCurrent: true,
                           sideBySide: true
                     })
                  </script>
               @endpush
            </div>
            <div class="form-group row">
               <label for="search" class="col-lg-3">Search</label>
               <input type="text" id="search" placeholder="Search account number or name" class="form-control col-lg-9" autofocus value="{{ $accountNo != null ? $accountNo : '' }}">
            </div>
            
         </div>
         <div class="card-body table-responsive p-0">
            <table class="table table-hover table-sm table-bordered" id="res-table">
               <thead>
                  <th>Account No</th>
                  <th>Account Name</th>
                  <th></th>
               </thead>
               <tbody>

               </tbody>
            </table>
         </div>
      </div>
   </div>

   {{-- ACCOUNTS --}}
   <div class="col-lg-8">
      <div class="card shadow-none">
         <div class="card-header border-0">
            <div class="card-tools">
               <button class="btn btn-sm btn-warning" id="print-btn"><i class="fas fa-print ico-tab-mini"></i>Print</button>
            </div>
         </div>
         <div class="card-body table-responsive">
            <h4 class="text-center">DEMAND LETTER</h4>

            <span>Date: <strong>{{ date('F d, Y') }}</strong></span><br>
            <span>Account Name: <strong>{{ $serviceAccounts != null ? $serviceAccounts->ServiceAccountName : '-' }}</strong></span><br>
            <span>Account No.: <strong>{{ $serviceAccounts != null ? $serviceAccounts->OldAccountNo : '-' }}</strong></span><br>
            <span>Address: <strong>{{ $serviceAccounts != null ? ServiceAccounts::getAddress($serviceAccounts) : '-' }}</strong></span><br><br>

            <p>Sir/Madamme,</p>

            <p style="text-indent: 30px;">We are writing about your overdue electric account which remain outstanding as follows:</p>

            <table class="table table-sm table-hover">
               <thead>
                  <th>Billing Month</th>
                  <th class="text-right">Amount</th>
                  <th class="text-right">Surcharge</th>
                  <th class="text-right">Interest</th>
                  <th class="text-right">Amount Due</th>
               </thead>
               <tbody>
                  @if ($serviceAccounts != null)
                     @php
                        $total = 0;
                     @endphp
                      @foreach ($bills as $item)
                          <tr id="{{ $item->id }}">
                              <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                              <td class="text-right">{{ is_numeric($item->NetAmount) ? number_format($item->NetAmount, 2) : $item->NetAmount }}</td>
                              <td class="text-right">{{ number_format(Bills::getSurchargeOnly($item), 2) }}</td>
                              <td class="text-right">{{ number_format(Bills::getInterestOnly($item), 2) }}</td>
                              @php
                                 $tmpTotal = floatval(Bills::assessDueBillAndGetSurcharge($item)) + (is_numeric($item->NetAmount) ? floatval($item->NetAmount) : 0);
                              @endphp
                              <td class="text-right">{{ number_format($tmpTotal, 2) }}</td>
                          </tr>
                          @php
                              $total += $tmpTotal;
                          @endphp
                      @endforeach
                      <tr>
                        <th colspan="4">TOTAL AMOUNT DUE</th>
                        <th class="text-right">{{ number_format($total, 2) }}</th>
                      </tr>
                  @endif
               </tbody>
            </table>
            <br>
            <p style="text-indent: 30px;">{{ env('DEMAND_LETTER_BODY_1') }}</p>
            <p style="text-indent: 30px;">{{ env('DEMAND_LETTER_BODY_2') }}</p>
            <p>Thank you.</p>
            <br>
            <p>Very Truly Yours,</p>
            <br>
            <p style="margin: 0px !important; padding: 0px !important;"><strong>{{ env('SC_ISD_MANAGER') }}</strong></p>
            <p style="text-indent: 30px; margin: 0px !important; padding: 0px !important;">ISD Manager</p>
            <br>
            <br>
            <div class="row">
               <div class="col-lg-6">
                  <span>CC: <span style="margin-left: 30px;">Legal Counsel</span></span><br>
                  <span style="margin-left: 55px;">OGM - Audit, ISD</span><br>
                  <span style="margin-left: 55px;">Area Office</span>
               </div>

               <div class="col-lg-6">
                  <span>Received By: _______________________________</span><br>
                  <span>Date Received: _____________________________</span><br>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@endsection

@push('page_scripts')
    <script>
      var group = ""
      $(document).ready(function() {
         $('#search').keyup(function() {
            var word = this.value

            if (word.length > 4) {
               getAccounts(word)
            }
         })

         $('#print-btn').on('click', function() {
            var acct = "{{ $accountNo }}"

            if (jQuery.isEmptyObject(acct)) {
               Swal.fire({
                  icon : 'warning',
                  text : 'Select Account first!'
               })
            } else {
               if (acct == 0) {
                  Swal.fire({
                     icon : 'warning',
                     text : 'Select Account first!'
                  })
               } else {
                  window.location.href = "{{ url('/demand_letters/print-per-account') }}/" + acct + "/" + $('#assOf').val()
               }
            }
         })
      })

       function getAccounts(search) {
         $('#res-table tbody tr').remove()
         $.ajax({
            url : "{{ route('demandLetters.search-account-for-demand-letter') }}",
            type : "GET",
            data : {
               Search : search,
            },
            success : function(success) {
               $('#res-table tbody').append(success)
            },
            error : function(err) {
               Toast.fire({
                  icon : 'error',
                  text : 'Error getting accounts!'
               })
            }
         })
       }

       function go(accountno) {
         if (jQuery.isEmptyObject($('#assOf').val())) {
            Swal.fire({
               icon : 'warning',
               text : 'Please put date in AS OF field!'
            })
         } else {
            window.location.href = "{{ url('/demand_letters/per-account') }}/" + accountno + "/" + $('#assOf').val()
         }
       }

    </script>
@endpush

