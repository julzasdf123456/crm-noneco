@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h4>Create Ticket</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="card">
                    {!! Form::open(['route' => 'tickets.store']) !!}
                    <div class="card-body">                
                        <div class="row">                 

                            @include('tickets.fields')

                            {{-- HIDDEN FIELDS --}}
                            <input type="hidden" value="{{ Auth::id(); }}" name="UserId">

                            @if ($serviceAccount != null)  
                                <input type="hidden" value="{{ $serviceAccount->id }}" name="AccountNumber">
                            @endif  
                            
                        </div>
                    </div>

                    <div class="card-footer">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('tickets.index') }}" class="btn btn-default">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <span class="card-title">Ticket History</span>
                    </div>

                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>

        
    </div>
@endsection
