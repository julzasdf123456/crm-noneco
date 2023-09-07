@php
    use App\Models\ServiceAccounts;
    use App\Models\Bills;
@endphp
@extends('layouts.app')

@push('page_css')
   <style>
      table {
         width: 100%;
      }

      table td,
      table th {
         background-color: rgba(0, 0, 0, 0);
         font-family: monospace;
         font-size: .8em;
      }
   </style>
@endpush

@section('content')
<section class="content-header">
   <div class="container-fluid">
       <div class="row mb-2">
           <div class="col-sm-6">
               <h4>Create Demand Letter | Per Route</h4>
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
               <input type="text" id="assOf" placeholder="As Of" class="form-control form-control-sm col-lg-9" value="{{ date('Y-m-d') }}">

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
               <label for="town" class="col-lg-3">Town</label>
               <select name="Town" id="town" class="form-control form-control-sm col-lg-9">
                  @foreach ($towns as $item)
                      <option value="{{ $item->id }}">{{ $item->Town }}</option>
                  @endforeach
               </select>
            </div>
            <div class="form-group row">
               <label for="route" class="col-lg-3">Search Route</label>
               <input type="text" id="route" placeholder="Search route" class="form-control form-control-sm col-lg-9" autofocus value="{{ $route != null ? $route : '' }}">
            </div>
            
         </div>
         <div class="card-body table-responsive p-0">
            <table class="table table-hover table-sm table-bordered" id="res-table">
               <thead>
                  <th>Routes</th>
                  <th>No. of Accounts</th>
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
         <div class="card-body">
            <table>
               <thead>
                   <th style="width: 50px;"></th>
                   <th>Account No</th>
                   <th>Consumer Name</th>
                   <th class="text-right">No. Of Bills</th>
                   <th class="text-right">Total Amount Due</th>
               </thead>
               <tbody>
                   @php
                       $i = 0;
                   @endphp
                   @foreach ($data as $item)
                       <tr>
                           <td>{{ $i+1 }}</td>
                           <td>{{ $item->OldAccountNo }}</td>
                           <td>{{ $item->ServiceAccountName }}</td>
                           <td class="text-right">{{ $item->BillingMonths }}</td>
                           <td class="text-right">{{ number_format($item->TotalAmount, 2) }}</td>
                       </tr>
                       @php
                           $i++;
                       @endphp
                   @endforeach
               </tbody>
           </table> 
         </div>
      </div>
   </div>
</div>

@endsection

@push('page_scripts')
    <script>
      var group = ""
      $(document).ready(function() {
         $('#route').keyup(function() {
            var word = this.value
            getRoutes(word)
         })

         $('#print-btn').on('click', function() {
            var route = "{{ $route }}"

            if (jQuery.isEmptyObject(route)) {
               Swal.fire({
                  icon : 'warning',
                  text : 'Select Route first!'
               })
            } else {
               if (route == 0) {
                  Swal.fire({
                     icon : 'warning',
                     text : 'Select Route first!'
                  })
               } else {
                  window.location.href = "{{ url('/demand_letters/print-per-route') }}/" + route + "/" + $('#assOf').val() + "/" + $('#town').val()
               }
            }
         })
      })

      function getRoutes(search) {
         $('#res-table tbody tr').remove()
         $.ajax({
            url : "{{ route('demandLetters.search-route') }}",
            type : "GET",
            data : {
               Search : search,
               Town : $('#town').val()
            },
            success : function(success) {
               $('#res-table tbody').append(success)
            },
            error : function(err) {
               Toast.fire({
                  icon : 'error',
                  text : 'Error getting routes!'
               })
            }
         })
       }

       function go(route) {
         if (jQuery.isEmptyObject($('#assOf').val())) {
            Swal.fire({
               icon : 'warning',
               text : 'Please put date in AS OF field!'
            })
         } else {
            window.location.href = "{{ url('/demand_letters/per-route') }}/" + route + "/" + $('#assOf').val() + "/" + $('#town').val()
         }
       }

    </script>
@endpush

