@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <p><strong><span class="badge-lg bg-warning">Step 3</span>Apply for Service Connection</strong></p>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-12">
            <div class="content px-3">


                @include('adminlte-templates::common.errors')

                <div class="card">

                    <div class="card-header">
                        {{-- <span class="card-title">Step <strong>1</strong> of 4 - <strong>Applicant Basic Account Info</strong></span> --}}
                        <span class="card-title">Applicant Basic Account Info</strong></span>

                        <div class="card-tools">
                            <a href="{{ route('memberConsumers.show', [ $memberConsumer->ConsumerId ]) }}">Skip For Now</a>
                        </div>
                    </div>

                    {!! Form::open(['route' => 'serviceConnections.store']) !!}

                    <div class="card-body">

                        <div class="row">
                            <input type="hidden" name="Status" value="For Inspection">

                            <input type="hidden" name="Office" value="{{ env("APP_LOCATION") }}">

                            @include('service_connections.fields')
                        </div>

                    </div>

                    <div class="card-footer">
                        {!! Form::submit('Next', ['class' => 'btn btn-primary']) !!}
                    </div>

                    {!! Form::close() !!}

                </div>
             </div>
        </div>
    </div>
@endsection
