<?php

use App\Models\MemberConsumers;
use App\Models\ServiceConnections;
use App\Models\IDGenerator;

?>

@if($cond == 'new') 
    <input type="hidden" name="id" id="Membership_Id" value="{{ IDGenerator::generateID() }}">
@else 
    <input type="hidden" name="id" id="Membership_Id" value="{{ $serviceConnections->id }}">
@endif

<!-- Connectionapplicationtype Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('ConnectionApplicationType', 'Application Type') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                </div>
                {!! Form::select('ConnectionApplicationType', ['New Installation' => 'New Installation', 'Rewiring' => 'Rewiring', 'Street Lighting' => 'Street Lighting'], $cond=='new' ? '' : $serviceConnections->ConnectionApplicationType, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- BuildingType Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('BuildingType', 'Building Type') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-tools"></i></span>
                </div>
                {!! Form::select('BuildingType', ['Concrete' => 'Concrete', 'Non-Concrete' => 'Non-Concrete', 'Special Lighting' => 'Special Lighting'], $cond=='new' ? '' : $serviceConnections->BuildingType, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- Memberconsumerid Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('MemberConsumerId', 'Member Consumer ID') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                </div>
                {!! Form::text('MemberConsumerId', $cond=='new' ? $memberConsumer->ConsumerId : $serviceConnections->MemberConsumerId, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255, 'readonly' => 'true']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Dateofapplication Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('DateOfApplication', 'Date of Application') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                </div>
                    {!! Form::text('DateOfApplication', $cond=='new' ? date('Y-m-d') : $serviceConnections->DateOfApplication, ['class' => 'form-control','id'=>'DateOfApplication']) !!}
            </div>
        </div>
    </div> 
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateOfApplication').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<div class="divider"></div>
<br>

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
                {!! Form::text('ServiceAccountName', $cond=='new' ? MemberConsumers::serializeMemberNameFormal($memberConsumer) : $serviceConnections->ServiceAccountName, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Accountcount Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('AccountCount', 'Account Count') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search-plus"></i></span>
                </div>
                {!! Form::text('AccountCount', $cond=='new' ? (ServiceConnections::getAccountCount($memberConsumer->ConsumerId)+1) : $serviceConnections->AccountCount, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255, 'readonly' => 'true']) !!}
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
                {!! Form::select('Town', $towns, $cond=='new' ? $memberConsumer->TownId : $serviceConnections->TownId, ['class' => 'form-control']) !!}
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
                {!! Form::text('Sitio', $cond=='new' ? $memberConsumer->Sitio : $serviceConnections->Sitio, ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000, 'placeholder' => 'Sitio']) !!}
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
                {!! Form::text('ContactNumber', $cond=='new' ? $memberConsumer->ContactNumbers : $serviceConnections->ContactNumber, ['class' => 'form-control','maxlength' => 300,'maxlength' => 300, 'placeholder' => 'Contact Numbers']) !!}
            </div>
        </div>
    </div> 
</div>

<!-- Emailaddress Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('EmailAddress', 'Email Address') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-envelope-open"></i></span>
                </div>
                {!! Form::text('EmailAddress', $cond=='new' ? $memberConsumer->EmailAddress : $serviceConnections->EmailAddress, ['class' => 'form-control','maxlength' => 300,'maxlength' => 300, 'placeholder' => 'Email Address']) !!}
            </div>
        </div>
    </div> 
</div>

<div class="divider"></div>
<br>

<!-- Accounttype Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('AccountType', 'Account Type') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                </div>
                {!! Form::select('AccountType', $accountTypes, $cond=='new' ? '' : $serviceConnections->AccountType, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- Accountorganization Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('AccountOrganization', 'Account Classification') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                </div>
                {!! Form::select('AccountOrganization', ['Individual' => 'Individual', 'BAPA' => 'BAPA', 'ECA' => 'ECA'], $cond=='new' ? '' : $serviceConnections->AccountOrganization, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- Organizationaccountnumber Field -->
<div id="organizationNo" class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('OrganizationAccountNumber', 'Organization Account No') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                </div>
                {!! Form::text('OrganizationAccountNumber', null, ['class' => 'form-control','maxlength' => 100,'maxlength' => 100, 'placeholder' => 'Organization Account Number']) !!}
            </div>
        </div>
    </div> 
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#AccountOrganization').on('change', function() {
            if (this.value == 'Individual') {
                $('#organizationNo').hide();
            } else {
                $('#organizationNo').show();
            }
        });
    </script>
@endpush

<!-- Accountapplicationtype Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('AccountApplicationType', 'Energization Classification') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                </div>
                {!! Form::select('AccountApplicationType', ['Permanent' => 'Permanent', 'Temporary' => 'Temporary'], $cond=='new' ? '' : $serviceConnections->AccountApplicationType, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>  
</div>

<!-- Isnihe Field -->
<input type="hidden" name="IsNIHE" value="NO">

<!-- Notes Field -->
<div class="form-group col-sm-12">
    <div class="row">
        <div class="col-lg-3 col-md-5">
            {!! Form::label('Notes', 'Notes/Comments') !!}
        </div>

        <div class="col-lg-9 col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-comments"></i></span>
                </div>
                {!! Form::text('Notes', null, ['class' => 'form-control','maxlength' => 100,'maxlength' => 100, 'placeholder' => 'Notes or Comments']) !!}
            </div>
        </div>
    </div> 
</div>

<p id="Def_Brgy" style="display: none;">{{ $cond=='new' ? $memberConsumer->BarangayId : $serviceConnections->Barangay }}</p>