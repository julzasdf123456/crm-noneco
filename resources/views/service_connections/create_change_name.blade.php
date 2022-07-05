@php
    use App\Models\IDGenerator;
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <span>
                    <h4>Change Name Application</h4>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-12">
        {!! Form::open(['route' => 'serviceConnections.store-change-name']) !!}
        <div class="card">
            <div class="card-body">
                <input type="hidden" name="ConnectionApplicationType" value="Change Name">

                <input type="hidden" name="id" value="{{ IDGenerator::generateID() }}">

                <input type="hidden" name="Status" value="Approved">

                <input type="hidden" name="Office" value="{{ env('APP_LOCATION') }}">

                <!-- Account Number Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('AccountNumber', 'Account ID') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                {!! Form::text('AccountNumber', $account->id, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255, 'readonly' => 'true']) !!}
                            </div>
                        </div>
                    </div> 
                </div>

                <!-- Serviceaccountname Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('ServiceAccountName', 'Service Account Name') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                </div>
                                {!! Form::text('ServiceAccountName', $account->ServiceAccountName, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255, 'readonly' => 'true']) !!}
                            </div>
                        </div>
                    </div> 
                </div>

                <!-- ChangeTo Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('OrganizationAccountNumber', 'Change To') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                </div>
                                {!! Form::text('OrganizationAccountNumber', null, ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000, 'placeholder' => 'Change To']) !!}
                            </div>
                        </div>
                    </div> 
                </div>

                <!-- Town Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('Town', 'Town') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                {!! Form::select('Town', $towns, $account != null ? $account->Town : '', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>    
                </div>

                <!-- Barangay Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('Barangay', 'Barangay') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                {!! Form::select('Barangay', [], null, ['class' => 'form-control',]) !!}
                            </div>
                        </div>
                    </div>    
                </div>

                <!-- Sitio Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('Sitio', 'Sitio') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                {!! Form::text('Sitio', $account != null ? $account->Purok : '', ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000, 'placeholder' => 'Sitio']) !!}
                            </div>
                        </div>
                    </div> 
                </div>

                <!-- Contactnumbers Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('ContactNumber', 'Contact Numbers') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                </div>
                                {!! Form::text('ContactNumber', '-', ['class' => 'form-control','maxlength' => 300,'maxlength' => 300, 'placeholder' => 'Contact Numbers']) !!}
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="divider"></div>

                <!-- Dateofapplication Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('DateOfApplication', 'Date of Change Name Application') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                </div>
                                {!! Form::text('DateOfApplication', date('Y-m-d'), ['class' => 'form-control','id'=>'DateOfApplication']) !!}
                                @push('page_scripts')
                                    <script type="text/javascript">
                                        $('#DateOfApplication').datetimepicker({
                                            format: 'YYYY-MM-DD',
                                            useCurrent: true,
                                            sideBySide: true
                                        })
                                    </script>
                                @endpush
                            </div>
                        </div>
                    </div> 
                </div>

                <!-- Notes Field -->
                <div class="form-group col-sm-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            {!! Form::label('Notes', 'Reason for Change of Name') !!}
                        </div>

                        <div class="col-lg-9 col-md-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-comments"></i></span>
                                </div>
                                {!! Form::textarea('Notes', null, ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000, 'placeholder' => 'Type reason', 'rows' => '2']) !!}
                            </div>
                        </div>
                    </div> 
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Next', ['class' => 'btn btn-primary']) !!}
                <!-- <a href="{{ route('serviceConnections.index') }}" class="btn btn-default">Cancel</a> -->
            </div>

        {!! Form::close() !!}   
        </div>
    </div>
    
</div>

<p id="Def_Brgy" style="display: none;">{{ $account->Barangay }}</p>
@endsection