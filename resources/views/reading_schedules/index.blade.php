@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Reading Schedules</h4>
                </div>
                <div class="col-sm-6">
                    {{-- <a class="btn btn-primary float-right"
                       href="{{ route('readingSchedules.create') }}">
                        Add New
                    </a> --}}
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table">
                    <thead>
                        <th>Meter Readers</th>
                        <th width="80px"></th>
                    </thead>
                    <tbody>
                        @if ($meterReaders != null)
                            @foreach ($meterReaders as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <span>
                                            <a href="{{ route('readingSchedules.view-schedule', [$item->id]) }}" class="ico-tab-mini" title="View All schedule"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('readingSchedules.update-schedule', [$item->id]) }}" class="text-warning" title="Add schedule"><i class="fas fa-calendar-plus"></i></a>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection

