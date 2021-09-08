@extends('layouts.app')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Dashboard</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard v1</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    {{-- DASHBOARD COUNTER --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="for-inspection-count">...</h3>

                    <p>Received Applicants For Inspection</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="" data-toggle="modal" data-target="#approved-modal" class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- APPROVED --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success" title="Applicants that are approved during the inspection and are yet to pay the fees.">
                <div class="inner">
                    <h3 id="approved-count">...</h3>

                    <p>Approved Applicants</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="" id="show-approved" data-toggle="modal" data-target="#approved-modal"  class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- METERING DASH --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger" title="Already paid applications without metering data yet.">
                <div class="inner">
                    <h3 id="metering-unassigned">...</h3>

                    <p>Unassigned Meters</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.assigning') }}" class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3 id="energization-count">...</h3>

              <p>Applications For Energization</p>
            </div>
            <div class="icon">
              <i class="fas fa-charging-station"></i>
            </div>
            <a href="{{ route('serviceConnections.energization') }}" class="small-box-footer">Show More <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>     
        
    </div>
</div>
@endsection

{{-- MODALS SECTION --}}
{{-- MODAL FOR APPROVED AND FOR PAYMENT --}}
<div class="modal fade" id="approved-modal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approved Applicants</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="approved-table">
                    <thead>
                        <th>ID</th>
                        <th>Service Account Name</th>
                        <th>Address</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@push('page_scripts')
    <script type="text/javascript">
        // NEW CONNECTIONS DASH
        $.ajax({
            url : '/home/get-new-service-connections',
            type: "GET",
            dataType : "json",
            success : function(response) {
                // $.each(response, function(index, element) {
                //     console.log(response[index]['id']);
                // });
                console.log(response.length);
                $('#for-inspection-count').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });

        // APPROVED
        $.ajax({
            url : '/home/get-approved-service-connections',
            type: "GET",
            dataType : "json",
            success : function(response) {
                console.log(response.length);
                $('#approved-count').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });
        
        // METERING DASH
        $.ajax({
            url : '/home/get-unassigned-meters',
            type: "GET",
            dataType : "json",
            success : function(response) {
                // $.each(response, function(index, element) {
                //     console.log(response[index]['id']);
                // });
                console.log(response.length);
                $('#metering-unassigned').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });

        // FOR ENERGIZATION
        $.ajax({
            url : '/home/get-for-engergization',
            type: "GET",
            dataType : "json",
            success : function(response) {
                // $.each(response, function(index, element) {
                //     console.log(response[index]['id']);
                // });
                console.log(response.length);
                $('#energization-count').text(response.length);
            },
            error : function(error) {
                alert(error);
            }
        });

        // LOAD CONTENT FOR APPROVED
        $('#show-approved').on('click', function() {
            $.ajax({
                url : '/home/get-approved-service-connections',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    $('#approved-table tbody tr').remove();
                    $.each(response, function(index, element) {
                        console.log(response[index]['id']);
                        $('#approved-table tbody').append('<tr><td><a href="/serviceConnections/' + response[index]["id"] + '">' + response[index]['id'] + '</a></td><td>' + response[index]['ServiceAccountName'] + '</td><td>' + response[index]['Barangay'] + ', ' + response[index]['Town'] + '</td></tr>');
                    });
                },
                error : function(error) {
                    alert(error);
                }
            });
        });
    </script>
@endpush
