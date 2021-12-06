
<div class="form-group col-sm-12">
    <div class="row">
        <!-- Transformernumber Field -->
        <div class="col-lg-2 col-md-2">
            {!! Form::label('TransformerNumber', 'Transformer Number:') !!}
        </div>

        <div class="col-lg-5 col-md-4">
            <div class="input-group">
                {!! Form::text('TransformerNumber', $meterAndTransformer!=null ? $meterAndTransformer->TransformerNumber : null, ['class' => 'form-control','maxlength' => 120,'maxlength' => 120]) !!}
            </div>
        </div>

        <!-- Rating Field -->
        <div class="col-lg-2 col-md-2">
            {!! Form::label('Rating', 'Rating (in KVA):') !!}
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="input-group">
                {!! Form::text('Rating', $meterAndTransformer!=null ? $meterAndTransformer->TransformerRating : null, ['class' => 'form-control','maxlength' => 20,'maxlength' => 20]) !!}
            </div>
        </div>
    </div> 
</div>

<div class="form-group col-sm-12">
    <div class="row">
        <!-- Rentalfee Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('RentalFee', 'Rental Fee:') !!}
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="input-group">
                {!! Form::number('RentalFee', null, ['class' => 'form-control','maxlength' => 30,'maxlength' => 30, 'step' => 'any']) !!}
            </div>
        </div>

        <!-- Load Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('Load', 'Load:') !!}
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="input-group">
                {!! Form::text('Load', null, ['class' => 'form-control','maxlength' => 50,'maxlength' => 50]) !!}
            </div>
        </div>

        <!-- CoreLoss Field -->
        <div class="col-lg-1 col-md-2">
            {!! Form::label('Coreloss', 'Coreloss:') !!}
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="input-group">
                {!! Form::text('Coreloss', null, ['class' => 'form-control','maxlength' => 50,'maxlength' => 50, 'step' => 'any']) !!}
            </div>
        </div>
    </div> 
</div>

<div class="divider"></div>

<div class="form-group col-sm-12">
    <div class="row">
        <!-- Main Field -->
        <div class="col-lg-1 col-md-1">
            {!! Form::label('Main', 'Main:') !!}
        </div>

        <div class="col-lg-1 col-md-1">
            <div class="input-group">
                {{ Form::checkbox('Main', 'Yes', false, ['class' => 'custom-checkbox']) }}
            </div>
        </div>

        <!-- BAPA Field -->
        <div class="col-lg-1 col-md-1">
            {!! Form::label('BAPA', 'BAPA:') !!}
        </div>

        <div class="col-lg-1 col-md-1">
            <div class="input-group">
                {{ Form::checkbox('BAPA', 'BAPA', true, ['class' => 'custom-checkbox']) }}
            </div>
        </div>

        <!-- 5% Field -->
        <div class="col-lg-1 col-md-1">
            {!! Form::label('Evat5Percent', '5% EVAT:') !!}
        </div>

        <div class="col-lg-1 col-md-1">
            <div class="input-group">
                {{ Form::checkbox('Evat5Percent', 'Yes', false, ['class' => 'custom-checkbox']) }}
            </div>
        </div>

        <!-- 2% Field -->
        <div class="col-lg-1 col-md-1">
            {!! Form::label('Ewt2Percent', '2% EWT:') !!}
        </div>

        <div class="col-lg-1 col-md-1">
            <div class="input-group">
                {{ Form::checkbox('Ewt2Percent', 'Yes', false, ['class' => 'custom-checkbox']) }}
            </div>
        </div>
    </div> 
</div>