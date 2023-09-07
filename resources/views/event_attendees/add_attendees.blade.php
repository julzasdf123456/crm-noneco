@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3><strong>{{ $events->EventTitle }}</strong> <span class="text-muted">Add Attendance</span></h3>
                </div>
                <div class="col-sm-6">
                  <a class="btn btn-default float-right"
                     href="{{ route('events.show', [$events->id]) }}">
                     Back
                  </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="row">
            {{-- EVENT DETAILS --}}
            <div class="col-lg-5 col-md-6">
                <div class="card shadow-none" style="height: 80vh;">
                    <div class="card-header">
                        <input type="text" name="Search" id="Search" class="form-control" placeholder="Search account number or account name" autofocus>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-bordered table-sm" id="search-table">
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

            {{-- ATTENDEES --}}
            <div class="col-lg-7 col-md-6">
                <div class="card shadow-none" style="height: 80vh;">
                    <div class="card-header">
                        <span class="card-title"><i class="fas fa-info-circle ico-tab"></i> Attendees</span>
                        <div class="card-tools">
                            <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-walkin"><i class="fas fa-plus ico-tab-mini"></i>Add Walk-in</button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-sm table-hover table-bordered" id="attendees-table">
                            <thead>
                                <th>Account No</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Date/Time</th>
                                <th></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection

{{-- APPREHEND --}}
<div class="modal fade" id="modal-walkin" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Walk-in Attendee</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="Name">Name</label>
                    <input type="text" name="Name" id="Name" value="" class="form-control" autofocus>

                    <label for="Address">Address</label>
                    <input type="text" name="Address" id="Address" value="" class="form-control">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-walkin">Save</button>
            </div>
        </div>
    </div>
</div>

@push('page_scripts')
    <script>
        $(document).ready(function() {
            getAttendees("{{ $events->id }}")

            $('#Search').keyup(function() {
                var len = this.value.length
                if (len > 5) {
                    search(this.value)
                } else {
                    $('#search-table tbody tr').remove()
                }
            })

            $('#save-walkin').on('click', function() {
                if (jQuery.isEmptyObject($('#Name').val())) {
                    Toast.fire({
                        icon : 'warning',
                        text : 'Please input name'
                    })
                } else {
                    addWalkin()
                }
            })
        })

        function search(regex) {
            $('#search-table tbody tr').remove()
            $.ajax({
                url : "{{ route('eventAttendees.search-account-for-attendees') }}",
                type : 'GET',
                data : {
                    Search : regex,
                },
                success : function(res) {
                    $('#search-table tbody').append(res)
                },
                error : function (err) {
                    Toast.fire({
                        icon : 'error',
                        text : 'Error searching'
                    })
                }
            })
        }

        function addToAttendance(id) {
            $.ajax({
                url : "{{ route('eventAttendees.add-attendance') }}",
                type : 'GET',
                data : {
                    id : id,
                    EventId : "{{ $events->id }}",
                },
                success : function(res) {
                    if (res != 'exist') {
                        getAttendees("{{ $events->id }}")
                        Toast.fire({
                            icon : 'success',
                            text : 'Attendee added!'
                        })
                    } else {
                        Toast.fire({
                            icon : 'warning',
                            text : 'Attendee already exists!'
                        })
                    }                    
                },
                error : function(err) {
                    Toast.fire({
                        icon : 'error',
                        text : 'Error adding attendee'
                    })
                }
            })
        }

        function getAttendees(id) {
            $('#attendees-table tbody tr').remove()
            $.ajax({
                url : "{{ route('eventAttendees.get-attendees') }}",
                type : 'GET',
                data : {
                    id : id,
                },
                success : function(res) {
                    $('#attendees-table tbody').append(res)
                },
                error : function(err) {
                    Toast.fire({
                        icon : 'error',
                        text : 'Error getting attendees'
                    })
                }
            })
        }

        function deleteAttendee(id) {
            Swal.fire({
                title: 'Confirm Delete',
                text: "Are you sure you want to delete this attendance entry? This can't be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url : "{{ route('eventAttendees.delete') }}",
                        type : "GET",
                        data : {
                            id : id,
                        },
                        success : function(res) {
                            Toast.fire({
                                icon : 'success',
                                text : 'Attendee deleted!'
                            })
                            getAttendees("{{ $events->id }}")
                        },
                        error : function(err) {
                            Toast.fire({
                                icon : 'error',
                                text : 'Error deleting attendee!'
                            })
                        }
                    })
                }
            })

            
        }

        function addWalkin() {
            $.ajax({
                url : "{{ route('eventAttendees.add-walkin') }}",
                type : 'GET',
                data : {
                    EventId : "{{ $events->id }}",
                    Name : $('#Name').val(),
                    Address : $('#Address').val(),
                },
                success : function(res) {
                    if (res != 'exist') {
                        getAttendees("{{ $events->id }}")
                        Toast.fire({
                            icon : 'success',
                            text : 'Attendee added!'
                        })
                    } else {
                        Toast.fire({
                            icon : 'warning',
                            text : 'Attendee already exists!'
                        })
                    } 
                    $('#modal-walkin').modal('hide')
                    $('#Name').val('')
                    $('#Address').val('')
                },
                error : function(err) {
                    Toast.fire({
                        icon : 'error',
                        text : 'Error adding attendee'
                    })
                    $('#modal-walkin').modal('hide')
                    $('#Name').val('')
                    $('#Address').val('')
                }
            })
        }
    </script>
@endpush
