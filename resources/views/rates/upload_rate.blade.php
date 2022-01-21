@php
    // GET PREVIOUS MONTHS
    for ($i = 0; $i <= 12; $i++) {
        $months[] = date("Y-m-01", strtotime( date( 'Y-m-01' )." -$i months"));
    }
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Upload New Unbundled Rates Template</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-6 offset-md-3 col-md-8 offset-md-2">

            @include('flash::message')

            <div class="clearfix"></div>

            <div class="card">
                <form method="POST" enctype="multipart/form-data" action="{{ route('rates.validate-rate-upload') }}" >
                <div class="card-header">
                    <span class="card-title">Upload Form</span>
                </div>
                <div class="card-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!-- Serviceperiod Field -->
                    <div class="form-group col-lg-8 offset-lg-2 col-md-12 col-sm-12">
                        {!! Form::label('ServicePeriod', 'Rates For the Month:') !!}
                        <select name="ServicePeriod" id="ServicePeriod" class="form-control">
                            @for ($i = 0; $i < count($months); $i++)
                                <option value="{{ $months[$i] }}">{{ date('F Y', strtotime($months[$i])) }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- RateFor Field -->
                    <div class="form-group col-lg-8 offset-lg-2 col-md-12 col-sm-12">
                        {!! Form::label('RateFor', 'Rates For District:') !!}
                        <select name="RateFor" id="RateFor" class="form-control">
                            <option value="OVERALL">OVERALL</option>
                            <option value="CADIZ">CADIZ</option>
                            <option value="CALATRAVA">CALATRAVA</option>
                            <option value="E.B. MAGALONA">E.B. MAGALONA</option>
                            <option value="ESCALANTE">ESCALANTE</option>
                            <option value="MANAPLA">MANAPLA</option>
                            <option value="SAGAY">SAGAY</option>
                            <option value="SAN CARLOS">SAN CARLOS</option>
                            <option value="TOBOSO">TOBOSO</option>
                            <option value="VICTORIAS">VICTORIAS</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-8 offset-lg-2 col-md-12 col-sm-12">
                        {!! Form::label('file', 'Select Rate Template (.xls, .xlsx):') !!}
                        <input type="file" name="file" placeholder="Choose File" id="file">
                        <span class="text-danger">{{ $errors->first('file') }}</span>
                    </div>
                </div>
                <div class="card-footer">
                    {!! Form::submit('Upload', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('rates.index') }}" class="btn btn-default">Done</a>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection