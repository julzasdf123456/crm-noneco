@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3><strong>{{ $events->EventTitle }}</strong></h3>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="row">
            {{-- EVENT DETAILS --}}
            <div class="col-lg-3 col-md-4">
                <div class="card shadow-none">
                    <div class="card-body">
                        <div class="row">
                            @include('events.show_fields')
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATTENDEES --}}
            <div class="col-lg-9 col-md-8">
                <div class="card shadow-none" style="height: 80vh;">
                    <div class="card-header">
                        <span class="card-title"><i class="fas fa-info-circle ico-tab"></i> Attendees</span>
                        <div class="card-tools">
                            <a href="{{ route('eventAttendees.add-attendees', [$events->id]) }}" class="btn btn-primary btn-xs"><i class="fas fa-plus ico-tab-mini"></i>Manage Attendees</a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-sm table-bordered">
                            <thead>
                                <th>Account No</th>
                                <th>Name</th>
                                <th>Address</th>
                            </thead>
                            <tbody>
                                @foreach ($attendees as $item)
                                    <tr>
                                        <td>{{ $item->AccountNumber }}</td>
                                        <td>{{ $item->Name }}</td>
                                        <td>{{ $item->Address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
