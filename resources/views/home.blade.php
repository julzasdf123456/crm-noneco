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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                  <a href="{{ route('serviceConnectionMtrTrnsfrmrs.assigning') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            <a href="{{ route('serviceConnections.energization') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>     
        
    </div>
</div>
@endsection

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
                // $.each(response, function(index, element) {
                //     console.log(response[index]['id']);
                // });
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
    </script>
@endpush
