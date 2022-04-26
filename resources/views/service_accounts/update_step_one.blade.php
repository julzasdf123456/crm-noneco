@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <span>
                    <h4 style="display: inline; margin-right: 15px;">Update Account Wizzard</h4>
                    <i class="text-muted">Edit Consumer Information</i>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="row px-2">
    <div class="col-lg-12">
        <div class="card">
            {!! Form::model($serviceAccount, ['route' => ['serviceAccounts.update', $serviceAccount->id], 'method' => 'patch']) !!}
            <div class="card-header">
                <span class="card-title">Account Information</span>

                <div class="card-tools">
                    <!-- New Account Number Field -->
                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div class="row">   
                    <!-- Legacy Account Number Field -->
                    <div class="form-group col-lg-7 col-md-8 col-sm-12">
                        <div class="row">
                            <div class="col-lg-2 col-md-4">
                                {!! Form::label('OldAccountNo', 'Legacy Acct. No:') !!}
                            </div>

                            <div class="col-lg-10 col-md-8">
                                <div class="input-group">
                                    {!! Form::text('OldAccountNo', $serviceAccount != null ? $serviceAccount->OldAccountNo : null, ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
                                </div>
                            </div>
                        </div> 
                    </div>

                    <!-- Sequencecode Field -->
                    <div class="form-group col-lg-5 col-md-4 col-sm-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-5">
                                {!! Form::label('SequenceCode', 'Seq. No:') !!}
                            </div>

                            <div class="col-lg-9 col-md-7">
                                <div class="input-group">
                                    {!! Form::text('SequenceCode', $serviceAccount != null ? $serviceAccount->SequenceCode : null, ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="divider"></div>

                    <div class="form-group col-sm-12">
                        <div class="row">
                            <!-- Serviceaccountname Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('ServiceAccountName', 'Account Name:') !!}
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="input-group">
                                    {!! Form::text('ServiceAccountName', $serviceAccount != null ? $serviceAccount->ServiceAccountName : null, ['class' => 'form-control','maxlength' => 600,'maxlength' => 600]) !!}
                                </div>
                            </div>

                            <!-- Areacode Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('AreaCode', 'Areacode:') !!}
                            </div>

                            <div class="col-lg-4 col-md-2">
                                <div class="input-group">
                                    {!! Form::text('AreaCode', $serviceAccount != null ? $serviceAccount->AreaCode : null, ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
                                </div>
                            </div>
                        </div> 
                    </div>

                    {{-- ADDRESS --}}
                    <div class="form-group col-sm-12">
                        <div class="row">
                            <!-- Purok Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Purok', 'Purok:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::text('Purok', $serviceAccount != null ? $serviceAccount->Purok : null, ['class' => 'form-control','maxlength' => 600,'maxlength' => 600]) !!}
                                </div>
                            </div>

                            <!-- Barangay Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Barangay', 'Barangay:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::select('Barangay', $barangays, $serviceAccount != null ? $serviceAccount->Barangay : null, ['class' => 'form-control', 'id' => 'BarangaySA']) !!}
                                </div>
                            </div>

                            <!-- Town Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Town', 'Town:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::select('Town', $towns, $serviceAccount != null ? $serviceAccount->Town : null, ['class' => 'form-control', 'id' => 'TownSA']) !!}
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="divider"></div>

                    {{-- STATUS --}}
                    <div class="form-group col-sm-12">
                        <div class="row">
                            <!-- Purok Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('AccountType', 'Acct. Type:') !!}
                            </div>

                            <div class="col-lg-2 col-md-2">
                                <div class="input-group">
                                    <select class="form-control" name="AccountType" id="AccountType">
                                        @foreach ($accountTypes as $item)
                                            <option value="{{ $item->AccountType }}" f-name="{{ $item->Alias }}" {{ $item->Alias==$serviceAccount->AccountType ? 'selected' : '' }}>{{ $item->AccountType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-1 col-md-1" style="margin-top: 5px;">
                                <i id="AccountTypeFull"></i>
                            </div>

                            <!-- Meter Reader Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('MeterReader', 'Meter Reader:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    <select class="custom-select select2"  name="MeterReader">
                                        @foreach ($meterReaders as $items)
                                            <option value="{{ $items->id }}">{{ $items->MeterReaderCode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Group Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('GroupCode', 'Group:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::text('GroupCode', null, ['class' => 'form-control','maxlength' => 30,'maxlength' => 30]) !!}
                                </div>
                            </div>
                        </div> 
                    </div>

                    {{-- ACCOUNT RETENTION --}}
                    <div class="form-group col-sm-12">
                        <div class="row">
                            <!-- AccountRetention Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('AccountRetention', 'Account Longevity:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::select('AccountRetention', ['Permanent' => 'Permanent', 'Temporary' => 'Temporary'], $serviceAccount != null ? $serviceAccount->AccountRetention : 'Permanent', ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <!-- Account Duration -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Duration', 'Application Duration:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::text('DurationInMonths', ($serviceAccount != null ? $serviceAccount->DurationInMonths : null), ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
                                </div>
                            </div>

                            <!-- Account Expiration -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('AccountExpiration', 'Account Expiration:') !!}
                            </div>

                            <div class="col-lg-3 col-md-2">
                                <div class="input-group">
                                    {!! Form::text('AccountExpiration', ($serviceAccount != null ? $serviceAccount->AccountExpiration : null), ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
                                </div>
                            </div>

                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#AccountExpiration').datetimepicker({
                                        format: 'YYYY-MM-DD',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div> 
                    </div>
                    
                    @push('page_scripts')
                        <script>
                            $(document).ready(function() {
                                $('#AccountTypeFull').text($('#AccountType option:selected').attr('f-name'))

                                $('#AccountType').on('change', function() {
                                    $('#AccountTypeFull').text($('#AccountType option:selected').attr('f-name'))
                                })
                            })
                        </script>
                    @endpush

                    {{-- CHECKBOXES --}}
                    <div class="form-group col-sm-12">
                        <div class="row">

                            <!-- Senior Citizen Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('SeniorCitizen', 'Senior Citizen:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="SeniorCitizen">
                                    <input type="checkbox" value="Yes" name="SeniorCitizen" class="custom-checkbox" {{ $serviceAccount->SeniorCitizen=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Main Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Main', 'Main:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="Main">
                                    <input type="checkbox" value="Yes" name="Main" class="custom-checkbox" {{ $serviceAccount->Main=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- EVAT Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Evat5Percent', '5% EVAT:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="Evat5Percent">
                                    <input type="checkbox" value="Yes" name="Evat5Percent" class="custom-checkbox" {{ $serviceAccount->Evat5Percent=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- EWT Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Ewt2Percent', '2% EWT:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="Ewt2Percent">
                                    <input type="checkbox" value="Yes" name="Ewt2Percent" class="custom-checkbox" {{ $serviceAccount->Ewt2Percent=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div> 
                    </div>

                    {{-- CONTESTABLE --}}
                    <div class="form-group col-sm-12">
                        <div class="row">
                            <!-- Contestable Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Contestable', 'Contestable:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="Contestable">
                                    <input type="checkbox" value="Yes" name="Contestable" class="custom-checkbox" {{ $serviceAccount->Contestable=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Net Metered Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('NetMetered', 'Net Metered:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="NetMetered">
                                    <input type="checkbox" value="Yes" name="NetMetered" class="custom-checkbox" {{ $serviceAccount->NetMetered=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div> 
                    </div>

                    {{-- FOR DISTRIBUTION --}}
                    <div class="form-group col-sm-12">
                        <div class="row">                            
                            <!-- Distribution Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('ForDistribution', 'For Distribution:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="ForDistribution">
                                    <input type="checkbox" id="ForDistribution" value="Yes" name="ForDistribution" class="custom-checkbox" {{ $serviceAccount->ForDistribution=='Yes' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Distribution Account -->
                            <div class="col-lg-1 col-md-1">
                                {!! Form::label('DistributionAccountCode', 'GL Code:') !!}
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="input-group">
                                    <input type="text" id="DistributionAccountCode" name="DistributionAccountCode" class="form-control" value="{{ $serviceAccount->DistributionAccountCode != null ? $serviceAccount->DistributionAccountCode : '' }}">
                                </div>
                            </div>

                            <!-- Organization Field -->
                            <div class="col-lg-1 col-md-2">
                                {!! Form::label('Organization', 'BAPA:') !!}
                            </div>

                            <div class="col-lg-1 col-md-1">
                                <div class="input-group">
                                    <input type="hidden" value="" name="Organization">
                                    <input type="checkbox" id="BAPA" value="BAPA" name="Organization" class="custom-checkbox" {{ $serviceAccount->Organization=='BAPA' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- OrganizationParentAccount/BAPA NAME Field -->
                            <div class="col-lg-1 col-md-1">
                                {!! Form::label('OrganizationParentAccount', 'Select BAPA:') !!}
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="input-group">
                                    <select class="custom-select select2"  name="OrganizationParentAccount" id="OrganizationParentAccount" disabled>
                                        <option value="NULL">-- Select --</option>
                                        @foreach ($bapa as $item)
                                            <option value="{{ $item->OrganizationParentAccount }}">{{ $item->OrganizationParentAccount }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('serviceAccounts.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@push('page_scripts')
<script>
    $(document).ready(function() {
        checkBapa()
        checkDistribution()

        $('#BAPA').change(function() {
            checkBapa()
        })

        $('#ForDistribution').change(function() {
            checkDistribution()
        })
    })

    function checkBapa() {
        if ($('#BAPA').is(':checked')) {
            $('#OrganizationParentAccount').removeAttr('disabled')
        } else {
            $('#OrganizationParentAccount').attr('disabled', 'disabled')
            $("#OrganizationParentAccount").val("NULL").change()
        }
    }

    function checkDistribution() {
        if ($('#ForDistribution').is(':checked')) {
            $('#DistributionAccountCode').removeAttr('disabled')
        } else {
            $('#DistributionAccountCode').attr('disabled', 'disabled')
            $("#DistributionAccountCode").val("").change()
        }
    }
</script>
@endpush