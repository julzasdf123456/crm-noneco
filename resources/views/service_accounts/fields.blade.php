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
                {!! Form::text('ServiceAccountName', $serviceConnection!=null ? $serviceConnection->ServiceAccountName : '', ['class' => 'form-control','maxlength' => 600,'maxlength' => 600]) !!}
            </div>
        </div>

        <!-- Areacode Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('AreaCode', 'Areacode:') !!}
        </div>

        <div class="col-lg-4 col-md-2">
            <div class="input-group">
                {!! Form::text('AreaCode', null, ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
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
                {!! Form::text('Purok', $serviceConnection!=null ? $serviceConnection->Sitio : '', ['class' => 'form-control','maxlength' => 600,'maxlength' => 600]) !!}
            </div>
        </div>

        <!-- Barangay Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('Barangay', 'Barangay:') !!}
        </div>

        <div class="col-lg-3 col-md-2">
            <div class="input-group">
                {!! Form::select('Barangay', $barangays, $serviceConnection!=null ? $serviceConnection->Barangay : '', ['class' => 'form-control', 'id' => 'BarangaySA']) !!}
            </div>
        </div>

        <!-- Town Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('Town', 'Town:') !!}
        </div>

        <div class="col-lg-3 col-md-2">
            <div class="input-group">
                {!! Form::select('Town', $town, $serviceConnection!=null ? $serviceConnection->Town : '', ['class' => 'form-control', 'id' => 'TownSA']) !!}
            </div>
        </div>
    </div> 
</div>

<div class="divider"></div>

{{-- STATUS --}}
<div class="form-group col-sm-12">
    <div class="row">
        <!-- Accountstatus Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('AccountStatus', 'Status:') !!}
        </div>

        <div class="col-lg-3 col-md-2">
            <div class="input-group">
                {!! Form::select('AccountStatus', ['ACTIVE' => 'ACTIVE', 'DISCONNECTED' => 'DISCONNECTED'], null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <!-- Purok Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('AccountType', 'Acct. Type:') !!}
        </div>

        <div class="col-lg-3 col-md-2">
            <div class="input-group">
                <select class="form-control" name="AccountType" id="AccountType">
                    @foreach ($accountTypes as $item)
                        <option value="{{ $item->Alias }}" f-name="{{ $item->Alias }}" {{ $item->id==$serviceConnection->AccountType ? 'selected' : '' }}>{{ $item->AccountType }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-md-2" style="margin-top: 5px;">
            <i id="AccountTypeFull"></i>
        </div>
    </div> 
</div>

{{-- METER READER --}}
<div class="form-group col-sm-12">
    <div class="row">
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

        <!-- Distribution Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('ForDistribution', 'For Distribution:') !!}
        </div>

        <div class="col-lg-1 col-md-1">
            <div class="input-group">
                {{ Form::checkbox('ForDistribution', 'Yes', false, ['class' => 'custom-checkbox']) }}
            </div>
        </div>

        <!-- Senior Citizen Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('SeniorCitizen', 'Senior Citizen:') !!}
        </div>

        <div class="col-lg-1 col-md-1">
            <div class="input-group">
                {{ Form::checkbox('SeniorCitizen', 'Yes', false, ['class' => 'custom-checkbox']) }}
            </div>
        </div>
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
