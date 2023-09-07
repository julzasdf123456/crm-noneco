@php
    use App\Models\IDGenerator;
    use Illuminate\Support\Facades\Auth;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h4>Create New Event</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')
        
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
                <div class="card">

                    {!! Form::open(['route' => 'events.store']) !!}

                    <div class="card-body">

                        <input type="hidden" name="id" value="{{ IDGenerator::generateID() }}">
                        <input type="hidden" name="UserId" value="{{ Auth::id() }}">

                        <div class="row">
                            @include('events.fields')
                        </div>

                    </div>

                    <div class="card-footer">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('events.index') }}" class="btn btn-default">Cancel</a>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>

        
    </div>
@endsection
